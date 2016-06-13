<?php

/*

Plugin Name: Sub Page Navigation Widget
Description: You can add this widget to sidebars on pages to show all sub pages of the current one.
Plugin URI: http://dennishoppe.de/wordpress-plugins/sub-page-navigation
Version: 1.0.4
Author: Dennis Hoppe
Author URI: http://DennisHoppe.de

*/

// Please think about a donation
If (Is_File(DirName(__FILE__).'/donate.php')) Include DirName(__FILE__).'/donate.php';

// Plugin Class
If (!Class_Exists('widget_sub_page_navigation')){
Class widget_sub_page_navigation Extends WP_Widget {
  var $base_url;
  var $arr_option;
  
  Function __construct(){
    // Get ready to translate
    $this->Load_TextDomain();
    
    // Setup the Widget data
    parent::__construct (
      False,
      $this->t('Sub Pages'),
      Array('description' => $this->t('You can add this widget to sidebars on pages to show all sub pages of the current one.'))
    );

    // Read base_url
    $this->base_url = get_bloginfo('wpurl').'/'.Str_Replace("\\", '/', SubStr(RealPath(DirName(__FILE__)), Strlen(ABSPATH)));
  }
  
  Function Load_TextDomain(){
    $locale = Apply_Filters( 'plugin_locale', get_locale(), __CLASS__ );
    load_textdomain (__CLASS__, DirName(__FILE__).'/language/' . $locale . '.mo');
  }
  
  Function t ($text, $context = ''){
    // Translates the string $text with context $context
    If ($context == '')
      return __($text, __CLASS__);
    Else
      return _x($text, $context, __CLASS__);
  }
  
  Function default_options(){
    // Default settings
    return Array(
      'title' => $this->t('Navigation'),
      'sortby' => 'menu_order, post_title'    
    );
  }
  
  Function load_options($options){
    $options = (ARRAY) $options;
    
    // Delete empty values
    ForEach ($options AS $key => $value)
      If (!$value) Unset($options[$key]);
    
    // Load options
    $this->arr_option = Array_Merge ($this->default_options(), $options);
  }
  
  Function get_option($key, $default = False){
    If (IsSet($this->arr_option[$key]) && $this->arr_option[$key])
      return $this->arr_option[$key];
    Else
      return $default;
  }
  
  Function set_option($key, $value){
    $this->arr_option[$key] = $value;
  }

  Function widget ($widget_args, $options){
    // Load options
    $this->load_options ($options); Unset ($options);
    
    // if this isn't a page we bail out.
    If ( !is_page() ) return False;
    
    If ($GLOBALS['post']->post_parent != 0)
      $parent = get_post($GLOBALS['post']->post_parent);
    Else
      $parent = False;

    // Default Args for selecting sub pages
    $page_args = Array(
      'title_li' => '',
      'child_of' => $GLOBALS['post']->ID,
      'sort_column' => $this->get_option('sortby'),
      'exclude'  => $this->get_option('exclude'),
      'depth'    => 1,
      'echo'     => False
    );

    // What to show?
    If ($page_listing = wp_list_pages($page_args)){
      // There are some sub pages
      If ($this->get_option('replace_widget_title'))
        $this->set_option('title', get_the_title());
    }
    Else {
      // There are no sub pages
      
      If (!$parent && $this->get_option('do_not_show_on_top_leves_without_subs')){
        // there are no sub pages and there is no parent.
        return False;
      }
      
      // there are no sub pages we try to show all pages in the same depth level.
      $page_args['child_of'] = ($parent ? $parent->ID : 0);
      
      // If the parent page is a real page its title will be our widget title
      If ($parent && $this->get_option('replace_widget_title'))
        $this->set_option('title', $parent->post_title);
      
      // Read the subpages again
      If (!$page_listing = wp_list_pages($page_args)) return False;
    }

    // Widget output    
    Echo $widget_args['before_widget'];
    
      // Widget title
      If (!$this->get_option('hide_widget_title'))
        Echo $widget_args['before_title'] . $this->get_option('title') . $widget_args['after_title'];
      
      // output Page listing
      Echo '<ul>';
        Echo $page_listing;
        If (!$this->get_option('hide_upward_link') && $parent)
          Echo '<li class="upward"><a href="'.get_permalink($parent->ID).'" title="'.$parent->post_title.'">'.$parent->post_title.'</a></li>';
      Echo '</ul>';
    
    // Widget bottom  
    Echo $widget_args['after_widget'];
  }
 
  Function form ($options){
    // Load options
    $this->load_options ($options); Unset ($options);
    
    Include DirName(__FILE__) . '/form.php';
  }
 
  Function update ($new_settings, $old_settings){ return $new_settings; }

} /* End of Class */
Add_Action ('widgets_init', Create_Function ('','Register_Widget(\'widget_sub_page_navigation\');') );
} /* End of If-Class-Exists-Condition */
/* End of File */
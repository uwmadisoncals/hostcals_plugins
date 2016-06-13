<?php

/*
Plugin Name: Cals WP Twitterfetcher Widget
Plugin URI: http://cals.wisc.edu/developers/
Description: Display Tweets in WordPress using an extension of Jason Mayes Twitter Post Fetcher v10.0 ( www.jasonmayes.com ).
Author:Al Nemec and Daniel Dropik - CALS Information Technology
Version: 1
Author URI: http://cals.wisc.edu/developers/
*/

class cals_twitterfetcher extends WP_Widget {

		
		//$plugins_url = plugins_url();
		//echo $plugins_url;
	    //wp_enqueue_script( 'twitfetchmain', 'twitfetchmain.js' );

	//Constructor--initiate the widget
		function cals_twitterfetcher(){
			//add_action('wp_footer',array($this,'front_end_scripts'));
			//parent::WP_Widget(false, $name = __('Cals WP Twitterfetcher Widget','cals-twitterfetcher')); //need to use __() syntax eventually
		  parent::WP_Widget(false, $name = __('Cals WP Twitterfetcher Widget', 'cals-twitterfetcher') );
		}


// widget form creation create the widget form in the administration back-end
function form($instance) { 
		// Check values 
			if( $instance) { 
		     $WidgetID = esc_attr($instance['WidgetID']); 
		     $checkbox = esc_attr($instance['checkbox']); 
		  	 $checkbox2 = esc_attr($instance['checkbox2']); 
		  	 $select = esc_attr($instance['select']);
		  	 $checkbox3 = esc_attr($instance['checkbox3']); 
		  	 $checkbox4 = esc_attr($instance['checkbox4']); 
		  	 $checkbox5 = esc_attr($instance['checkbox5']); 

		} else { 
		     $WidgetID = '';  
		     $checkbox = ''; 
		     $checkbox2 = ''; 
		     $select = '';
		     $checkbox3 = ''; 
		     $checkbox4 = ''; 
		     $checkbox5 = ''; 
		} 

		?>
		<!-- textfield form for twitter ID -->
		<p>
		<label for="<?php echo $this->get_field_id('WidgetID'); ?>"><?php _e('Twitter Widget ID:', 'cals-twitterfetcher'); ?></label>
		<input id="<?php echo $this->get_field_id('WidgetID'); ?>" name="<?php echo $this->get_field_name('WidgetID'); ?>" type="text" value="<?php echo $WidgetID; ?>" />
		</p>

		<!-- select form for max tweets -->
		<p>
		<label for="<?php echo $this->get_field_id('select'); ?>"><?php _e('Number of tweets to display', 'cals-twitterfetcher'); ?></label>
		<select name="<?php echo $this->get_field_name('select'); ?>" id="<?php echo $this->get_field_id('select'); ?>" class="widefat">
		<?php
		$options = (range(0, 20) );
		foreach ($options as $option) {
		echo '<option value="' . $option . '" id="' . $option . '"', $select == $option ? ' selected="selected"' : '', '>', $option, '</option>';
		}
		?>
		</select>
		</p>

		<!-- checkbox for Hyperlinked option -->
		<p>
		<input id="<?php echo $this->get_field_id('checkbox'); ?>" name="<?php echo $this->get_field_name('checkbox'); ?>" type="checkbox" value="1" <?php checked( '1', $checkbox ); ?> />
		<label for="<?php echo $this->get_field_id('checkbox'); ?>"><?php _e('Hyperlink URLs & hastags', 'cals-twitterfetcher'); ?></label>
		</p>
		
		<!-- checkbox for photo option -->
		<p>
		<input id="<?php echo $this->get_field_id('checkbox2'); ?>" name="<?php echo $this->get_field_name('checkbox2'); ?>" type="checkbox" value="1" <?php checked( '1', $checkbox2 ); ?> />
		<label for="<?php echo $this->get_field_id('checkbox2'); ?>"><?php _e('Display twitter icon', 'cals-twitterfetcher'); ?></label>
		</p>

		<!-- checkbox for showing time of tweet  -->
		<p>
		<input id="<?php echo $this->get_field_id('checkbox3'); ?>" name="<?php echo $this->get_field_name('checkbox3'); ?>" type="checkbox" value="1" <?php checked( '1', $checkbox3 ); ?> />
		<label for="<?php echo $this->get_field_id('checkbox3'); ?>"><?php _e('Display time of tweet', 'cals-twitterfetcher'); ?></label>
		</p>

		<!-- checkbox for showing retweets-->
		<p>
		<input id="<?php echo $this->get_field_id('checkbox4'); ?>" name="<?php echo $this->get_field_name('checkbox4'); ?>" type="checkbox" value="1" <?php checked( '1', $checkbox4 ); ?> />
		<label for="<?php echo $this->get_field_id('checkbox4'); ?>"><?php _e('Show Re-tweets', 'cals-twitterfetcher'); ?></label>
		</p>

		<!-- checkbox for Reply/Retweet/Favorite links -->
		<p>
		<input id="<?php echo $this->get_field_id('checkbox5'); ?>" name="<?php echo $this->get_field_name('checkbox5'); ?>" type="checkbox" value="1" <?php checked( '1', $checkbox5 ); ?> />
		<label for="<?php echo $this->get_field_id('checkbox5'); ?>"><?php _e('Include links for Reply, Retweet, Favorite', 'cals-twitterfetcher'); ?></label>
		</p>
		
<?php 
}


	/**
	 *Update--save widget data during edition
	 */
	
		function update($new_instance, $old_instance) {
      $instance = $old_instance;
      // Fields
      $instance['WidgetID'] = strip_tags($new_instance['WidgetID']);    //define variables
      $instance['checkbox'] = strip_tags($new_instance['checkbox']);
      $instance['checkbox2'] = strip_tags($new_instance['checkbox2']);
      $instance['select'] = strip_tags($new_instance['select']);
      $instance['checkbox3'] = strip_tags($new_instance['checkbox3']);
      $instance['checkbox4'] = strip_tags($new_instance['checkbox4']);
       $instance['checkbox5'] = strip_tags($new_instance['checkbox5']);
     return $instance;
}



	/**
	 * widget--display the widget content on the front-end
	 */

function widget($args, $instance) {
   extract( $args );
   // these are the widget options
   $WidgetID = $instance['WidgetID'];
   $checkbox=$instance['checkbox'];
   $checkbox2=$instance['checkbox2'];
   $select=$instance['select'];
   $checkbox3=$instance['checkbox3'];
   $checkbox4=$instance['checkbox4'];
   $checkbox5=$instance['checkbox5'];
   echo $before_widget;
   // Display the widget
   echo '<style>#twitFetch ul { margin-left: 0px; } #twitFetch ul li { padding-bottom: 2.4em; position: relative; } .interact { position:absolute; bottom: 0px; right: 0px; } </style>';
  
   echo '<div><h3 class="widget-title twitterHeader">Tweets from @LabArchives</h3></div><div id="twitFetch"></div>';
   echo '<div>';

   // Output Widget ID
   if( $WidgetID ) {
      echo '<p id="cals_twitterfetcher_0" style="display:none;">'.$WidgetID.'</p>';
   }

 // output max No. of tweets select form choice
if( $select !== '0' ) {
     echo '<p id="cals_twitterfetcher_1" style="display:none;">'.$select.'</p>';
   }else{
   	echo '<p style="display:none;"></p>';
   }

 // Output hyperlink checkbox choice
   if( $checkbox AND $checkbox == '1' ) {
     echo '<p id="cals_twitterfetcher_2" style="display:none;">'.__('true', 'cals-twitterfetcher').'</p>';
   }

   // Output photo checkbox choice
   if( $checkbox2 AND $checkbox2 == '1' ) {
     echo '<p id="cals_twitterfetcher_3" style="display:none;">'.__('true', 'cals-twitterfetcher').'</p>';
   }

//output timestamp checkbox choice
if( $checkbox3 AND $checkbox3 == '1' ) {
     echo '<p id="cals_twitterfetcher_4" style="display:none;">'.__('true', 'cals-twitterfetcher').'</p>';
   }

// Output re-tweet checkbox choice
   if( $checkbox4 AND $checkbox4 == '1' ) {
     echo '<p id="cals_twitterfetcher_5" style="display:none;">'.__('true', 'cals-twitterfetcher').'</p>';
   }
   // Output reply/re-tweet/favorite  checkbox choice
   if( $checkbox5 AND $checkbox5 == '1' ) {
     echo '<p id="cals_twitterfetcher_6" style="display:none;">'.__('true', 'cals-twitterfetcher').'</p>';
   }

   echo '</div>';
   echo '<script>jQuery( document ).ready(function( $ ) { setTimeout(function() { $(".interact").hide(); $("#twitFetch li").mouseover(function() { $(this).find(".interact").show(); });  $("#twitFetch li").mouseout(function() { $(this).find(".interact").hide(); });  },500); });</script>';
   echo $after_widget;
}

} 
	
// class Foo_Widget

// register Foo_Widget widget, enables use in the wordpress dashboard

add_action('widgets_init', create_function('', 'return register_widget("cals_twitterfetcher");'));
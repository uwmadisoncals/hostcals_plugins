<?php 
//Tabs PRO Widget
class WordPress_Pro_Post_Tabs_Widget extends WP_Widget {
	function WordPress_Pro_Post_Tabs_Widget() {
		$widget_options = array('classname' => 'wpts_widget', 'description' => 'Add Tabs to Widgetized Section' );
		$this->WP_Widget('wpts_wid', 'Tabs PRO', $widget_options);
	}

	function widget($args, $instance) {
		extract($args, EXTR_SKIP);
	    global $wpts;
		
		echo $before_widget;
		
		$post_id = empty($instance['post_id']) ? '' : apply_filters('widget_post_id', $instance['post_id']);

		echo $before_title . $after_title; 
			//$tab_post=get_post( $post_id );
			//echo do_shortcode($tab_post->post_content);
			global $post;
			$post=get_post( $post_id );
			setup_postdata( $post ); 
			the_content();
			wp_reset_postdata();
		echo $after_widget;

}

	function update($new_instance, $old_instance) {
	    global $wpts;
		$instance = $old_instance;	
		$instance['post_id'] = strip_tags($new_instance['post_id']);
		return $instance;
	}

	function form($instance) {
	    global $wpts;

		$instance = wp_parse_args( (array) $instance, array( 'post_id' => '' ) );
		$post_id = strip_tags($instance['post_id']);
			
			$posts = get_posts('post_type=custom_tab');
			$cpost_html='<option value="" selected >Select the Custom Tab</option>';
	 
		  foreach ($posts as $post) { 
			 if($post->ID==$post_id){$selected = 'selected';} else{$selected='';}
			 $cpost_html =$cpost_html.'<option value="'.$post->ID.'" '.$selected.'>'.$post->post_title.'</option>';
		  } 
	?>
		  <p><label for="<?php echo $this->get_field_id('post_id'); ?>">Select the Custom Tab: <select class="widefat" id="<?php echo $this->get_field_id('post_id'); ?>" name="<?php echo $this->get_field_name('post_id'); ?>"><?php echo $cpost_html;?></select></label></p>
<?php  }
}
add_action( 'widgets_init', create_function('', 'return register_widget("WordPress_Pro_Post_Tabs_Widget");') );

?>

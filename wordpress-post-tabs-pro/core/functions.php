<?php
function wpts_include_from_folder($dir, $ext='php'){
	$opened_dir = opendir($dir);
	while ($element=readdir($opened_dir)){
	   $fext=substr($element,strlen($ext)*-1);
	   if(($element!='.') && ($element!='..') && ($fext==$ext)){
			include($dir.$element);
	   }
	  else {
			if( is_dir($dir.$element.'/') && ($element!='.') && ($element!='..') ) {
				wpts_include_from_folder($dir.$element.'/', $ext='php');
			}
	   }
	}
	closedir($opened_dir);
}
//Template tag
function get_custom_wordpress_post_tabs( $post_id ){
	if ( $post_id ) {
		global $post;
		$post=get_post( $post_id );
		if( $post ){
			setup_postdata( $post ); 
			the_content();
		}
		wp_reset_postdata();
	}
}
?>

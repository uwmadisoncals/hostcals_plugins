<?php 
/*
Plugin Name: UW Madison Header Slides
Plugin URI: http://www.cals.wisc.edu
Description: Adds Header Slides to your UW Madison 2015 Theme
Version: 1.0
Author: Al Nemec
Author URI: https://alnemec.com
License: GPL
*/

add_action( 'init', 'create_uwmadisonwptheme_post_type' );

function create_uwmadisonwptheme_post_type() {
	register_post_type( 'headerslides',
		array(
			'labels' => array(
				'name' => __( 'Header Slides', 'uw_madison_wp_2015' ),
				'singular_name' => __( 'Header Slide', 'uw_madison_wp_2015' )
			),
		'public' => true,
		'has_archive' => true,
		'supports' => array(
	  'title',
	  'editor',
	  'excerpt',
	  'revisions',
	  'thumbnail',
	  'author',
	  'page-attributes',
	  )
		)
	);
}


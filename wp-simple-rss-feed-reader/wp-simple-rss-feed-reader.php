<?php
    /*
    Plugin Name: WP Simple Rss Feed Reader 
    Description: Plugin to view an rss feed on your page or post.
    [simple-rss feed="http://rss.nytimes.com/services/xml/rss/nyt/InternationalHome.xml" limit=10 hide_description="0" hide_url="0" show_date="1" show_images="1" amount_of_words="10"].
    Or use it as a widget
    Version: 0.8.1
    Author: Viancen
    Author URI: http://viancen.com
    License: GPL2
    */
    require_once(dirname(__FILE__).'/functions.php');
    require_once(dirname(__FILE__).'/widget.php');
    require_once(dirname(__FILE__).'/shorttag.php');   
?>
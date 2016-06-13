<?php
/**
 * Short tag function
 *
 * @param mixed $url
 */

function wp_simple_rss_feed_reader($url, $limit = 50, $hide_description = 0, $hide_url = 0, $show_date = 0, $show_images = 0, $amount_of_words = 0)
{

    if ($amount_of_words == 0) $amount_of_words = 9999;

    $content = SimplRssFetchFeed($url);

    try {
        $xml = new SimpleXmlElement($content);
    } catch (Exception $e) {
        /* the data provided is not valid XML */
        return '<div style="background:#ffa1a1;color:#ff0000;padding:10px;">Unfortunaly, this xml/rss feed does not work correctly...</div>';
        exit();
    }

    if (!is_object($xml)) {
        return 'Unfortunally the feed you provided is not valid...';
    }

    $return = SimplRssParse($xml, $limit, $hide_description, $hide_url, $show_date, $show_images, $amount_of_words);

    return $return;
}


/**
 * Include the shortscriptfunctions for rss
 *
 * enables:
 * [simple-rss feed="http://www.xxx.feed"]
 *
 */
function wp_simple_rss_feed_shorttag($atts)
{
    extract(shortcode_atts(array(
        'feed' => '',
        'hide_description' => 0,
        'hide_url' => 0,
        'show_date' => 0,
        'show_images' => 1,
        'amount_of_words' => 0,
        'limit' => ''
    ), $atts));

    return wp_simple_rss_feed_reader($feed, $limit, $hide_description, $hide_url, $show_date, $show_images, $amount_of_words);
}

//add shortcodes
add_shortcode('simple-rss', 'wp_simple_rss_feed_shorttag');
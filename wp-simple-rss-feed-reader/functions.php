<?php
/**
 * select offset of words from text
 *
 * @param mixed $string
 * @param mixed $words
 */
function SimplRssfirstWords($string, $words = 100)
{
    $string = strip_tags($string, '<p><a><br><li><ul>');
    $wo = explode(" ", $string);
    $c = 0;
    $return = '';
    foreach ($wo as $piece) {
        $c++;
        if ($words > $c)
            $return .= " " . $piece;
    }
    return $return;
}

/**
 * Fetch a document from the internet somewhere
 * and return the string
 *
 * @param $url
 * @return mixed|string
 */
function SimplRssFetchFeed($url)
{

    //decode
    $url = html_entity_decode($url);

    //test feed first
    $content = @file_get_contents($url);

    //access media in a common way
    $content = str_replace('<media:', '<', $content);

    return $content;
}

/**
 * turn feed into html
 *
 * @param mixed $xml
 * @param mixed $limit
 * @param mixed $amount_of_words
 */
function SimplRssParse($xml, $limit = 10, $hide_description = 0, $hide_url = 0, $show_date = 0, $show_images = 1, $amount_of_words = 10)
{
    if (empty($xml)) return '<div style="background:#ffa1a1;color:#ff0000;">Unfortunaly, this xml/rss feed does not work correctly...</div>';

    $return = '';
    $return .= "<ul class=\"wp-simple-rss-list\">";
    $i = 0;

    if ($xml->channel) $main = $xml->channel->item;
    if ($xml->entry) $main = $xml->entry;

    foreach ($main as $item) {
        // $test = $item->media->attributes::thumbnail;

        $i++;

        $titel = $item->title;
        $link = $item->link;

        $date = '';
        if (!empty($item->pubDate)) {
            $date = date('F d, Y ', strtotime($item->pubDate));
        }

        if ($hide_url == 0) {
            $return .= '<li class="wp-simple-rss-item"><h3 class="wp-simple-rss-h3"><a href="' . $link . '" target="_blank" title="' . ($titel) . '" class="wp-simple-rss-link">' . ($titel) . '</a></h3>';
        } else {
            $return .= '<li class="wp-simple-rss-item"><h3 class="wp-simple-rss-h3">' . ($titel) . '</h3>';
        }
        if ($show_date == 1 && !empty($date)) {
            $return .= '<span class="wp-simple-rss-date">' . $date . '</span>';
        }
        if ($hide_description == 0) {
            $img = '';

            if ($show_images == 1) {
                if (isset($item->enclosure['url']) && !empty($item->enclosure['url'])) {
                    $img = '<img src="' . $item->enclosure['url'] . '" align="left" width="100" style="padding-right:10px;padding-bottom:10px;" class="wp-simple-rss-img" >';
                } elseif (isset($item->thumbnail['url']) && !empty($item->thumbnail['url'])) {
                    $img = '<img src="' . $item->thumbnail['url'] . '" align="left" width="100" style="padding-right:10px;padding-bottom:10px;" class="wp-simple-rss-img" >';
                }
            }

            $return .= '<span class="wp-simple-rss-description">' . $img . SimplRssfirstWords($item->description, (int)($amount_of_words + 1)) . '</span>';
        }
        $return .= '</li>';

        if ($i == $limit) break;
    }
    $return .= "</ul>";
    return $return;
}

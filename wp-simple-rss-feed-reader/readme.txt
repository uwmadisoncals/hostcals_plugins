=== WP Simple Rss Feed Reader ===
Contributors: Viancen
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=nieuwenhuizen%40gmail%2ecom&lc=NL&currency_code=EUR&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted
Tags: shortcodes, rssfeed, simple rss, rss reader, twitter, rss widget
Requires at least: 3.0
Tested up to: 4.2.3
Stable tag: 0.8.1

Plugin to view an rss feed on your page or post. Or add a feed as widget. Clean and simple.

== Description ==

Plugin to view an rss feed on your page or post. As a shortcode or widget


== Installation ==

1. Upload contents of `wp-simple-rss-feed-reader` to your `/wp-content/plugins` folder
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Use the shortcode in your posts or pages: [simple-rss feed="http://rss.nytimes.com/services/xml/rss/nyt/InternationalHome.xml" limit="10" show_date="1" hide_description="0" show_images="1" hide_url="0" amount_of_words="10"]
4. Use the widget from the widget menu in the Wordpress admin pages

== Changelog ==

= 0.1 =
* The first release
= 0.5 =
* first complete stable release with a widget functionality
= 0.6 =
* fixed the bug where feeds with oldschool & and ? sighns weren't working
= 0.6.1 =
* removed utf8 decoding, was causing trouble on special characters
= 0.6.2 =
* added a limit function to the shortcode
= 0.6.3 =
* added the option to shorten read more link (widget)
* added the option to not display descriptions (widget)
= 0.6.4 =
* error catching on invalid feeds
* added option to limit the words in the description
* added configuration options in the shorttag
= 0.7 =
* error catching on invalid feeds > improved
* added option to show the pubDate in shorttag and widget
* cleanup of files
= 0.8 =
* added options to remove all url/links and just show the texts
= 0.8.1 =
* added options to show images (media or enclosed thumbnails) show_image, default on
* removed link on top of list (you should add this yourself if you want)

=== Google maps ===
Contributors: Pierre Sudarovich
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=9933153
Tags: Google maps, map, itinerary
Requires at least: 1.5
Tested up to: 3.0
Stable tag: trunk

A plugin that will let you easily embed clean Google maps (without iframe) in your posts.

== Description ==

This plugin will let you easily embed Google maps in your posts. It will produce clean XHTML code without any iframe.

Features:
--------
* add an embed Google map in your post by using this syntax [map:http://permalink_to_your_Google_map 640 480] "width" (640 in the example) and "height" are optional parameters. You can use px or %,
* it could be a recorded map or any other map you're consulting (some complicated map can't be shown) some examples are availables on my [blog](http://pierre.dommiers.com/2009/02/21/google-maps-embed/ ""),
* double-click or use your scrolling mouse button to zoom on,
* internationalization of the plugin,
* XHTML conform,
* tested and working on IE6, IE7, IE8, Firefox, Opera, Chrome, Safari,


== Installation ==

1. Upload the folder `google-maps` to your `/wp-content/plugins/` directory,
2. Activate the plugin through the "Plugins" menu in WordPress,
3. Go to the plugin settings to enter your API key then play with maps :).
 

== Frequently Asked Questions ==

In some explanations, i ask to edit a php file. Be careful to edit them with a good editor like [Notepad++](http://notepad-plus.sourceforge.net/ "") and open each file with the format "UTF-8 without BOM".

= I'v added my first map in one of my post, but when try to see it there's an error message who tells me something like "This website require an API key...". What's wrong ? =
For this plugin to works, you have to firstly ask Google for an API key (its free, quick and easy to obtain).

= I'd like to get the Google maps interface in my native language. How can i do that? =
Edit the file `googlemap-en_US.po` by using a PO file editor such as :
KBabel (Linux) should be available as a package for your Linux distribution, so install the package.
poEdit (Linux/Windows) available from http://www.poedit.net/.
For french in this example, save it as `googlemap-fr_FR.po` a `googlemap-fr_FR.mo` will be automatically generated.
Put the `googlemap-xx_XX.mo` file in the `lang` folder (under `google-maps`), the PO file is just here to generate the MO translation file...

= Ok, i've done what you explain above, but the Google maps interface is still in english How to make it works? =
Open your `wp-config.php` file (at the root of your blog) and search for : `define ('WPLANG', 'xx_XX');` where xx_XX is your language. If this line doesn't exist add it in your file. Save your modifications and re-upload the wp-config on your server.

= Does Marquee works with WP-MU? =
Yes


== Screenshots ==
1. Example of Google maps (with layers).

2. Example of Google maps on my blog.

3. Example of Google maps itinerary.

== Changelog ==

= 1.81 =
* nothing really important, just the update of my blog url ;)

= 1.8 =
* Compatibility with Wordpress version 3.x,
* favor to the use of `define('WP_DEBUG', true);` definition of some forgotten variables :)

= 1.7.1 =
* I've forget to change the code in directions.php and kml.php...

= 1.7 =
* When you call a saved map from Google maps the map type is now kept (map, satellite, hybrid or terrain)

= 1.6 =
* No more need to wait for page load to begin the loading of the map,
* added the possibility to load kml files (you'll can for example add some Picasa web albums ;)),
* added a button more to be able to display Photos, Videos, Wikipedia, Webcams. You'll can check it on the settings page of the plugin by default it's on
* fixed, the "zoom in" label have now the same width that the map,
* fixed, load just one time the Google script with the API key.

= 1.5 =
* (23 Nov 2009) - First Release.

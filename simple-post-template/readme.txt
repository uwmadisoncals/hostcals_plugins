=== Plugin Name ===
Contributors: clifgriffin
Donate link: http://cgd.io/downloads/advanced-content-templates
Tags: post template, templates, post from template, posts
Requires at least: 2.5.1
Tested up to: 3.8.1
Stable tag: 2.0.16

Simple, pre-written post content templates for your posts and pages. Use HTML and PHP to pre-fill new posts with the default content of your choice.

== Description ==

**Custom Post Types, Custom Fields, Tags, Categories, Featured Images: if you need any of these, you should check out our premium plugin: <a href="http://cgd.io/downloads/advanced-content-templates">Advanced Content Templates</a>**

Many bloggers use a similar format for each of their posts. WordPress doesn't provide a native way to pre-fill your new posts with default content. This plugin provides the simplest solution to this problem. It takes 5 minutes to install and setup. The templates allows you to define a title, post body, and even an excerpt. Each template supports HTML, as well as PHP for dynamically generated content.

**Support**

If you need support, use: [Contact CGD](http://cgd.io/contact/ "Contact CGD")

**Hire Me**

If you need a WordPress developer, I'm available for hire: [Contact CGD](http://cgd.io/contact/ "Contact CGD")

= Version History =
**Version 2.0.16**

* Fix upgrade menu item bug that causes menu to show up for non-privileged users. 

**Version 2.0.15**

* Change menu permission to edit_others_pages so that editors can manage Simple Content Templates. 

**Version 2.0.14**

* Fix one more menu related issue with Subscriber role. 

**Version 2.0.13**

* Change role requirement to manage_options from edit_dashboard so Subscribers and other low permission users can’t see SCT menu or modify settings. 

**Version 2.0.12**

* In my clean up of the last release, I forgot to apply the fixes to the template editor, which caused some abnormalities.  I apologize!
* Warning: Tiny MCE isn’t a fan of PHP. If you need to do php tags in text view, switching to the Visual view will cause them to be commented out. I’d work in one view or the other. 

**Version 2.0.11**

* Tighten up HTML parsing with PHP.  May make PHP parsing more strict at the expense of fewer HTML template problems.
* Fix issue loading template manually on page post type. 

**Version 2.0.10**

* Renamed plugin to Simple Content Templates.  This should be a better description of what the plugin actually does!
* Fixed bug with editing an existing template where HTML was loaded improperly into editor.

**Version 2.0.9**

* Sort templates alphabetically.

**Version 2.0.8**

* Properly handles the WordPress propensity to add quotes to all request data.

**Version 2.0.7**

* Disables that horrible 'feature' known as magic_quotes_gpc, if it is enabled (< PHP 5.4 only).

**Version 2.0.6.2**

* Fixed PHP eval handling.

**Version 2.0.6.1**

* UTF-8 support!

**Version 2.0.6**

* WYSIWYG editor for post content.
* Reformatted settings page.
* Now uses singleton pattern.
* Added a few shameless plugs for Advanced Content Templates <http://cgd.io/downloads/advanced-content-templates>

**Version 2.0.5**

* Bug fixes.

**Version 2.0.4:**

* Sacre bleu! A change in the way Wordpress handles plugin updates 3 major releases ago prevented upgrades from 1.x to 2.x from working. This should be fixed with this release. So sorry for the confusion!

**Version 2.0.3:**

* Fixed bug where HTML in templates messed up templates layout.
* Fixed bug with 1.x to 2.x migration script. Previous versions did not work at all.

**Version 2.0.2:**

* Fixed bug that prevented proper use of HTML in templates.

**Version 2.0.1:**

* Fixed error in upgrade process. Should import old templates no matter what, now. Error in logic prevented this.

**Version 2.0:**

* NEW! Add / maintain multiple templates.
* Rewritten from the ground up to support OOP coding techniques, and the latest Wordpress methods.

**Version 1.1.0.2:**

* Fixes blank excerpt problem
* Fixes deprecated options page syntax.
* Sorry for taking so long to update this. 

**Version 1.1.0.1:**

* Fixes problem with blank excerpts.

**Version 1.1:**

* You can now specify a default excerpt in your template (in case you wanted to)
* Rewrote backend to use standard supported methods and less hacking.
* Completely fixed issue with template content breaking "Insert Template" button. Contents is now encoded to HEX and decoded upon insert.
* Removed upgrade option for legacy versions. I never received any feedback on this feature and suspect it was rarely used, if used at all. 
* Redesigned admin panel for aesthetic and informational purposes. Now includes PHP examples and considerations.

**Version 1.0.0.1:**

* Fixed security issue. Now only Administrators can access admin pages. 

**Version 1.0:**

* Moved *all* settings to administration pages under Settings -> Simple Content Template.
* Moved content of template and title to WordPress options.
* Provided upgrade mechanism to convert old templates to the new format. (May not work for all applications. Some may work but may be more complex than neccessary.)
* Added optional "Insert Template" button to new post page. You can now determine from the settings page whether or not the template will be automatically applied to all new posts.
* Plugin is now upgrade proof. Settings are stored using Wordpress's setting functions.
* Various other improvements were made to address inefficiencies in the first version.

**Version 0.1:**

* Original release.

== Installation ==

1. Upload the directory "simple-post-template" to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Find "Simple Content Templates" in menu on left.
1. Add as many templates as you wish. (review help page for information on adding dynamic content)
1. Configure settings and select a default template for auto-fill, if desired.

== Frequently Asked Questions ==

= Does it support multiple templates? =

As of version 2.0, yes!

= Does it work with pages? =

Yep!

= Can it work with custom post types? =

For custom post type support and much more, please check out [Advanced Content Templates](http://cgd.io/downloads/advanced-content-templates/ "Advanced Content Templates")

= Can feature x be added? =

Maybe. E-mail me: clifgriffin+hireme@gmail.com

= Can Simple Content Template specify default custom fields? = 

For custom fields and much more, check out [Advanced Content Templates](http://cgd.io/downloads/advanced-content-templates/ "Advanced Content Templates")

== Screenshots ==

1. The meta box in the post/page editor.
2. The settings view.
3. The templates view.
4. New template view.
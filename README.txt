=== Plugin Name ===
Contributors: annedorko
Donate link: https://paypal.me/annedorko/
Tags: show post, random posts, automatic, featured
Requires at least: 3.0.1
Tested up to: 4.7.2
Stable tag: 0.1.0
License: GPLv3 or any later version
License URI: http://www.gnu.org/licenses/gpl-3.0.html

A simple plugin that allows you to automatically feature a post of the day.

== Description ==

This plugin is a simple and straightforward way to set one random post as the “______ of the Day”. It allows for custom post types, categories, tags, and custom taxonomies. Each unique query is cached until the end of the day using transients.

=== Using the Shortcode ===

Once the plugin is activated, you can use a shortcode to display your post of the day within any text area that supports shortcodes.

==== Default ====
```
[oftheday]
```
This will choose a random post (the default post type) and set it until the end of the day.

==== Post Types ====
```
[oftheday type='page']
```
You can choose any post type registered on your site using the _type_ selector.

==== Taxonomies ====
```
[oftheday category='Example Category']
```
You can choose any category using the _category_ selector. This searches for the Name of a category.

```
[oftheday tag='Example Tag']
```
You can choose any tag using the _tag_ selector. This searches for the Name of a tag.

```
[oftheday custom-taxonomy='Example Taxonomy Name']
```
You can choose any taxonomy registered on your site using its coresponding _taxonomy_ selector. For example, if your taxonomy is registered using the unique ID _sections_, this would be sections='Example Section'.

**Please note:** Your taxonomy should be registered to the post type you specify. Otherwise, the shortcode will return no posts.

== Installation ==

1. Upload `of-the-day.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

That’s it!

== Frequently Asked Questions ==

= A question that someone might have =

An answer to that question.

= What about foo bar? =

Answer to foo bar dilemma.

== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the /assets directory or the directory that contains the stable readme.txt (tags or trunk). Screenshots in the /assets
directory take precedence. For example, `/assets/screenshot-1.png` would win over `/tags/4.3/screenshot-1.png`
(or jpg, jpeg, gif).
2. This is the second screen shot

== Changelog ==

= 0.1.0 =
* Initial plugin release

== Upgrade Notice ==

= 0.1.0 =
This version is the initial release of the plugin.

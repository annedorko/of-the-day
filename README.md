# Of The Day

This plugin is a simple and straightforward way to set one random post as the “______ of the Day”. It allows for custom post types, categories, tags, and custom taxonomies. Each unique query is cached until the end of the day using transients.

## Using the Shortcode

Once the plugin is activated, you can use a shortcode to display your post of the day within any text area that supports shortcodes.

### Default
```
[oftheday]
```
This will choose a random post (the default post type) and set it until the end of the day.

### Post Types
```
[oftheday type='page']
```
You can choose any post type registered on your site using the _type_ selector.

### Taxonomies
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

## Transients

The shortcode uses get_posts() to query based on your inputs, always returning one or zero posts. The resulting post object is serialized and hashed MD5 to create a unique transient code. That code is designated to expire at midnight according to the local time of your WordPress site.

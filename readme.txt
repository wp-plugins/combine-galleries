=== Combine Galleries ===
Contributors: jackfilms
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6XT9FH6XW7EPJ
Tags: gallery, shortcode, portfolio
Requires at least: 3.5
Tested up to: 4.2
Stable tag: trunk
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.html

Combine and display image galleries from multiple posts on a single page using categories and tags. Quickly and easily create image portfolios.

== Description ==

This plugin gives you the ability to use standard WordPress galleries and taxonomies to organize and reuse your images.

Let's say you're a builder and you post all your jobs on your site and include an image gallery with each post. Now, suppose you want to display all your job images on a single portfolio page. What's the easiest way to do that?  Use the following:


    [combine-galleries]


Suppose you build patios, decks, and enclosures. Now, suppose you want to display all your job images, grouped by job type, on a single portfolio page. Categorize your job posts (patio, deck, enclosure) and use the following:

    [combine-galleries category="patio"]
    [combine-galleries category="deck"]
    [combine-galleries category="enclosure"]

Finally, suppose you want to create a page optimized for a specific city (let's use Raleigh). And, on that page you want to display all your Raleigh jobs grouped by type. Tag your job posts by city and use the following:

    [combine-galleries category="patio" tag="raleigh"]
    [combine-galleries category="deck" tag="raleigh"]
    [combine-galleries category="enclosure" tag="raleigh"]

Combine-Galleries allows you to combine and display gallery images from multiple posts based on taxonomy. The combine-galleries shortcode combines and displays image galleries from posts that match the categories and/or tags you specify.  You don't need to categorize or tag your images, just the posts.  If you just want to display all gallery images on a single page, you don't need to categorize or tag anything.

Supports custom types. Compatible with the WordPress gallery shortcode and WordPress gallery plugins and viewers.  Quickly and easily create galleries for your archive pages. See usage examples and options below...

If you find this plugin useful, please consider a small donation of $5. All proceeds go to fund my current feature film project, Mango Dreams.

##Usage##
-------------------------------------------------

There are several options that may be specified using this syntax:

    [combine-galleries option1="value1" option2="value2"]

You can also print a gallery directly in a template like so:

    <?php echo do_shortcode('[combine-galleries option1="value1"]'); ?>

##Options##
-----------------------------------------------------

Supported options are listed below. All options are, well, optional. You are not required to provide any options. For example, "[combine-galleries]" will work just fine and will display all gallery images from every post and page on your site. Default option values are used when options are not specified. 

type
: Comma delimited list of post types from which to display images. Custom types are supported.

Default = "page,post".

category
: Comma delimited list of categories used to filter posts. If more than one category is specified, the default behavior is to *AND* the categories together. For example, if category="featured,decks" then only images from posts categorized as both "Featured" and "Decks" will be displayed. Category values are case-insensitive. If no category is specified then no filter is applied. By default, all categories are included.

tags
: Comma delimited list of tags used to filter posts. If more than one tag is specified, the default behavior is to **OR** the tags together. For example, if tag="raleigh,durham,chapel hill" then images from posts tagged as "Raleigh" or "Durham" or "Chapel Hill" will be displayed. Tag values are case-insensitive. If no tags are specified then no filter is applied.  By default, all tags are included.

taxonomy_join
: Specify the boolean operator used to join multiple taxonomies, i.e. categories and tags. Combine-galleries does not support custom taxonomies... yet.  The default boolean operator used to join multiple taxonomies is *AND*. For example, if category="Patios" and tag="Denver" then, by default, only images from posts categorized as "Patios" *AND* tagged as "Denver" will be displayed. Bool operators include:

* AND (Default)
* OR

category_join
: Specify the boolean operator used to join multiple categories.

* AND (Default)
* OR

tag_join
: Specify the boolean operator used to join multiple tags.

* AND
* OR (Default)

orderby 
:  Specify the order in which galleries are combined.  Order of galleries is based on their post.  The default is post_date.

-  ID - order by post ID
-  post\_author - order by post author
-  post\_title - order by post title
-  post\_date (Default) - order by post date
-  comment_count - order by post comment count

order 
:  Specify the sort order of gallery posts. ASC or DESC (Default). For example, to sort by post ID from biggest to smallest:

    [combine-galleries orderby="ID" order="DESC"]

limitposts
:  Limit the number of posts. By default there is no limit.

limitpostimages
:  Limit the number of images displayed per post. By default there is no limit.

columns 
:  specify the number of columns. The gallery will include a break tag at the end of each row, and calculate the column width as appropriate. The default value is 3. If columns is set to 0, no row breaks will be included. For example, to display a 4 column gallery:

    [combine-galleries columns="4"]

size 
:  Specify the image size to use for the thumbnail display. Valid values include:

* "thumbnail" (Default)
* "medium"
* "large"
* "full"
* Any additional image size registered with [add\_image\_size](https://codex.wordpress.org/Function_Reference/add_image_size).

The size of the images for “thumbnail”, “medium” and “large” can be configured in WordPress admin panel under Settings \> Media. For example, to display a gallery of medium sized images:

    [combine-galleries size="medium"]

###Advanced Options###

itemtag 
:  the name of the XHTML tag used to enclose each item in the gallery. The default is “dl”.

icontag 
:  the name of the XHTML tag used to enclose each thumbnail icon in the gallery. The default is “dt”.

captiontag
:  the name of the XHTML tag used to enclose each caption. The default is “dd”. For example, to change the gallery markup to use div, span and p tags:

    [combine-galleries itemtag="div" icontag="span" captiontag="p"]

link
:  Specify where you want the image to link. If this option is not specified or if no value is given, the image links to the attachment's permalink. Options:

* "file" - Link directly to image file
* "none" - No link

###Ignored WordPress Gallery Option###

id 
:  specify the post ID. combine-galleries ignores the id option. The design goal of combine-galleries is to combine and display images from multiple posts based on taxonomy. The id option is used to display images from just one specific post. If you need to display images from a single post then the gallery shortcode is best.

== Installation ==

Upload the plugin to your blog and Activate it. Now you are ready to use the combine-galleries shortcode. To test your installation,
create a new post, add the following shortcode to the body of your post, and preview:

[combine-galleries limitposts="50" type="page,post" orderby="post_date" order="desc"]

This test should display all WordPress gallery images from your 50 most recent pages/posts.

== Frequently Asked Questions ==


== Screenshots ==


== Changelog ==

= 1.0.2 =
* Fix - Remove database table prefix from debug output string as a security precaution.

= 1.0.1 =
* Fix - Gallery does not display when only one taxonomy is given.

= 1.0.0 =
* Initial release of plugin.
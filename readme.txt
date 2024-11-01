=== TIEdupedeleter Simple Duplicate Post Deleter ===
Contributors: TIEro
Donate link: http://www.setupmyvps.com/tiedupedeleter
Tags: post, duplicate post, expiry, expiration, expire, delete, deletion, automatic, automated, category, categories, autoblog, auto blog
Requires at least: 3.0.1
Tested up to: 4.0
Stable tag: 1.0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Simple duplicate post deleter. Trashes duplicate posts based on status and category. Keeps newest or oldest original copy.

== Description ==

*This plugin is now available as part of [TIEtools](http://wordpress.org/plugins/tietools-automatic-maintenance-kit/ "TIEtools"), which also includes post expiry and server log file removal.*

A simple duplicate post deletion plugin. Spots posts with the same title and removes all but one of each.

- Checks in published, draft, pending and private posts on demand
- Includes or excludes user-defined list of categories
- Moves all duplicate posts to the Trash (leaving the oldest or newest original copy)
- Permanent post deletion is handled by WP's built-in Trash removal
- Power button to switch everything on and off without messing with the plugins page

== Installation ==

1. Upload the plugin folder and its contents to the /wp-content/plugins directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Set your options using Dupe Deleter at the bottom of the Dashboard menu.

Alternatively, use the built-in 'Add New' option on the Plugins menu to install.

== Frequently Asked Questions ==

= Is this plugin actively maintained? =

Yes, it is. Nothing new is added, but bugs will be fixed. All new functionality goes into [TIEtools](http://wordpress.org/plugins/tietools-automatic-maintenance-kit/ "TIEtools").

= Does it find duplicated post content? =

No, it just checks titles.

= Does it work on multisite? =

Apparently not, according to someone who tried it. It was never designed for that, so I'm not surprised.

= Can I switch off the scheduled job? =

Yes. Either deactivate the plugin or use the power setting on the options page. The power button doesn't actually remove the scheduled job, but it makes the plugin skip all the checking functionality.

= How often does the wp_cron job run? =

At most once per hour. You can change this in the do_TIEdupedeleter_activation function: switch the value 'hourly' to whatever suits you (and will work with wp_cron).

= How does the plugin determine the oldest and newest copies of a post? =

Since dates can be changed through the WordPress interface, the plugin uses the post ID in the database, not the published date or anything else. Whichever copy was created first/last, that's the oldest/newest (respectively). In SQL terms, it does a min(ID) or max(ID) check.

= Can I include or exclude specific categories for checking? =

Yes. Only duplicates where both (or all) copies are in the categories being searched will be affected. i.e. If a post in an included category has a copy in an excluded category, it won't be removed.

= I chose one of the category options and now I can't switch it off... help! =

The include/exclude option is a radio button, so it can't be set to "neither". However, you can switch off the category filter for each expiration method. Setting the categories to include (or exclude) to "0" and clicking the radio button efffectively stops all category filtering as well.

= If I enter a category number which has sub-categories, what happens? =

The parent category will be taken into account and all sub-categories will be ignored. Yes, this makes entering a dozen sub-cats a long process, but it gives you much finer control over precisely what is included or excluded in the expiry process.

= How can I get category numbers for my lists? =

You can go to Posts -> Categories, click a category name and look at the URL, which includes a "tag_ID=xxx" part, showing the category number (xxx). Or you could install and activate the Reveal IDs plugin, which adds a column on the categories page to show the ID number of each one. Much easier. The plugin URL is http://wordpress.org/plugins/reveal-ids-for-wp-admin-25

= Can I include or exclude specific posts or tags? =

No. The plugin only handles categories at the moment.

= Can I tell the pluging to keep specific posts and delete any other copies, rather than oldest or newest? =

No. That's just way too complex to do without a load of post lists and manual clicking. The plugin is designed to be easy and simple.

= Does the plugin cause major slowdowns when it runs? =

The very first time the queries run, it might. This is especially true if you have a *lot* of posts. In testing, it caused a delay of a few seconds in page serving the first time it ran. After that, I never noticed a delay again.

= Is there any documentation? =

You're reading it. The plugin code is also heavily commented to help you find your way and there's the plugin's homepage at http://www.setupmyvps.com/tiedupedeleter

= What plans do you have for the next version? =

There are a few tests which are really easy to add, the most obvious of which is duplicate post content. That would be an additional option, to allow for either/both checks. I guess the plugin could also offer the option of determining the newest and oldest post by publication date. After that, the only additions I can think of go into the area of fuzzy logic matching for similar posts rather than exact matches, which is really beyond the scope of the plugin.


== Changelog ==

= 1.0.1 =

Multiple text corrections, additions and clarifications.

= 1.0 =
Original working release.

=== Plugin Name ===
Contributors: Aricura, mark.cheret
Tags: competition, competitors, statistics
Requires at least: 3.9
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6Z6CZDW8PPBBJ
Tested up to: 4.1
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Stable Tag: 1.0.5

== Description ==

**competition** is a WordPress Plugin aimed at developers. It lets you check out your "competitors" on wordpress.org and displays beautiful, yet simple stats about WordPress Plugins with a certain tag.

= Main Features =
- Aimed at WordPress Plugin developers
- Check out your "competition" on WordPress.org by entering the tags of plugins in the WordPress.org Plugin Directory
- Visual stats for your own Plugin and Plugins of your "competition"
- Correlate your Stats against the Stats of your "competition"

= Example Usage =
This is an example.

 Insert the following example code into the text editor part of the WordPress WYSIWYG Editor for a Page or Post
 [[cmp-tag tag=footnotes task=total own=footnotes]]
 the results can be seen at http://manfisher.net/footnotes/

The current version is available on wordpress.org:
http://downloads.wordpress.org/plugin/competition.zip

Development of the plugin is an open process.
Latest code is available on wordpress.org.
Please report feature requests on GitHub:
https://github.com/media-competence-institute/competition
Report Bugs in the wordpress support forum for this Plugin

== Frequently Asked Questions ==

= How come you developed this awesome Plugin? =

1. Too much time
2. We wanted to check out our "competition" on wordpress.org regarding our plugin called **footnotes**

== Installation ==
- Visit your WordPress Admin area
- Navigate to `Plugins\Add`
- Search for **competition** and find this Plugin among others
- Install the latest version of the **competition** Plugin from WordPress.org
- Activate the Plugin

== Screenshots ==
1. Find the competition plugin settings in the newly added "ManFisher" Menu
2. Take a look on the example tab and see how you can implement stats to your public pages/posts
3. Here you can see the **competition** Plugin at work. Uses a line chart for downloads per day.
4. Using a pie chart for total amount of downloads. Isn't that plain beautiful?

== Changelog ==

= 1.0.5 =
- **IMPORTANT**: You need to activate the Plugin again. (Settings won't change).
- Update: Changed Plugins init file to improve performance. (Re-activation required).
- Update: ManFisher note styling
- Update: Plugin CI

= 1.0.4 =
- Update: Removed js console logs
- Bugfix: Rating calculation for the 'other plugins' list

= 1.0.3 =
- Add: Pie chart to the Example tab
- Update: Detail description for some short code tags
- Update: 'own' short code tag is available for total amount of downloads (pie chart)
- Bugfix: changed settings and support link

= 1.0.2 =
- Bugfix: short code description on Example tag

= 1.0.1 =
- Add: ManFisher main menu
- Add: ManFisher layout
- Add: Other ManFisher Plugins sub page
- Add: Diagnostics sub page
- Update: Settings layout
- Update: Refactored source code to have the same structure as in other ManFisher Plugins
- Update: Load chart series asynchronously
- Update: short code to display charts on public pages

= 1.0.0 =
- Bugfix: Naming of Stats tag
- Bugfix: Naming of Settings page in the dashboard
- First release version of the Plugin

= 0.0.1 =
- First development Version of the Plugin

== Upgrade Notice ==
to upgrade our plugin is simple. Just update the plugin within your WordPress installation.
To cross-upgrade from other footnotes plugins, there will be a migration assistant in the future

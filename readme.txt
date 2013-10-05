=== Redirection Reporting ===
Contributors: mrdenny
Donate Link: http://mrdenny.com/go/RedirectionReporting
Tags: reporting, redirection
Requires at least: 3.0.1
Tested up to: 3.6.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Allows for daily reporting for redirected requests.

== Description ==

Allows for more details reporting for the "Redirection" plugin by John Godley.  
This plugin will not do anything for you without using the Redirection plugin 
to handle your 301 redirections.  This plugin was built to fix a gap in the 
reporting which the Redirection plugin has.

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload `redirection-reporting.php` to the `/wp-content/plugins/redirection-reporting` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure the global settings through the settings page.


== Frequently Asked Questions ==

= What does this plugin do? =

This plugin simply reports on the redirection logs with day by day reporting.

= What date format should I be using? =

You should be able to use any date format.  The PHP is going to reformat it to the
way that MySQL wants to see it.

= Where do I find the reports? =

The "Redirection Reporting" link can be found under the tools menu, probably
right near the "Redirection" link.

== Screenshots ==

1. A sample report


== Changelog ==

= 1.9 =
* Added links from RegEx parent child report to normal RegEx report.
* Disabled report selection button when button isn't useful.
* Code cleanup.
* Fixed row by row colorization of report output

= 1.8 =
* Added in parent child style reporting for RegEx enabled URLs.  This allows you to report
on just the parent URL, but see the data for all the child values.
* Cleaned up the code, removed lots of duplicate code to make code maintenance easier.

= 1.7 =
* Added in reporting specific for RegEx enabled redirection URLs.  This report shows 
all available regex values and allows reports to be run for those specific values.
This report only works when redirection URLs are configured with the RegEx flag.

= 1.6 =
* Added the ability to report on all configured URLs in a single report for a date range.

= 1.1 =
* Fixed the FAQ and readme file.

= 1.0 =
* Created the report


== Upgrade Notice ==



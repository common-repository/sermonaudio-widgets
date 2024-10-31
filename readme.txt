=== SermonAudio Widgets ===
Contributors: SermonAudio
Tags: sermon, sermonaudio, audio, sermonaudio.com, church, media, video
Requires at least: 3.0.1
Tested up to: 4.0
Stable tag: 1.9.3
License: FreeBSD

Customizable plugin to show sermons from your SermonAudio account on your Wordpress page.

== Description ==

The SermonAudio Widgets plugin will help you show sermons from your SermonAudio account on your Wordpress-based website. Using this plugin, you can embed the different widgets from [SermonAudio's website](http://sermonaudio.com/goodies.asp "SermonAudio.com Goodies"), such as the Sermon Browser or Featured Sermon widgets. The plugin also makes it easy to customize the different widgets to fit your needs!

SermonAudio.com is the largest library of audio sermons on the web from conservative Christian churches and ministries with over 600,000+ FREE MP3 sermons which can be streamed online for immediate listening or downloaded to your computer or enjoyed via mobile device at any time. You can easily search through the entire sermon library by <a class=addtocartlink href="http://www.sermonaudio.com/sermonssource.asp">broadcaster</a>, <a class=addtocartlink href="http://www.sermonaudio.com/sermonsbible.asp">Bible reference</a>, <a class=addtocartlink href="http://www.sermonaudio.com/sermonstopic.asp">topic</a>, <a class=addtocartlink href="http://www.sermonaudio.com/sermonsspeaker.asp">speaker</a>, <a class=addtocartlink href="http://www.sermonaudio.com/sermonsdate.asp">date preached</a>, <a class=addtocartlink href="http://www.sermonaudio.com/sermonstopic.asp">language</a>, or any keyword. 
<P>
The mission of SermonAudio.com is to help faithful, local churches broadcast their audio sermons to the maximum amount of people with the least amount of cost. Our chief purpose is for the <B>preservation and propagation</b> of great Bible preaching and teaching in its audio form for this generation and the next. 
<P>
Our site motto text is taken from Romans 10:17 where it reads, <B>"faith cometh by hearing."</b>
<P>
All broadcasters must adhere to the site's <a href="http://www.sermonaudio.com/services_articles.asp"><b>Articles of Faith</b></a>. Absolutely no exceptions. Some of our broadcasters include John MacArthur, R. C. Sproul, Sinclair Ferguson, Ian Paisley, Ken Ham, Bob Jones University, Alan Cairns, Al Martin, Clarence Sexton, Joel Beeke, John Barnett, Richard Phillips, Jay Adams, Jeff Noblit, Voddie Baucham, Steve Lawson, and a host of "classic" sermons by Spurgeon, A. W. Tozer, Jonathan Edwards, and <a href="http://www.sermonaudio.com/sermonssource.asp"><b>many more..</b></a>
<P>
<a href="http://www.sermonaudio.com/services.asp"><B>Click here to learn much more about our services..</b></a>

== Installation ==

1. Upload the `sermonaudiowidget` folder to the `/wp-content/plugins/` directory, or install through the 'Plugins' menu in WordPress.
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Under the 'Plugins' menu, find the 'SermonAudio Plugins' settings page.
1. Create and modify widgets.
1. Use the 'Short Code' in your pages and posts to embed the specific widgets (example: \[SermonAudio id='1'\]).

== Frequently Asked Questions ==

= How many SermonAudio widgets can I have on my site at once? =

The plugin allows you to create and customize as many widgets as you want!

= What widgets are available? =

Here is a list of the currently available widgets:

* Sermon Browser
* Featured Sermon
* Flash
* Newest Sermons
* Recommended Picks
* Live Webcast
* Photos
* Daily Devotional
* SermonAudio Buttons

Check  [SermonAudio's website](http://sermonaudio.com/goodies.asp "SermonAudio.com Goodies") for more information on a specific widget.

== Screenshots ==

1. The main settings page for the plugin, where you can add, edit, and delete widgets.
2. The edit screen where you can change each widget's settings. The available settings will change depending on which widget is selected.

== Changelog ==

= 1.9.3 =
* Added a check for sites using SSL to make the script calls to SermonAudio use HTTPS.

= 1.9.2 =
* Fixed bug with the Live Webcast widget, where the radio button for the 4th styling option was unclickable.

= 1.9.1 =
* Apostrophes are now properly escaped and un-escaped in the plugin's options screens.

= 1.9 =
* Patched bug where series names in different widget would break if they had an apostrophe in their name.

= 1.8 =
* Added feature to Newest Sermons widget: with the new CSS style (style option 4), you can now show the Bible references for the sermons.

= 1.7 =
* Fixed bug where Newest Sermons widget would only use the new CSS styles.

= 1.6 =
* New, CSS-friendly version of the Sermon Browser is now available! To use it, select style number 2.
* Changed database storage format, which will allow future updates to upgrade fluidly. Unforunately, updating from the previous version will reset all of the saved widget settings. We are sorry for the inconvenience; with the fixes in this update, this won't be a problem again in the future.
* Added further customization for the Sermon Browser Widget
* Added Calendar and Seach Box widgets (please note that theme CSS styles will probably cause display issues for these widgets).

= 1.5 = 
* Fixed bug with Edit form for Live Webcast widget.

= 1.4 =
* Fixed bug with WordPress installs that are not on the root directory.

= 1.3 =
* Added rows fields to Sermon Browser, Newest Sermons, and Recommended Picks.

= 1.2 =
* Clean up work on plugin options interface

= 1.1 =
* Fixed major bug with image directory in plugin.
* Fixed Sort Order selection for Recommended Sermons
* Fixed height setting in Live Webcast widget.

= 1.0 =
* First release of the plugin.

== Upgrade Notice ==

Plugin now checks for SSL and will use HTTPS to communicate with SermonAudio.
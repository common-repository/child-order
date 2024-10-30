=== Child Order ===
Contributors: marcus.downing, diddledan
Tags: search
Requires at least: 3.0
Tested up to: 3.9
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Quickly adjust the order of child pages.


== Description ==

Adds an admin panel when editing edit pages, to let you rearrange this page's child pages and switch their display on/off.

This will only affect parts of your theme that display a list of links to child pages, and only those which respect the `menu_order` setting. It has *no effect* on Menus. You need to edit those with the existing menu interface.

Actually displaying the list of child pages is outside the scope of this plugin - there are plenty of themes and plugins that do that.


== Installation ==

1. Upload the `child-order` directory to your `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress


== Screenshots ==

1. The edit screen, with options to drag pages up and down.


== Changelog ==

= 1.1 =
* Updates to improve apprearance with WordPress 3.8+
* Fixed to work on multisite
* Added more hooks for other plugins

= 1.0 =
* First version


== Developers ==

For developers, there are a lot of hooks exposed by this plugin, which you can use to add fields and behaviours 

When displaying the meta box:

*  `child_order_before_list` - Action called before writing the list of child pages in the admin area
*  `child_order_before_item ($post)` - Action called at the start of each item
*  `child_order_after_item ($post)` - Action called at the end of each item
*  `child_order_after_list` - Action called after writing the whole list
*  `child_order_item_title ($title, $post)` - Filter to control the title displayed for an item
*  `child_order_message ($message)` - Filter to control the message displayed after the list

When saving (in AJAX):

*  `child_order_children ($array, $parent_id)` - Filter to adjust the list of post IDs before saving
*  `child_order_before_save ($array, $parent_id)` - Action called before saving the order of items
*  `child_order_save_item ($id, $menu_order)` - Action called on saving each item
*  `child_order_after_save ($array, $parent_id)` - Action called after saving the order of items

If you wish to remove the "Edit" and "View" buttons from the output, call this on setup:

`remove_action('child_order_after_item', 'child_order_after_item_edit_link');`

Be aware that the internal workings of the plugin are subject to change,
so the names and details of these hooks may change in future version.
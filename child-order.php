<?php
/*
Plugin Name: Page Child Order
Plugin URI: http://www.bang-on.net/
Description: Adds an admin panel to pages, to rearrange and enable/disable their child pages.
Version: 1.1
Author: Marcus Downing
Author URI: http://www.bang-on.net
License: Private
*/

/*  Copyright 2011  Marcus Downing  (email : marcus@bang-on.net)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


if (!defined('BANG_CHILD_ORDER_DEBUG'))
  define('BANG_CHILD_ORDER_DEBUG', false);

add_action('admin_init', 'child_order_init');
function child_order_init() {
  add_action('wp_ajax_child_order_save', 'child_order_save');
}

add_action('admin_menu', 'child_order_page_init');
function child_order_page_init() {
  $post_id = $_REQUEST['post'];
  if (isset($post_id) && $post_id > 0) {
    $post = get_post($post_id);
    $children = get_posts(array('post_parent' => $post_id, 'post_type' => $post->post_type, 'orderby' => 'none', 'post_status' => 'publish'));
    if (BANG_CHILD_ORDER_DEBUG) do_action('log', 'Child order: checking for children of post %s (%s) => %s', $post_id, $post->post_type, count($children));
    if (!empty($children)) {
      if (BANG_CHILD_ORDER_DEBUG) do_action('log', 'Child order: adding meta box');
      add_meta_box('child_order_box', '<i class="dashicons dashicons-sort"></i> Order Child Pages', 'child_order_box', $post->post_type, 'advanced', 'core');
      wp_enqueue_script('jquery-ui-core');
      wp_enqueue_script('jquery-ui-draggable');
      wp_enqueue_script('child_order_js', plugins_url('scripts/child-order.js', __FILE__), array('jquery', 'jquery-ui-core', 'jquery-ui-draggable'));
      wp_enqueue_style('child_order_css', plugins_url('admin.css', __FILE__));
    }
  }
}

function child_order_box($post) {
  if (BANG_CHILD_ORDER_DEBUG) do_action('log', 'Child order: writing meta box');
  $children = get_posts(array('post_parent' => $post->ID, 'post_type' => $post->post_type, 'orderby' => 'menu_order', 'order' => ASC, 'post_status' => 'publish', 'numberposts' => 1000));

  do_action('child_order_before_list');
  echo "<ul id='child_order_list' data-post-id='$post->ID'>";
  foreach ($children as $child) {
    $title = apply_filters('the_title', $child->post_title);
    $title = apply_filters('child_order_item_title', $title, $child);

    echo "<li class='child_order_item' id='child_order-$child->ID' data-post-id='$child->ID'><span class='grip'></span>";
    do_action('child_order_before_item', $child);
    echo $title;
    do_action('child_order_after_item', $child);
    echo "</li>";
  }
  ?></ul><?php
  do_action('child_order_after_list');
  echo '<p>'.__(apply_filters('child_order_message', 'Drag child pages into the desired order.'), 'child-order').'</p>';
}

function child_order_save () {
  $post_id = $_REQUEST['post'];
  if (BANG_CHILD_ORDER_DEBUG) do_action('log', 'Child order: saving order for post', $post_id);
  $order = $_REQUEST['order'];
  $ids = explode(",",$order);
  $ids = array_filter($ids);
  $ids = apply_filters('child_order_children', $ids, $post_id);
  $ids = array_filter($ids);
  if (BANG_CHILD_ORDER_DEBUG) do_action('log', 'Child order: IDs', $ids);
  if (empty($ids)) return;

  global $wpdb;
  $i = 1;
  do_action('child_order_before_save', $ids, $post_id);
  foreach ($ids as $id) {
    $post = array('ID' => $id, 'menu_order' => $i);
    if (BANG_CHILD_ORDER_DEBUG >= 2) do_action('log', 'Child order: Saving child page', $id, $i);
    
    $err = $wpdb->update( $wpdb->posts, array( 'menu_order' => $i ), array( 'ID' => $id ) );
    if (is_wp_error($err)) {
      if (BANG_CHILD_ORDER_DEBUG) do_action('log', 'Child order: Error saving page', $err);
    } else {
      do_action('child_order_save_item', $id, $i);
    }
    $i++;
  }
  do_action('child_order_after_save', $ids, $post_id);
}

add_action('child_order_after_item', 'child_order_after_item_edit_link', 10, 1);
function child_order_after_item_edit_link($child) {
  if (current_user_can('edit_page', $child->ID)) {
    echo " &nbsp; <a href='post.php?post=$child->ID&action=edit' class='button button-small'>".__('Edit', 'child-order')."</a>";
  }

  $link = get_permalink($child->ID);
  echo " &nbsp; <a href='$link' class='button button-small'>".__('View', 'child-order')."</a>";
}
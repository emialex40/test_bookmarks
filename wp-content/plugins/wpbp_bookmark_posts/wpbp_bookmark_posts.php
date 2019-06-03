<?php
/*
Plugin Name: Bookmark Posts
Description: Plugin to add posts to bookmarks
Version: 1.0
Author: Alexander Yemeliantsev
*/

require __DIR__ . '/functions.php';

add_filter('the_content', 'wpbp_bookmarks_content');
add_action('wp_enqueue_scripts', 'wpbm_bookmarks_scripts');
add_action('wp_ajax_wpbm_send', 'wp_ajax_wpbm_send');
add_action('wp_ajax_wpbm_del', 'wp_ajax_wpbm_del');
add_action('wp_ajax_wpbm_remove', 'wp_ajax_wpbm_remove');
add_action('wp_ajax_wpbm_removeall', 'wp_ajax_wpbm_removeall');
add_action( 'init', 'bookmark_admin_init' );
add_filter( 'wp_nav_menu_items', 'wpbp_bookmark_add_menu', 10, 2 );
add_shortcode( 'wpbp_bookmarks_post_shortcode', 'wpbp_bookmarks_post_shortcode' );





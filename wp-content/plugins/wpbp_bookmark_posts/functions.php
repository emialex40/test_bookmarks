<?php

function wpbp_bookmarks_content ($content)
{
    if (!is_single() || !is_user_logged_in()) return $content;

    global $post;

    //TODO: add links
    if (wpbp_is_bookmark ($post->ID)) {
        return $content . '
            <p class="wpbp_bookmark_link">
              <a href="#" data-action="del">Remove from bookmarks</a>
            </p>';
    }

    return $content . '
      <p class="wpbp_bookmark_link">
        <a href="#" data-action="send">Add to bookmarks</a>
      </p>
';
}


function wpbm_bookmarks_scripts ()
{
    if (!is_user_logged_in()) return;

    wp_enqueue_script('wpbp_bookmark_scripts', plugins_url('js/wpbp_bookmark_scripts.js', __FILE__), array('jquery'), null, true);
    wp_enqueue_style('wpbp_bookmark_style', plugins_url('css/wpbp_bookmark_style.css', __FILE__));
    global $post;
    wp_localize_script('wpbp_bookmark_scripts', 'wpbpBookmarks', ['url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('wpbp_bookmarks'),
        'postId' => $post->ID
        ]
    );
}

//TODO: add bookmark
function wp_ajax_wpbm_send ()
{
    if (!wp_verify_nonce($_POST['security'], 'wpbp_bookmarks')) {
        wp_die('Security Error!');
    }

    $post_id = (int)$_POST['postId'];
    $user = wp_get_current_user();

    if (wpbp_is_bookmark ($post_id)) wp_die();

    if (add_user_meta($user->ID, 'wpbp_bookmarks', $post_id)) {
        wp_die('<p class="wpbp_info">Done!</p>');
    }

    wp_die('Add failed!');
}

//TODO: delete bookmark
function wp_ajax_wpbm_del ()
{
    if (!wp_verify_nonce($_POST['security'], 'wpbp_bookmarks')) {
        wp_die('Security Error!');
    }

    $post_id = (int)$_POST['postId'];
    $user = wp_get_current_user();

    if (!wpbp_is_bookmark ($post_id)) wp_die();

    if (delete_user_meta($user->ID, 'wpbp_bookmarks', $post_id)) {
        wp_die('<p class="wpbp_info">Remove!</p>');
    }

    wp_die('Remove failed!');
}

//TODO: remove bookmark
function wp_ajax_wpbm_remove ()
{
    if (!wp_verify_nonce($_POST['security'], 'wpbp_bookmarks')) {
        wp_die('Security Error!');
    }

    $del_post = (int)$_POST['del_post'];
    $user = wp_get_current_user();

    if (delete_user_meta($user->ID, 'wpbp_bookmarks', $del_post)) {
        wp_die('<p class="wpbp_info wpbp_bookmark_info">Post removed from bookmarks!</p>');
    }

    wp_die('Remove failed!');
}

//TODO: remove all
function wp_ajax_wpbm_removeall ()
{
    if (!wp_verify_nonce($_POST['security'], 'wpbp_bookmarks')) {
        wp_die('Security Error!');
    }

    $user = wp_get_current_user();

    if (delete_user_meta($user->ID, 'wpbp_bookmarks')) {
        wp_die('<p class="wpbp_info">You have deleted all bookmarks!</p>');
    }

    wp_die('Remove failed!');
}

function wpbp_is_bookmark ($post_id)
{
    $user = wp_get_current_user();
    $bookmarks = get_user_meta($user->ID, 'wpbp_bookmarks');

    foreach ($bookmarks as $bookmark) {
        if ($bookmark == $post_id) return true;
    }
    return false;
}

function wpbp_bookmarks_post_shortcode()
{
    $user = wp_get_current_user();
    $bookmarks = get_user_meta($user->ID, 'wpbp_bookmarks');

    if (!$bookmarks) {
        echo 'You don\'t have bookmarks now.';
        return;
    }

    echo '<div class="wpbp_bookmarks_block">';
    echo '<div class="wpbp_remove_all">
            <a href="#" data-action="removeall">Remove All</a>
          </div>';
    echo '<ul class="wpbp_bookmarks_list">';
    foreach($bookmarks as $bookmark) {
        $post = get_post($bookmark);

        echo '<li class="wpbp_bookmarks_item wpbp_bookmarks_item_' . $bookmark . '">
                  <a class="wpbp_bookmark_thumb" href="' .get_permalink($bookmark) . '">
                    <span>' . get_the_post_thumbnail($bookmark, 'thumbnail') . '</span>
                  </a>
                  <div class="wpbp_bookmark_content">
                      <a href="' .get_permalink($bookmark) . '">
                        <h3>' . get_the_title($bookmark) . '</h3>
                      </a>
                      <p>' . wp_trim_words($post->post_content, 20, '...') . '</p>
                      <a href="#" class="wpbp_bookmark_remove" data-action="remove" data-post="' . $bookmark . '">Remove Bookmark</a>
                  </div>
              </li>';
    }
    echo '</ul>';
    echo '</div>';
}

function bookmark_admin_init()
{
    if (!is_user_logged_in()) return;

    global $wpdb;
    $user = wp_get_current_user();
    $post_title = 'Bookmarks';

    $new_bookmark = $wpdb->get_row("SELECT post_title FROM wp_posts WHERE post_title = '" . $post_title . "'", 'ARRAY_A');


    if ($new_bookmark) {
        return;
    }

    $bookmark_page = array(
        'post_type'     => 'page',
        'post_title'        => $post_title,
        'post_content'      => '[wpbp_bookmarks_post_shortcode]',
        'comment_status'    => 'closed',
        'post_status'       => 'publish',
        'post_author'       => $user,
        'post_name'     => 'page-bookmarks',
        'post_password' => ''
    );
    wp_insert_post(wp_slash($bookmark_page));
}

//TODO: add to menu
function wpbp_bookmark_add_menu($items, $args)
{
    if (!is_user_logged_in()) return $items;

    if( $args->theme_location == 'primary' ) {
        return $items . '
            <li id="wpbp_bookmark_item" class="menu-item menu-item-type-custom menu-item-object-custom wpbp_bookmark_menu">
                <a href="/page-bookmarks/"><i>★</i>Bookmarks</a>
            </li>
        ';
    }
    return $items;
}

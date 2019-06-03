jQuery(document).ready(function ($) {

    $('.wpbp_bookmark_link a').click(function (e) {
        e.preventDefault();
        var action = $(this).data('action');

        $.ajax({
            type: 'POST',
            url: wpbpBookmarks.url,
            data: {
                security: wpbpBookmarks.nonce,
                action: 'wpbm_' + action,
                postId: wpbpBookmarks.postId
            },
            success: function (result) {
                $('.wpbp_bookmark_link').html(result);
            },
            error: function () {
                alert('Error sending data!');
            }
        });
    });

    $('.wpbp_bookmark_remove').click(function (e) {
        e.preventDefault();
        var action = $(this).data('action');
        var del_post = $(this).data('post');

        $.ajax({
            type: 'POST',
            url: wpbpBookmarks.url,
            data: {
                security: wpbpBookmarks.nonce,
                action: 'wpbm_' + action,
                del_post: del_post
            },
            success: function (result) {
                $('.wpbp_bookmarks_item_' + del_post).html(result);
            },
            error: function () {
                alert('Error sending data!');
            }
        });
    });

    $('.wpbp_remove_all a').click(function (e) {
        e.preventDefault();
        var action = $(this).data('action');;
        console.log(action)

        $.ajax({
            type: 'POST',
            url: wpbpBookmarks.url,
            data: {
                security: wpbpBookmarks.nonce,
                action: 'wpbm_' + action,
            },
            success: function (result) {
                $('.wpbp_bookmarks_block').html(result);
            },
            error: function () {
                alert('Error sending data!');
            }
        });
    });

    // current-menu-item

    var li = $('.wpbp_bookmark_menu'),
        location = window.location.href;

        location = '/' + location.split('/',)[3];
        if (location == '/page-bookmarks') {
            li.addClass('current-menu-item');
        }

});
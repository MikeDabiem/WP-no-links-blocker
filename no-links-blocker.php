<?php
/*
Plugin name: No links blocker
Version: 1.0
Author: Viktor Hvozdakov
*/

if (!defined('ABSPATH')) {
    die;
}

add_action( 'admin_enqueue_scripts', function() {
    wp_enqueue_style('noLinksBlockerStyle', plugins_url('/assets/style.css', __FILE__));
});

add_filter( 'use_block_editor_for_post', '__return_false', 10 );

add_action( 'save_post', 'check_links');
function check_links($post_id) {
    $regexp = '/<a href=.*'.str_replace( '/', '\/', home_url() ).'/';
    if (isset($_POST['post_content']) && preg_match_all($regexp, $_POST['post_content']) < 3) {
        remove_action('save_post', 'check_links');
        wp_update_post(['ID' => $post_id, 'post_status' => 'draft'], true);
        add_action('save_post', 'check_links');
    }
}

add_action( 'post_submitbox_start', function() {
    global $post;
    $regexp = '/<a href=.*'.str_replace( '/', '\/', home_url() ).'/';
    if (strlen($post->post_content) > 0 && preg_match_all($regexp, $post->post_content) < 3) {
        echo '<p class="no_links_blocker_warning">To publish a '.$post->post_type.' you need to add at least 3 internal links.</p>';
    }
});

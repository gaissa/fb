<?php
/*
Plugin Name: Manage/View Your Posts Only
Version: 0.1
Plugin URI: http://zibeb.com
Description: Allows contributors to see and manage only their posts and drafts from the manage posts screen. This plugin is best suited for blogs that allow guest posts or have a number of contributors. Although Wordpress doesn't allow a contributor to edit posts or view the post content submitted by others, it does allow them to see the titles. This plugin simply prevents this from happening so that they can only view the posts they have submitted. The plugin will also hide the total number of posts and drafts, which the webmaster or the blog owner may not want to show to the regular contributors. 
Author: Brian Davidson
Author URI: http://zibeb.com
*/

function mypo_parse_query_useronly( $wp_query ) {
    if ( strpos( $_SERVER[ 'REQUEST_URI' ], '/wp-admin/edit.php' ) !== false ) {
        if ( !current_user_can( 'activate_plugins' ) )  {
			add_action( 'views_edit-post', 'child_remove_some_post_views' );
            global $current_user;
            $wp_query->set( 'author', $current_user->id );
        }
    }
}

add_filter('parse_query', 'mypo_parse_query_useronly' );

/**
 * Remove All, Published and Trashed posts views.
 *
 * Requires WP 3.1+.
 * @param array $views
 * @return array
 */
function child_remove_some_post_views( $views ) {
	unset($views['all']);
	unset($views['publish']);
	unset($views['trash']);
	unset($views['draft']);
	unset($views['pending']);
	return $views;
}
?>
<?php

/**
 * RW Author Activity
 *
 * @author    Frank Neumann-Staude
 * @license   GPL-2.0+
 * @link      https://github.com/rpi-virtuell/rw-author-avtivity
 */

/*
 * Plugin Name:       RW Author Activity
 * Plugin URI:        https://github.com/rpi-virtuell/rw-author-activity
 * Description:       Autor Activity
 * Version:           0.0.1
 * Author:            Frank Neumann-Staude
 * Author URI:        https://staude.net
 * License:           GNU General Public License v2
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.html
 * Domain Path:       /languages
 * Text Domain:       rw-author-activity
 * GitHub Plugin URI: https://github.com/rpi-virtuell/rw-author-activity
 * GitHub Branch:     master
 * Requires WP:       4.0
 * Requires PHP:      5.3
 */

class RWAuthorActivity {
	/**
	 * Plugin constructor.
	 *
	 * @since   0.0.1
	 * @access  public
	 * @uses    plugin_basename
	 * @action  materialpool_init
	 */
	public function __construct () {
		add_action( 'save_post', array( 'RWAuthorActivity', 'set_createdate') );
		add_action('wp_dashboard_setup',  array( 'RWAuthorActivity', 'add_dashboard_widgets' ) );
	}

	static public function add_dashboard_widgets() {
		wp_add_dashboard_widget(
			'rw-autor',         // Widget slug.
			'Autorenaktivität',         // Title.
			array( 'RWAuthorActivity', 'autor') // Display function.
		);
	}

	static public function set_createdate( $postID = null ) {
		global $post;

		if ( $postID == null ) {
			$postID = $post->ID;
		}
		$date = get_post_meta( $postID, 'create_date', true );
		if ( $date == '' ) {
			add_post_meta( $postID, 'create_date', date( 'Y-m-d' ), true );
		}
	}

	static public function autor() {
		global $wpdb;

		$result = $wpdb->get_results( $wpdb->prepare( "SELECT distinct( post_author) FROM $wpdb->posts WHERE $wpdb->posts.post_type = %s " , 'post' )  );
		echo "<table  style='width: 100%'><tr><th style='width: 40%'>Autoren</th><th style='width: 20%'>";
		echo date( 'F', mktime(0, 0, 0, date("m")-2  , date("d"), date("Y")) );
		echo "</th><th style='width: 20%'>";
		echo date( 'F', mktime(0, 0, 0, date("m")-1  , date("d"), date("Y")) );
		echo "</th><th style='width: 20%'>";
		echo date( 'F', mktime(0, 0, 0, date("m")  , date("d"), date("Y")) );
		echo "</th></tr>";
		foreach ( $result as $obj ) {
			$user = get_user_by( 'ID', $obj->post_author );
			echo "<tr><td>";
			echo $user->display_name;
			echo "</td><td>";
			$start =  date( 'Y-m-d', mktime(0, 0, 0, date("m")-2  , -1, date("Y")) );
			$end =  date( 'Y-m-d', mktime(0, 0, 0, date("m")-1  , 1, date("Y")) );
			$result = $wpdb->get_results( $wpdb->prepare( "SELECT count( post_ID) as anzahl FROM $wpdb->posts , $wpdb->postmeta WHERE $wpdb->posts.post_type = %s  and $wpdb->posts.post_author = %d  and $wpdb->posts.ID = $wpdb->postmeta.post_id and $wpdb->postmeta.meta_key = %s and $wpdb->postmeta.meta_value < %s and $wpdb->postmeta.meta_value > %s" , 'post', $obj->post_author, 'create_date', $end, $start )  );
			echo $result[0]->anzahl;
			echo "</td><td>";
			$start =  date( 'Y-m-d', mktime(0, 0, 0, date("m")-1  , -1, date("Y")) );
			$end =  date( 'Y-m-d', mktime(0, 0, 0, date("m")  , 1, date("Y")) );
			$result = $wpdb->get_results( $wpdb->prepare( "SELECT count( post_ID) as anzahl FROM $wpdb->posts , $wpdb->postmeta WHERE $wpdb->posts.post_type = %s  and $wpdb->posts.post_author = %d  and $wpdb->posts.ID = $wpdb->postmeta.post_id and $wpdb->postmeta.meta_key = %s and $wpdb->postmeta.meta_value < %s and $wpdb->postmeta.meta_value > %s" , 'post', $obj->post_author, 'create_date', $end, $start )  );
			echo $result[0]->anzahl;
			echo "</td><td>";
			$start =  date( 'Y-m-d', mktime(0, 0, 0, date("m")  , -1, date("Y")) );
			$end =  date( 'Y-m-d', mktime(0, 0, 0, date("m")+1  , 1, date("Y")) );
			$result = $wpdb->get_results( $wpdb->prepare( "SELECT count( post_ID) as anzahl FROM $wpdb->posts , $wpdb->postmeta WHERE $wpdb->posts.post_type = %s  and $wpdb->posts.post_author = %d  and $wpdb->posts.ID = $wpdb->postmeta.post_id and $wpdb->postmeta.meta_key = %s and $wpdb->postmeta.meta_value < %s and $wpdb->postmeta.meta_value > %s" , 'post', $obj->post_author, 'create_date', $end, $start )  );
			echo $result[0]->anzahl;

			echo "</td></tr>";
		}

		echo "</table>";
	}

}

$RWAuthorActivity = new RWAuthorActivity();
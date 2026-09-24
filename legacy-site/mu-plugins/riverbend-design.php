<?php
/**
 * Plugin Name: Riverbend Design
 * Description: Atlanta skyline framing, tinted sections and a skyline footer band. Decorative only (CSS backgrounds).
 */

add_action(
	'wp_enqueue_scripts',
	function () {
		$file = __DIR__ . '/riverbend-design/design.css';
		wp_enqueue_style( 'riverbend-design', WPMU_PLUGIN_URL . '/riverbend-design/design.css', array(), filemtime( $file ) );
	},
	100
);

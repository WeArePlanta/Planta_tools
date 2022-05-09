<?php
/**
 * Plugin Name: Planta Tools
 * Version:     1.0.0
 * Description: Customizaciones para el sitio weareplanta.com
 * Author:      Natalia Ciraolo 
 * License:     GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

define( 'PLANTA_VERSION', '1.0.0' );

add_action( 'init', function() {

	wp_enqueue_script( 
		'copy_to_clipboard_script', 
		'/wp-content/plugins/planta-tools/js/copy-to-clipboard.js', 
		array(), 
		PLANTA_VERSION, 
		true 
	);
	
	wp_enqueue_style( 
		'copy_to_clipboard_style', 
		'/wp-content/plugins/planta-tools/css/copy-to-clipboard.css', 
		array(), 
		PLANTA_VERSION, 
		true 
	);


} );



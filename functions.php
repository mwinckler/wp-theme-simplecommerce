<?php


add_theme_support( 'custom-header' );
add_theme_support( 'title-tag' );
add_theme_support( 'automatic-feed-links' );


// Enqueue main styles
add_action( 'wp_enqueue_scripts', function() {
	wp_enqueue_style( 'style-primary', get_bloginfo( 'stylesheet_directory' ) . '/css/site.css' );
});

// Nav menus
add_action( 'after_setup_theme', function() {
	register_nav_menu( 'primary', __( 'Primary Menu', 'simplecommerce' ) );
});

// Sidebars
add_action( 'widgets_init', function() {
	register_sidebar( array(
		'name'		=> __( 'Right Sidebar', 'simplecommerce' ),
		'id'		=> 'sidebar-right',
		'description' => __( 'Widgets added to this sidebar will be displayed on the right side of page templates supporting a sidebar.' )
	) );
});

?>
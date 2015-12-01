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
		'name'		=> __( 'Footer Section 1', 'simplecommerce' ),
		'id'		=> 'footer-col-1',
		'description' => __( 'Widgets added to this sidebar will be displayed in the first column of the footer.' )
	) );

	register_sidebar( array(
		'name'		=> __( 'Footer Section 2', 'simplecommerce' ),
		'id'		=> 'footer-col-2',
		'description' => __( 'Widgets added to this sidebar will be displayed in the second column of the footer.' )
	) );

	register_sidebar( array(
		'name'		=> __( 'Footer Section 3', 'simplecommerce' ),
		'id'		=> 'footer-col-3',
		'description' => __( 'Widgets added to this sidebar will be displayed in the third column of the footer.' )
	) );	
});

?>
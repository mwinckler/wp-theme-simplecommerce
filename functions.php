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


// Shortcodes
add_shortcode( 'columns', 'simplecommerce_shortcode_columns' );
add_shortcode( 'column', 'simplecommerce_shortcode_column' );

function simplecommerce_shortcode_columns( $attrs, $content = '' ) {
	$column_count = 0;
	$column_regex = '/\[column\]/s';
	// Detect the number of columns specified in the content so that we know how wide to make each one.
	if ( preg_match_all( $column_regex, $content, $matches ) ) {
		$column_count = count($matches[0]);
	}

	$content = preg_replace( $column_regex, "[column total_count='$column_count']", $content );

	if ( $column_count == 0 ) {
		return $content;
	}

	return "<div class='u-cf'>" . do_shortcode( $content ) . "</div>";
}

function simplecommerce_shortcode_column( $attrs, $content = '' ) {
	$parsed_attrs = shortcode_atts( array(
		'total_count' => 1
	), $attrs );
	$css_class = '';
	switch( $parsed_attrs['total_count'] ) {
		case '2':
			$css_class = 'one-half';
			break;
		case '3':
			$css_class = 'one-third';
			break;
	}
	return "<div class='nested column $css_class'>$content</div>";
}

?>
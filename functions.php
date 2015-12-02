<?php


add_theme_support( 'custom-header' );
add_theme_support( 'title-tag' );
add_theme_support( 'automatic-feed-links' );

// Remove WordPress's auto paragraph "feature" because that's Markdown's job
remove_filter( 'the_content', 'wpautop' );
remove_filter( 'the_excerpt', 'wpautop' );

// Enqueue main styles
add_action( 'wp_enqueue_scripts', function() {
	global $wp_styles;
	wp_enqueue_style( 'style-primary', get_bloginfo( 'stylesheet_directory' ) . '/css/site.css' );
	wp_enqueue_style( 'fontawesome', get_bloginfo( 'stylesheet_directory' ) . '/css/font-awesome.min.css' );
	//wp_enqueue_style( 'foundicons', get_bloginfo( 'stylesheet_directory' ) . '/css/general_foundicons.css' );
	//wp_enqueue_style( 'foundicons-IE7', get_bloginfo( 'stylesheet_directory' ) . '/css/general_foundicons_ie7.css' );
	//$wp_styles->add_data( 'foundicons-ie7', 'conditional', 'IE 7' );

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
add_shortcode( 'testimonial', 'simplecommerce_shortcode_testimonial' );
add_shortcode( 'toggle', 'simplecommerce_shortcode_toggle' );
add_filter( 'no_texturize_shortcodes', function( $non_texturized_shortcodes ) {
	$non_texturized_shortcodes[] = 'columns';
	$non_texturized_shortcodes[] = 'column';
	return $non_texturized_shortcodes;
});

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
	return "<div class='nested column $css_class'>" . do_shortcode( $content ) . "</div>";
}

function simplecommerce_shortcode_testimonial( $attrs, $content = '' ) {
	$parsed_attrs = shortcode_atts( array(
		'name' => '',
		'url' => '',
		'image_url' => ''
	), $attrs );

	$cite = '';
	$cite_style = '';

	if ( !empty( $parsed_attrs['name'] ) ) {
		$cite = $parsed_attrs['name'];

		if ( !empty( $parsed_attrs['image_url'] ) ) {
			$cite = $cite . "<img src='" . $parsed_attrs['image_url'] . "' alt='" . $parsed_attrs['name'] . "' class='cite_img' />";
		} else {
			// Ick. Have to do an inline style on the cite to get margin right in the absence of an image.
			// Note intentional leading space.
			$cite_style = " style='margin-right: 0;'";
		}

		if ( !empty( $parsed_attrs['url'] ) ) {
			$cite = "<a href='" . $parsed_attrs['url'] . "'>$cite</a>";
		}

		$cite = "<cite$cite_style>$cite</cite>";
	}



	return "<blockquote class='testimonial'>" . $content . $cite . "</blockquote>";
}


function simplecommerce_shortcode_toggle( $attrs, $content = '' ) {
	// The ID of each toggle-able must be unique per request for the CSS styling to work.
	// This is not threadsafe. :P
	static $simplecommerce_toggle_id = 0; 
	$parsed_attrs = shortcode_atts( array(
		'title' => '',
		'initial_state' => 'closed'
	), $attrs );

	$id = ++$simplecommerce_toggle_id;

	$check_state = $parsed_attrs['initial_state'] == 'open' ? 'checked="checked"' : '';

	return "<style type='text/css'>#sc-toggle-chk-$id { display: none; } #sc-toggle-$id { display: none; } #sc-toggle-chk-$id:checked ~ #sc-toggle-$id { display: block; }</style>" .
			"<input type='checkbox' id='sc-toggle-chk-$id' $check_state />" .
			"<label class='toggle' for='sc-toggle-chk-$id'>" .
				"<i class='fa fa-angle-double-down fa-lg collapsed'></i>" .
				"<i class='fa fa-angle-double-up fa-lg expanded'></i>" .
				 $parsed_attrs['title'] . "</label>" .
			"<div id='sc-toggle-$id' class='toggle-content'>" . $content . "</div>";

}

?>
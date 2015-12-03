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

// ==========================================================
// Nav menus
// ==========================================================

add_action( 'after_setup_theme', function() {
	register_nav_menu( 'primary', __( 'Primary Menu', 'simplecommerce' ) );
});

// ==========================================================
// Sidebars
// ==========================================================


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

// ==========================================================
// Shortcodes
// ==========================================================

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
			"<div class='toggle-container'>" .
			"<input type='checkbox' id='sc-toggle-chk-$id' $check_state />" .
			"<label class='toggle noselect' for='sc-toggle-chk-$id'>" .
				"<i class='fa fa-angle-double-down fa-lg collapsed'></i>" .
				"<i class='fa fa-angle-double-up fa-lg expanded'></i>" .
				 $parsed_attrs['title'] . "</label>" .
			"<div id='sc-toggle-$id' class='toggle-content'>" . $content . "</div>" .
			"</div>"; // .toggle-container

}


// ==========================================================
// Theme Settings (Customizer)
// ==========================================================

add_action( 'customize_register', 'simplecommerce_customize_register' );

function simplecommerce_customize_register( $wp_customize ) {
	$wp_customize->add_setting( 'color_background_light', array(
		'default' => '#f8f8f8',
		'transport' => 'refresh'
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'color_background_light', array( 
		'label' => __( 'Light Background Color', 'simplecommerce' ),
		'section' => 'colors',
		'settings' => 'color_background_light' ) ) );


	$wp_customize->add_setting( 'color_link', array(
		'default' => '#1eaedb',
		'transport' => 'refresh'
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'color_link', array( 
		'label' => __( 'Link Color', 'simplecommerce' ),
		'section' => 'colors',
		'settings' => 'color_link' ) ) );

	$wp_customize->add_setting( 'color_link_visited', array(
		'default' => '#845ba4',
		'transport' => 'refresh'
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'color_link_visited', array( 
		'label' => __( 'Link Visited Color', 'simplecommerce' ),
		'section' => 'colors',
		'settings' => 'color_link_visited' ) ) );

	$wp_customize->add_setting( 'color_link_hover', array(
		'default' => '#0fa0ce',
		'transport' => 'refresh'
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'color_link_hover', array( 
		'label' => __( 'Link Hover Color', 'simplecommerce' ),
		'section' => 'colors',
		'settings' => 'color_link_hover' ) ) );


	$wp_customize->add_setting( 'color_border', array(
		'default' => '#e1e1e1',
		'transport' => 'refresh'
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'color_border', array( 
		'label' => __( 'Border Color', 'simplecommerce' ),
		'section' => 'colors',
		'settings' => 'color_border' ) ) );


	$wp_customize->add_setting( 'color_button_background', array(
		'default' => '#0073d4',
		'transport' => 'refresh'
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'color_button_background', array( 
		'label' => __( 'Button Background Color', 'simplecommerce' ),
		'section' => 'colors',
		'settings' => 'color_button_background' ) ) );

	$wp_customize->add_setting( 'color_button_background_hover', array(
		'default' => '#4fa5ed',
		'transport' => 'refresh'
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'color_button_background_hover', array( 
		'label' => __( 'Button Background Hover Color', 'simplecommerce' ),
		'section' => 'colors',
		'settings' => 'color_button_background_hover' ) ) );


	$wp_customize->add_setting( 'color_button_text', array(
		'default' => '#fff',
		'transport' => 'refresh'
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'color_button_text', array( 
		'label' => __( 'Button Text Color', 'simplecommerce' ),
		'section' => 'colors',
		'settings' => 'color_button_text' ) ) );


	$wp_customize->add_setting( 'color_button_text_hover', array(
		'default' => '#fff',
		'transport' => 'refresh'
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'color_button_text_hover', array( 
		'label' => __( 'Button Text Hover Color', 'simplecommerce' ),
		'section' => 'colors',
		'settings' => 'color_button_text_hover' ) ) );


	$wp_customize->add_setting( 'color_background_dark', array(
		'default' => '#222',
		'transport' => 'refresh'
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'color_background_dark', array( 
		'label' => __( 'Dark Background Color', 'simplecommerce' ),
		'section' => 'colors',
		'settings' => 'color_background_dark' ) ) );


	$wp_customize->add_setting( 'color_label_text', array(
		'default' => '#555',
		'transport' => 'refresh'
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'color_label_text', array( 
		'label' => __( 'Label Text Color', 'simplecommerce' ),
		'section' => 'colors',
		'settings' => 'color_label_text' ) ) );


	$wp_customize->add_setting( 'color_accent_background', array(
		'default' => '#aaa',
		'transport' => 'refresh'
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'color_accent_background', array( 
		'label' => __( 'Accent Background Color', 'simplecommerce' ),
		'section' => 'colors',
		'settings' => 'color_accent_background' ) ) );


}


add_action( 'wp_head', 'simplecommerce_customize_css');

function is_valid_color( $color ) {
	return preg_match( '/#([a-f0-9]{3}|[a-f0-9]{6})/', $color );
}
function ensure_starts_with( $subject, $start_str ) {
	return ( strpos( $subject, $start_str, 0 ) !== FALSE )
			? $subject
			: $start_str . $subject;
}
function simplecommerce_customize_css() {
	$color_background_light = get_theme_mod( 'color_background_light', '#f8f8f8' );
	$color_label_text = get_theme_mod( 'color_label_text', '#555' );
	$color_border = get_theme_mod( 'color_border', '#e1e1e1' );
	$color_accent_background = get_theme_mod( 'color_accent_background', '#aaa' );
	$color_link = get_theme_mod( 'color_link', '#1eaedb' );
	$color_link_visited = get_theme_mod( 'color_link_visited', '#845ba4' );
	$color_link_hover = get_theme_mod( 'color_link_hover', '#0fa0ce' );
	$color_button_text = get_theme_mod( 'color_button_text', '#fff' );
	$color_button_background = get_theme_mod( 'color_button_background', '#0073d4' );
	$color_button_background_hover = get_theme_mod( 'color_button_background_hover', '#4fa5ed' );
	$color_button_text_hover = get_theme_mod( 'color_button_text_hover', '#fff' );
	$color_background_dark = get_theme_mod( 'color_background_dark', '#222' );

	?>
		<style type='text/css'>
			<?php if ( !empty($color_background_light) )
			code, aside, blockquote, label.toggle, .toggle-content {
				background: <?php echo $color_background_light; ?>;
			}
			label.toggle {
				color: <?php echo $color_label_text; ?>;
			}
			code, th, td, hr {
				border-color: <?php echo $color_border; ?>;
			} 
			ul#menu-primary-nav:before, ul#menu-primary-nav:after {
				background: <?php echo $color_border; ?>;
			}

			input + label.toggle > i.collapsed,
			input:checked + label.toggle > i.expanded {
				background: <?php echo $color_accent_background; ?>;
			}

			a {
				color: <?php echo $color_link; ?>;
			}
			a:visited {
				color: <?php echo $color_link_visited; ?>;
			}
			a:hover {
				color: <?php echo $color_link_hover; ?>;
			}

			#hero h1, #hero h1 a, #hero h2, #hero h2 a {
				color: #<?php echo get_header_textcolor(); ?>;
			}

			a.btn {
				background: <?php echo $color_button_background; ?>;
				color: <?php echo $color_button_text; ?>;
			}
			a.btn:hover {
				background: <?php echo $color_button_background_hover; ?>;
				color: <?php echo $color_button_text_hover; ?>;
			}

			.footer-nav {
				background: <?php echo $color_background_dark; ?>;
			}





		</style>
	<?php
}

?>
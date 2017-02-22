<?php

add_theme_support( 'custom-header' );
add_theme_support( 'title-tag' );
add_theme_support( 'automatic-feed-links' );
add_theme_support( 'post-thumbnails' );

// Enqueue main styles
add_action( 'wp_enqueue_scripts', function() {
	global $wp_styles;
	wp_enqueue_style(
		'style-primary', // Warning: Identifier is referenced in child themes
		get_template_directory_uri() . '/css/site.css',
		array(),
		'20170221'
	);
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
		'name'		=> __( 'Right Sidebar', 'simplecommerce' ),
		'id'		=> 'right',
		'description' => __( 'Widgets added to this sidebar will be displayed on the right-hand side of the course template page.' )
	) );

	register_sidebar( array(
		'name' => 'WTP Course sidebar',
		'id' => 'wtp-course-sidebar',
		'description' => __( 'WTP Course Sidebar' )
	));

	register_sidebar( array(
		'name' => 'AOH Course sidebar',
		'id' => 'aoh-course-sidebar',
		'description' => __( 'AOH Course Sidebar' )
	));
});

// ==========================================================
// WooCommerce
// ==========================================================

remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

add_action( 'woocommerce_before_main_content', 'woocommerce_theme_wrapper_start', 10 );
add_action( 'woocommerce_after_main_content', 'woocommerce_theme_wrapper_end', 10 );

function woocommerce_theme_wrapper_start() {
	echo '<div class="row"><div class="twelve columns">';
}

function woocommerce_theme_wrapper_end() {
	echo '</div></div>';
}

add_action( 'after_setup_theme', 'woocommerce_support' );

function woocommerce_support() {
    add_theme_support( 'woocommerce' );
}

// ==========================================================
// Shortcodes
// ==========================================================

require_once( 'functions-shortcodes.php' );

// ==========================================================
// Theme Settings (Customizer)
// ==========================================================

add_action( 'customize_register', 'simplecommerce_customize_register' );

function simplecommerce_customize_register( $wp_customize ) {
	//####################################
	// Site archive page ID
	//####################################
	$wp_customize->add_setting( 'site_archives_page_id', array( 'default' => '' ));
	$wp_customize->add_control( 'site_archives_page_id_control', array(
		'label' => __( 'Site Archives Page ID', 'simplecommerce' ),
		'section' => 'title_tagline',
		'settings' => 'site_archives_page_id',
		'type' => 'text'
	) );

	//####################################
	// Color settings
	//####################################

	$wp_customize->add_setting( 'color_background_light', array(
		'default' => '#f8f8f8',
		'transport' => 'refresh'
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'color_background_light', array(
		'label' => __( 'Aside/Box Background Color', 'simplecommerce' ),
		'section' => 'colors',
		'settings' => 'color_background_light' ) ) );

	$wp_customize->add_setting( 'color_box_foreground', array(
		'default' => '#222',
		'transport' => 'refresh'
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'color_box_foreground', array(
		'label' => __( 'Aside/Box Text Color', 'simplecommerce' ),
		'section' => 'colors',
		'settings' => 'color_box_foreground' ) ) );


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

	$wp_customize->add_setting( 'color_footer_text', array(
		'default' => '#eee',
		'transport' => 'refresh'
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'color_footer_text', array(
		'label' => __( 'Footer Text Color', 'simplecommerce' ),
		'section' => 'colors',
		'settings' => 'color_footer_text' ) ) );

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

	$wp_customize->add_setting( 'color_header_stripe', array(
		'default' => '',
		'transport' => 'refresh'
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'color_header_stripe', array(
		'label' => __( 'Header Stripe Color', 'simplecommerce' ),
		'section' => 'colors',
		'settings' => 'color_header_stripe' ) ) );

	//####################################
	// Error page settings
	//####################################

	$wp_customize->add_section( 'theme_error_pages', array(
		'title' => __( 'Error Pages', 'simplecommerce' )
	) );

	$wp_customize->add_setting( 'error_page_404_custom_html', array(
		'default' => '',
		'transport' => 'refresh'
	) );
	$wp_customize->add_control( 'error_page_404_custom_html_control', array(
		'label' => __( 'Custom 404 HTML', 'simplecommerce' ),
		'section' => 'theme_error_pages',
		'settings' => 'error_page_404_custom_html',
		'type' => 'textarea'
	) );
}

add_action( 'wp_head', 'simplecommerce_customize_css');

function is_valid_color( $color ) {
	return preg_match( '/#([a-f0-9]{3}|[a-f0-9]{6})/', $color );
}

function ensure_starts_with( $subject, $start_with ) {
	return ( strpos( $subject, $start_with, 0 ) !== FALSE )
			? $subject
			: $start_with . $subject;
}

function simplecommerce_customize_css() {
	$color_background_light = ensure_starts_with( get_theme_mod( 'color_background_light', '#f8f8f8' ), '#' );
	$color_box_foreground = ensure_starts_with( get_theme_mod( 'color_box_foreground', '#222' ), '#' );
	$color_label_text = ensure_starts_with( get_theme_mod( 'color_label_text', '#555' ), '#' );
	$color_border = ensure_starts_with( get_theme_mod( 'color_border', '#e1e1e1' ), '#' );
	$color_accent_background = ensure_starts_with( get_theme_mod( 'color_accent_background', '#aaa' ), '#' );
	$color_link = ensure_starts_with( get_theme_mod( 'color_link', '#1eaedb' ), '#' );
	$color_link_visited = ensure_starts_with( get_theme_mod( 'color_link_visited', '#0078a0' ), '#' );
	$color_link_hover = ensure_starts_with( get_theme_mod( 'color_link_hover', '#0fa0ce' ), '#' );
	$color_header_text = ensure_starts_with( get_header_textcolor(), '#' );
	$color_button_text = ensure_starts_with( get_theme_mod( 'color_button_text', '#fff' ), '#' );
	$color_button_background = ensure_starts_with( get_theme_mod( 'color_button_background', '#0073d4' ), '#' );
	$color_button_background_hover = ensure_starts_with( get_theme_mod( 'color_button_background_hover', '#4fa5ed' ), '#' );
	$color_button_text_hover = ensure_starts_with( get_theme_mod( 'color_button_text_hover', '#fff' ), '#' );
	$color_background_dark = ensure_starts_with( get_theme_mod( 'color_background_dark', '#222' ), '#' );
	$color_footer_text = ensure_starts_with( get_theme_mod( 'color_footer_text', '#eee' ), '#' );
	?>
		<style type='text/css'>

		<?php if ( is_valid_color( $color_background_light ) || is_valid_color( $color_box_foreground ) ): ?>
			code, aside, blockquote, label.toggle, .toggle-content, article.comment.row, .author-box {
				<?php echo is_valid_color( $color_background_light ) ? "background: $color_background_light;" : "" ?>
				<?php echo is_valid_color( $color_box_foreground ) ? "color: $color_box_foreground;" : ""; ?>
			}
		<?php endif;

		if ( is_valid_color( $color_label_text ) ): ?>
			label.toggle {
				color: <?php echo $color_label_text; ?>;
			}
		<?php endif;
		if ( is_valid_color( $color_border ) ): ?>
			code, th, td, hr {
				border-color: <?php echo $color_border; ?>;
			}
			ul#menu-primary-nav:before, ul#menu-primary-nav:after {
				background: <?php echo $color_border; ?>;
			}
		<?php endif;
		if ( is_valid_color( $color_accent_background ) ): ?>
			input + label.toggle > i.collapsed,
			input:checked + label.toggle > i.expanded {
				background: <?php echo $color_accent_background; ?>;
			}
		<?php endif;
		if ( is_valid_color( $color_link ) ): ?>
			a {
				color: <?php echo $color_link; ?>;
			}
		<?php endif;
		if ( is_valid_color( $color_link_visited ) ): ?>
			a:visited {
				color: <?php echo $color_link_visited; ?>;
			}
		<?php endif;
		if ( is_valid_color( $color_link_hover ) ): ?>
			a:hover {
				color: <?php echo $color_link_hover; ?>;
			}
		<?php endif;
		if ( is_valid_color( $color_header_text ) ): ?>
			#hero h1, #hero h1 a, #hero h2, #hero h2 a {
				color: #<?php echo $color_header_text; ?>;
			}
		<?php endif;
		if ( is_valid_color( $color_button_text ) || is_valid_color( $color_button_background ) ): ?>
			.button,
			button,
			input[type="submit"],
			input[type="reset"],
			input[type="button"],
			a.btn {
				<?php if ( is_valid_color( $color_button_background ) ) {
					echo "background: $color_button_background; ";
				}
				if (is_valid_color( $color_button_text ) ) {
					echo "color: $color_button_text;";
				} ?>
			}
		<?php endif;
		if ( is_valid_color( $color_button_background_hover ) || is_valid_color( $color_button_text_hover ) ): ?>
			.button:hover,
			button:hover,
			input[type="submit"]:hover,
			input[type="reset"]:hover,
			input[type="button"]:hover,
			.button:focus,
			button:focus,
			input[type="submit"]:focus,
			input[type="reset"]:focus,
			input[type="button"]:focus,
			a.btn:hover {
				<?php if ( is_valid_color( $color_button_background_hover ) ) {
					echo "background: $color_button_background_hover;";
				}
				if ( is_valid_color( $color_button_text_hover ) ) {
					echo "color: $color_button_text_hover;";
				} ?>
			}
		<?php endif;
		if ( is_valid_color( $color_background_dark ) || is_valid_color( $color_footer_text ) ): ?>
			.footer-nav, nav.pagination span.page_numbers {
				<?php if ( is_valid_color( $color_background_dark ) ) {
					echo "background: $color_background_dark;";
				}
				if ( is_valid_color( $color_footer_text ) ) {
					echo "color: $color_footer_text;";
				} ?>
			}
		<?php endif; ?>

		</style>
	<?php
}

// Must be called from within the loop.
function simplecommerce_author_box() {
?>
	<div class="author-box clearfix">
		<img src="<?php echo get_avatar_url( get_the_author_meta( 'ID' ), array( 'size' => 150, 'default' => 'mm' ) ); ?>" alt="" />
		<h2>About the Author: <?php the_author(); ?></h2>
		<?php echo get_the_author_meta( 'description' ); ?>
	</div>
<?php
}

// ==========================================================
// Previous-Version Compatibility
// ==========================================================

if ( !function_exists('get_the_post_thumbnail_url') ) {
	if ( !function_exists('wp_get_attachment_image_url') ) {
		function wp_get_attachment_image_url( $attachment_id, $size = 'thumbnail', $icon = false ) {
		    $image = wp_get_attachment_image_src( $attachment_id, $size, $icon );
		    return isset( $image['0'] ) ? $image['0'] : false;
		}
	}

	function get_the_post_thumbnail_url( $post = null, $size = 'post-thumbnail' ) {
	    $post_thumbnail_id = get_post_thumbnail_id( $post );
	    if ( ! $post_thumbnail_id ) {
	        return false;
	    }
	    return wp_get_attachment_image_url( $post_thumbnail_id, $size );
	}
}

?>
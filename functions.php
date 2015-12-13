<?php

add_theme_support( 'custom-header' );
add_theme_support( 'title-tag' );
add_theme_support( 'automatic-feed-links' );

// This is bad, but necessary. wpautop(), apart from being a poor idea in the first place (IMO),
// doesn't know anything about shortcodes and consistently breaks shortcodes with content. The
// shortcode_unautop() function is supposed to undo the damage, but it uses regular expressions
// like a hammer to solve a problem which in reality needs a token parser (nested tags) and
// therefore does not work.
//
// That leaves the hapless theme/plugin developer three options:
//
// 1. Rejigger the shortcode filter priority to bump it ahead of wpautop(). This could break 
//    other plugins relying on priority.
// 2. Disable wpautop() entirely. (I would rejoice in principle, but most other people like wpautop.)
// 3. Leave shortcodes broken and generating invalid output, and just recommend to users that they
//    download a plugin to disable wpautop on a per-post basis.
//
// I'm taking option #1 here as the lesser evil. wpautop() still puts extraneous <p> elements all
// over the dang place, but at least it no longer walks all over the shortcode-generated markup.
//
// For more information about this problem, see also:
//
// - https://core.trac.wordpress.org/ticket/6984
// - https://core.trac.wordpress.org/ticket/14050
// - http://customcreative.co.uk/resolving-wpautop-and-shortcodes/
// - http://betterwp.net/protect-shortcodes-from-wpautop-and-the-likes/

remove_filter( 'the_content', 'do_shortcode' );
add_filter( 'the_content', 'do_shortcode', 9);



// Enqueue main styles
add_action( 'wp_enqueue_scripts', function() {
	global $wp_styles;
	wp_enqueue_style( 'style-primary', get_bloginfo( 'stylesheet_directory' ) . '/css/site.css' );
	wp_enqueue_style( 'fontawesome', get_bloginfo( 'stylesheet_directory' ) . '/css/font-awesome.min.css' );
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
add_shortcode( 'contentbox', 'simplecommerce_shortcode_contentbox' );
add_filter( 'no_texturize_shortcodes', function( $non_texturized_shortcodes ) {
	$non_texturized_shortcodes[] = 'columns';
	$non_texturized_shortcodes[] = 'column';
	$non_texturized_shortcodes[] = 'testimonial';
	$non_texturized_shortcodes[] = 'contentbox';
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

function simplecommerce_shortcode_contentbox( $attrs, $content = '' ) {
	$parsed_attrs = shortcode_atts( array(
		'align' => 'right'
	), $attrs );
	return "<aside class='content-box " . $parsed_attrs['align'] . "'>" . do_shortcode( $content ) . "</aside>";
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
		<?php if ( is_valid_color( $color_background_light ) ): ?>
			code, aside, blockquote, label.toggle, .toggle-content, article.comment.row, .author-box, span.post-date-year {
				background: <?php echo $color_background_light; ?>;
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
			input:checked + label.toggle > i.expanded,
			div.post-date {
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
	<div class="twelve columns author-box clearfix">
		<img src="<?php echo get_avatar_url( get_the_author_meta( 'ID' ), array( 'size' => 150, 'default' => 'mm' ) ); ?>" alt="" />
		<h2>About the Author: <?php the_author(); ?></h2>
		<?php echo get_the_author_meta( 'description' ); ?>
	</div>
<?php
}

?>
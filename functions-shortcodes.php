<?php

$shortcodes = array( 'columns', 'column', 'testimonial', 'toggle', 'contentbox' );
$shortcode_replacements = array();

foreach ( $shortcodes as $shortcode ) {
    add_shortcode( $shortcode, 'simplecommerce_shortcode_' . $shortcode );
}

// This is the story of a terrible design decision in WordPress. At some point in the distant
// past (at least ten years ago as of this writing), the `wpautop` function was introduced to
// automatically add <p> and <br> to post content instead of implementing a Markdown parser.
// wpautop doesn't know anything about shortcodes and consistently breaks them if they contain content.
// WordPress has a `shortcode_unautop` function which is supposed to undo the damage, but
// `shortcode_unautop` uses regular expressions to solve a problem which needs a smarter token
// parser (due to nested tags) and therefore does not work. ("A developer has a problem. He decides
// to solve it via regex. A developer has two problems...")
//
// That leaves the hapless theme/plugin developer four options:
//
// 1. Rejigger the shortcode filter priority to bump it ahead of `wpautop`. This could break
//    other plugins relying on priority or implementing their own workarounds.
// 2. Disable `wpautop` entirely. (I would rejoice in principle, but most other people like wpautop.)
// 3. Leave shortcodes broken and generating invalid output, and just recommend to users that they
//    download a plugin to disable `wpautop` on a per-post basis.
// 4. Sometime before `wpautop` murders the content, parse the entire post content yourself to
//    find your shortcodes, yank them out, leave random strings in their place, wait until `wpautop`
//    commits its debauchery, then parse the content again and replace the random strings with
//    your shortcodes again (all before `do_shortcode` runs).
//
// I've opted for #4 here, which is why this file is about three times as complicatd as it needs to be.
// I previously used #1, but that does subtly break some popular plugins such as WooCommerce. Even this
// approach is not perfect because it's still not a true token parser, but it's "good enough"...for now.
//
// For more information about this problem, see also:
//
// - https://core.trac.wordpress.org/ticket/6984
// - https://core.trac.wordpress.org/ticket/14050
// - http://customcreative.co.uk/resolving-wpautop-and-shortcodes/
// - http://betterwp.net/protect-shortcodes-from-wpautop-and-the-likes/

// See wp-includes/default-filters.php for core WordPress filter priorities
// 9 comes right before wpautop at 10
add_filter('the_content', 'simplecommerce_rescue_shortcodes_from_wpautop', 9);
// Same level as wp_autop, executed after (because ours is added later) but before do_shortcode at 11
add_filter('the_content', 'simplecommerce_restore_shortcodes_after_wpautop', 10);

function simplecommerce_rescue_shortcodes_from_wpautop( $content ) {
    global $shortcode_replacements;

    function find_first_shortcode_pos( $content, $offset ) {
        global $shortcodes;

        $earliest_pos = -1;
        $first_shortcode = null;

        foreach ( $shortcodes as $shortcode ) {
            $pos = strpos( $content, '[' . $shortcode . ']', $offset );

            if ( $pos !== false && ( $earliest_pos == -1 || $pos < $earliest_pos ) ) {
                $earliest_pos = $pos;
                $first_shortcode = $shortcode;
            }
        }

        return $earliest_pos;
    }

    // Search for any instance of known shortcodes.
    // Upon finding a shortcode opening tag, continue searching forward for the corresponding closing tag.
    $pos = 0;

    while ( $pos < strlen( $content ) ) {
        $pos = find_first_shortcode_pos( $content, $pos );

        if ( $pos == -1 ) {
            break;
        }

        // Found an opening tag? Continue searching until we find the matching closing tag.
        // There is the possibility of nested opening tags before reaching the matching close.
        $tag_name = substr( $content, $pos + 1, strpos( $content, ']', $pos ) - $pos - 1 );
        $opening_tag_regex = '/\[' . $tag_name . '( [^]]+)?\]/';
        $closing_tag = '[/' . $tag_name . ']';
        $open_tag_count = 1;
        $current_index = $pos;

        while ( $open_tag_count > 0 && $current_index > -1 && $current_index < strlen( $content )) {
            $closing_pos = strpos( $content, $closing_tag, $current_index );
            $open_tag_found = preg_match( $opening_tag_regex, $content, $matches, PREG_OFFSET_CAPTURE, $current_index );

            if ( $open_tag_found ) {
                $matched_open_tag = $matches[0][0];
                $next_opening_pos = $matches[0][1];
            }

            if ( $closing_pos === false ) {
                $closing_pos = -1;
            }

            if ( !$open_tag_found || $closing_pos < $next_opening_pos ) {
                if ( $closing_pos == -1 ) {
                    // This is a problem; we have no closing tag. Just take the rest of the content.
                    $open_tag_count = 0;
                    $closing_pos = strlen( $content ) - strlen( $closing_tag );
                    break;
                }

                $current_index = $closing_pos + strlen( $closing_tag );
                $open_tag_count--;
            } else {
                $current_index = $next_opening_pos + strlen( $matched_open_tag );
                $open_tag_count++;
            }
        }

        // Replace everything from $pos to $current_index with a placeholder and stash that placeholder.
        // Don't forget to wrap it in <p> so that wpautop doesn't. :P
        $key = '<p>' . md5(rand()) . '</p>';
        $shortcode_replacements[$key] = substr( $content, $pos, $current_index - $pos );
        $content = substr_replace( $content, $key, $pos, $current_index - $pos );

        $pos = $pos + strlen( $key );
    }

    return $content;
}

function simplecommerce_restore_shortcodes_after_wpautop( $content ) {
    global $shortcode_replacements;

    foreach ( $shortcode_replacements as $key => $value ) {
        $content = str_replace( $key, $value, $content );
    }

    return $content;
}

add_filter( 'no_texturize_shortcodes', function( $non_texturized_shortcodes ) {
    global $shortcodes;

    foreach ( $shortcodes as $shortcode ) {
        $non_texturized_shortcodes[] = $shortcode;
    }

    return $non_texturized_shortcodes;
});

function simplecommerce_parse_markdown( $content ) {
    if ( class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'markdown' ) ) {
        return WPCom_Markdown::get_instance()->transform( $content, array( 'unslash' => false ) );
    }

    return $content;
}

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
    return "<div class='nested column $css_class'>" . do_shortcode( simplecommerce_parse_markdown( $content ) ) . "</div>";
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

    return "<blockquote class='testimonial'>" . do_shortcode( simplecommerce_parse_markdown( $content ) ) . $cite . "</blockquote>";
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
            "<div id='sc-toggle-$id' class='toggle-content' markdown='1'>" . simplecommerce_parse_markdown( $content ) . "</div>" .
            "</div>"; // .toggle-container

}

function simplecommerce_shortcode_contentbox( $attrs, $content = '' ) {
    $parsed_attrs = shortcode_atts( array(
        'align' => ''
    ), $attrs );
    return "<aside class='content-box " . $parsed_attrs['align'] . "'>" . do_shortcode( simplecommerce_parse_markdown( $content ) ) . "</aside>";
}

?>
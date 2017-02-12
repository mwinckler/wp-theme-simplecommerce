<?php
/*
Template Name: Archives
*/

function get_post_thumbnail_image_url() {
    if (has_post_thumbnail()) {
        return get_the_post_thumbnail_url();
    }

    // Attempt to retrieve the first image from within the post.
    $img_src_regex = '/\<img [^>\/]*src=[\'"]([^\'"]+)[\'"]/';
    if ( preg_match( $img_src_regex, apply_filters( 'the_content', get_the_content() ), $matches ) > 0  && count( $matches ) > 1 ) {
        return $matches[1];
    }

    // Fallback on author avatar.
    return get_avatar_url( get_the_author_meta( 'ID' ), array( 'size' => 128, 'default' => 'blank' ) );
}

get_header(); ?>

    <div class="row">
        <div class="twelve columns">
            <h1>Site Archives</h1>
        </div>
    </div>

    <div class="row site-archive">
        <div class="eight columns">
            <?php
                function ensure_closed( $is_open, $closing_tag ) {
                    if ( !$is_open ) {
                        return;
                    }

                    print $closing_tag;
                }

                function get_year_id( $year ) {
                    return 'year-' . $year;
                }

                function get_month_id( $year, $month ) {
                    return 'year-' . $year . '-month-' . $month;
                }

                $year_link_data = array();
                $month_link_data = array();
                $month_post_count = 0;
                $year_post_count = 0;

                $is_list_open = false;
                $is_month_section_open = false;
                $is_year_section_open = false;

                $current_year = -1;
                $current_month = '';
                $query = new WP_Query( array(
                    'post_type' => 'post',
                    'posts_per_page' => -1
                ));

                foreach ( $query->posts as $post ) {
                    $post_date = new DateTime( $post->post_date );

                    if ( $post_date->format( 'm' ) != $current_month || $post_date->format( 'Y' ) != $current_year ) {
                        if ($month_post_count > 0) {
                            $month_link_data[$current_month] = DateTime::createFromFormat('!m', $current_month)->format( 'F' ) . ' (' . $month_post_count . ')';
                        }

                        $current_month = $post_date->format( 'm' );
                        $current_month_desc = $post_date->format( 'F' ) . ' &rsquo;' . $post_date->format( 'y' );
                        $month_post_count = 0;
                        $month_section_link_id = get_month_id( $post_date->format( 'Y' ), $current_month );

                        ensure_closed( $is_list_open, "</ul>" );
                        ensure_closed( $is_month_section_open, "</section>" );

                        $is_month_section_open = false;
                    }

                    if ( $post_date->format( 'Y' ) != $current_year ) {

                        if ( $is_year_section_open ) {
                            ksort( $month_link_data );
                            $year_link_data[$current_year] = $month_link_data;
                            $month_link_data = array();
                        }

                        $current_year = $post_date->format( 'Y' );
                        $section_link_id = get_year_id( $current_year );

                        ensure_closed( $is_list_open, "</ul>" );
                        ensure_closed( $is_year_section_open, "</section>" );

                        $is_year_section_open = true;

                        ?>
                        <section class="year">
                            <h3 id="<?php echo get_year_id( $current_year ); ?>"><?php echo $current_year; ?></h3>
                        <?php
                    }


                    if (!$is_month_section_open) { ?>
                        <section class="month">
                            <h4 id="<?php echo $month_section_link_id; ?>"><?php echo $current_month_desc; ?></h4>
                            <ul>
                    <?php
                        $is_month_section_open = true;
                    }

                    ?>

                    <li><a href="<?php echo get_permalink( $post ); ?>"><?php echo $post->post_title; ?></a></li>

                    <?php

                    $month_post_count++;
                    $year_post_count++;
                } // foreach post

                ensure_closed( $is_month_section_open, "</section>" );
                ensure_closed( $is_year_section_open, "</section>" );
            ?>
            </ul>
        </div>
        <div class="four columns">
            <section id="archive-toc">
                <ul>
                <?php
                foreach ( $year_link_data as $year => $month_data ) {
                    ?>
                    <li><a href="#<?php echo get_year_id( $year ); ?>"><?php echo $year; ?></a></li>
                    <ul>
                    <?php
                    foreach ( $month_data as $month => $link_text ) { ?>
                        <li><a href="#<?php echo get_month_id( $year, $month ); ?>"><?php echo $link_text; ?></a></li>
                    <?php
                    } ?>
                    </ul>
                <?php
                } ?>
                </ul>
            </section>
        </div>

    <div class="row post-navigation">
        <div class="twelve columns">
        <?php
        echo get_next_posts_link( '<i class="fa fa-arrow-circle-left"></i> Older' );
        echo get_previous_posts_link ( 'Newer <i class="fa fa-arrow-circle-right"></i>' );
        ?>
        </div>
    </div>

<?php get_footer(); ?>
<?php
/* Search results - very similar to archive.php */

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
			<h1><?php echo 'Search results for "' . get_search_query() . '"' ?></h1>
		</div>
	</div>
	<?php
		if ( have_posts() ) {
			while ( have_posts() ) {
				the_post();
				?>
				<div class="row archive_post_row">
					<div class="five columns">
						<div class="post-meta">
							<div class="post-image"  style="background-image: url('<?php echo get_post_thumbnail_image_url(); ?>');">
								<a href="<?php echo esc_url( get_permalink() )?>"></a>
							</div>
							<div class="post-date">
								<?php the_time( 'M. jS, Y' ); ?>
							</div>
							<span class="post-author"><?php the_author(); ?></span>
						</div>
					</div>
					<div class="seven columns">
						<h2 class="post-title"><?php the_title( sprintf( '<a href="%s">', esc_url( get_permalink() ) ), '</a>' ); ?></h2>
						<article><?php the_excerpt(); ?></article>
					</div>
				</div>
				<?php
			}

			the_posts_pagination( array(
				'prev_text' => '<i class="fa fa-arrow-circle-left"></i>',
				'next_text' => '<i class="fa fa-arrow-circle-right"></i>'
			) );
		} else { ?>
			<div class="row">
				<div class="six columns offset-by-three">
					<p>Sorry, nothing matched your search. Try again?</p>
					<?php get_search_form(); ?>
				</div>
			</div>
		<?php
		}
	?>

<?php get_footer();

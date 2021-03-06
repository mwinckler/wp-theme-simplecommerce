<?php
	// Default template for site content.
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php
		$post_type = get_post_type();
		$is_page = $post_type == 'page';
		$is_forum = $post_type == 'forum' || $post_type = 'topic';

		if ( is_single() ) {
			the_title( '<h1>', '</h1>' );
		} else {
			the_title( sprintf( '<h1><a href="%s">', esc_url( get_permalink() ) ), '</a></h1>' );
		}

		if ( !$is_page && !$is_forum ): ?>
			<div class="author-byline">
				<time><?php the_time( 'F jS, Y' ); ?></time>
				&SmallCircle;
				<span><?php the_author(); ?></span>
				<?php
					$categories = get_the_category();
					$output = array();
					if ( ! empty( $categories ) ) {
					    foreach( $categories as $category ) {
					        $output[] = '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '" alt="' . esc_attr( sprintf( __( 'View all posts in %s', 'textdomain' ), ucwords( $category->name ) ) ) . '">' . esc_html( ucwords( $category->name ) ) . '</a>';
					    }
					    echo '&SmallCircle; <span>' . implode( $output, ', ' ) . '</span>';
					}
				?>

				<?php edit_post_link( __( 'Edit', 'simplecommerce' ), '&SmallCircle; <span>', '</span>' ); ?>
			</div>
		<?php endif;

		the_content( __( 'Continue reading...', 'simplecommerce' ) );

		if ( !$is_page && !$is_forum ) {
			simplecommerce_author_box();
		}

		if ( !is_single() && !$is_page && !$is_forum  )  {
		?>
		<div class="comments-link">
			<span>
				<i class="fa fa-comments"></i>
				<?php comments_popup_link( __( 'Reply', 'simplecommerce' ), __( '1 Comment', 'simplecommerce' ), __( '% Comments', 'simplecommerce' ) ); ?>
			</span>
		</div>
		<?php
		}


		wp_link_pages( array(
			'before'		=> '<nav class="pagination">',
			'after'			=> '</nav>'
		) );


	?>
</article>
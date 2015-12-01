<?php
	// Default template for site content.
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php 
		if ( is_single() ) {
			the_title( '<h1>', '</h1>' );
		} else {
			the_title( sprintf( '<h1><a href="%s">', esc_url( get_permalink() ) ), '</a></h1>' );
		}

		the_content( __( 'Continue reading...', 'simplecommerce' ) );

		wp_link_pages( array(
			'before'		=> '<nav class="pagination">',
			'after'			=> '</nav>'
		) );

		
	?>
</article>
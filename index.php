<?php
	get_header();

	function simplecommerce_index_add_main_content() {
		if ( have_posts() ) {
			while ( have_posts() ) {
				the_post();
				get_template_part( 'content', get_post_format() );
			}
		} else {
			get_template_part( 'content', 'none' );
		} 


		// Previous/next page navigation.
		the_posts_pagination( array(
			'prev_text'          => __( 'Previous page', 'simplecommerce' ),
			'next_text'          => __( 'Next page', 'simplecommerce' ),
			'before_page_number' => '<span class="meta-nav screen-reader-text">' . __( 'Page', 'simplecommerce' ) . ' </span>',
		) );
	}

?>

	<div class="row">
		<div class="twelve columns">
			<?php simplecommerce_index_add_main_content(); ?>
		</div>
	</div>


<?php
	get_footer();
?>
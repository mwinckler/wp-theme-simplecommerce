<?php		
	get_header();
?>
	<div class="row">
		<div class="twelve columns">
		<?php
			if ( have_posts() ) {
				while ( have_posts() ) {
					the_post();
					get_template_part( 'content', get_post_format() );

					if ( comments_open() || get_comments_number() ) {
						comments_template();
					}

					the_post_navigation( array(
						'next_text' => '%title',
						'prev_text' => '%title'
					) );
				}
			} else {
				get_template_part( 'content', 'none' );
			} 
		?>
		</div>
	</div>

<?php get_footer(); ?>

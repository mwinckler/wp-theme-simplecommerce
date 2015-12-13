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

					simplecommerce_author_box();

					if ( comments_open() || get_comments_number() ) {
						comments_template();
					}

				}
			} else {
				get_template_part( 'content', 'none' );
			} 
		?>
		</div>
	</div>

	<div class="row post-navigation">
		<div class="twelve columns">
		<?php

		$post_nav = get_the_post_navigation( array(
			'next_text' => '<i class="fa fa-arrow-circle-left"></i> %title',
			'prev_text' => '%title <i class="fa fa-arrow-circle-right"></i>'
		) );

		if ( !empty( $post_nav ) ) {
			echo "<h2>More " . get_bloginfo( 'title' ) . "</h2>";
			echo $post_nav;
		}

		?>
		</div>
	</div>

<?php get_footer(); ?>

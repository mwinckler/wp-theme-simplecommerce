<?php		
/**
 * Template Name: Course Page
 */
	get_header();
?>
	<div class="row">
		<div class="nine columns">
		<?php
			if ( have_posts() ) {
				while ( have_posts() ) {
					the_post();
					get_template_part( 'content', get_post_format() );
				}
			} else {
				get_template_part( 'content', 'none' );
			} 
		?>
		</div>
		<div class="three columns">
			<?php 
			$sidebar_name = 'wtp-course-sidebar';
			if ( is_active_sidebar( $sidebar_name ) ): ?>
			<ul class="widget-area">
				<?php dynamic_sidebar( $sidebar_name ); ?>
			</ul>
			<?php else: ?>
			&nbsp;
			<?php endif; // is_active_sidebar ?>
		</div>
	</div>

<?php get_footer(); ?>
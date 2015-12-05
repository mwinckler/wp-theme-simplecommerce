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
	}

?>

	<div class="row">
		<div class="twelve columns">
			<?php simplecommerce_index_add_main_content(); ?>
		</div>
	</div>

	<div class="row post-navigation">
		<div class="twelve columns">
		<?php
		echo get_next_posts_link( '<i class="fa fa-arrow-circle-left"></i> Older' );
		echo get_previous_posts_link ( 'Newer <i class="fa fa-arrow-circle-right"></i>' );
		?>
		</div>
	</div>

<?php
	get_footer();
?>
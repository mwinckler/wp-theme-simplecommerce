<?php
	get_header();

?>

	<div class="row">
		<div class="twelve columns">
			<?php
			$is_page = is_page();
			$visible_post_ids = array();

			if ( have_posts() ) {
				while ( have_posts() ) {
					the_post();
					$visible_post_ids[] = get_the_ID();
					get_template_part( 'content', get_post_format() );
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
		echo get_next_posts_link( '<i class="fa fa-arrow-circle-left"></i> Older' );
		echo get_previous_posts_link ( 'Newer <i class="fa fa-arrow-circle-right"></i>' );
		?>
		</div>
	</div>


	<?php
	$recent_posts = wp_get_recent_posts( array(
		'numberposts' => 3,
		'exclude' => array( 'post__not_in', $visible_post_ids ),
		'post_status' => 'publish'
	));
	if ( is_home() ) :
		?>
		<div class="row">
			<div class="twelve columns">
				<h2>Recent Posts</h2>
			</div>
		</div>
		<div class="row">
		<?php foreach ( $recent_posts as $post ): ?>
			<div class="four columns">
				<h3><a href="<?php echo get_permalink($post["ID"]); ?>"><?php print $post["post_title"]; ?></a></h3>
			</div>
		<?php endforeach; ?>
		</div>
	<?php endif; ?>

<?php
	get_footer();
?>
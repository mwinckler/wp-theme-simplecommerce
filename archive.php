<?php get_header(); ?>

	<div class="row">
		<div class="twelve columns">
			<h1><?php echo single_cat_title( '', false ); ?></h1>
		</div>
	</div>
	<?php
		if ( have_posts() ) {
			while ( have_posts() ) {
				the_post();
				?>
				<div class="row">
					<div class="two columns">
						<div class="post-meta" style="background-image: url('<?php echo get_avatar_url( get_the_author_meta( 'ID' ), array( 'size' => 128, 'default' => 'blank' ) ); ?>');">
							<div class="post-date">
								<?php the_time( 'M. jS, Y' ); ?>
							</div>
							<span class="post-author"><?php the_author(); ?></span>
						</div>
					</div>
					<div class="ten columns">
						<h2 class="post-title"><?php the_title( sprintf( '<a href="%s">', esc_url( get_permalink() ) ), '</a>' ); ?></h2>
						<article><?php the_excerpt(); ?></article>
					</div>
				</div>
				<?php
			}
		}
	?>

	<div class="row post-navigation">
		<div class="twelve columns">
		<?php
		echo get_next_posts_link( '<i class="fa fa-arrow-circle-left"></i> Older' );
		echo get_previous_posts_link ( 'Newer <i class="fa fa-arrow-circle-right"></i>' );
		?>
		</div>
	</div>

<?php get_footer(); ?>
<?php get_header(); ?>

	<div class="row">
		<div class="three columns">
			<i class="fa fa-frown-o fa-5x fa-pull-right"></i>
		</div>
		<div class="nine columns">
			<p>Oops, that didn't work.</p>

			<p>Try a search:</p>
			<?php 

			get_search_form();

			$custom_404_html = get_theme_mod( 'error_page_404_custom_html', false );

			if ( $custom_404_html ) {
				echo $custom_404_html;
			}

			?>
			

		</div>
	</div>

<?php get_footer(); ?>
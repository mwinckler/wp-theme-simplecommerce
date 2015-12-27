<?php get_header(); ?>

	<div class="row">
		<div class="twelve columns">
			<h2>Oops, that didn't work.</h2>
		</div>
	</div>

	</div>
	<div class="row">
		<div class="six columns offset-by-three">
			<i class="fa fa-frown-o fa-5x fa-pull-left"></i>
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
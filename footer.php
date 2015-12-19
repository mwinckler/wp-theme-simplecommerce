		</div><!-- .container -->
		<div class="footer-nav">
			<div class="container">
				<div class="row footer-widget-area">
					<?php foreach ( Array('footer-col-1', 'footer-col-2') as $sidebar_name ): ?>
						<div class="six columns">
							<?php if ( is_active_sidebar( $sidebar_name ) ): ?>
							<ul class="widget-area">
								<?php dynamic_sidebar( $sidebar_name ); ?>
							</ul>
							<?php else: ?>
							&nbsp;
							<?php endif; // is_active_sidebar ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>

		<?php wp_footer(); ?>
	</body>
</html>
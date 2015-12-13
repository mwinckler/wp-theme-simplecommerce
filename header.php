<!DOCTYPE html>
<html>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width">
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
		<link href='https://fonts.googleapis.com/css?family=Raleway:400,600' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
		<!--[if lt IE 9]>
		<script src="js/html5shiv.js"></script>
		<![endif]-->		

		<?php wp_head(); ?>
	</head>
	<body <?php body_class(); ?>>
		<?php 
			$custom_header_img = get_custom_header()->url; 
			$header_stripe_color = ensure_starts_with( get_theme_mod( 'color_header_stripe', '' ), '#' );
			if ( is_valid_color( $header_stripe_color ) || !empty( $custom_header_img ) ): ?>
				<div class="header-stripe" style="<?php 
					echo is_valid_color( $header_stripe_color ) ? "background-color: $header_stripe_color;" : ""; 
					echo !empty( $custom_header_img ) ? "background-image:url('$custom_header_img');" : ""; ?>"></div>
			<?php endif;
		?>
		<div class="container">
			<div class="row">

				<div class="twelve columns" id="hero">
					<hgroup>
						<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
						<h2 class="site-subtitle"><?php bloginfo( 'description' ); ?></h2>
					</hgroup>
				</div>
			</div>
			<?php if ( has_nav_menu( 'primary' ) ) : ?>
			<div class="row">
				<div class="twelve columns" id="nav-primary">
				<?php wp_nav_menu( array( 'theme_location' => 'primary' ) ); ?>
				</div>
			</div>
			<?php endif // has_nav_menu( 'primary' ) ?>

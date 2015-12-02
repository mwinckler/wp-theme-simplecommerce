<!DOCTYPE html>
<html>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width">
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
		<link href='https://fonts.googleapis.com/css?family=Raleway:400,600|Signika:700' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
		<!--[if lt IE 9]>
		<script src="js/html5shiv.js"></script>
		<![endif]-->		

		<?php wp_head(); ?>
	</head>
	<body <?php body_class(); ?>>

		<div class="container">
			<div class="row">
				<?php 
					$custom_header_img = get_custom_header()->url; 
				?>
				<div class="twelve columns" id="hero" <?php echo $custom_header_img ? "style=\"background-image:url('$custom_header_img') 50% 50% no-repeat;\"" : "" ?>>
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

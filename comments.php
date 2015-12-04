<?php
if ( post_password_required() ) {
	return;
}

function simplecommerce_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	$gravatar_url = get_avatar_url( $comment, array( 'size' => 300, 'default' => 'blank' ) );

	switch ( $comment->comment_type ) :
		case 'pingback' :
		case 'trackback' :
	?>
	<li class="post pingback">
		<p><?php _e( 'Pingback:'); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( 'Edit' ), '<span class="edit-link">', '</span>' ); ?></p>
	<?php
			break;
		default :
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<article id="comment-<?php comment_ID(); ?>" class="comment row one-true-container">
			<div class="comment-author vcard column">
				<div class="comment-author-avatar" style="background-image: url(<?php echo $gravatar_url; ?>);">
					<!-- <img src="<?php echo $gravatar_url; ?>" alt="" /> -->
				</div>
				<?php
						printf( __( '%1$s' ),
							sprintf( '<span class="author-link">%s</span>', get_comment_author_link() )
						);
				?>
				<div class="comment-date">
				<?php
					printf( '<a href="%1$s"><time datetime="%2$s">%3$s</time></a>',
						esc_url( get_comment_link( $comment->comment_ID ) ),
						get_comment_time( 'c' ),
						get_comment_time( 'M j, Y g:ia' )
					);
				?>
				</div>
			</div>
			<div class="comment-content column">
				<?php comment_text(); ?>

				<?php if ( $comment->comment_approved == '0' ) : ?>
					<em class="comment-awaiting-moderation"><?php _e( 'Thanks! Your comment is awaiting moderation and should appear shortly.' ); ?></em>
					<br />
				<?php endif; ?>

				<div class="reply">
					<span>
						<?php edit_comment_link( __( '<i class="fa fa-pencil"></i>Edit' ) ); ?>
					</span>
					<span>
						<?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( '<i class="fa fa-commenting-o"></i>Reply' ), 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
					</span>
				</div><!-- .reply -->

			</div>

		</article><!-- #comment-## -->

	<?php
			break;
	endswitch;		

}

?>

<div class="comments-section">
<?php if ( have_comments() ): ?>
	<h2>And now, from you:</h2>

	<ol class="comment-list">
		<?php 
			wp_list_comments( array( 
				'style' => 'ol',
				'callback' => 'simplecommerce_comment'
			) );
		?>
	</ol>	
<?php 
	endif; // have_comments
	comment_form(); 
?>
</div>
	<div class="comments">
	<?php if ( post_password_required() ) : ?>
		<p class="nopassword"><?php 'This post is password protected. Enter the password to view any comments.' ; ?></p>
	</div>
	<?php
		return;
		endif;
	?>

	<?php if ( have_comments() ) : ?>
		<h2 class="comments-title">
			<?php
				printf( _n( __('One comment:', 'pandorabox'), __('%1$s Comments:', 'pandorabox'), get_comments_number(), 'pandorabox' ),
					number_format_i18n( get_comments_number() ), '<span>' . get_the_title() . '</span>' );
			?>
		</h2>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
			<nav id="comment-nav-above">
				<h1 class="assistive-text"><?php 'Comment navigation'; ?></h1>
				<div class="nav-previous"><?php previous_comments_link( '&larr; Older Comments' ); ?></div>
				<div class="nav-next"><?php next_comments_link( 'Newer Comments &rarr;' ); ?></div>
			</nav>
		<?php endif; ?>

		<ul class="commentlist">
			<?php wp_list_comments( array( 'callback' => 'pandora_comment' ) );	?>
		</ul>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
			<nav id="comment-nav-below">
				<h1 class="assistive-text"><?php 'Comment navigation'; ?></h1>
				<div class="nav-previous"><?php previous_comments_link( '&larr; Older Comments' ); ?></div>
				<div class="nav-next"><?php next_comments_link( 'Newer Comments &rarr;' ); ?></div>
			</nav>
		<?php endif; ?>

		<?php
		if ( ! comments_open() && get_comments_number() ) : ?>
		<p class="nocomments"><?php _e( 'Comments are closed.' , 'pandorabox' ); ?></p>
		<?php endif; ?>

	<?php endif;  

	$comment_args = array( 'title_reply'=>__('Leave a Comment:','pandorabox'),

	'fields' => apply_filters( 'comment_form_default_fields', array(
		'author' => '<p class="comment-form-author">'.'<input id="author" name="author" type="text" value="'.esc_attr( $commenter['comment_author'] ).'" size="30"'.'placeholder="'.__("YOUR NAME","pandorabox").( $req ? '*' : '' ).'" /></p>',   
		'email'  => '<p class="comment-form-email">' .'<input id="email" name="email" type="text" value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30"' . 'placeholder="'.__("YOUR MAIL","pandorabox").( $req ? '*' : '' ).'" />'.'</p>', 'url' => '' )),
	    'comment_field' => '' . '<textarea id="comment" name="comment" aria-required="true" placeholder="'.__("YOUR MESSAGE", "pandorabox").( $req ? '*' : '' ).'"></textarea>',    	
		'comment_notes_before' => '',
		'comment_notes_after' => '<p class="notes-after">* - '.__("required fields","pandorabox").'</p>'
	);

	comment_form($comment_args); 

	?>

</div>

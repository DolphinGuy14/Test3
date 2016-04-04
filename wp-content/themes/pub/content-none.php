<?php
/**
 * Template for pages with empty content.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

?>
<div class="page-404">
<?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>
	<div class="page-404__box padding-all">
		<?php printf( wp_kses_post( __( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'adventure-tours' ) ), admin_url( 'post-new.php' ) ); ?>
	</div>
<?php elseif ( is_search() ) : ?>
	<div class="page-404__box padding-top padding-bottom">
		<div class="page-404__notice padding-left padding-right"><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with different keywords.', 'adventure-tours' ); ?></div>
		<form method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="page-404__form page-404__form--style2 padding-left padding-right">
			<input type="text" placeholder="<?php esc_attr_e( 'Type in your request...', 'adventure-tours' ); ?>" value="<?php echo get_search_query(); ?>" name="s">
			<div class="button button--style1 button--with-icon page-404__form--style2__button">
				<i class="fa fa-search"></i>
				<input type="submit" value="<?php esc_attr_e( 'Search', 'adventure-tours' ); ?>">
			</div>
		</form>
	</div>
<?php else : ?>
	<div class="page-404__container stick-to-top stick-to-bottom">
		<div class="page-404__content">
			<div class="page-404__image"></div>
			<div class="page-404__map"></div>
			<p class="page-404__description"><?php esc_html_e( 'Oops! The page you are looking for is not found!', 'adventure-tours' ); ?></p>
			<form method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="page-404__form">
				<div class="page-404__form__item">
					<input type="text" placeholder="<?php esc_attr_e( 'Type in your phrase', 'adventure-tours' ); ?>" value="<?php echo get_search_query(); ?>" name="s">
					<i class="fa fa-search"></i>
					<input type="submit" value="<?php esc_attr_e( 'Search', 'adventure-tours' ); ?>">
				</div>
			</form>
		</div>
	</div>
<?php endif; ?>
</div>
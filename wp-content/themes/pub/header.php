<?php
/**
 * Header template part.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

get_template_part( 'header','clean' );
?>
<header class="header" role="banner">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<?php get_template_part( 'templates/header/info' ); ?>
				<div class="header__content-wrap">
					<div class="row">
						<div class="col-md-12 header__content">
							<?php get_template_part( 'templates/header/logo' ); ?>
							<?php if ( has_nav_menu( 'header-menu' ) ) : ?>
							<nav class="main-nav-header" role="navigation">
								<?php wp_nav_menu(array(
									'theme_location' => 'header-menu',
									'container' => 'ul',
									'menu_class' => 'main-nav',
									'menu_id' => 'navigation',
									'depth' => 3,
								)); ?>
							</nav>
							<?php endif; ?>
							<div class="clearfix"></div>
						</div><!-- .header__content -->
					</div>
				</div><!-- .header__content-wrap -->
			</div>
		</div>
	</div><!-- .container -->
</header>
<?php get_template_part( 'templates/header/header-section' ); ?>
<div class="container layout-container margin-top margin-bottom">

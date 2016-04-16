<?php 
get_header(); 
$pandora_options = get_option('pandora_options');
?>

<div class="colorblock" id="bg"></div>
    <div class="block whiteblock blogblock">
        <div id="blog" class="block-container">
            <h2 class="searchtitle"> <?php printf( __( 'Search for "%s":', 'pandorabox' ), get_search_query() ); ?> </h2>

            <div class="postlist"> 
            <div class="as-post"> <?php get_search_form(); ?> </div>
            <?php if(have_posts()) : ?>
                <?php while(have_posts()) : the_post(); ?>
                    <?php get_template_part( 'content', get_post_format() );  ?>
                <?php endwhile; ?>

                <?php if (function_exists('wp_corenavi')) wp_corenavi(); else posts_nav_link(); ?>

                <?php else : ?>
                    <h4 class="searchtitle"><?php echo __('Posts not found!','pandorabox'); ?></h4>
                <?php endif; ?>
            </div> 
            <?php get_sidebar(); ?>
        </div>
    </div>

<?php get_footer(); ?>
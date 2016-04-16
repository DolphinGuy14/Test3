<?php 
get_header(); 
$pandora_options = get_option('pandora_options');
?>

<div class="colorblock" id="bg"></div>
        
 <div class="block whiteblock blogblock">
        <div id="blog" class="block-container">
            <h1 class="title"> <?php if (isset($pandora_options['blog_title'])) echo $pandora_options['blog_title'];?></h1>
            <div class="description"><?php if (isset($pandora_options['blog_subtitle'])) echo $pandora_options['blog_subtitle'];?></div>
            
            <?php query_posts(array('post_type'=>'post','paged' => get_query_var('paged'))); 
                    if ( ! isset( $content_width ) ) $content_width = 900;
            ?>

            <div class="table">
            <div class="postlist"> 
            <?php if(have_posts()) : ?>
                <?php while(have_posts()) : the_post(); ?>
                    <?php get_template_part( 'content', get_post_format() );  ?>
                <?php endwhile; ?>

                <?php if (function_exists('wp_corenavi')) wp_corenavi(); else posts_nav_link(); ?>

                <?php else : ?>
                    <article><h4><?php echo __('Posts not found!','pandorabox'); ?></h4></article>
            <?php endif; ?>
            </div>
            <?php get_sidebar(); ?>
            </div>
        </div>
    </div>

<?php get_footer(); ?> 
<article <?php post_class('article-post'); ?>>
    <div class="meta">
        <div class="type">
            <a href="<?php the_permalink() ?>">
            <?php if (has_post_format('chat')) {echo '<i class="icon-comments icon-2x"> </i>';} 
             else if (has_post_format('gallery')) {echo '<i class="icon-picture icon-2x"> </i>';}
             else if (has_post_format('link')) {echo '<i class="icon-link icon-2x"> </i>';}
             else if (has_post_format('image')) {echo '<i class="icon-camera icon-2x"> </i>';}
             else if (has_post_format('quote')) {echo '<i class="icon-quote-left icon-2x"> </i>';}
             else if (has_post_format('video')) {echo '<i class="icon-facetime-video icon-2x"> </i>';}
             else if (has_post_format('audio')) {echo '<i class="icon-headphones icon-2x"> </i>';}
             else {echo '<i class="icon-file-text icon-2x"> </i>';}
            ?>
            </a>
        </div>
        <div class="date metablock"><?php echo get_the_date('d M Y')?></div>
        <div class="author metablock">by&nbsp;<?php the_author() ?></div>
        <div class="comments-count metablock"> <?php comments_popup_link( __('No comments', 'pandorabox'), __('1 comment','pandorabox'), __('% comments', 'pandorabox'), 'comments-link', __('Comments are closed','pandorabox')); ?> </div>
        <div class="category metablock"> <?php the_category(', '); ?> </div>
        <div class="tags metablock"> <?php the_tags('', ', ', ''); ?>  </div>
    </div>
    <div class="post-body">
        <?php the_post_thumbnail(); ?>
        <h3 class="title">
            <a href="<?php the_permalink() ?>"><?php the_title(); ?></a>
        </h3>
        <?php the_content(__('Read more','pandorabox')); ?>
    </div>
</article>
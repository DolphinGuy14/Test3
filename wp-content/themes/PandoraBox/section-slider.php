<?php
 $pandora_options = get_option('pandora_options');
 include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
?>

<?php if (isset($pandora_options["slider_check"]) && $pandora_options["slider_check"] == "on") { ?>
<div id="slider" class="appsblock whiteblock block">
    <div class="block-container">
    
        <?php if (!empty($pandora_options['slider_title'])) { ?><h2 class="title"><?php echo $pandora_options['slider_title']; ?> </h2> <?php } ?>
        <?php if (!empty($pandora_options['slider_subtitle'])) { ?><div class="description"><?php echo $pandora_options['slider_subtitle']; ?> </div> <?php } ?>

        <div class="apps">
            <?php if (is_plugin_active('pandora-slider/pandora-slider.php')) echo do_shortcode('[pb_slider]'); ?>
        </div>
    </div>
</div>
<?php } ?>


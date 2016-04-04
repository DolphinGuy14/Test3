<?php
/**
 * View that renders theme import page.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

$list = isset( $gateways ) && $gateways ? $gateways : array();

if ( ! isset( $results ) ) {
	$results = array();
}
?>

<style type="text/css">
.import-row{
	margin-top:20px;
}
.import-row__title{
	font-size:1.2em;
}
.import-row__description{
	padding:5px 0 0 25px;
}
.import-row__results{
	color:#137913;
}
.import-row__errors{
	color:#EE0000;
}
.import-notice{}
</style>

<div>
	<h2>Adventure Tours Demo Data Importer</h2>

	<div class="import-notice"><b>NOTE:</b> Please install "WordPress Importer" plugin (<a target="_blank" href="https://wordpress.org/plugins/wordpress-importer/">wordpress-importer</a>) to be able to import posts, pages, products and tours.</div>
	<form id="theme_import_form" action="<?php echo esc_url( $form_action ); ?>" method="POST">
		<?php foreach ($list as $option_key => $option) { ?>
			<?php
				if ( empty( $option['enabled'] ) ) {
					continue;
				}

				$is_available = !empty($option['available']);

				echo strtr('<div class="import-row">
					<div class="import-row__title">
						<input type="checkbox" name="do_import_type[{option_key}]" value="{option_key}" {checbox_state}/>
						{label}
					</div>
					<div class="import-row__description">{description}</div>
					<div class="import-row__errors">{errors}</div>
					<div class="import-row__results">{results}</div>
				</div>',array(
					'{option_key}' => $option_key,
					'{label}' => isset( $option['title'] ) ? $option['title'] : ucwords( str_replace('_', ' ', $option_key ) ),
					'{description}' => isset( $option['description'] ) ? $option['description'] : '',
					'{checbox_state}' => $is_available ? '' : 'disabled="disabled"',
					'{errors}' => !empty($option['errors']) ? join( '<br>', $option['errors'] ) : '',
					'{results}' => !empty($results[$option_key]) ? $results[$option_key] : '',
				) );
			?>
		<?php } ?>
		<div class="import-row">
			<input type="submit" value="<?php esc_attr_e( 'Start', 'adventure-tours' ); ?>" />
		</div>
	</form>
</div>

<script type="text/javascript">
jQuery(function($){
	var import_form = $('#theme_import_form'),
		checkboxes = import_form.find('input[type=checkbox]'),
		main_btn = import_form.find('input[type=submit]');

	checkboxes.on('change',function(){
			var has_checked = checkboxes.filter(':checked');
			if (has_checked.length) {
				main_btn.prop('disabled', false);
			} else {
				main_btn.prop('disabled', true);
			}
		})
		.first().trigger('change');

	return;
	import_form.submit(function(){
		var form = $(this),
			data = form.serialize();
		$.ajax({
			method:'POST',
			url: form.attr('action'),
			data: data,
			success:function(r){
				alert(r);
			}
		})
		return false;
	})
});
</script>
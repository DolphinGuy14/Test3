<?php
/**
 * View for rendering tour booking period settings.
 *
 * @var assoc $row
 * @var int   $rowIndex
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

if ( ! isset( $rowIndex ) ) {
	$rowIndex = 0;
}

$daysList = array(
	'Mon' => __( 'Monday', 'adventure-tours' ),
	'Tue' => __( 'Tuesday', 'adventure-tours' ),
	'Wed' => __( 'Wednesday', 'adventure-tours' ),
	'Thu' => __( 'Thursday', 'adventure-tours' ),
	'Fri' => __( 'Friday', 'adventure-tours' ),
	'Sat' => __( 'Saturday', 'adventure-tours' ),
	'Sun' => __( 'Sunday', 'adventure-tours' ),
);

$yesNoList = array(
	'0' => __( 'No', 'adventure-tours' ),
	'1' => __( 'Yes', 'adventure-tours' ),
);

$row_field_name = "tour-booking-row[{$rowIndex}]";
?>
<tr class="tour-booking-row">
	<td class="sort">&nbsp;</td>
	<td>
		<div class="tour-booking-row__date-wrapper">
			<span><?php esc_html_e( 'Start date', 'adventure-tours' ); ?></span>
			<input type="text" style="width:95%;border:1px solid #ddd;" class="dateselector" name="<?php echo esc_attr( $row_field_name ); ?>[from]" value="<?php if ( ! empty( $row['from'] ) ) { echo esc_attr( $row['from'] ); } ?>" />
		</div>
		<div class="tour-booking-row__date-wrapper">
			<span><?php esc_html_e( 'End date', 'adventure-tours' ); ?></span>
			<input type="text" style="width:95%;border:1px solid #ddd;" class="dateselector" name="<?php echo esc_attr( $row_field_name ); ?>[to]" value="<?php if ( ! empty( $row['to'] ) ) { echo esc_attr( $row['to'] ); } ?>" />
		</div>
		<div style="clear:both"></div>
		<div class="tour-booking-row__days">
		<?php
		$selectedDays = ! empty( $row['days'] ) ? $row['days'] : array();
		$dayColumns = array_chunk( $daysList, 4, true );
		foreach ( $dayColumns as $colDays ) {
			echo '<div class="tour-booking-row__days__column">';
			foreach ( $colDays as $val => $text ) {
				printf('<div class="tour-booking-row__days__item"><input type="checkbox" name="%s[days][]" value="%s"%s> %s</div>',
					esc_attr( $row_field_name ),
					esc_attr( $val ),
					$selectedDays && in_array( $val, $selectedDays ) ? ' checked="checked"' : '',
					esc_html( $text )
				);
			}
			echo '</div>';
		}
		?>
			<div style="clear:both"></div>
		</div>
	</td>
	<td>
		<div class="tour-booking-row__number-cnt">
			<div><?php esc_html_e( 'Number of tickets per tour', 'adventure-tours' ); ?></div>
			<input type="text" name="<?php echo esc_attr( $row_field_name ); ?>[limit]" value="<?php echo isset( $row['limit'] ) ? esc_attr( $row['limit'] ) : '1' ; ?>">
			<div style="clear:both"></div>
		</div>
		<div class="tour-booking-row__status-cnt">
			<div><?php esc_html_e( 'Is active?', 'adventure-tours' ); ?></div>
			<select name="<?php echo esc_attr( $row_field_name ); ?>[type]">
			<?php
			$typeValue = ! empty( $row['type'] ) ? $row['type'] : '1';
			foreach ( $yesNoList as $val => $text ) {
				printf('<option value="%s"%s>%s</option>',
					esc_attr( $val ),
					$typeValue == $val ? ' selected="selected"' : '',
					esc_html( $text )
				);
			}
			?>
			</select>
			<div style="clear:both"></div>
		</div>
		<div class="tour-booking-row__actions-cnt">
			<a href="#" data-role="remove-row" title="<?php esc_attr_e( 'remove', 'adventure-tours' ); ?>" class="tour-booking-row__remove-btn"></a>
		</div>
	</td>
</tr>

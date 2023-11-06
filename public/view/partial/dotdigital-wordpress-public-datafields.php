<?php
/**
 * Provide a public-facing view for data fields
 *
 * @package    Dotdigital_WordPress
 *
 * @var array $datafields
 * @var string $identifier
 */

?>

<?php
add_filter(
	'public/datafield/input/data_type',
	function ( $data_type ) {
		switch ( $data_type ) {
			case 'Date':
				return 'date';
			case 'String':
				return 'text';
			case 'Numeric':
				return 'number';
		}
		return $data_type;
	}
);
?>

<?php foreach ( $datafields as $datafield ) : ?>
	<div class="ddg-form-group">
		<?php
		$name = $datafield['name'];
		$label = $datafield['label'];
		$data_type = $datafield['type'];
		$is_required = is_string( $datafield['isRequired'] ) ? 'true' === $datafield['isRequired'] : $datafield['isRequired'];
		?>
		<?php $item_identifier = $identifier . '[' . $name . ']'; ?>

		<label for="<?php echo esc_attr( $item_identifier ); ?>[value]">
			<?php echo esc_html( $label ); ?>
			<?php
			if ( $is_required ) :
				?>
				*<?php endif; ?>
		</label>

		<input name="<?php echo esc_attr( $item_identifier ); ?>[type]" value="<?php echo esc_attr( apply_filters( 'public/datafield/' . $name . '/data_type/value', $data_type ) ); ?>" type="hidden"/>
		<input name="<?php echo esc_attr( $item_identifier ); ?>[required]" value="<?php echo esc_attr( apply_filters( 'public/datafield/' . $name . '/required/value', $is_required ) ); ?>" type="hidden"/>

		<?php if ( 'Boolean' === $data_type ) : ?>
			<div class="ddg-radio-group">
				<input class='radio' type='radio' id='yes' name='<?php echo esc_attr( $item_identifier ); ?>[value]' value='Yes' checked/>
				<label for='yes'>Yes</label><br>
				<input class='radio' type='radio' id='no' name='<?php echo esc_attr( $item_identifier ); ?>[value]' value='No'/>
				<label for='no'>No</label><br>
			</div>
		<?php else : ?>
			<input
				class="<?php echo esc_attr( apply_filters( 'public/datafield/' . $name . '/input/class', 'form-control datafield' ) ); ?>"
				type="<?php echo esc_attr( apply_filters( 'public/datafield/input/data_type', $data_type ) ); ?>"
				id="<?php echo esc_attr( $item_identifier ); ?>[value]"
				name="<?php echo esc_attr( $item_identifier ); ?>[value]"
				<?php echo esc_attr( apply_filters( 'public/datafield/' . $name . '/input/attributes', '' ) ); ?>
				<?php
				if ( $is_required ) :
					?>
					required<?php endif; ?>
			/>
		<?php endif; ?>
	</div>

<?php endforeach; ?>
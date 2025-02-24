<?php
/**
 * Lists tab view
 *
 * This file is used to display the lists tab
 *
 * @package    Dotdigital_WordPress
 *
 * @var $available_lists array
 * @var $lists array
 * @var Dotdigital_WordPress_Setting_Form $form
 * @var Dotdigital_WordPress\Admin\Page\Tab\Dotdigital_WordPress_Lists_Admin $view
 */

use Dotdigital_WordPress\Includes\Setting\Form\Dotdigital_WordPress_Setting_Form;

?>
<div class="wrap">

	<div class="card w-100 widefat">
		<div class="flex-column">
			<span><h2><?php echo esc_html( $form->get_title() ); ?></h2></span>
			<span><input type="text" id="filterInput" placeholder="Type to filter..."></span>
		</div>
		<form method="<?php echo esc_attr( $form->get_method() ); ?>" action="<?php echo esc_attr( $form->get_action() ); ?>" class="interactive-table-form">

			<?php settings_fields( $view->get_slug() ); ?>
			<table class="wp-list-table widefat fixed form-table">

				<thead>
				<tr>
					<th scope="col" class="manage-column column-cb check-column " style="">
						<input class="multiselector" type="checkbox">
					</th>
					<th scope="col" id="lists" class="manage-column column-lists desc" style="">
						<a href="?page=<?php echo esc_attr( $view->get_page_slug() ); ?>&tab=<?php echo esc_attr( $view->get_url_slug() ); ?>&order=<?php echo ( $view->get_sort_order() === 'desc' ) ? 'asc' : 'desc'; ?>">
							<span>List</span><span class="sorting-indicator"></span>
						</a>
					</th>
					<th scope="col" id="changelabel" class="manage-column column-changelabel" style="">Change label</th>
					<th scope="col" id="visible" class="manage-column column-visible" style="text-align: center;">Visible | Order</th>
				</tr>
				</thead>

				<tbody class="filter-list">
				<?php foreach ( $form->get_grouped_inputs() as $group_id => $inputs ) : ?>
					<tr id="<?php echo esc_attr( $group_id ); ?>" class="toggle-inputs" >
						<?php list($id_input, $label_input, $visible_input, $number_input) = $inputs; ?>
						<th scope="row"><?php $id_input->render(); ?></th>
						<td class="list-column"><?php echo esc_html( $label_input->get_label() ); ?></td>
						<td><?php $label_input->render(); ?></td>
						<td class="flex-column flex-left">
							<?php $visible_input->render(); ?>
							<?php $number_input->render(); ?>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>

			</table>
			<?php submit_button(); ?>

		</form>
	</div>

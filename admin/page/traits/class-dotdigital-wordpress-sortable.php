<?php
/**
 * Sortable trait.
 *
 * This file is used to sort data on admin pages .
 *
 * @package    Dotdigital_WordPress
 * @subpackage Dotdigital_WordPress\Admin\Page\Traits
 * @since      7.3.0
 */

namespace Dotdigital_WordPress\Admin\Page\Traits;

use Dotdigital_WordPress\Admin\Page\Dotdigital_WordPress_Page_Tab_Interface;

trait Sortable {

	/**
	 * @var string
	 */
	private $sort_order;

	/**
	 * Set the sort order.
	 *
	 * @return void
	 */
	private function set_sort_order() {
		$this->sort_order = isset( $_GET['order'] ) ? sanitize_text_field( wp_unslash( $_GET['order'] ) ) : 'asc';
	}

	/**
	 * Sorts the lists by the supplied order.
	 *
	 * @param array $lists
	 * @return mixed|null
	 */
	private function sort( $lists ) {

		$this->sort_list( $lists, $this->sort_order );
		if ( is_a( $this, Dotdigital_WordPress_Page_Tab_Interface::class ) ) {
			return apply_filters( "{$this->get_slug()}/lists/sort", $lists );
		}
	}

	/**
	 * Get the sort order.
	 *
	 * @return string
	 */
	public function get_sort_order() {
		return $this->sort_order;
	}

	/**
	 * @param array  $data
	 * @param string $sort_order
	 *
	 * @return void
	 */
	private function sort_list( array &$data, string $sort_order = 'asc' ) {
		if ( 'asc' == $sort_order ) {
			\uasort( $data, self::class . '::dotdigital_item_sort_asc' );
		} elseif ( 'desc' == $sort_order ) {
			\uasort( $data, self::class . '::dotdigital_item_sort_desc' );
		}
	}

	/**
	 * Sort ascending.
	 *
	 * @param object $a Object A.
	 * @param object $b Object B.
	 * @return int
	 */
	private static function dotdigital_item_sort_asc( object $a, object $b ) {
		if ( ! method_exists( $a, 'getName' ) || ! method_exists( $b, 'getName' ) ) {
			return 0;
		}

		$a_name = strtolower( $a->getName() );
		$b_name = strtolower( $b->getName() );

		if ( $a_name === $b_name ) {
			return 0;
		}

		return $a_name > $b_name ? 1 : -1;
	}

	/**
	 * Sort descending.
	 *
	 * @param object $a Object A.
	 * @param object $b Object B.
	 * @return int
	 */
	private static function dotdigital_item_sort_desc( object $a, object $b ) {
		if ( ! method_exists( $a, 'getName' ) || ! method_exists( $b, 'getName' ) ) {
			return 0;
		}

		$a_name = strtolower( $a->getName() );
		$b_name = strtolower( $b->getName() );

		if ( $a_name === $b_name ) {
			return 0;
		}

		return $a_name > $b_name ? -1 : 1;
	}
}

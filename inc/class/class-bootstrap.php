<?php
/**
 * Start the plugin
 */

declare(strict_types=1);

namespace J7\DokanCustomExtension;

use J7\DokanCustomExtension\Utils;

/**
 * Class Bootstrap
 */
final class Bootstrap {


	/**
	 * Constructor.
	 */
	public function __construct() {
		\add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );
		\add_action( 'dokan_order_detail_after_order_general_details', array( $this, 'add_paynow_metabox' ) );
		\add_action( 'paynow_shipping_order_created', array( $this, 'sync_sub_order_meta' ), 100, 1 );
	}

	/**
	 * Load scripts
	 */
	public function load_scripts() {
		\wp_enqueue_script(
			Utils::KEBAB,
			Utils::get_plugin_url() . '/js/dist/index.js',
			array( 'jquery' ),
			Utils::get_plugin_ver(),
			array(
				'strategy'  => 'async',
				'in_footer' => true,
			)
		);

		\wp_enqueue_style( Utils::KEBAB, Utils::get_plugin_url() . '/js/dist/assets/css/index.css', array(), Utils::get_plugin_ver(), 'all' );
	}

	/**
	 * Add pay now metabox
	 * 原本想說顯示母訂單的 paynow info 資訊
	 * 因為 2024/03/23 改版後，現在 會同步母訂單與子訂單的 paynow info 資訊
	 * 修改為，顯示子訂單的 paynow info 資訊
	 *
	 * @param \WC_Order $order The order object.
	 *
	 * @return void
	 */
	public function add_paynow_metabox( $order ): void {

		$order_id = $order->get_id();
		if ( ! $order_id ) {
			return;
		}

		$order = \wc_get_order( $order_id );

		if ( class_exists( '\PayNow_Shipping_Order_Meta_Box' ) && method_exists( '\PayNow_Shipping_Order_Meta_Box', 'output' ) ) {
			echo '<div class="dokan-panel dokan-panel-default">';
			echo '<div class="dokan-panel-heading"><strong>Paynow 物流資訊</strong></div>';
			\PayNow_Shipping_Order_Meta_Box::output( $order );
			echo '</div>';
		}
	}

	/**
	 * Sync sub order meta
	 *
	 * @param \WC_Order $order The sub order object.
	 * @param \WC_Order $parent_order The parent order object.
	 *
	 * @return void
	 */
	public function do_sync_sub_order_meta( $order, $parent_order ): void {
		$_paynow_shipping_logistic_service   = $parent_order->get_meta( '_paynow_shipping_logistic_service' );
		$_paynow_shipping_logistic_number    = $parent_order->get_meta( '_paynow_shipping_logistic_number' );
		$_paynow_shipping_paymentno          = $parent_order->get_meta( '_paynow_shipping_paymentno' );
		$_paynow_shipping_validation_no      = $parent_order->get_meta( '_paynow_shipping_validation_no' );
		$_paynow_shipping_return_msg         = $parent_order->get_meta( '_paynow_shipping_return_msg' );
		$_paynow_shipping_status             = $parent_order->get_meta( '_paynow_shipping_status' );
		$_paynow_shipping_logistic_sno       = $parent_order->get_meta( '_paynow_shipping_logistic_sno' );
		$_paynow_shipping_delivery_status    = $parent_order->get_meta( '_paynow_shipping_delivery_status' );
		$_paynow_shipping_logistic_code      = $parent_order->get_meta( '_paynow_shipping_logistic_code' );
		$_paynow_shipping_detail_status_desc = $parent_order->get_meta( '_paynow_shipping_detail_status_desc' );
		$_paynow_shipping_status_update_at   = $parent_order->get_meta( '_paynow_shipping_status_update_at' );

		$_shipping_paynow_storeaddress = $parent_order->get_meta( '_shipping_paynow_storeaddress' );
		$_shipping_paynow_storeid      = $parent_order->get_meta( '_shipping_paynow_storeid' );
		$_shipping_paynow_storename    = $parent_order->get_meta( '_shipping_paynow_storename' );
		$_shipping_phone               = $parent_order->get_meta( '_shipping_phone' );

		ob_start();
		print_r(
			array(
				'_paynow_shipping_logistic_service'   => $_paynow_shipping_logistic_service,
				'_paynow_shipping_logistic_number'    => $_paynow_shipping_logistic_number,
				'_paynow_shipping_paymentno'          => $_paynow_shipping_paymentno,
				'_paynow_shipping_validation_no'      => $_paynow_shipping_validation_no,
				'_paynow_shipping_return_msg'         => $_paynow_shipping_return_msg,
				'_paynow_shipping_status'             => $_paynow_shipping_status,
				'_paynow_shipping_logistic_sno'       => $_paynow_shipping_logistic_sno,
				'_paynow_shipping_delivery_status'    => $_paynow_shipping_delivery_status,
				'_paynow_shipping_logistic_code'      => $_paynow_shipping_logistic_code,
				'_paynow_shipping_detail_status_desc' => $_paynow_shipping_detail_status_desc,
				'_paynow_shipping_status_update_at'   => $_paynow_shipping_status_update_at,
				'_shipping_paynow_storeaddress'       => $_shipping_paynow_storeaddress,
				'_shipping_paynow_storeid'            => $_shipping_paynow_storeid,
				'_shipping_paynow_storename'          => $_shipping_paynow_storename,
				'_shipping_phone'                     => $_shipping_phone,
			)
		);
		\J7\WpToolkit\Utils::debug_log( '' . ob_get_clean() );

		$order->update_meta_data( '_paynow_shipping_logistic_service', $_paynow_shipping_logistic_service );
		$order->update_meta_data( '_paynow_shipping_logistic_number', $_paynow_shipping_logistic_number );
		$order->update_meta_data( '_paynow_shipping_paymentno', $_paynow_shipping_paymentno );
		$order->update_meta_data( '_paynow_shipping_validation_no', $_paynow_shipping_validation_no );
		$order->update_meta_data( '_paynow_shipping_return_msg', $_paynow_shipping_return_msg );
		$order->update_meta_data( '_paynow_shipping_status', $_paynow_shipping_status );
		$order->update_meta_data( '_paynow_shipping_logistic_sno', $_paynow_shipping_logistic_sno );
		$order->update_meta_data( '_paynow_shipping_delivery_status', $_paynow_shipping_delivery_status );
		$order->update_meta_data( '_paynow_shipping_logistic_code', $_paynow_shipping_logistic_code );
		$order->update_meta_data( '_paynow_shipping_detail_status_desc', $_paynow_shipping_detail_status_desc );
		$order->update_meta_data( '_paynow_shipping_status_update_at', $_paynow_shipping_status_update_at );

		$order->update_meta_data( '_shipping_paynow_storeaddress', $_shipping_paynow_storeaddress );
		$order->update_meta_data( '_shipping_paynow_storeid', $_shipping_paynow_storeid );
		$order->update_meta_data( '_shipping_paynow_storename', $_shipping_paynow_storename );
		$order->update_meta_data( '_shipping_phone', $_shipping_phone );

		$order->save();
	}

	/**
	 * Fires when order status is changed.
	 *
	 * @since 1.0.0
	 *
	 * @param WC_Order $parent_order $order Order object.
	 */
	public function sync_sub_order_meta( $parent_order ) {

		$parent_order_id = $parent_order->get_id();
		if ( ! $parent_order_id ) {
			return;
		}

		$sub_order_ids = \get_children(
			array(
				'post_parent' => $parent_order_id,
				'post_type'   => 'shop_order',
				'post_status' => array( 'wc-pending', 'wc-processing', 'wc-completed' ),
				'fields'      => 'ids',
			)
		);
		if ( empty( $sub_order_ids ) ) {
			return;
		}

		ob_start();
		print_r( $sub_order_ids );
		\J7\WpToolkit\Utils::debug_log( 'sub_order_ids ' . ob_get_clean() );

		foreach ( $sub_order_ids as $order_id ) {
			$order = \wc_get_order( $order_id );
			if ( ! $order ) {
				continue;
			}
			$this->do_sync_sub_order_meta( $order, $parent_order );
		}
	}
}

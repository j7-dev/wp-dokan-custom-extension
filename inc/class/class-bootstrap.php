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
	 */
	public function add_paynow_metabox( $order ) {

		$parent_order_id = \wp_get_post_parent_id( $order->get_id() );
		if ( ! $parent_order_id ) {
			return;
		}

		$order = \wc_get_order( $parent_order_id );

		if ( class_exists( '\PayNow_Shipping_Order_Meta_Box' ) && method_exists( '\PayNow_Shipping_Order_Meta_Box', 'output' ) ) {
			echo '<div class="dokan-panel dokan-panel-default">';
			echo '<div class="dokan-panel-heading"><strong>Paynow 物流資訊</strong></div>';
			\PayNow_Shipping_Order_Meta_Box::output( $order );
			echo '</div>';
		}
	}
}

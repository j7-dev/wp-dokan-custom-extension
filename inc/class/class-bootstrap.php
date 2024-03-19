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
		\add_action( 'wp_enqueue_scripts', [ $this, 'load_scripts' ] );
	}

	/**
	 * Load scripts
	 */
	public function load_scripts() {
		\wp_enqueue_script(
			Utils::KEBAB,
			Utils::get_plugin_url() . '/js/dist/index.js',
			[ 'jquery' ],
			Utils::get_plugin_ver(),
			[
				'strategy'  => 'async',
				'in_footer' => true,
			]
		);

		\wp_enqueue_style( Utils::KEBAB, Utils::get_plugin_url() . '/js/dist/index.css', [], Utils::get_plugin_ver(), 'all' );
	}
}

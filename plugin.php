<?php

/**
 * Plugin Name:       My Plugin | 我的 WordPress 外掛
 * Plugin URI:        https://cloud.luke.cafe/plugins/
 * Description:       這是一個 WordPress 外掛的範本，可以用來開發新的外掛。
 * Version:           0.0.2
 * Requires at least: 5.7
 * Requires PHP:      7.4
 * Author:            J7
 * Author URI:        https://github.com/j7-dev
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       power-partner-server
 * Domain Path:       /languages
 * Tags:
 */

declare(strict_types=1);

namespace J7\MyPlugin;

use J7\MyPlugin\Utils;
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

class Plugin
{
	private static $instance;
	public static $is_all_plugins_activated = false;
	const WP_TOOLKIT_CLASS                  = 'J7\WpToolkit\Plugin';

	public function __construct()
	{
		require_once __DIR__ . '/required_plugins/index.php';
		require_once __DIR__ . '/vendor/autoload.php';
		require_once __DIR__ . '/utils/index.php';
		require_once __DIR__ . '/class/index.php';

		\register_activation_hook(__FILE__, [$this, 'activate']);
		\register_deactivation_hook(__FILE__, [$this, 'deactivate']);
		\add_action('tgmpa_register', [$this, 'register_required_plugins']);
		\add_action('plugins_loaded', [$this, 'check_required_plugins']);

		$this->plugin_update_checker();
	}

	public function check_required_plugins()
	{
		self::$is_all_plugins_activated = \class_exists(self::WP_TOOLKIT_CLASS);

		if (self::$is_all_plugins_activated) {
			new Bootstrap();
		} else {
			$instance = \TGM_Plugin_Activation::get_instance();
			$instance->has_notices = false;
		}
	}

	public static function instance()
	{
		if (empty(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * wp plugin 更新檢查 update checker
	 */
	public function plugin_update_checker(): void
	{
		$updateChecker = PucFactory::buildUpdateChecker(
			Utils::GITHUB_REPO,
			__FILE__,
			Utils::KEBAB
		);
		$updateChecker->setBranch('master');
		// $updateChecker->setAuthentication(Utils::GITHUB_PAT);
		$updateChecker->getVcsApi()->enableReleaseAssets();
	}

	public function register_required_plugins(): void
	{
		$plugins = [
			// [
			// 	'name'     => 'WooCommerce',
			// 	'slug'     => 'woocommerce',
			// 	'required' => true,
			// 	'version'  => '7.6.1',
			// ],
			[
				'name'     => 'WP Toolkit',
				'slug'     => 'wp-toolkit',
				'source'   => 'https://github.com/j7-dev/wp-toolkit/releases/latest/download/wp-toolkit.zip',
				'required' => true,
			],
		];

		$config = array(
			'id'           => Utils::KEBAB, // Unique ID for hashing notices for multiple instances of TGMPA.
			'default_path' => '', // Default absolute path to bundled plugins.
			'menu'         => 'tgmpa-install-plugins', // Menu slug.
			'parent_slug'  => 'plugins.php', // Parent menu slug.
			'capability'   => 'manage_options', // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
			'has_notices'  => true, // Show admin notices or not.
			'dismissable'  => false, // If false, a user cannot dismiss the nag message.
			'dismiss_msg'  => __('這個訊息將在依賴套件被安裝並啟用後消失。' . Utils::APP_NAME . ' 沒有這些依賴套件的情況下將無法運作！', Utils::SNAKE), // If 'dismissable' is false, this message will be output at top of nag.
			'is_automatic' => true, // Automatically activate plugins after installation or not.
			'message'      => '', // Message to output right before the plugins table.
			'strings'      => array(
				'page_title'                      => __('安裝依賴套件', Utils::SNAKE),
				'menu_title'                      => __('安裝依賴套件', Utils::SNAKE),
				'installing'                      => __('安裝套件: %s', Utils::SNAKE), // translators: %s: plugin name.
				'updating'                        => __('更新套件: %s', Utils::SNAKE), //translators: %s: plugin name.
				'oops'                            => __('OOPS! plugin API 出錯了', Utils::SNAKE),
				'notice_can_install_required'     => _n_noop(
					//translators: 1: plugin name(s).
					Utils::APP_NAME . ' 依賴套件: %1$s.',
					Utils::APP_NAME . ' 依賴套件: %1$s.',
					Utils::SNAKE
				),
				'notice_can_install_recommended'  => _n_noop(
					// translators: 1: plugin name(s).
					Utils::APP_NAME . ' 推薦套件: %1$s.',
					Utils::APP_NAME . ' 推薦套件: %1$s.',
					Utils::SNAKE
				),
				'notice_ask_to_update'            => _n_noop(
					// translators: 1: plugin name(s).
					'以下套件需要更新到最新版本來兼容 ' . Utils::APP_NAME . ': %1$s.',
					'以下套件需要更新到最新版本來兼容 ' . Utils::APP_NAME . ': %1$s.',
					Utils::SNAKE
				),
				'notice_ask_to_update_maybe'      => _n_noop(
					// translators: 1: plugin name(s).
					'以下套件有更新: %1$s.',
					'以下套件有更新: %1$s.',
					Utils::SNAKE
				),
				'notice_can_activate_required'    => _n_noop(
					// translators: 1: plugin name(s).
					'以下依賴套件目前為停用狀態: %1$s.',
					'以下依賴套件目前為停用狀態: %1$s.',
					Utils::SNAKE
				),
				'notice_can_activate_recommended' => _n_noop(
					// translators: 1: plugin name(s).
					'以下推薦套件目前為停用狀態: %1$s.',
					'以下推薦套件目前為停用狀態: %1$s.',
					Utils::SNAKE
				),
				'install_link'                    => _n_noop(
					'安裝套件',
					'安裝套件',
					Utils::SNAKE
				),
				'update_link'                     => _n_noop(
					'更新套件',
					'更新套件',
					Utils::SNAKE
				),
				'activate_link'                   => _n_noop(
					'啟用套件',
					'啟用套件',
					Utils::SNAKE
				),
				'return'                          => __('回到安裝依賴套件', Utils::SNAKE),
				'plugin_activated'                => __('套件啟用成功', Utils::SNAKE),
				'activated_successfully'          => __('以下套件已成功啟用:', Utils::SNAKE),
				// translators: 1: plugin name.
				'plugin_already_active'           => __('沒有執行任何動作 %1$s 已啟用', Utils::SNAKE),
				// translators: 1: plugin name.
				'plugin_needs_higher_version'     => __(Utils::APP_NAME . ' 未啟用。' . Utils::APP_NAME . ' 需要新版本的 %s 。請更新套件。', Utils::SNAKE),
				// translators: 1: dashboard link.
				'complete'                        => __('所有套件已成功安裝跟啟用 %1$s', Utils::SNAKE),
				'dismiss'                         => __('關閉通知', Utils::SNAKE),
				'notice_cannot_install_activate'  => __('有一個或以上的依賴/推薦套件需要安裝/更新/啟用', Utils::SNAKE),
				'contact_admin'                   => __('請聯繫網站管理員', Utils::SNAKE),

				'nag_type'                        => 'error', // Determines admin notice type - can only be one of the typical WP notice classes, such as 'updated', 'update-nag', 'notice-warning', 'notice-info' or 'error'. Some of which may not work as expected in older WP versions.
			),
		);

		\tgmpa($plugins, $config);
	}

	public function activate(): void
	{
	}

	public function deactivate(): void
	{
		// 刪除會員等級 post type 或是 transient
	}
}

Plugin::instance();
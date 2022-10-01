<?php
/**
 * Plugin Name: WooCommerce Blocks Incompatibility
 * Description: Shows a notice if a plugin gets activated that is incompatible with WooCommerce Blocks.
 * Version: 1.0.0
 * Author: Niels Lange
 * Author URI: https://nielslange.com/
 * Text Domain: woocommerce-blocks-incompatibility
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package WooCommerce\Blocks
 */

defined( 'ABSPATH' ) || exit;

/**
 * WooCommerce Blocks Incompatibility plugin class
 *
 * @class WC_Blocks_Incompatibility
 */
class WC_Blocks_Incompatibility {

	/**
	 * Initialise the plugin.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'admin_notices', array( __CLASS__, 'display_incompatibility_notices' ) );
	}

	/**
	 * Check if WooCommerce Blocks is active.
	 *
	 * @return bool Return true if WooCommerce Blocks is active, false otherwise.
	 */
	private static function is_woocommerce_blocks_active() {
		return class_exists( 'Automattic\WooCommerce\Blocks\Package' );
	}

	/**
	 * Get incompatible extensions.
	 *
	 * @return array Incompatible extensions, if available, empty array otherwise.
	 */
	private static function get_incompatible_extensions():array {
		$extension_file = __DIR__ . '/extensions.json';
		if ( ! is_readable( $extension_file ) ) {
			return array();
		}
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$data = json_decode( file_get_contents( $extension_file ), true );

		// Return empty array if the file is empty or invalid.
		if ( null === $data || ( ! array_key_exists( 'breaking', $data ) ) && ! array_key_exists( 'non-breaking', $data ) ) {
			return array();
		}

		// Return the list of incompatible extensions.
		return $data;
	}

	/**
	 * Get active extensions.
	 *
	 * @return array Active extensions, if available, empty array otherwise.
	 */
	private static function get_active_extensions():array {
		$all_plugins    = get_plugins();
		$active_plugins = get_option( 'active_plugins', array() );
		$result         = array();

		foreach ( $all_plugins as $key => $value ) {
			if ( in_array( $key, $active_plugins, true ) ) {
				$result[ $key ] = $value;
			}
		}

		return $result;
	}

	/**
	 * Check if incompatibility notices should be displayed.
	 *
	 * @return bool True, if incompatibility notices should be displayed, false otherwise.
	 */
	private static function should_display_incompatibility_notices():bool {
		$current_screen = get_current_screen();

		if ( ! isset( $current_screen ) ) {
			return false;
		}

		$is_plugins_page =
		property_exists( $current_screen, 'id' ) &&
		'plugins' === $current_screen->id;

		return $is_plugins_page;
	}

	/**
	 * Display incompatibility.
	 *
	 * @return void
	 */
	public static function display_incompatibility_notices():void {
		// Return early if WooCommerce Blocks is not active.
		if ( ! self::is_woocommerce_blocks_active() ) {
			return;
		}

		// Return early if we shouldn't display notices.
		if ( ! self::should_display_incompatibility_notices() ) {
			return;
		}

		$active_extensions        = self::get_active_extensions();
		$incompatible_extensions  = self::get_incompatible_extensions();
		$breaking_extensions      = '';
		$non_breaking_extensions  = '';
		$show_breaking_notice     = false;
		$show_non_breaking_notice = false;

		// Return early if there are no active or incompatible extensions.
		if ( empty( $active_extensions ) || empty( $incompatible_extensions ) ) {
			return;
		}

		foreach ( $active_extensions as $value ) {
			if ( in_array( $value['TextDomain'], $incompatible_extensions['breaking'], true ) ) {
				$show_breaking_notice = true;
				$breaking_extensions .= sprintf(
					'<details>
					<summary><strong>%1$s</strong></summary>
					<p>%2$s</p>
				</details>',
					$value['Name'],
					sprintf(
					/* translators: %1$s is the plugin URL, %2$s is the plugin name. */
						__( '<a href="%1$s">%1$s</a>', 'woocommerce-blocks-incompatibility' ),
						$value['PluginURI'],
						$value['Name']
					)
				);
			}
			if ( in_array( $value['TextDomain'], $incompatible_extensions['non-breaking'], true ) ) {
				$show_non_breaking_notice = true;
				$non_breaking_extensions .= sprintf(
					'<details>
					<summary><strong>%1$s</strong></summary>
					<p>%2$s</p>
				</details>',
					$value['Name'],
					sprintf(
					/* translators: %1$s is the plugin URL, %2$s is the plugin name. */
						__( '<a href="%1$s">%1$s</a>', 'woocommerce-blocks-incompatibility' ),
						$value['PluginURI'],
						$value['Name']
					)
				);
			}
		}

		if ( $show_breaking_notice ) {
			$breaking_notice = sprintf(
				'<div class="notice notice-error">
                <p>%1$s</p>
                %2$s
                <p>
                    <a href="https://woocommerce.com/document/cart-checkout-blocks-support-status/#section-3" class="button">%3$s</a>
                    <a href="https://woocommerce.com/document/cart-checkout-blocks-support-status/#section-7" class="button">%4$s</a>
                </p>
            </div>',
				__( 'WooCommerce Blocks compatibility issues discovered. The following plugin(s) might break your store:', 'woocommerce-blocks-incompatibility' ),
				$breaking_extensions,
				__( 'View Compatible Extensions', 'woocommerce-blocks-incompatibility' ),
				__( 'View Incompatible Extensions', 'woocommerce-blocks-incompatibility' ),
			);

			echo wp_kses( $breaking_notice, 'post' );
		}

		if ( $non_breaking_extensions ) {
			$non_breaking_notice = sprintf(
				'<div class="notice notice-warning">
                <p>%1$s</p>
                %2$s
                <p>
                    <a href="https://woocommerce.com/document/cart-checkout-blocks-support-status/#section-3" class="button">%3$s</a>
                    <a href="https://woocommerce.com/document/cart-checkout-blocks-support-status/#section-7" class="button">%4$s</a>
                </p>
            </div>',
				__( 'WooCommerce Blocks compatibility issues discovered. The following plugin(s) might not work as expected:', 'woocommerce-blocks-incompatibility' ),
				$non_breaking_extensions,
				__( 'View Compatible Extensions', 'woocommerce-blocks-incompatibility' ),
				__( 'View Incompatible Extensions', 'woocommerce-blocks-incompatibility' ),
			);

			echo wp_kses( $non_breaking_notice, 'post' );
		}
	}

}

WC_Blocks_Incompatibility::init();

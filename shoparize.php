<?php

/**
 * Plugin Name: Shoparize
 * Description: Shoparize partner tracking
 * Plugin URI: https://www.shoparize.com
 * Version: 0.0.1
 * Author: Shoparize
 * Author URI: https://www.shoparize.com
 */

// Test to see if WooCommerce is active (including network activated).
$plugin_path = trailingslashit( WP_PLUGIN_DIR ) . 'woocommerce/woocommerce.php';

if (
    in_array( $plugin_path, wp_get_active_and_valid_plugins() )
    || in_array( $plugin_path, wp_get_active_network_plugins() )
) {
    // Custom code here. WooCommerce is active, however it has not
    // necessarily initialized (when that is important, consider
    // using the `woocommerce_init` action).

    add_action('wp_enqueue_scripts', 'shoparize_public_scripts');

    function shoparize_public_scripts()
    {
 	    wp_enqueue_script( 'shoparize_script', plugin_dir_url( __FILE__ ) . 'shoparize.js' );
    }

    function after_purchase_action($order_id)
    {
        echo '<script>' . PHP_EOL;

        echo 'console.log("oldu mu alper")';

        echo '</script>' . PHP_EOL;
        echo "<script>console.log('Order ID: " . $order_id . "');</script>";
        wp_register_script( 'myprefix-dummy-js-header', '',);
        wp_enqueue_script( 'myprefix-dummy-js-header' );
        wp_add_inline_script( 'myprefix-dummy-js-header', "console.log('loaded in header');");
    }

    add_action('woocommerce_payment_complete', 'after_purchase_action', 10, 1 );
}

?>

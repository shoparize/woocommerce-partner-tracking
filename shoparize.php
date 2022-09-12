<?php

/**
 * Plugin Name: Shoparize Partner
 * Description: Shoparize partner
 * Version: 1.0.1
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
        $options = get_option( 'shoparize_partner_tracking' );
 	    wp_enqueue_script( 'shoparize_script', 'https://partner-cdn.shoparize.com/js/shoparize.js' );
        wp_register_script( 'myprefix-dummy-js-header', '',);
        wp_enqueue_script( 'myprefix-dummy-js-header' );
        wp_add_inline_script( 'myprefix-dummy-js-header', '
        window.addEventListener("load", function () {
            SHOPARIZE_API().init(' . $options['shop_id'] . ');
        });');
    }

    function shoparize_after_purchase_action($order_id)
    {
        $options = get_option( 'shoparize_partner_tracking' );
        $order = wc_get_order($order_id);
        $order_data = $order->get_data();
        $order_items = $order_data['line_items'];
        $custom_order = [
            'event' => 'purchase',
            'ecommerce' => [
                'transaction_id' => $order_data['id'],
                'value' => $order_data['total'],
                'tax' => $order_data['total_tax'],
                'shipping' => $order_data['shipping_total'],
                'currency' => $order_data['currency']
            ]
        ];
        foreach($order_items as $item) {
            $custom_order['ecommerce']['items'][] = [
                'item_id' => $item['product_id'],
                'item_name' => $item['name'],
                'currency' => $order_data['currency'],
                'price' => $item['subtotal'],
                'quantity' => $item['quantity']
            ];
        }
        echo "<script>";
            echo  "var dataLayerShoparize = [" . esc_attr(json_encode($custom_order)) . "];";
            echo  "window.addEventListener('load', function () { SHOPARIZE_API().conv(" . esc_attr($options['shop_id']) . "); });";
        echo  "</script>";


        wp_register_script( 'myprefix-dummy-js-footer', '',);
        wp_enqueue_script( 'myprefix-dummy-js-footer' );
        wp_add_inline_script( 'myprefix-dummy-js-footer', '');
    }

    add_action('woocommerce_thankyou', 'shoparize_after_purchase_action', 10, 1 );
}

require_once(plugin_dir_path(__FILE__) . 'shoparize-admin.php');
?>

<?php

require_once(plugin_dir_path(__FILE__) . 'vendor/autoload.php');

use Shoparize\PartnerPluginProductApi\Responses\FeedResponse;
use Shoparize\PartnerPluginProductApi\Responses\FeedItem;
use Shoparize\PartnerPluginProductApi\Responses\FeedShipping;

const AVAILABILITY_IN_STOCK = 'in_stock';

const AVAILABILITY_OUT_OF_STOCK = 'out_of_stock';

function shoparize_partner_api_get_products(\WP_REST_Request $request)
{
    $page = $request->get_param('page');
    $limit = $request->get_param('limit');
    $updated_after = $request->get_param('updated_after');

    $args = [
        'status' => ['publish'],
        'limit' => $limit > 0 ? $limit : -1,
        'page' => $page > 0 ? $page : 0,
        'date_modified' => $updated_after ? '>=' . $updated_after : null,
        'orderby' => 'ID',
        'order' => 'ASC',
    ];

    // Perform Query
    $query = new WC_Product_Query($args);

    $response = new FeedResponse();

    // Collect Product Object
    /**
     * @var WC_Product_Simple[] $products
     */
    $products = $query->get_products();

    foreach ($products as $product) {
        $feed_item = new FeedItem();
        $feed_item->setId($product->get_id());
        $feed_item->setTitle($product->get_title());
        $feed_item->setLink($product->get_permalink());
        $feed_item->setMobileLink($product->get_permalink());
        $feed_item->setImage(wp_get_attachment_url($product->get_image_id()));
        foreach ($product->get_gallery_image_ids() as $id) {
            $feed_item->setImage(wp_get_attachment_url($id));
        }

        $feed_item->setAvailability(
            $product->get_stock_status() == 'instock'
                ? AVAILABILITY_IN_STOCK
                : AVAILABILITY_OUT_OF_STOCK
        );
        $feed_item->setPrice($product->get_regular_price());
        $feed_item->setSalePrice($product->get_price());
        $feed_item->setDescription(strip_tags($product->get_description()));
        $feed_item->setBrand($product->get_meta('shoparize_partner_brand'));
        $feed_item->setGtin($product->get_meta('shoparize_partner_brand'));
        $feed_item->setCondition($product->get_meta('shoparize_partner_brand'));
        $feed_item->setCurrencyCode(get_woocommerce_currency());
        $feed_item->setShippingHeight($product->get_height());
        $feed_item->setShippingWeight($product->get_weight());
        $feed_item->setShippingWidth($product->get_width());
        $feed_item->setShippingLength($product->get_length());

        $shipping_methods = shoparize_partner_shipping($product, get_option('woocommerce_default_country'));
        $feed_shipping = new FeedShipping();
        if (!empty($shipping_methods)) {
            $feed_shipping->setCountry(get_option('woocommerce_default_country'));
            $feed_shipping->setPrice($shipping_methods[0]['price']);
            $feed_shipping->setService($shipping_methods[0]['name']);
        }
        $feed_item->setShipping($feed_shipping);
        $feed_item->setSizeUnit(get_option('woocommerce_dimension_unit'));
        $feed_item->setWeightUnit(get_option('woocommerce_weight_unit'));

        $sizes = [];
        $options = get_option('shoparize_partner_tracking');
        $values = wc_get_product_terms($product->get_id(), $options['size_attr_taxonomy_name'], array('fields' => 'all')
        );
        foreach ($values as $value) {
            $sizes[] = $value->name;
        }
        $feed_item->setSizes($sizes);

        $colors = [];
        $options = get_option('shoparize_partner_tracking');
        $values = wc_get_product_terms(
            $product->get_id(),
            $options['color_attr_taxonomy_name'],
            array('fields' => 'all')
        );
        foreach ($values as $value) {
            $colors[] = $value->name;
        }
        $feed_item->setColors($colors);

        $response->setItem($feed_item);
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

add_action('rest_api_init', function () {
    register_rest_route('shoparize-partner', '/products', array(
        'methods' => 'GET',
        'callback' => 'shoparize_partner_api_get_products',
        'permission_callback' => 'shoparize_partner_api_is_allow',
    ));
});

function shoparize_partner_api_is_allow(): bool
{
    $options = get_option('shoparize_partner_tracking');
    $option_shop_id = $options['shop_id'] ?? '';
    $header = strtoupper(str_replace('-', '_', 'Shoparize-Partner-Key'));
    $shopId = $_SERVER['HTTP_' . $header] ?? null;
    if ($shopId != $option_shop_id) {
        return false;
    }

    return true;
}

function shoparize_partner_shipping($product, $country)
{
    global $woocommerce;

    $active_methods = [];
    $values = [
        'country' => $country,
        'amount' => $product->get_price(),
    ];

    $woocommerce->cart->add_to_cart($product->get_id());

    WC()->shipping->calculate_shipping(shoparize_partner_get_shipping_packages($values));
    $shipping_methods = WC()->shipping->packages;

    if (!empty($shipping_methods)) {
        foreach ($shipping_methods[0]['rates'] as $shipping_method) {
            $active_methods[] = array(
                'id' => $shipping_method->method_id,
                'type' => $shipping_method->method_id,
                'provider' => $shipping_method->method_id,
                'name' => $shipping_method->label,
                'price' => number_format($shipping_method->cost, 2, '.', ''),
            );
        }
    }

    return $active_methods;
}

function shoparize_partner_get_shipping_packages($value)
{
    // Packages array for storing 'carts'
    $packages = [];
    $packages[0]['contents'] = WC()->cart->cart_contents;
    $packages[0]['contents_cost'] = $value['amount'];
    $packages[0]['applied_coupons'] = '';
    $packages[0]['destination']['country'] = $value['country'];
    $packages[0]['destination']['state'] = '';
    $packages[0]['destination']['postcode'] = '';
    $packages[0]['destination']['city'] = '';
    $packages[0]['destination']['address'] = '';
    $packages[0]['destination']['address_2'] = '';


    return apply_filters('woocommerce_cart_shipping_packages', $packages);
}
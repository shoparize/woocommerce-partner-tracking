<?php

function shoparize_partner_settings_init()
{
    register_setting('shoparize-partner', 'shoparize_partner_tracking');


    add_settings_section(
        'shoparize_partner_dev',
        __('Tracking Settings', 'shoparize_partner'),
        'shoparize_partner_dev_callback',
        'shoparize-partner'
    );

    add_settings_field(
        'wporg_field_pill',
        __('Shop ID', 'shoparize_partner'),
        'shoparize_partner_cb',
        'shoparize-partner',
        'shoparize_partner_dev',
        array(
            'label_for' => 'shop_id',
            'class' => 'shoparize_partner_row',
        )
    );

    add_settings_field(
        'wporg_field_color_attr',
        __('Color attribute', 'shoparize_partner'),
        'shoparize_partner_attributes',
        'shoparize-partner',
        'shoparize_partner_dev',
        array(
            'label_for' => 'color_attr_taxonomy_name',
            'class' => 'shoparize_partner_row',
        )
    );
    add_settings_field(
        'wporg_field_size_attr',
        __('Size attribute', 'shoparize_partner'),
        'shoparize_partner_attributes',
        'shoparize-partner',
        'shoparize_partner_dev',
        array(
            'label_for' => 'size_attr_taxonomy_name',
            'class' => 'shoparize_partner_row',
        )
    );
}

add_action('admin_init', 'shoparize_partner_settings_init');

function shoparize_partner_dev_callback($args)
{
    ?>
    <p id="<?php
    echo esc_attr($args['id']); ?>"><?php
        esc_html_e(
            'By entering your shop ID and click Save Changes, your WooCommerce store will be automatically set up for Shoparize Partner.

',
            'shoparize_partner'
        ); ?></p>
    <p>
        <?php
        esc_html_e(
            "If you don't have a shop ID please sign up to the Shoparize network first to receive your shop ID, via ",
            'shoparize_partner'
        ); ?><a href="https://partner.shoparize.com">https://partner.shoparize.com</a>
    </p>
    <?php
}

function shoparize_partner_cb($args)
{
    $options = get_option('shoparize_partner_tracking');
    $shop_id = $options[$args['label_for']] ?? '';
    ?>
    <input
            id="<?php
            echo esc_attr($args['label_for']); ?>"
            name="shoparize_partner_tracking[<?php
            echo esc_attr($args['label_for']); ?>]"
            value="<?php
            echo esc_attr($shop_id); ?>"
    />
    <?php
}

function shoparize_partner_attributes($args)
{
    $options = get_option('shoparize_partner_tracking');
    $selected_attr = $options[$args['label_for']] ?? '';
    $attribute_taxonomies = wc_get_attribute_taxonomies();

    ?>
    <select name="shoparize_partner_tracking[<?php
    echo esc_attr($args['label_for']); ?>]" value="<?php
    echo esc_attr($selected_attr); ?>">
        <?php
        foreach ($attribute_taxonomies as $taxonomy) : ?>
            <?php
            $taxonomy_name = wc_attribute_taxonomy_name($taxonomy->attribute_name); ?>
            <option
                <?php
                if ($selected_attr == $taxonomy_name) {
                    echo 'selected';
                } ?>
                    value="<?php
                    echo $taxonomy_name; ?>">
                <?php
                echo $taxonomy->attribute_name; ?>
            </option>
        <?php
        endforeach; ?>
    </select>
    <?php
}

function shoparize_partner_options_page()
{
    add_menu_page(
        'Shoparize Partner',
        'Shoparize Partner',
        'manage_options',
        'shoparize-partner',
        'shoparize_partner_options_page_html'
    );
}

add_action('admin_menu', 'shoparize_partner_options_page');

function shoparize_partner_options_page_html()
{
    if (!current_user_can('manage_options')) {
        return;
    }

    if (isset($_GET['settings-updated'])) {
        add_settings_error(
            'shoparize_partner_messages',
            'shoparize_partner_message',
            __('Settings Saved', 'shoparize_partner'),
            'updated'
        );
    }

    settings_errors('shoparize_partner_messages');
    ?>
    <div class="wrap">
        <h1><?php
            echo esc_html(get_admin_page_title()); ?></h1>
        <form action="options.php" method="post">
            <?php
            settings_fields('shoparize-partner');
            do_settings_sections('shoparize-partner');
            submit_button('Save Settings');
            ?>
        </form>
    </div>
    <?php
}

add_filter('woocommerce_product_data_tabs', 'shoparize_partner_product_data_tab', 99, 1);
function shoparize_partner_product_data_tab($product_data_tabs)
{
    $product_data_tabs['shoparize_partner'] = array(
        'label' => __('Shoparize Partner', 'shoparize_partner'),
        'target' => 'shoparize_partner_product_data',
    );
    return $product_data_tabs;
}

add_action('woocommerce_product_data_panels', 'shoparize_partner_product_data_fields');
function shoparize_partner_product_data_fields()
{
    global $post;

    $post_id = $post->ID;

    echo '<div id="shoparize_partner_product_data" class="panel woocommerce_options_panel">';

    woocommerce_wp_text_input(array(
        'id' => 'shoparize_partner_brand',
        'label' => __('Brand', 'shoparize_partner'),
        'description' => __('Product brand name', 'shoparize_partner'),
        'desc_tip' => true,
    ));

    woocommerce_wp_text_input(array(
        'id' => 'shoparize_partner_gtin',
        'label' => __('Gtin', 'shoparize_partner'),
        'description' => __('Product gtin number', 'shoparize_partner'),
        'desc_tip' => true,
    ));

    woocommerce_wp_select(array(
        'id' => 'shoparize_partner_condition',
        'label' => __('Product condition', 'shoparize_partner'),
        'description' => __('Product condition', 'shoparize_partner'),
        'desc_tip' => true,
        'options' => array(
            '' => __('Chose an option', 'shoparize_partner'),
            'new' => __('New', 'shoparize_partner'),
            'refurbished' => __('Refurbished', 'shoparize_partner'),
            'used' => __('Used', 'shoparize_partner')
        ),
    ));

    echo '</div>';
}

add_action('woocommerce_process_product_meta', 'shoparize_partner_process_product_meta_fields_save');
function shoparize_partner_process_product_meta_fields_save($post_id)
{
    if (isset($_POST['shoparize_partner_brand'])) {
        update_post_meta($post_id, 'shoparize_partner_brand', esc_attr($_POST['shoparize_partner_brand']));
    }
    if (isset($_POST['shoparize_partner_gtin'])) {
        update_post_meta($post_id, 'shoparize_partner_gtin', esc_attr($_POST['shoparize_partner_gtin']));
    }
    if (isset($_POST['shoparize_partner_condition'])) {
        update_post_meta($post_id, 'shoparize_partner_condition', esc_attr($_POST['shoparize_partner_condition']));
    }
}

function shoparize_partner_create_attribute_taxonomies()
{
    $attributes = wc_get_attribute_taxonomies();

    $slugs = wp_list_pluck($attributes, 'attribute_name');

    if (!in_array('color', $slugs)) {
        $args = array(
            'slug' => 'color',
            'name' => __('Color', 'shoparize_partner'),
            'type' => 'select',
            'orderby' => 'menu_order',
            'has_archives' => false,
        );

        $result = wc_create_attribute($args);
    }
    if (!in_array('size', $slugs)) {
        $args = array(
            'slug' => 'size',
            'name' => __('Size', 'shoparize_partner'),
            'type' => 'select',
            'orderby' => 'menu_order',
            'has_archives' => false,
        );

        $result = wc_create_attribute($args);
    }
}

add_action('admin_init', 'shoparize_partner_create_attribute_taxonomies');
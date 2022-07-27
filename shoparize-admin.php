<?php

function shoparize_partner_settings_init() {

    register_setting( 'shoparize-partner', 'shoparize_partner_tracking' );


    add_settings_section(
        'shoparize_partner_dev',
        __( 'Shop Settings', 'shoparize_partner' ), 'shoparize_partner_dev_callback',
        'shoparize-partner'
    );

    add_settings_field(
        'wporg_field_pill',
        __( 'Shop ID', 'shoparize_partner' ),
        'shoparize_partner_cb',
        'shoparize-partner',
        'shoparize_partner_dev',
        array(
            'label_for'         => 'shop_id',
            'class'             => 'shoparize_partner_row',
        )
    );
}

add_action( 'admin_init', 'shoparize_partner_settings_init' );

function shoparize_partner_dev_callback( $args ) {
    ?>
    <p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Please enter your shop id.', 'shoparize_partner' ); ?></p>
    <?php
}

function shoparize_partner_cb( $args ) {
    $options = get_option( 'shoparize_partner_tracking' );
    ?>
    <input
            id="<?php echo esc_attr( $args['label_for'] ); ?>"
            name="shoparize_partner_tracking[<?php echo esc_attr( $args['label_for'] ); ?>]"
            value="<?php echo $options[ $args['label_for'] ]; ?>"
            />
    <?php
}

function shoparize_partner_options_page() {
    add_menu_page(
        'Shoparize Partner',
        'Shoparize Partner',
        'manage_options',
        'shoparize-partner',
        'shoparize_partner_options_page_html'
    );
}

add_action( 'admin_menu', 'shoparize_partner_options_page' );

function shoparize_partner_options_page_html() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    if ( isset( $_GET['settings-updated'] ) ) {
        add_settings_error( 'shoparize_partner_messages', 'shoparize_partner_message', __( 'Settings Saved', 'shoparize_partner' ), 'updated' );
    }

    settings_errors( 'shoparize_partner_messages' );
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <form action="options.php" method="post">
            <?php
            settings_fields( 'shoparize-partner' );
            do_settings_sections( 'shoparize-partner' );
            submit_button( 'Save Settings' );
            ?>
        </form>
    </div>
    <?php
}

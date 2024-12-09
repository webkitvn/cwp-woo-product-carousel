<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class CWP_Woo_Product_Carousel_Settings {
    private $option_name = 'cwp_woo_product_carousel_options';
    private $options = array();

    public function __construct() {
        $this->options = get_option( $this->option_name );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
    }

    public function register_settings() {
        register_setting(
            'cwp_woo_product_carousel_options_group',
            $this->option_name,
            array( $this, 'sanitize_options' )
        );

        // Layout and Design Section
        add_settings_section(
            'cwp_layout_section',
            __( 'Layout and Design Settings', 'cwp-woo-product-carousel' ),
            array( $this, 'section_callback' ),
            'cwp_woo_product_carousel'
        );

        add_settings_field(
            'products_per_slide',
            __( 'Number of Products per Slide', 'cwp-woo-product-carousel' ),
            array( $this, 'field_number_callback' ),
            'cwp_woo_product_carousel',
            'cwp_layout_section',
            array( 'id' => 'products_per_slide', 'default' => 3 )
        );

        add_settings_field(
            'spacing',
            __( 'Spacing / Gutter Size (px)', 'cwp-woo-product-carousel' ),
            array( $this, 'field_number_callback' ),
            'cwp_woo_product_carousel',
            'cwp_layout_section',
            array( 'id' => 'spacing', 'default' => 10 )
        );

        add_settings_field(
            'image_size',
            __( 'Image Size', 'cwp-woo-product-carousel' ),
            array( $this, 'field_text_callback' ),
            'cwp_woo_product_carousel',
            'cwp_layout_section',
            array( 'id' => 'image_size', 'default' => 'medium' )
        );

        add_settings_field(
            'show_title_price',
            __( 'Show Title and Price', 'cwp-woo-product-carousel' ),
            array( $this, 'field_checkbox_callback' ),
            'cwp_woo_product_carousel',
            'cwp_layout_section',
            array( 'id' => 'show_title_price', 'default' => true )
        );

        // Carousel Behavior Section
        add_settings_section(
            'cwp_behavior_section',
            __( 'Carousel Behavior', 'cwp-woo-product-carousel' ),
            array( $this, 'section_callback' ),
            'cwp_woo_product_carousel'
        );

        add_settings_field(
            'autoplay',
            __( 'Autoplay (seconds, 0 = off)', 'cwp-woo-product-carousel' ),
            array( $this, 'field_number_callback' ),
            'cwp_woo_product_carousel',
            'cwp_behavior_section',
            array( 'id' => 'autoplay', 'default' => 0 )
        );

        add_settings_field(
            'looping',
            __( 'Enable Looping', 'cwp-woo-product-carousel' ),
            array( $this, 'field_checkbox_callback' ),
            'cwp_woo_product_carousel',
            'cwp_behavior_section',
            array( 'id' => 'looping', 'default' => false )
        );

        add_settings_field(
            'animation_speed',
            __( 'Animation Speed (ms)', 'cwp-woo-product-carousel' ),
            array( $this, 'field_number_callback' ),
            'cwp_woo_product_carousel',
            'cwp_behavior_section',
            array( 'id' => 'animation_speed', 'default' => 300 )
        );

        add_settings_field(
            'navigation_controls',
            __( 'Show Navigation Controls', 'cwp-woo-product-carousel' ),
            array( $this, 'field_checkbox_callback' ),
            'cwp_woo_product_carousel',
            'cwp_behavior_section',
            array( 'id' => 'navigation_controls', 'default' => true )
        );

        // Responsive Breakpoints Section
        add_settings_section(
            'cwp_responsive_section',
            __( 'Responsive Breakpoints', 'cwp-woo-product-carousel' ),
            array( $this, 'section_callback' ),
            'cwp_woo_product_carousel'
        );

        add_settings_field(
            'mobile_products',
            __( 'Products per Slide (Mobile)', 'cwp-woo-product-carousel' ),
            array( $this, 'field_number_callback' ),
            'cwp_woo_product_carousel',
            'cwp_responsive_section',
            array( 'id' => 'mobile_products', 'default' => 1 )
        );

        add_settings_field(
            'tablet_products',
            __( 'Products per Slide (Tablet)', 'cwp-woo-product-carousel' ),
            array( $this, 'field_number_callback' ),
            'cwp_woo_product_carousel',
            'cwp_responsive_section',
            array( 'id' => 'tablet_products', 'default' => 2 )
        );

        add_settings_field(
            'desktop_products',
            __( 'Products per Slide (Desktop)', 'cwp-woo-product-carousel' ),
            array( $this, 'field_number_callback' ),
            'cwp_woo_product_carousel',
            'cwp_responsive_section',
            array( 'id' => 'desktop_products', 'default' => 3 )
        );

        add_settings_field(
            'hide_on_mobile',
            __( 'Hide Carousel on Mobile?', 'cwp-woo-product-carousel' ),
            array( $this, 'field_checkbox_callback' ),
            'cwp_woo_product_carousel',
            'cwp_responsive_section',
            array( 'id' => 'hide_on_mobile', 'default' => false )
        );

        // Script Loading Section
        add_settings_section(
            'cwp_script_section',
            __( 'Script Loading', 'cwp-woo-product-carousel' ),
            array( $this, 'section_callback' ),
            'cwp_woo_product_carousel'
        );

        add_settings_field(
            'conditional_loading',
            __( 'Load Scripts Only on Shortcode Pages', 'cwp-woo-product-carousel' ),
            array( $this, 'field_checkbox_callback' ),
            'cwp_woo_product_carousel',
            'cwp_script_section',
            array( 'id' => 'conditional_loading', 'default' => false )
        );
    }

    public function add_menu_page() {
        add_options_page(
            __( 'CWP Product Carousel Settings', 'cwp-woo-product-carousel' ),
            __( 'CWP Product Carousel', 'cwp-woo-product-carousel' ),
            'manage_options',
            'cwp-woo-product-carousel',
            array( $this, 'settings_page' )
        );
    }

    public function sanitize_options( $input ) {
        $new_input = array();
        $fields = array(
            'products_per_slide' => 'int',
            'spacing' => 'int',
            'image_size' => 'text',
            'show_title_price' => 'bool',
            'autoplay' => 'int',
            'looping' => 'bool',
            'animation_speed' => 'int',
            'navigation_controls' => 'bool',
            'mobile_products' => 'int',
            'tablet_products' => 'int',
            'desktop_products' => 'int',
            'hide_on_mobile' => 'bool',
            'conditional_loading' => 'bool'
        );

        foreach ( $fields as $field => $type ) {
            if ( isset( $input[$field] ) ) {
                switch ( $type ) {
                    case 'int':
                        $new_input[$field] = intval( $input[$field] );
                        break;
                    case 'bool':
                        $new_input[$field] = (bool) $input[$field];
                        break;
                    default:
                        $new_input[$field] = sanitize_text_field( $input[$field] );
                        break;
                }
            }
        }

        return $new_input;
    }

    public function section_callback() {
        // Optional: Add descriptions per section
    }

    public function field_number_callback( $args ) {
        $value = isset( $this->options[$args['id']] ) ? $this->options[$args['id']] : $args['default'];
        echo '<input type="number" name="' . $this->option_name . '[' . $args['id'] . ']" value="' . esc_attr($value) . '">';
    }

    public function field_text_callback( $args ) {
        $value = isset( $this->options[$args['id']] ) ? $this->options[$args['id']] : $args['default'];
        echo '<input type="text" name="' . $this->option_name . '[' . $args['id'] . ']" value="' . esc_attr($value) . '">';
    }

    public function field_checkbox_callback( $args ) {
        $value = isset( $this->options[$args['id']] ) ? $this->options[$args['id']] : $args['default'];
        $checked = $value ? 'checked' : '';
        echo '<input type="checkbox" name="' . $this->option_name . '[' . $args['id'] . ']" value="1" ' . $checked . '>';
    }

    public function settings_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php _e( 'CWP Product Carousel Settings', 'cwp-woo-product-carousel' ); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'cwp_woo_product_carousel_options_group' );
                do_settings_sections( 'cwp_woo_product_carousel' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}

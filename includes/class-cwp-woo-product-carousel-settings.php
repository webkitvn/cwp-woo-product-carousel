<?php
if (! defined('ABSPATH')) {
    exit;
}

class CWP_Woo_Product_Carousel_Settings
{
    private string $option_name = 'cwp_woo_product_carousel_options';
    private array $options = [];
    private array $sections = [];
    private array $fields = [];

    public function __construct()
    {
        $this->load_options();
        $this->define_sections();
        $this->define_fields();
        $this->init_hooks();
    }

    private function load_options(): void
    {
        $this->options = get_option($this->option_name, []);
    }

    private function define_sections(): void
    {
        $this->sections = [
            'layout' => [
                'id'    => 'cwp_layout_section',
                'title' => __('Layout and Design Settings', 'cwp-woo-product-carousel'),
                'description' => __('Configure the layout and design settings for the product carousel.', 'cwp-woo-product-carousel'),
                'tab'   => 'layout-settings',
            ],
            'behavior' => [
                'id'    => 'cwp_behavior_section',
                'title' => __('Carousel Behavior', 'cwp-woo-product-carousel'),
                'description' => __('Configure the behavior settings for the product carousel.', 'cwp-woo-product-carousel'),
                'tab'   => 'behavior-settings',
            ],
            'advanced' => [
                'id'    => 'cwp_advanced_section',
                'title' => __('Advanced Settings', 'cwp-woo-product-carousel'),
                'description' => __('Configure the advanced settings for the product carousel.', 'cwp-woo-product-carousel'),
                'tab'   => 'advanced-settings',
            ],
            'credits' => [
                'id'    => 'cwp_credits_section',
                'title' => __('Credits', 'cwp-woo-product-carousel'),
                'description' => sprintf(
                    __('Thank you for using CWP Product Carousel! <a href="%s" target="_blank">Donate via PayPal</a>', 'cwp-woo-product-carousel'),
                    'https://www.paypal.com/donate?hosted_button_id=YOUR_BUTTON_ID'
                ),
                'tab'   => 'credits',
            ]
        ];
    }

    private function define_fields(): void
    {
        $this->fields = [
            // Layout Fields
            [
                'id'        => 'mobile_products',
                'title'     => __('Number of Products (Mobile)', 'cwp-woo-product-carousel'),
                'type'      => 'number',
                'section'   => 'cwp_layout_section',
                'tab'       => 'layout-settings',
                'default'   => 3,
                'min'       => 1,
                'max'       => 10
            ],
            [
                'id'        => 'tablet_products',
                'title'     => __('Number of Products (Tablet)', 'cwp-woo-product-carousel'),
                'type'      => 'number',
                'section'   => 'cwp_layout_section',
                'tab'       => 'layout-settings',
                'default'   => 4,
                'min'       => 1,
                'max'       => 10
            ],
            [
                'id'        => 'desktop_products',
                'title'     => __('Number of Products (Desktop)', 'cwp-woo-product-carousel'),
                'type'      => 'number',
                'section'   => 'cwp_layout_section',
                'tab'       => 'layout-settings',
                'default'   => 5,
                'min'       => 1,
                'max'       => 10
            ],
            [
                'id'        => 'spacing',
                'title'     => __('Spacing / Gutter Size (px)', 'cwp-woo-product-carousel'),
                'type'      => 'number',
                'section'   => 'cwp_layout_section',
                'tab'       => 'layout-settings',
                'default'   => 10
            ],
            [
                'id'        => 'show_title',
                'title'     => __('Show Product Title', 'cwp-woo-product-carousel'),
                'type'      => 'checkbox',
                'section'   => 'cwp_layout_section',
                'tab'       => 'layout-settings',
                'default'   => true
            ],
            [
                'id'        => 'show_price',
                'title'     => __('Show Product Price', 'cwp-woo-product-carousel'),
                'type'      => 'checkbox',
                'section'   => 'cwp_layout_section',
                'tab'       => 'layout-settings',
                'default'   => true
            ],
            [
                'id'        => 'show_arrows',
                'title'     => __('Show Navigation Arrows', 'cwp-woo-product-carousel'),
                'type'      => 'checkbox',
                'section'   => 'cwp_layout_section',
                'tab'       => 'layout-settings',
                'default'   => true
            ],
            [
                'id'        => 'show_dots',
                'title'     => __('Show Navigation Dots', 'cwp-woo-product-carousel'),
                'type'      => 'checkbox',
                'section'   => 'cwp_layout_section',
                'tab'       => 'layout-settings',
                'default'   => true
            ],
            [
                'id'        => 'autoplay',
                'title'     => __('Autoplay', 'cwp-woo-product-carousel'),
                'type'      => 'checkbox',
                'section'   => 'cwp_behavior_section',
                'tab'       => 'behavior-settings',
                'default'   => true
            ],
            [
                'id'        => 'looping',
                'title'     => __('Enable Looping', 'cwp-woo-product-carousel'),
                'type'      => 'checkbox',
                'section'   => 'cwp_behavior_section',
                'tab'       => 'behavior-settings',
                'default'   => true
            ],
            [
                'id'        => 'animation_speed',
                'title'     => __('Animation Speed (ms)', 'cwp-woo-product-carousel'),
                'type'      => 'number',
                'section'   => 'cwp_behavior_section',
                'tab'       => 'behavior-settings',
                'default'   => 500,
            ],
            [
                'id'        => 'autoplay_speed',
                'title'     => __('Autoplay Speed (ms)', 'cwp-woo-product-carousel'),
                'type'      => 'number',
                'section'   => 'cwp_behavior_section',
                'tab'       => 'behavior-settings',
                'default'   => 3000,
            ],
            [
                'id'        => 'conditional_loading',
                'title'     => __('Load Scripts Only on Shortcode Pages', 'cwp-woo-product-carousel'),
                'type'      => 'checkbox',
                'section'   => 'cwp_advanced_section',
                'tab'       => 'advanced-settings',
                'default'   => false
            ],
            [
                'id'        => 'custom_css',
                'title'     => __('Custom CSS', 'cwp-woo-product-carousel'),
                'type'      => 'textarea',
                'section'   => 'cwp_advanced_section',
                'tab'       => 'advanced-settings',
                'default'   => ''
            ]
        ];
    }

    private function init_hooks(): void
    {
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_menu', [$this, 'add_menu_page']);
        add_action('admin_footer', [$this, 'add_tab_navigation_script']);
    }

    public function add_tab_navigation_script(): void
    {
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                $('.nav-tab').click(function(e) {
                    e.preventDefault();
                    $('.nav-tab').removeClass('nav-tab-active');
                    $(this).addClass('nav-tab-active');
                    $('.tab-content').hide();
                    $($(this).attr('href')).show();
                });
            });
        </script>
        <?php
    }

    public function register_settings(): void
    {
        register_setting(
            'cwp_woo_product_carousel_options_group',
            $this->option_name,
            [$this, 'sanitize_options']
        );

        $this->register_sections();
        $this->register_fields();
    }

    private function register_sections(): void
    {
        foreach ($this->sections as $section) {
            add_settings_section(
                $section['id'],
                $section['title'],
                [$this, 'section_callback'],
                'cwp_woo_product_carousel',
                ['tab' => $section['tab']]
            );
        }
    }

    private function register_fields(): void
    {
        foreach ($this->fields as $field) {
            add_settings_field(
                $field['id'],
                $field['title'],
                [$this, 'render_field'],
                'cwp_woo_product_carousel',
                $field['section'],
                $field
            );
        }
    }

    public function add_menu_page(): void
    {
        add_options_page(
            __('CWP Product Carousel Settings', 'cwp-woo-product-carousel'),
            __('CWP Product Carousel', 'cwp-woo-product-carousel'),
            'manage_options',
            'cwp-woo-product-carousel',
            [$this, 'settings_page']
        );
    }

    public function sanitize_options(array $input): array
    {
        $new_input = [];
        
        foreach ($this->fields as $field) {
            $id = $field['id'];
            
            if (isset($input[$id])) {
                switch ($field['type']) {
                    case 'number':
                        $new_input[$id] = $this->sanitize_number($input[$id], $field);
                        break;
                    case 'checkbox':
                        $new_input[$id] = filter_var($input[$id], FILTER_VALIDATE_BOOLEAN);
                        break;
                    case 'textarea':
                        $new_input[$id] = sanitize_textarea_field($input[$id]);
                        break;
                    default:
                        $new_input[$id] = sanitize_text_field($input[$id]);
                }
            }
        }

        return $new_input;
    }

    private function sanitize_number($value, array $field): int
    {
        $number = intval($value);
        
        if (isset($field['min']) && $number < $field['min']) {
            $number = $field['min'];
        }
        
        if (isset($field['max']) && $number > $field['max']) {
            $number = $field['max'];
        }
        
        return $number;
    }

    public function section_callback(array $args): void
    {
        $descriptions = [
            'cwp_layout_section'     => __('Configure the layout and design settings for the product carousel.', 'cwp-woo-product-carousel'),
            'cwp_behavior_section'   => __('Configure the behavior settings for the product carousel.', 'cwp-woo-product-carousel'),
            'cwp_advanced_section'   => __('Configure the advanced settings for the product carousel.', 'cwp-woo-product-carousel')
        ];

        $section = $args['id'];
        echo '<p>' . ($descriptions[$section] ?? '') . '</p>';
    }

    public function render_field(array $args): void
    {
        $value = $this->options[$args['id']] ?? $args['default'];
        $name = $this->option_name . '[' . $args['id'] . ']';

        switch ($args['type']) {
            case 'number':
                $min = $args['min'] ?? 0;
                $max = $args['max'] ?? 100;
                echo sprintf(
                    '<input type="number" name="%s" value="%s" min="%d" max="%d">',
                    esc_attr($name),
                    esc_attr($value),
                    $min,
                    $max
                );
                break;

            case 'checkbox':
                $checked = $value ? 'checked' : '';
                echo sprintf(
                    '<input type="checkbox" name="%s" value="1" %s>',
                    esc_attr($name),
                    $checked
                );
                break;
            case 'textarea':
                echo sprintf(
                    '<textarea name="%s" rows="10" cols="50">%s</textarea>',
                    esc_attr($name),
                    esc_textarea($value)
                );
                break;
        }
    }

    public function settings_page(): void
    {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php _e('CWP Product Carousel Settings', 'cwp-woo-product-carousel'); ?></h1>
            <h2 class="nav-tab-wrapper">
                <?php 
                    $first = true;
                    foreach ($this->sections as $section) {
                        $class = $first ? 'nav-tab nav-tab-active' : 'nav-tab';
                        echo sprintf(
                            '<a href="#%s" class="%s">%s</a>',
                            esc_attr($section['tab']),
                            $class,
                            esc_html($section['title'])
                        );
                        $first = false;
                    }
                ?>
            </h2>
            <form action="options.php" method="post">
                <?php 
                settings_fields('cwp_woo_product_carousel_options_group');
                
                foreach ($this->sections as $section) {
                    $this->render_tab_content($section);
                }
                
                submit_button('Save Settings'); 
                ?>
            </form>
        </div>
        <?php
    }

    private function render_tab_content(array $section): void
    {
        printf(
            '<div id="%s" class="tab-content" style="display:%s;">',
            esc_attr($section['tab']),
            $section['tab'] === 'layout-settings' ? 'block' : 'none'
        );

        // Add tab title and description
        echo sprintf(
            '<h2>%s</h2>',
            esc_html($section['title'])
        );

        if (!empty($section['description'])) {
            echo sprintf(
                '<div class="tab-description"><p>%s</p></div>',
                $section['description']
            );
        }

        // Capture the output of settings fields for this specific section
        ob_start();
        foreach ($this->fields as $field) {
            if ($field['section'] === $section['id']) {
                echo '<table class="form-table">';
                do_settings_fields('cwp_woo_product_carousel', $section['id']);
                echo '</table>';
                break;
            }
        }
        $content = ob_get_clean();
        
        echo $content;
        echo '</div>';
    }
}
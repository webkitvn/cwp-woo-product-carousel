<?php
if (! defined('ABSPATH')) {
    exit;
}

class CWP_Woo_Product_Carousel_Settings
{
    const OPTION_NAME = 'cwp_woo_product_carousel_options';
    const SECTION_LAYOUT = 'cwp_layout_section';

    private $option_name = self::OPTION_NAME;
    private array $options = [];
    private array $sections = [];
    private array $fields = [];

    private bool $settings_registered = false;

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
            'how_to_use' => [
                'id'    => 'cwp_how_to_use_section',
                'title' => __('How to Use', 'cwp-woo-product-carousel'),
                'tab'   => 'how-to-use',
            ],
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
        ];
    }

    private function define_fields(): void
    {
        $this->fields = [
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
                'id'        => 'looping',
                'title'     => __('Looping', 'cwp-woo-product-carousel'),
                'type'      => 'checkbox',
                'section'   => 'cwp_behavior_section',
                'tab'       => 'behavior-settings',
                'default'   => true
            ],
            [
                'id'        => 'autoplay',
                'title'     => __('Autoplay', 'cwp-woo-product-carousel'),
                'type'      => 'checkbox',
                'section'   => 'cwp_behavior_section',
                'tab'       => 'behavior-settings',
                'default'   => true
            ]
        ];
    }

    private function init_hooks(): void
    {
        add_action('admin_init', [$this, 'register_settings'], 10);
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
        if ($this->settings_registered) {
            return;
        }
        $this->settings_registered = true;

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
            'cwp_woo_product_carousel',
            [$this, 'settings_page']
        );
    }

    public function sanitize_options(array $input): array
    {
        $new_input = [];

        foreach ($this->fields as $field) {
            $id = $field['id'];

            if ($field['type'] === 'checkbox') {
                // Explicitly handle checkboxes - set false if not in input
                $new_input[$id] = isset($input[$id]) ? filter_var($input[$id], FILTER_VALIDATE_BOOLEAN) : false;
            } else if (isset($input[$id])) {
                // Handle other field types as before
                switch ($field['type']) {
                    case 'number':
                        $new_input[$id] = $this->sanitize_number($input[$id], $field);
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
        $section = $args['id'];
        $description = $this->sections[$section]['description'] ?? '';
        if ($description) {
            echo '<p>' . esc_html($description) . '</p>';
        }
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
            <p><?php _e('Lightweight and customizable product carousel for WooCommerce.', 'cwp-woo-product-carousel'); ?></p>
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
            $section['tab'] === 'how-to-use' ? 'block' : 'none'
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

        if ($section['tab'] === 'how-to-use') {
            // Display specific content for "how-to-use" tab
            echo '<div class="cwp-documentation">';
            // How to Use Section
            echo '<div class="cwp-how-to-use">';
            echo '<h3>' . __('Quick Start Guide', 'cwp-woo-product-carousel') . '</h3>';
            echo '<div class="cwp-shortcode-usage">';
            echo '<p><strong>' . __('Basic Usage:', 'cwp-woo-product-carousel') . '</strong></p>';
            echo '<code>[cwp_woo_products_slider id="1,2,3"]</code>';
            echo '<p>' . __('Replace 1,2,3 with your desired product IDs', 'cwp-woo-product-carousel') . '</p>';
            echo '</div>';

            echo '<div class="cwp-find-product-id">';
            echo '<p><strong>' . __('Finding Product IDs:', 'cwp-woo-product-carousel') . '</strong></p>';
            echo '<ol>';
            echo '<li>' . __('Go to WooCommerce Products', 'cwp-woo-product-carousel') . '</li>';
            echo '<li>' . __('Edit your product', 'cwp-woo-product-carousel') . '</li>';
            echo '<li>' . __('Look at the URL: post.php?post=123&action=edit', 'cwp-woo-product-carousel') . '</li>';
            echo '<li>' . __('The number (123) is your product ID', 'cwp-woo-product-carousel') . '</li>';
            echo '</ol>';
            echo '</div>';
            echo '</div>';

            // Support Section
            echo '<div class="cwp-support">';
            echo '<h3>' . __('Need Help?', 'cwp-woo-product-carousel') . '</h3>';
            echo '<p>' . __('Send me a message on Telegram:', 'cwp-woo-product-carousel') . '</p>';
            echo '<p><a href="https://t.me/cuongwp" class="button button-secondary">@cuongwp</a></p>';
            echo '</div>';

            // Donation Section
            echo '<div class="cwp-donate">';
            echo '<h3>' . __('Support the Development', 'cwp-woo-product-carousel') . '</h3>';
            echo '<p>' . __('Help keep this plugin free and actively maintained!', 'cwp-woo-product-carousel') . '</p>';
            echo '<a href="https://buymeacoffee.com/cuongwp" target="_blank" class="bmc-button">';
            echo '<img src="https://cdn.buymeacoffee.com/buttons/v2/default-yellow.png" alt="Buy Me A Coffee" style="height: 60px; width: 217px;">';
            echo '</a>';
            echo '</div>';
            echo '</div>';
        } else {
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
        }

        echo '</div>';
    }
}

<?php

/**
 * Plugin Name: CWP Woo Product Carousel
 * Description: Lightweight WooCommerce product carousel using Embla Carousel.
 * Version: 1.0.0
 * Author: CuongPham
 * Author URI: https://cuongwp.com
 */

if (! defined('ABSPATH')) {
    exit;
}

// Include settings class
require_once plugin_dir_path(__FILE__) . 'includes/class-cwp-woo-product-carousel-settings.php';

// Initialize settings
add_action('plugins_loaded', function () {
    new CWP_Woo_Product_Carousel_Settings();
});

class CWP_Product_Carousel
{
    private static $instance = null;
    private $shortcode_found = false;

    public static function instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        add_action('init', array($this, 'register_shortcode'));
        add_action('wp_enqueue_scripts', array($this, 'conditional_enqueue_assets'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_custom_css'));
    }

    /**
     * Registers the [cwp_woo_products_slider] shortcode.
     */
    public function register_shortcode()
    {
        add_shortcode('cwp_woo_products_slider', array($this, 'render_shortcode'));
    }

    /**
     * Renders the shortcode output.
     *
     * @param array $atts
     * @return string
     */
    public function render_shortcode($atts)
    {
        $this->shortcode_found = true; // Mark that shortcode is present on this page

        $atts = shortcode_atts(array(
            'id' => '',
        ), $atts, 'cwp_woo_products_slider');

        $ids = array_filter(array_map('trim', explode(',', $atts['id'])));

        if (empty($ids)) {
            return '<p>No products found. Please specify product IDs.</p>';
        }

        // Use WP_Query to fetch specified products
        $args = array(
            'post_type'      => 'product',
            'post__in'       => $ids,
            'posts_per_page' => -1,
            'orderby'        => 'post__in',
        );
        $query = new WP_Query($args);

        if (! $query->have_posts()) {
            wp_reset_postdata();
            return '<p>No products found for the provided IDs.</p>';
        }

        ob_start();
?>
        <div class="embla cwp-carousel cwp-products products">
            <div class="embla__viewport">
                <div class="embla__container">
                    <?php while ($query->have_posts()) : $query->the_post();
                        global $product;
                        $image_id = $product->get_image_id();
                        $image_url = $image_id ? wp_get_attachment_image_src($image_id, 'medium')[0] : wc_placeholder_img_src();
                    ?>
                        <div class="embla__slide cwp-carousel-item">
                            <?php
                            wc_get_template_part('content', 'product');
                            ?>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
            <?php
            $this->render_nav_arrows();
            ?>
        </div>
    <?php
        wp_reset_postdata();

        return ob_get_clean();
    }

    /**
     * Renders the navigation arrows for the carousel.
     */
    private function render_nav_arrows()
    {
        $options = get_option('cwp_woo_product_carousel_options', []);
        $nav_arrows = $options['show_arrows'] ?? 'true';
        if ($nav_arrows !== true) {
            return;
        }
    ?>
        <button class="embla__arrow embla__prev" aria-label="Previous">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
            </svg>
        </button>
        <button class="embla__arrow embla__next" aria-label="Next">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
            </svg>
        </button>
<?php
    }

    /**
     * Conditionally enqueue scripts and styles only if the shortcode is found in the current post content.
     * Called at `wp_enqueue_scripts` action.
     */
    public function conditional_enqueue_assets()
    {
        // Check if shortcode appears in the global $post object
        global $post;
        if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'cwp_woo_products_slider')) {
            // Enqueue our initialization script
            wp_enqueue_script('cwp-init-embla', plugin_dir_url(__FILE__) . 'assets/dist/js/init-embla.js', array(), '1.0.0', true);
            // Enqueue our styles
            wp_enqueue_style('cwp-carousel-styles', plugin_dir_url(__FILE__) . 'assets/dist/css/cwp_woo_product_carousel.css', array(), '1.0.0');

            $options = get_option('cwp_woo_product_carousel_options', []);
            $js_options = [
                'looping' => $options['looping'] ?? true,
                'autoplay' => $options['autoplay'] ?? true,
                'autoplay_speed' => $options['autoplay_speed'] ?? 3000,
                'show_arrows' => $options['show_arrows'] ?? true,
                'show_dots' => $options['show_dots'] ?? true,
            ];
            wp_localize_script('cwp-init-embla', 'cwpCarouselOptions', $options);
        }
    }

    /**
     * Enqueue inline custom css for Embla Carousel.
     */
    public function enqueue_custom_css()
    {
        // Retrieve options with defaults
        $options = get_option('cwp_woo_product_carousel_options', []);
        $mobile_products = $options['mobile_products'] ?? 3;
        $tablet_products = $options['tablet_products'] ?? 4;
        $desktop_products = $options['desktop_products'] ?? 5;
        $spacing = $options['spacing'] ?? 10;

        // Generate dynamic CSS
        $custom_css = "
            .cwp-carousel-item {
                margin-right: {$spacing}px;
            }
            @media (max-width: 767px) {
                .cwp-carousel-item {
                    flex: 0 0 calc((100% - {$spacing}px * ({$mobile_products} - 1)) / {$mobile_products});
                }
            }
            @media (min-width: 768px) and (max-width: 1024px) {
                .cwp-carousel-item {
                    flex: 0 0 calc((100% - {$spacing}px * ({$tablet_products} - 1)) / {$tablet_products});
                }
            }
            @media (min-width: 1025px) {
                .cwp-carousel-item {
                    flex: 0 0 calc((100% - {$spacing}px * ({$desktop_products} - 1)) / {$desktop_products});
                }
            }
        ";

        // Add inline style
        wp_add_inline_style('cwp-carousel-styles', $custom_css);
    }
}

// Initialize the plugin
CWP_Product_Carousel::instance();

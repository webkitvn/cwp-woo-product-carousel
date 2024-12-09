<?php
/**
 * Plugin Name: CWP Woo Product Carousel
 * Description: Display a carousel of WooCommerce products.
 * Version: 1.0.0
 * Author: CuongPham
 * Author URI: https://cuongwp.com
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Include settings class
require_once plugin_dir_path( __FILE__ ) . 'includes/class-cwp-woo-product-carousel-settings.php';

// Initialize settings
add_action( 'plugins_loaded', function() {
    new CWP_Woo_Product_Carousel_Settings();
});

class CWP_Product_Carousel {
    private static $instance = null;
    private $shortcode_found = false;

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'init', array( $this, 'register_shortcode' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'conditional_enqueue_assets' ) );
    }

    /**
     * Registers the [cwp_woo_products_slider] shortcode.
     */
    public function register_shortcode() {
        add_shortcode( 'cwp_woo_products_slider', array( $this, 'render_shortcode' ) );
    }

    /**
     * Renders the shortcode output.
     *
     * @param array $atts
     * @return string
     */
    public function render_shortcode( $atts ) {
        $this->shortcode_found = true; // Mark that shortcode is present on this page

        $atts = shortcode_atts( array(
            'id' => '',
        ), $atts, 'cwp_woo_products_slider' );

        $ids = array_filter( array_map( 'trim', explode( ',', $atts['id'] ) ) );

        if ( empty( $ids ) ) {
            return '<p>No products found. Please specify product IDs.</p>';
        }

        // Use WP_Query to fetch specified products
        $args = array(
            'post_type'      => 'product',
            'post__in'       => $ids,
            'posts_per_page' => -1,
            'orderby'        => 'post__in',
        );
        $query = new WP_Query( $args );

        if ( ! $query->have_posts() ) {
            wp_reset_postdata();
            return '<p>No products found for the provided IDs.</p>';
        }

        ob_start();
        ?>
        <div class="embla">
            <div class="embla__container">
            <?php while ( $query->have_posts() ) : $query->the_post();
                global $product;
                $image_id = $product->get_image_id();
                $image_url = $image_id ? wp_get_attachment_image_src( $image_id, 'medium' )[0] : wc_placeholder_img_src();
                ?>
                <div class="embla__slide">
                    <a href="<?php the_permalink(); ?>">
                        <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" />
                        <h2><?php the_title(); ?></h2>
                        <span class="price"><?php echo $product->get_price_html(); ?></span>
                    </a>
                </div>
            <?php endwhile; ?>
            </div>
        </div>
        <?php
        wp_reset_postdata();

        return ob_get_clean();
    }

    /**
     * Conditionally enqueue scripts and styles only if the shortcode is found in the current post content.
     * Called at `wp_enqueue_scripts` action.
     */
    public function conditional_enqueue_assets() {
        // Check if shortcode appears in the global $post object
        global $post;
        if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'cwp_woo_products_slider' ) ) {
            // Enqueue Embla Carousel
            wp_enqueue_script( 'embla-carousel', 'https://cdn.jsdelivr.net/npm/embla-carousel@7.0.1/embla-carousel.umd.js', array(), '7.0.1', true );
            // Enqueue our initialization script
            wp_enqueue_script( 'cwp-init-embla', plugin_dir_url( __FILE__ ) . 'assets/js/init-embla.js', array( 'embla-carousel' ), '1.0.0', true );
            // Enqueue our styles
            wp_enqueue_style( 'cwp-carousel-styles', plugin_dir_url( __FILE__ ) . 'assets/css/style.css', array(), '1.0.0' );
        }
    }
}

// Initialize the plugin
CWP_Product_Carousel::instance();

<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class MFD_Filters {
    private static $instance = null;

    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'wp_ajax_mfd_filter_posts', array( $this, 'filter_posts' ) );
        add_action( 'wp_ajax_nopriv_mfd_filter_posts', array( $this, 'filter_posts' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
    }

    public function enqueue_scripts() {
        wp_enqueue_script( 'mfd-filters', plugins_url( '../assets/js/filters.js', __FILE__ ), array( 'jquery' ), '1.0', true );
        wp_localize_script( 'mfd-filters', 'mfd_filters', array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'mfd_filter_nonce' )
        ) );
    }

    public function filter_posts() {
        check_ajax_referer( 'mfd_filter_nonce', 'nonce' );

        $taxonomies = isset( $_POST['taxonomies'] ) ? $_POST['taxonomies'] : array();
        $paged = isset( $_POST['paged'] ) ? intval( $_POST['paged'] ) : 1;
        
        $args = array(
            'post_type' => 'mfd_departamento',
            'posts_per_page' => get_option( 'posts_per_page' ),
            'paged' => $paged,
            'tax_query' => array()
        );

        if ( ! empty( $taxonomies ) ) {
            foreach ( $taxonomies as $taxonomy => $terms ) {
                if ( ! empty( $terms ) ) {
                    $args['tax_query'][] = array(
                        'taxonomy' => sanitize_text_field( $taxonomy ),
                        'field' => 'term_id',
                        'terms' => array_map( 'intval', $terms ),
                        'operator' => 'IN'
                    );
                }
            }
        }

        $query = new WP_Query( $args );
        $response = array(
            'success' => true,
            'html' => '',
            'max_pages' => $query->max_num_pages
        );

        if ( $query->have_posts() ) {
            ob_start();
            while ( $query->have_posts() ) {
                $query->the_post();
                get_template_part( 'template-parts/content', 'departamento' );
            }
            $response['html'] = ob_get_clean();
            wp_reset_postdata();
        }

        wp_send_json( $response );
    }
}

// Inicializar la clase
MFD_Filters::get_instance(); 
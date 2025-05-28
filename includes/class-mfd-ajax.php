<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class MFD_Ajax {
    private static $instance = null;

    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'wp_ajax_mfd_filtrar_departamentos', array( $this, 'filtrar_departamentos' ) );
        add_action( 'wp_ajax_nopriv_mfd_filtrar_departamentos', array( $this, 'filtrar_departamentos' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
    }

    public function enqueue_scripts() {
        wp_enqueue_style( 'mfd-style', plugins_url( '../assets/css/mfd-style.css', __FILE__ ), array(), '1.0' );
        wp_enqueue_script( 'mfd-filters', plugins_url( '../assets/js/mfd-filters.js', __FILE__ ), array( 'jquery' ), '1.0', true );
        wp_localize_script( 'mfd-filters', 'mfd_ajax', array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'mfd_filtros_nonce' )
        ));
    }

    public function filtrar_departamentos() {
        check_ajax_referer( 'mfd_filtros_nonce', 'nonce' );

        $args = array(
            'post_type' => 'departamento',
            'posts_per_page' => 12,
            'paged' => isset($_POST['paged']) ? absint($_POST['paged']) : 1,
            'orderby' => 'date',
            'order' => 'DESC',
            'tax_query' => array('relation' => 'AND')
        );

        $taxonomias = array('tipo', 'amoblado', 'vista', 'disponibilidad');

        foreach ($taxonomias as $taxonomy) {
            if (!empty($_POST[$taxonomy])) {
                $terms = sanitize_text_field($_POST[$taxonomy]);
                if (!empty($terms)) {
                    $args['tax_query'][] = array(
                        'taxonomy' => $taxonomy,
                        'field' => 'slug',
                        'terms' => $terms
                    );
                }
            }
        }

        $query = new WP_Query($args);
        
        $response = array(
            'success' => true,
            'data' => array(
                'html' => '',
                'total' => $query->found_posts,
                'max_pages' => $query->max_num_pages,
                'current_page' => $args['paged']
            )
        );

        ob_start();

        if ($query->have_posts()) {
            echo '<div class="mfd-results-info">';
            echo '<p>Mostrando ' . $query->post_count . ' de ' . $query->found_posts . ' departamentos</p>';
            echo '</div>';

            echo '<div class="mfd-grid">';
            while ($query->have_posts()) {
                $query->the_post();
                include plugin_dir_path(__FILE__) . '../views/content-departamento.php';
            }
            echo '</div>';

            if ($query->max_num_pages > 1) {
                echo '<div class="mfd-pagination">';
                for ($i = 1; $i <= $query->max_num_pages; $i++) {
                    $active = $i === $args['paged'] ? ' active' : '';
                    echo '<button class="mfd-page-button' . $active . '" data-page="' . $i . '">' . $i . '</button>';
                }
                echo '</div>';
            }
        } else {
            echo '<div class="mfd-no-results">';
            echo '<p>No se encontraron departamentos que coincidan con los criterios de búsqueda.</p>';
            echo '<p>Intenta ajustar los filtros o <a href="#" class="mfd-reset-filters">limpiar todos los filtros</a>.</p>';
            echo '</div>';
        }

        wp_reset_postdata();
        $response['data']['html'] = ob_get_clean();

        wp_send_json_success($response);
    }
}

// Inicializar la clase
MFD_Ajax::get_instance();
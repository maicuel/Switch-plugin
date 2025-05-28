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
        $version = filemtime(plugin_dir_path(__DIR__) . 'assets/css/mfd-style.css');
        wp_enqueue_style( 'mfd-style', plugins_url( '../assets/css/mfd-style.css', __FILE__ ), array(), $version );
        
        $js_version = filemtime(plugin_dir_path(__DIR__) . 'assets/js/mfd-filters.js');
        wp_enqueue_script( 'mfd-filters', plugins_url( '../assets/js/mfd-filters.js', __FILE__ ), array( 'jquery' ), $js_version, true );
        wp_localize_script( 'mfd-filters', 'mfd_ajax', array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'mfd_filtros_nonce' ),
            'i18n' => array(
                'error_load' => __('Error al cargar los resultados.', 'mfd'),
                'price_error' => __('El precio mínimo no puede ser mayor que el precio máximo', 'mfd'),
                'no_results' => __('No se encontraron departamentos que coincidan con los criterios de búsqueda.', 'mfd'),
                'reset_filters' => __('limpiar todos los filtros', 'mfd')
            )
        ));
    }

    public function filtrar_departamentos() {
        if (!check_ajax_referer('mfd_filtros_nonce', 'nonce', false)) {
            wp_send_json_error(array('message' => 'Nonce inválido'));
        }

        $paged = filter_input(INPUT_POST, 'paged', FILTER_VALIDATE_INT);
        $paged = $paged ? $paged : 1;

        $args = array(
            'post_type' => 'departamento',
            'posts_per_page' => apply_filters('mfd_posts_per_page', 12),
            'paged' => $paged,
            'orderby' => 'date',
            'order' => 'DESC',
            'tax_query' => array('relation' => 'AND')
        );

        $taxonomias = array('tipo', 'amoblado', 'vista', 'disponibilidad');

        foreach ($taxonomias as $taxonomy) {
            $term = filter_input(INPUT_POST, $taxonomy, FILTER_SANITIZE_STRING);
            if ($term) {
                $args['tax_query'][] = array(
                    'taxonomy' => $taxonomy,
                    'field' => 'slug',
                    'terms' => $term
                );
            }
        }

        $query = new WP_Query($args);
        
        $response = array(
            'success' => true,
            'data' => array(
                'html' => '',
                'total' => $query->found_posts,
                'max_pages' => $query->max_num_pages,
                'current_page' => $paged
            )
        );

        ob_start();

        if ($query->have_posts()) {
            echo '<div class="mfd-results-info">';
            printf(
                _n(
                    'Mostrando %1$d de %2$d departamento',
                    'Mostrando %1$d de %2$d departamentos',
                    $query->found_posts,
                    'mfd'
                ),
                $query->post_count,
                $query->found_posts
            );
            echo '</div>';

            echo '<div class="mfd-grid">';
            while ($query->have_posts()) {
                $query->the_post();
                include apply_filters('mfd_template_departamento', plugin_dir_path(__FILE__) . '../views/content-departamento.php');
            }
            echo '</div>';

            if ($query->max_num_pages > 1) {
                echo '<div class="mfd-pagination">';
                for ($i = 1; $i <= $query->max_num_pages; $i++) {
                    $active = $i === $paged ? ' active' : '';
                    printf(
                        '<button class="mfd-page-button%s" data-page="%d">%d</button>',
                        esc_attr($active),
                        esc_attr($i),
                        esc_html($i)
                    );
                }
                echo '</div>';
            }
        } else {
            echo '<div class="mfd-no-results">';
            echo '<p>' . esc_html__('No se encontraron departamentos que coincidan con los criterios de búsqueda.', 'mfd') . '</p>';
            echo '<p>' . sprintf(
                __('Intenta ajustar los filtros o %s', 'mfd'),
                '<a href="#" class="mfd-reset-filters">' . esc_html__('limpiar todos los filtros', 'mfd') . '</a>'
            ) . '</p>';
            echo '</div>';
        }

        wp_reset_postdata();
        $response['data']['html'] = ob_get_clean();

        wp_send_json_success($response);
    }
}

MFD_Ajax::get_instance();
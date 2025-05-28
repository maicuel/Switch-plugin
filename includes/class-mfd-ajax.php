<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class MFD_Ajax {
    public function __construct() {
        add_action( 'wp_ajax_mfd_filtrar_departamentos', array( $this, 'filtrar_departamentos' ) );
        add_action( 'wp_ajax_nopriv_mfd_filtrar_departamentos', array( $this, 'filtrar_departamentos' ) );
    }

    public function filtrar_departamentos() {
        check_ajax_referer( 'mfd_filtros_nonce', 'nonce' );

        // Configuración base de la consulta
        $args = array(
            'post_type' => 'departamento',
            'posts_per_page' => 12, // Limitar resultados por página
            'paged' => isset($_POST['paged']) ? absint($_POST['paged']) : 1,
            'orderby' => 'date',
            'order' => 'DESC',
            'tax_query' => array('relation' => 'AND')
        );

        // Taxonomías disponibles para filtrado
        $taxonomias = array('tipo', 'amoblado', 'vista', 'disponibilidad');

        // Construir tax_query solo con los filtros seleccionados
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

        // Ejecutar la consulta
        $query = new WP_Query($args);
        
        // Preparar la respuesta
        $response = array(
            'success' => true,
            'html' => '',
            'total' => $query->found_posts,
            'pages' => $query->max_num_pages,
            'current_page' => $args['paged']
        );

        ob_start();

        if ($query->have_posts()) {
            echo '<div class="mfd-results-info">';
            echo '<p>Mostrando ' . $query->post_count . ' de ' . $query->found_posts . ' departamentos</p>';
            echo '</div>';

            echo '<div class="mfd-results-grid">';
            while ($query->have_posts()) {
                $query->the_post();
                include plugin_dir_path(__FILE__) . '../views/content-departamento.php';
            }
            echo '</div>';

            // Paginación
            if ($query->max_num_pages > 1) {
                echo '<div class="mfd-pagination">';
                for ($i = 1; $i <= $query->max_num_pages; $i++) {
                    $active = $i === $args['paged'] ? 'current-page' : '';
                    echo '<button class="mfd-page-button ' . $active . '" data-page="' . $i . '">' . $i . '</button>';
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
        $response['html'] = ob_get_clean();

        wp_send_json_success($response);
    }
}

new MFD_Ajax();
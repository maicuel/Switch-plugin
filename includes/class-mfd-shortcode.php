<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class MFD_Shortcode {

    public function __construct() {
        add_shortcode( 'mostrar_departamentos', array( $this, 'render' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
    }

    public function enqueue_scripts() {
        wp_enqueue_script( 'mfd-filtros', plugins_url( '../assets/js/filtros.js', __FILE__ ), array( 'jquery' ), '1.0', true );
        wp_localize_script( 'mfd-filtros', 'mfd_ajax', array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'mfd_filtros_nonce' )
        ));
    }

    public function render( $atts ) {
        $atts = shortcode_atts( array(
            'tipo' => '',
            'amoblado' => '',
            'vista' => '',
            'disponibilidad' => '',
            'habitaciones' => '',
            'banos' => ''
        ), $atts, 'mostrar_departamentos' );

        ob_start();
        ?>
        <div class="mfd-filter-container">
            <!-- Columna de filtros -->
            <div class="mfd-filter-column">
                <form id="mfd-filter-form" class="mfd-filter-form">
                    <div class="mfd-filter-row">
                        <?php
                        $taxonomias = array(
                            'tipo' => 'Tipo de Departamento',
                            'amoblado' => 'Amoblado',
                            'vista' => 'Vista',
                            'disponibilidad' => 'Disponibilidad',
                            'habitaciones' => 'Habitaciones',
                            'banos' => 'Baños'
                        );

                        foreach ($taxonomias as $tax_slug => $tax_label) {
                            $terms = get_terms(array(
                                'taxonomy' => $tax_slug,
                                'hide_empty' => true
                            ));
                            
                            if (!empty($terms) && !is_wp_error($terms)) : ?>
                                <div class="mfd-filter-group">
                                    <label for="<?php echo esc_attr($tax_slug); ?>"><?php echo esc_html($tax_label); ?></label>
                                    <select name="<?php echo esc_attr($tax_slug); ?>" id="<?php echo esc_attr($tax_slug); ?>" class="mfd-taxonomy-select">
                                        <option value="">Todos</option>
                                        <?php foreach ($terms as $term) : ?>
                                            <option value="<?php echo esc_attr($term->slug); ?>" <?php selected($atts[$tax_slug], $term->slug); ?>>
                                                <?php echo esc_html($term->name); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            <?php endif;
                        }
                        ?>
                    </div>
                </form>
            </div>

            <!-- Columna de resultados -->
            <div class="mfd-results-column">
                <div id="mfd-results">
                    <?php
                    $args = array(
                        'post_type' => 'departamento',
                        'posts_per_page' => -1,
                        'orderby' => 'date',
                        'order' => 'DESC'
                    );

                    $query = new WP_Query($args);

                    if ($query->have_posts()) {
                        // Contador de resultados fuera del grid
                        echo '<div class="mfd-results-count">';
                        echo sprintf(
                            _n('Se encontró %d departamento', 'Se encontraron %d departamentos', $query->found_posts, 'mfd'),
                            $query->found_posts
                        );
                        echo '</div>';
                        
                        echo '<div class="mfd-grid">';
                        while ($query->have_posts()) {
                            $query->the_post();
                            ob_start();
                            include plugin_dir_path(dirname(__FILE__)) . 'views/content-departamento.php';
                            echo ob_get_clean();
                        }
                        echo '</div>';
                        
                        // Paginación
                        if ($query->max_num_pages > 1) {
                            echo '<div class="mfd-pagination">';
                            for ($i = 1; $i <= $query->max_num_pages; $i++) {
                                echo sprintf(
                                    '<button class="mfd-page-button%s" data-page="%d">%d</button>',
                                    $i === 1 ? ' active' : '',
                                    $i,
                                    $i
                                );
                            }
                            echo '</div>';
                        }
                    } else {
                        echo '<p class="mfd-no-results">No se encontraron departamentos con los filtros seleccionados.</p>';
                    }
                    ?>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}

new MFD_Shortcode();
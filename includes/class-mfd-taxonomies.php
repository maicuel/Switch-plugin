<?php
class MFD_Taxonomies {

    public function __construct() {
        add_action( 'init', array( $this, 'registrar_taxonomias' ), 0 );
    }

    public function registrar_taxonomias() {
        $taxonomias = array(
            'tipo' => array(
                'label' => 'Tipo de Departamento',
                'terms' => array('roomie', 'personal', 'familiar')
            ),
            'amoblado' => array(
                'label' => 'Amoblado',
                'terms' => array('amoblado', 'semi_amoblado', 'no_amoblado')
            ),
            'vista' => array(
                'label' => 'Vista',
                'terms' => array('mar', 'ciudad', 'montaña', 'jardin')
            ),
            'disponibilidad' => array(
                'label' => 'Disponibilidad',
                'terms' => array('inmediata', 'fecha_exacta', 'pronto')
            )
        );

        foreach ( $taxonomias as $tax_slug => $tax_data ) {
            if ( taxonomy_exists( $tax_slug ) ) {
                unregister_taxonomy( $tax_slug );
            }

            $args = $this->get_taxonomy_args( $tax_slug, $tax_data['label'] );
            register_taxonomy( $tax_slug, 'departamento', $args );

            // Registrar términos por defecto
            foreach ( $tax_data['terms'] as $term ) {
                if ( ! term_exists( $term, $tax_slug ) ) {
                    wp_insert_term( $term, $tax_slug );
                }
            }
        }
    }

    private function get_taxonomy_args( $slug, $label ) {
        $labels = array(
            'name'              => $label,
            'singular_name'     => $label,
            'search_items'      => 'Buscar ' . $label,
            'all_items'         => 'Todos',
            'parent_item'       => 'Padre ' . $label,
            'parent_item_colon' => 'Padre ' . $label . ':',
            'edit_item'         => 'Editar ' . $label,
            'update_item'       => 'Actualizar ' . $label,
            'add_new_item'      => 'Añadir nuevo ' . $label,
            'new_item_name'     => 'Nuevo nombre de ' . $label,
            'menu_name'         => $label,
            'not_found'         => 'No se encontraron ' . $label . 's.',
        );

        return array(
            'labels'            => $labels,
            'hierarchical'      => true,
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'show_in_rest'      => true,
            'rewrite'           => array( 'slug' => sanitize_title( $slug ) ),
        );
    }
}

new MFD_Taxonomies();
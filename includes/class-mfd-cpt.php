<?php
class MFD_CPT {

    public function __construct() {
        add_action( 'init', array( $this, 'registrar_cpt' ) );
    }

    public function registrar_cpt() {
        $labels = array(
            'name'               => _x( 'Departamentos', 'post type general name' ),
            'singular_name'      => _x( 'Departamento', 'post type singular name' ),
            'menu_name'          => _x( 'Departamentos', 'admin menu' ),
            'add_new'            => _x( 'Añadir nuevo', 'departamento' ),
            'all_items'          => __( 'Todos los departamentos' ),
            'not_found'          => __( 'No se encontraron departamentos.' ),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'has_archive'        => true,
            'rewrite'            => array( 'slug' => 'departamento' ),
            'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
            'menu_icon'          => 'dashicons-building'
        );

        register_post_type( 'departamento', $args );
    }
}

new MFD_CPT();
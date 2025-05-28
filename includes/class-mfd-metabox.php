<?php
class MFD_Metabox {

    public function __construct() {
        add_action( 'add_meta_boxes', array( $this, 'agregar_metabox' ) );
        add_action( 'save_post', array( $this, 'guardar_datos' ) );
    }

    public function agregar_metabox() {
        add_meta_box(
            'mfd_datos_departamento',
            'Detalles del Departamento',
            array( $this, 'render_metabox' ),
            'departamento'
        );
    }

    public function render_metabox( $post ) {
        wp_nonce_field( 'mfd_guardar_datos', 'mfd_nonce' );

        $campos = array(
            '_disponibilidad',
            '_amoblado',
            '_tipo',
            '_habitaciones_banos',
            '_precio'
        );

        foreach ( $campos as $campo ) {
            $$campo = get_post_meta( $post->ID, $campo, true );
        }

        include plugin_dir_path( __FILE__ ) . '../views/metabox-template.php';
    }

    public function guardar_datos( $post_id ) {
        if ( ! isset( $_POST['mfd_nonce'] ) || ! wp_verify_nonce( $_POST['mfd_nonce'], 'mfd_guardar_datos' ) ) return;
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

        $campos = array( '_disponibilidad', '_amoblado', '_tipo', '_habitaciones_banos', '_precio' );
        foreach ( $campos as $campo ) {
            if ( isset( $_POST[ $campo ] ) ) {
                update_post_meta( $post_id, $campo, sanitize_text_field( $_POST[ $campo ] ) );
            }
        }
    }
}

new MFD_Metabox();
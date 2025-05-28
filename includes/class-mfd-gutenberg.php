<?php
class MFD_Gutenberg {

    public function __construct() {
        add_action( 'init', array( $this, 'registrar_bloque' ) );
    }

    public function registrar_bloque() {
        if ( ! function_exists( 'register_block_type' ) ) return;

        register_block_type( 'mfd/bloque-listado-departamentos', array(
            'editor_script' => 'mfd-gutenberg-block',
            'render_callback' => array( $this, 'render_bloque' )
        ));
    }

    public function render_bloque() {
        return do_shortcode( '[mostrar_departamentos]' );
    }
}

new MFD_Gutenberg();
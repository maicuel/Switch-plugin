<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Multifamily_Departamentos {

    public static function init() {
        $plugin = new self();

        // Cargar componentes
        add_action( 'plugins_loaded', array( $plugin, 'load_components' ) );
        add_action( 'wp_enqueue_scripts', array( $plugin, 'enqueue_assets' ) );
    }

    public function load_components() {
        require_once plugin_dir_path( __FILE__ ) . 'includes/class-mfd-cpt.php';
        require_once plugin_dir_path( __FILE__ ) . 'includes/class-mfd-metabox.php';
        require_once plugin_dir_path( __FILE__ ) . 'includes/class-mfd-shortcode.php';
        require_once plugin_dir_path( __FILE__ ) . 'includes/class-mfd-ajax.php';
        require_once plugin_dir_path( __FILE__ ) . 'includes/class-mfd-gutenberg.php';
        require_once plugin_dir_path( __FILE__ ) . 'includes/class-mfd-taxonomies.php';
        require_once plugin_dir_path( __FILE__ ) . 'includes/class-mfd-admin-ui.php';
        require_once plugin_dir_path( __FILE__ ) . 'includes/class-mfd-filters.php';
    }

    public function enqueue_assets() {
        wp_enqueue_style( 'mfd-estilo', plugins_url( 'assets/css/estilo.css', __FILE__ ), array(), '1.0' );
        wp_enqueue_script( 'mfd-filtrado', plugins_url( 'assets/js/filtrado.js', __FILE__ ), array( 'jquery' ), '1.0', true );
        wp_localize_script( 'mfd-filtrado', 'mfd_ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

        wp_enqueue_script( 'mfd-gutenberg-block', plugins_url( 'assets/js/gutenberg.js', __FILE__ ), array( 'wp-blocks', 'wp-element', 'wp-editor' ), '1.0', true );
    }
}
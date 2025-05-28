<?php
/*
Plugin Name: Multifamily Departamentos v2
Description: Plugin nativo para gestionar departamentos en arriendo. Compatible con Divi y Gutenberg.
Version: 1.0
Author: Tu Nombre
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require_once plugin_dir_path( __FILE__ ) . 'class-multifamily-plugin.php';

// Inicializa el plugin
Multifamily_Departamentos::init();
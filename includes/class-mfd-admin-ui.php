<?php
class MFD_Admin_UI {

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'agregar_menu_admin' ) );
    }

    public function agregar_menu_admin() {
        add_menu_page(
            'Departamentos',
            'Departamentos',
            'manage_options',
            'mfd-departamentos',
            array( $this, 'render_pagina_listado' ),
            'dashicons-building',
            25
        );

        add_submenu_page(
            'mfd-departamentos',
            'Listado de Departamentos',
            'Listado',
            'manage_options',
            'mfd-departamentos',
            array( $this, 'render_pagina_listado' )
        );

        add_submenu_page(
            'mfd-departamentos',
            'Gestionar Taxonomías',
            'Taxonomías',
            'manage_options',
            'mfd-taxonomias',
            array( $this, 'render_pagina_taxonomias' )
        );
    }

    public function render_pagina_listado() {
        ?>
        <div class="wrap">
            <h1>Departamentos</h1>
            <form method="post" action="">
                <?php wp_nonce_field( 'mfd_edicion_masiva', 'mfd_nonce' ); ?>

                <select name="accion">
                    <option value="">Seleccionar acción...</option>
                    <option value="disponibilidad">Actualizar disponibilidad</option>
                    <option value="amoblado">Actualizar amoblado</option>
                    <option value="tipo">Actualizar tipo</option>
                    <option value="precio">Actualizar precio</option>
                </select>

                <input type="number" name="porcentaje" placeholder="% Incremento" style="display:none" id="campo-precio">

                <input type="submit" name="submit" class="button button-primary" value="Aplicar a seleccionados">
                <br><br>

                <table class="wp-list-table widefat fixed striped">
                    <thead><tr>
                        <th><input type="checkbox" id="seleccionar-todos"></th>
                        <th>ID</th><th>Título</th><th>Fecha</th><th>Disponibilidad</th><th>Precio</th><th>Amoblado</th><th>Tipo</th><th>Habitaciones</th>
                    </tr></thead><tbody>
                    <?php
                    $query = new WP_Query( array( 'post_type' => 'departamento', 'posts_per_page' => -1 ) );
                    while ( $query->have_posts() ): $query->the_post(); 
                        $id = get_the_ID();
                        $disponibilidad = get_post_meta( $id, '_disponibilidad', true );
                        $precio = get_post_meta( $id, '_precio', true );
                        $amoblado = get_post_meta( $id, '_amoblado', true );
                        $tipo = get_post_meta( $id, '_tipo', true );
                        $habitaciones_banos = get_post_meta( $id, '_habitaciones_banos', true );
                    ?>
                        <tr>
                            <td><input type="checkbox" name="ids[]" value="<?php echo esc_attr( $id ); ?>"></td>
                            <td><?php echo esc_html( $id ); ?></td>
                            <td><strong><a href="<?php echo esc_url( get_edit_post_link() ); ?>"><?php the_title(); ?></a></strong></td>
                            <td><?php the_date(); ?></td>
                            <td><?php echo esc_html( ucfirst( $disponibilidad ?: '-' )) ?></td>
                            <td><?php echo esc_html( $precio ?: '-' ) ?></td>
                            <td><?php echo esc_html( ucfirst( str_replace('_', ' ', $amoblado) ?: '-' )) ?></td>
                            <td><?php echo esc_html( ucfirst( $tipo ?: '-' ) )?></td>
                            <td><?php echo esc_html( strtoupper( str_replace('d', 'Dormitorio(s), ', $habitaciones_banos) ?: '-' )) ?></td>
                        </tr>
                    <?php endwhile; wp_reset_postdata(); ?>
                    </tbody></table>
                </form>
            </div>
        <?php
    }

    public function render_pagina_taxonomias() {
        $taxonomias = array(
            'piso' => 'Piso',
            'tipo' => 'Tipo',
            'capacidad' => 'Capacidad',
            'ambiente' => 'Ambiente',
            'vista' => 'Vista',
            'terraza' => 'Terraza',
            'amoblado' => 'Amoblado'
        );
        ?>
        <div class="wrap">
            <h1>Gestionar Taxonomías</h1>
            <table class="wp-list-table widefat fixed striped">
                <thead><tr>
                    <th>Taxonomía</th><th>Cantidad</th><th>Acciones</th>
                </tr></thead><tbody>
                <?php foreach ($taxonomias as $slug => $nombre): 
                    $terms = get_terms(['taxonomy' => $slug, 'hide_empty' => false]);
                    $count = is_array($terms) ? count($terms) : 0;
                ?>
                    <tr>
                        <td><?php echo esc_html( $nombre ); ?></td>
                        <td><?php echo esc_html( $count ); ?> término(s)</td>
                        <td><a href="<?php echo admin_url( 'edit-tags.php?taxonomy=' . $slug . '&post_type=departamento' ); ?>">Administrar</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody></table>
            </div>
        <?php
    }
}

new MFD_Admin_UI();
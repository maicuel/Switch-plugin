<?php
// Check that we have $post and the meta fields
$post = isset( $post ) ? $post : null;
$disponibilidad = isset( $disponibilidad ) ? $disponibilidad : '';
$amoblado = isset( $amoblado ) ? $amoblado : '';
$tipo = isset( $tipo ) ? $tipo : '';
$habitaciones_banos = isset( $habitaciones_banos ) ? $habitaciones_banos : '';
$precio = isset( $precio ) ? $precio : '';
?>

<table class="form-table">
    <tr>
        <th><label for="_disponibilidad">Disponibilidad</label></th>
        <td>
            <select name="_disponibilidad" id="_disponibilidad">
                <option value="">Seleccionar...</option>
                <option value="inmediata" <?php selected( $disponibilidad, 'inmediata' ); ?>>Inmediata</option>
                <option value="fecha_exacta" <?php selected( $disponibilidad, 'fecha_exacta' ); ?>>Fecha Exacta</option>
                <option value="pronto" <?php selected( $disponibilidad, 'pronto' ); ?>>Pronto</option>
            </select>
        </td>
    </tr>
    <tr>
        <th><label for="_amoblado">Amoblado</label></th>
        <td>
            <select name="_amoblado" id="_amoblado">
                <option value="">Seleccionar...</option>
                <option value="amoblado" <?php selected( $amoblado, 'amoblado' ); ?>>Amoblado</option>
                <option value="semi_amoblado" <?php selected( $amoblado, 'semi_amoblado' ); ?>>Semi amoblado</option>
                <option value="no_amoblado" <?php selected( $amoblado, 'no_amoblado' ); ?>>No amoblado</option>
            </select>
        </td>
    </tr>
    <tr>
        <th><label for="_tipo">Tipo de Departamento</label></th>
        <td>
            <select name="_tipo" id="_tipo">
                <option value="">Seleccionar...</option>
                <option value="roomie" <?php selected( $tipo, 'roomie' ); ?>>Roomie</option>
                <option value="personal" <?php selected( $tipo, 'personal' ); ?>>Personal</option>
                <option value="familiar" <?php selected( $tipo, 'familiar' ); ?>>Familiar</option>
            </select>
        </td>
    </tr>
    <tr>
        <th><label for="_habitaciones_banos">Habitaciones y Baños</label></th>
        <td>
            <select name="_habitaciones_banos" id="_habitaciones_banos">
                <option value="">Seleccionar...</option>
                <option value="1d1b" <?php selected( $habitaciones_banos, '1d1b' ); ?>>1 Dormitorio 1 Baño</option>
                <option value="2d2b" <?php selected( $habitaciones_banos, '2d2b' ); ?>>2 Dormitorios 2 Baños</option>
                <option value="3d2b" <?php selected( $habitaciones_banos, '3d2b' ); ?>>3 Dormitorios 2 Baños</option>
            </select>
        </td>
    </tr>
    <tr>
        <th><label for="_precio">Precio Mensual</label></th>
        <td>
            <input type="number" name="_precio" id="_precio" value="<?php echo esc_attr( $precio ); ?>" style="width: 100%">
        </td>
    </tr>
</table>
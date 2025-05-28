<?php
/**
 * Template para mostrar un departamento individual
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('mfd-departamento'); ?>>
    <?php if (has_post_thumbnail()) : ?>
        <div class="mfd-departamento-imagen">
            <?php the_post_thumbnail('medium_large', array('class' => 'mfd-imagen-destacada')); ?>
        </div>
    <?php endif; ?>

    <div class="mfd-departamento-contenido">
        <div class="mfd-departamento-info">
            <?php
            // Obtener el tipo y disponibilidad
            $tipo = get_the_terms(get_the_ID(), 'tipo');
            $disponibilidad = get_the_terms(get_the_ID(), 'disponibilidad');
            
            if ($tipo && !is_wp_error($tipo)) {
                echo '<div class="mfd-tipo">' . esc_html($tipo[0]->name) . '</div>';
            }
            
            if ($disponibilidad && !is_wp_error($disponibilidad)) {
                echo '<div class="mfd-disponibilidad">' . esc_html($disponibilidad[0]->name) . '</div>';
            }
            ?>
        </div>

        <div class="mfd-departamento-footer">
            <?php
            $precio_mensual = get_post_meta(get_the_ID(), 'precio_mensual', true);
            if ($precio_mensual) {
                echo '<div class="mfd-departamento-precio">';
                echo '<span class="mfd-precio-label">Arriendo</span>';
                echo '<span class="mfd-precio-valor">$' . number_format($precio_mensual, 0, ',', '.') . '</span>';
                echo '</div>';
            }
            ?>
            <a href="<?php the_permalink(); ?>" class="mfd-departamento-link">Ver más</a>
        </div>
    </div>
</article> 
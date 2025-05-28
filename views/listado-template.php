<?php if ( $query->have_posts() ): while ( $query->have_posts() ): $query->the_post(); ?>
    <div class="departamento-item">
        <h3><?php the_title(); ?></h3>
        <div><?php the_excerpt(); ?></div>
        <p><strong>Precio:</strong> <?php echo esc_html( get_post_meta( get_the_ID(), '_precio', true ))?: '-' ?></p>
        <p><strong>Disponibilidad:</strong> <?php echo esc_html( get_post_meta( get_the_ID(), '_disponibilidad', true )) ?: '-' ?></p>
        <p><strong>Tipo:</strong> <?php echo esc_html( get_post_meta( get_the_ID(), '_tipo', true )) ?: '-' ?></p>
        <a href="<?php the_permalink(); ?>">Ver detalles</a>
    </div>
<?php endwhile; else: ?>
    <p>No hay departamentos disponibles.</p>
<?php endif; wp_reset_postdata(); ?>
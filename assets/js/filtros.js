jQuery(document).ready(function($) {
    const $form = $('#mfd-filter-form');
    const $results = $('#mfd-results');
    let isFirstLoad = true;
    let currentPage = 1;
    let filterTimeout;

    // Función para filtrar departamentos
    function filtrarDepartamentos(page = 1) {
        const formData = new FormData($form[0]);
        formData.append('action', 'mfd_filtrar_departamentos');
        formData.append('nonce', mfd_ajax.nonce);
        formData.append('paged', page);

        // Mostrar loading solo después de la primera carga
        if (!isFirstLoad) {
            $results.addClass('loading');
        }

        $.ajax({
            url: mfd_ajax.ajaxurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    // Actualizar el contenido de los resultados
                    $results.html(response.data.html);
                    currentPage = response.data.current_page;
                    
                    // Actualizar URL con los filtros actuales
                    updateURL();
                } else {
                    $results.html('<p class="mfd-no-results">Error al cargar los resultados.</p>');
                }
                isFirstLoad = false;
                $results.removeClass('loading');
            },
            error: function() {
                $results.html('<p class="mfd-no-results">Error al cargar los resultados.</p>');
                isFirstLoad = false;
                $results.removeClass('loading');
            }
        });
    }

    // Función para actualizar la URL con los filtros actuales
    function updateURL() {
        const formData = new FormData($form[0]);
        const params = new URLSearchParams();
        
        for (let [key, value] of formData.entries()) {
            if (value) {
                params.append(key, value);
            }
        }
        
        const newUrl = window.location.pathname + '?' + params.toString();
        window.history.pushState({}, '', newUrl);
    }

    // Cargar resultados iniciales
    filtrarDepartamentos();

    // Manejar cambios en los selects
    $form.find('select').on('change', function() {
        currentPage = 1;
        // Limpiar timeout anterior si existe
        if (filterTimeout) {
            clearTimeout(filterTimeout);
        }
        // Establecer nuevo timeout para evitar múltiples llamadas
        filterTimeout = setTimeout(function() {
            filtrarDepartamentos(currentPage);
        }, 300);
    });

    // Manejar envío del formulario
    $form.on('submit', function(e) {
        e.preventDefault();
        currentPage = 1;
        filtrarDepartamentos(currentPage);
    });

    // Manejar reset del formulario
    $form.on('reset', function(e) {
        e.preventDefault();
        $form.find('select').val('');
        currentPage = 1;
        filtrarDepartamentos(currentPage);
    });

    // Manejar clic en botones de paginación
    $(document).on('click', '.mfd-page-button', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        if (page !== currentPage) {
            currentPage = page;
            filtrarDepartamentos(page);
            // Scroll suave hacia arriba
            $('html, body').animate({
                scrollTop: $results.offset().top - 100
            }, 500);
        }
    });

    // Manejar clic en reset de filtros
    $(document).on('click', '.mfd-reset-filters', function(e) {
        e.preventDefault();
        $form.find('select').val('');
        currentPage = 1;
        filtrarDepartamentos(currentPage);
    });

    // Cargar filtros desde la URL al cargar la página
    function loadFiltersFromURL() {
        const params = new URLSearchParams(window.location.search);
        for (let [key, value] of params.entries()) {
            const $input = $form.find(`[name="${key}"]`);
            if ($input.length) {
                $input.val(value);
            }
        }
    }

    // Cargar filtros desde la URL
    loadFiltersFromURL();

    // Filtrado automático para inputs numéricos
    $form.find('input[type="number"]').on('input', function() {
        filtrarDepartamentos();
    });

    // Validación de precios
    $('#precio-min, #precio-max').on('input', function() {
        const min = parseInt($('#precio-min').val()) || 0;
        const max = parseInt($('#precio-max').val()) || 0;

        if (max > 0 && min > max) {
            $(this).val('');
            alert('El precio mínimo no puede ser mayor que el precio máximo');
        }
    });
}); 
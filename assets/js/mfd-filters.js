jQuery(document).ready(function($) {
    const $filterForm = $('#mfd-filter-form');
    const $resultsContainer = $('#mfd-results');
    const $pagination = $('#mfd-pagination');
    let isFirstLoad = true;
    let currentPage = 1;
    let filterTimeout;

    function loadResults(page = 1) {
        const formData = new FormData($filterForm[0]);
        formData.append('action', 'mfd_filtrar_departamentos');
        formData.append('nonce', mfd_ajax.nonce);
        formData.append('paged', page);

        if (!isFirstLoad) {
            $resultsContainer.addClass('loading');
        }

        $.ajax({
            url: mfd_ajax.ajaxurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $resultsContainer.html(response.data.html);
                    currentPage = response.data.current_page;
                    updateURL();
                } else {
                    $resultsContainer.html('<p class="mfd-no-results">Error al cargar los resultados.</p>');
                }
                isFirstLoad = false;
                $resultsContainer.removeClass('loading');
            },
            error: function() {
                $resultsContainer.html('<p class="mfd-no-results">Error al cargar los resultados.</p>');
                isFirstLoad = false;
                $resultsContainer.removeClass('loading');
            }
        });
    }

    function updateURL() {
        const formData = new FormData($filterForm[0]);
        const params = new URLSearchParams();
        
        for (let [key, value] of formData.entries()) {
            if (value) {
                params.append(key, value);
            }
        }
        
        const newUrl = window.location.pathname + '?' + params.toString();
        window.history.pushState({}, '', newUrl);
    }

    // Event Listeners
    $filterForm.on('change', 'select, input[type="checkbox"], input[type="number"]', function() {
        currentPage = 1;
        if (filterTimeout) {
            clearTimeout(filterTimeout);
        }
        filterTimeout = setTimeout(function() {
            loadResults(currentPage);
        }, 300);
    });

    $filterForm.on('submit', function(e) {
        e.preventDefault();
        currentPage = 1;
        loadResults(currentPage);
    });

    $filterForm.on('reset', function(e) {
        e.preventDefault();
        $form.find('select').val('');
        currentPage = 1;
        loadResults(currentPage);
    });

    $(document).on('click', '.mfd-page-button', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        if (page !== currentPage) {
            currentPage = page;
            loadResults(page);
            $('html, body').animate({
                scrollTop: $resultsContainer.offset().top - 100
            }, 500);
        }
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

    // Cargar filtros desde la URL
    function loadFiltersFromURL() {
        const params = new URLSearchParams(window.location.search);
        for (let [key, value] of params.entries()) {
            const $input = $filterForm.find(`[name="${key}"]`);
            if ($input.length) {
                $input.val(value);
            }
        }
    }

    loadFiltersFromURL();
    loadResults();
});
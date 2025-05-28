jQuery(document).ready(function($) {
    const $filterForm = $('#mfd-filter-form');
    const $resultsContainer = $('#mfd-results');
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
                    $resultsContainer.html(`<p class="mfd-no-results">${mfd_ajax.i18n.error_load}</p>`);
                }
                isFirstLoad = false;
            },
            error: function() {
                $resultsContainer.html(`<p class="mfd-no-results">${mfd_ajax.i18n.error_load}</p>`);
                isFirstLoad = false;
            },
            complete: function() {
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
        
        const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
        window.history.pushState({}, '', newUrl);
    }

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    const debouncedLoad = debounce(loadResults, 300);

    // Event Listeners
    $filterForm.on('change', 'select, input[type="checkbox"], input[type="number"]', function() {
        currentPage = 1;
        debouncedLoad(currentPage);
    });

    $filterForm.on('submit', function(e) {
        e.preventDefault();
        currentPage = 1;
        loadResults(currentPage);
    });

    $filterForm.on('reset', function(e) {
        e.preventDefault();
        this.reset();
        currentPage = 1;
        loadResults(currentPage);
    });

    $(document).on('click', '.mfd-page-button', function(e) {
        e.preventDefault();
        const page = parseInt($(this).data('page'), 10);
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
            alert(mfd_ajax.i18n.price_error);
        }
    });

    // Cargar filtros desde la URL y resultados iniciales
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
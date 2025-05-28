jQuery(document).ready(function($) {
    const $filterForm = $('#mfd-filter-form');
    const $resultsContainer = $('#mfd-results');
    const $pagination = $('#mfd-pagination');
    let currentPage = 1;

    function loadResults(page = 1) {
        const formData = new FormData($filterForm[0]);
        formData.append('action', 'mfd_filter_posts');
        formData.append('nonce', mfd_filters.nonce);
        formData.append('paged', page);

        $.ajax({
            url: mfd_filters.ajaxurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $resultsContainer.addClass('loading');
            },
            success: function(response) {
                if (response.success) {
                    $resultsContainer.html(response.html);
                    updatePagination(response.max_pages, page);
                }
            },
            complete: function() {
                $resultsContainer.removeClass('loading');
            }
        });
    }

    function updatePagination(maxPages, currentPage) {
        if (maxPages <= 1) {
            $pagination.hide();
            return;
        }

        $pagination.show();
        let paginationHtml = '<div class="mfd-pagination">';
        
        if (currentPage > 1) {
            paginationHtml += `<button class="prev-page" data-page="${currentPage - 1}">Anterior</button>`;
        }

        for (let i = 1; i <= maxPages; i++) {
            if (i === currentPage) {
                paginationHtml += `<span class="current-page">${i}</span>`;
            } else {
                paginationHtml += `<button class="page-number" data-page="${i}">${i}</button>`;
            }
        }

        if (currentPage < maxPages) {
            paginationHtml += `<button class="next-page" data-page="${currentPage + 1}">Siguiente</button>`;
        }

        paginationHtml += '</div>';
        $pagination.html(paginationHtml);
    }

    // Event Listeners
    $filterForm.on('change', 'select, input[type="checkbox"]', function() {
        currentPage = 1;
        loadResults(currentPage);
    });

    $pagination.on('click', 'button', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        currentPage = page;
        loadResults(page);
    });

    // Cargar resultados iniciales
    loadResults();
}); 
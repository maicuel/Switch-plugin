jQuery(document).ready(function($) {
    $('#mfd-formulario-filtrado').on('submit', function(e) {
        e.preventDefault();

        var formData = $(this).serialize();

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: formData + '&action=mfd_filtrar',
            success: function(response) {
                $('#resultado-filtrado').html($(response).find('.lista-departamentos').html());
            }
        });
    });
});
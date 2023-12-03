jQuery(document).ready(function($) {
    $('#new-vettore-form').submit(function(e) {
        e.preventDefault();
        var nomeVettore = $('#nome_vettore').val();

        $.ajax({
            type: 'POST',
            url: gv_ajax_object.ajax_url,
            data: {
                'action': 'add_vettore',
                'nome_vettore': nomeVettore
            },
            success: function(response) {
                alert(response);
                location.reload();
            }
        });
    });

    $(document).on('click', '.delete-vettore', function(e) {
        e.preventDefault();
        var vettoreId = $(this).data('id');
        var confirmDelete = confirm("Sei sicuro di voler eliminare questo vettore?");
        if (confirmDelete) {
            $.ajax({
                type: 'POST',
                url: gv_ajax_object.ajax_url,
                data: {
                    'action': 'delete_vettore',
                    'id': vettoreId
                },
                success: function(response) {
                    alert(response);
                    location.reload();
                }
            });
        }
    });

    $('#new-ordine-form').submit(function(e) {
        e.preventDefault();
        var numeroOrdine = $('#numero_ordine').val();
        var dataOrdine = $('#data_ordine').val();
        var dataConsegna = $('#data_consegna').val() || dataOrdine;
        var idVettore = $('#id_vettore').val();

        $.ajax({
            type: 'POST',
            url: gv_ajax_object.ajax_url,
            data: {
                'action': 'add_ordine',
                'numero_ordine': numeroOrdine,
                'data_ordine': dataOrdine,
                'data_consegna': dataConsegna,
                'id_vettore': idVettore
            },
            success: function(response) {
                alert('Ordine inserito con successo!');
                location.reload();
            }
        });
    });

    $(document).on('click', '.delete-ordine', function(e) {
        e.preventDefault();
        var ordineId = $(this).data('id');
        var confirmDelete = confirm("Sei sicuro di voler eliminare questo ordine?");
        if (confirmDelete) {
            $.ajax({
                type: 'POST',
                url: gv_ajax_object.ajax_url,
                data: {
                    'action': 'delete_ordine',
                    'id': ordineId
                },
                success: function(response) {
                    alert(response);
                    location.reload();
                }
            });
        }
    });

    $(document).on('click', '.update-ordine', function(e) {
        e.preventDefault();
        var ordineId = $(this).data('id');
        var dataConsegna = $(this).closest('tr').find('.data-consegna').val();

        $.ajax({
            type: 'POST',
            url: gv_ajax_object.ajax_url,
            data: {
                'action': 'update_ordine',
                'id': ordineId,
                'data_consegna': dataConsegna
            },
            success: function(response) {
                alert(response);
                location.reload();
            }
        });
    });
});

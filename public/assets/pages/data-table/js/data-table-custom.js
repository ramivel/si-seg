$(document).ready(function() {

    $('#tabla-listado').DataTable({
        order: [],
        columnDefs: [
            { width: 200, targets: 0 }
        ],
        fixedColumns: false,
        pageLength: 50,
        lengthMenu: [[50, 100, -1], [50, 100, "All"]],
        language: {
            "url": "//cdn.datatables.net/plug-ins/1.11.3/i18n/es-mx.json"
        },
        'aoColumnDefs': [{
            'bSortable': false,
            'aTargets': ['nosort']
        }]
    });

    $('#tabla-buscador').DataTable({
        order: [],
        pageLength: 50,
        lengthMenu: [[50, 100, -1], [50, 100, "All"]],
        language: {
            "url": "//cdn.datatables.net/plug-ins/1.11.3/i18n/es-mx.json"
        },
        searching: false,
        'aoColumnDefs': [{
            'bSortable': false,
            'aTargets': ['nosort']
        }]
    });

});
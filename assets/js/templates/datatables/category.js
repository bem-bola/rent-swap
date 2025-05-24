import * as dt from "./datatable.js";

document.addEventListener('DOMContentLoaded', e => {

    const createDatatable = () => {
        if(dt.info === null || (dt.info.currentRoute !== 'app_admin_category_all' && dt.info.currentRoute !== 'app_admin_category_update')) return;
        const table =  $(`#${dt.info.id}`).DataTable({
            serverSide: true,
            processing: true,
            autoWidth: true,
            searching: false,
            language: {
                lengthMenu: "_MENU_",
                zeroRecords: "Aucun résultat trouvé",
                info: "Affichage de _START_ à _END_ sur _TOTAL_ entrées",
                infoEmpty: "Aucune donnée disponible",
                infoFiltered: "(filtré de _MAX_ entrées totales)",
                search: "Rechercher un appareil:",
                paginate: {
                    first: "Premier",
                    last: "Dernier",
                    next: "Suivant",
                    previous: "Précédent"
                },
                loadingRecords: "Chargement...",
                processing: "Traitement en cours...",
            },
            ajax: {
                url: Routing.generate(dt.info.pathname),
                type: 'GET',
                data: function (d) {
                    const inputs = document.querySelectorAll('.table .datatable-filters')

                    const search = {}

                    inputs.forEach(input => {
                        if (input.value != null) {
                            search[input.name] = input.value
                        }
                    })

                    // Tu peux ici transformer les données envoyées
                    return {
                        draw: d.draw,
                        page: Math.floor(d.start / d.length) + 1, // DataTables utilise start/length
                        limit: d.length,
                        offset: d.start,
                        order: d.order,
                        columns: d.columns,
                        search: search
                    };
                }
            },
            columns: [
                {
                    data: 'name',
                    orderable: false,
                    name: 'c.name',
                },
                {
                    name: 'action',
                    className: 'text-center',
                    render: (data, type, row) => {
                        return `<a href="${Routing.generate('app_admin_category_update', {slug: row.slug})}" class="text-center" title="Modifier/valider/rejette l'annonce"><i class="bi bi-pencil-square fs-3"></i></a>
`
                    }
                },

            ]
        });


        dt.reloadFilters(table)

    }
    createDatatable()

})
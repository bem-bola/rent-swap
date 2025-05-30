import * as dt from "./datatable.js";

document.addEventListener('DOMContentLoaded', e => {

    const createDatatable = () => {
        if(dt.info === null || dt.info.currentRoute !== 'app_admin_devices_all') return;
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
                    data: 'title',
                    orderable: false,
                    name: 'title',
                },
                {
                    data: 'user',
                    orderable: false,

                    name: 'user',
                    render: data => {
                        return `<a href='${Routing.generate('app_admin_users_show', {id: data.id})}' class="">${data.firstname} ${data.lastname}</a>`
                    }
                },
                {
                    data: 'price',
                    name: 'price',
                    orderable: false,
                    className: 'text-end',
                    render: data => {
                        return dt.formattedMonary(data)
                    }
                },
                {
                    data: 'status',
                    orderable: false,
                    name: 'status',
                    render: data => {

                        return `<span class="${dt.status(data).color} ">${dt.status(data).status}</span>`
                    }
                },
                {
                    data: 'location',
                },
                {
                    data: 'created',
                    name: 'created',
                    render: data => {
                        return dt.formattedDate(data)
                    }
                },
                {
                    data: 'updated',
                    name: 'updated',
                    render: data => {
                        return dt.formattedDate(data)
                    }
                },
                {
                    name: 'action',
                    className: 'text-center',
                    render: (data, type, row) => {
                        return `<a href="${Routing.generate('app_admin_devices_show', {slug: row.slug})}" class="text-center" title="Modifier/valider/rejette l'annonce"><i class="fa fa-peace"></i></a>`
                    }
                },

            ]
        });


        dt.reloadFilters(table)

    }
    createDatatable()

})
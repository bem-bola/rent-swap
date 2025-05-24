import * as dt from "./datatable.js";

document.addEventListener('DOMContentLoaded', e => {

    const createDatatables = () => {
        if(dt.info !== null && (dt.info.currentRoute === 'app_admin_warn_message' || dt.info.currentRoute ===  'app_admin_warn_message_show')) {
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
                    url: Routing.generate(dt.info.pathname, dt.info.paramRoute),
                    type: 'GET',
                    data: (d) => {
                        const params = new URLSearchParams(window.location.search);

                        const inputs = document.querySelectorAll('.table .datatable-filters')

                        const search = {}

                        inputs.forEach(input => {
                            if (input.value != null) {
                                search[input.name] = input.value
                            }
                        })

                        return {
                            draw: d.draw,
                            page: Math.floor(d.start / d.length) + 1, // DataTables utilise start/length
                            limit: d.length,
                            offset: d.start,
                            order: d.order,
                            columns: d.columns,
                            search: search,
                            reviewed: params.get('reviewed') ? params.get('reviewed') : 0
                        };
                    }
                },
                columns: [
                    {
                        data: 'informant',
                        name: 'user.username',
                        orderable: false,
                        render: data => {
                            return `<a href="${Routing.generate('app_admin_users_show', {id: data.id})}" class="text-center">${data.username}</a>`
                        }
                    },
                    {
                        data: 'message',
                        name: 'author.username',
                        orderable: false,
                        render: data => {
                            return `<a href="${Routing.generate('app_admin_users_show', {id: data.author.id})}" class="text-center" >${data.author.username}</a>`
                        }
                    },
                    {
                        data: 'message',
                        name: 'm.content',
                        orderable: false,
                        render: data => {
                            return data.content
                        }
                    },
                    {
                        data: 'created',
                        name: 'created',
                        orderable: false,
                        render: data => {
                            return dt.formattedDate(data)
                        }
                    },
                    {
                        data: 'reviewer',
                        name: 'reviewer.lastname',
                        orderable: false,
                        render: data => {console.log(data)
                            return data !== null ? data.lastname : '';
                        }
                    },


                    {
                        data: 'reviewed',
                        name: 'reviewed',
                        render: data => {
                            return dt.formattedDate(data)
                        }
                    },
                    {
                        name: 'action',
                        orderable: false,
                        className: 'text-center',
                        render: (data, type, row) => {
                            return `<a href="${Routing.generate('app_admin_warn_message_show', {id: row.id})}" class="text-center" title="Modifier/valider/"><i class="bi bi-pencil-square"></i></a>`
                        }
                    },

                ]
            });

            dt.reloadFilters(table)
        }
    }
    createDatatables()

})
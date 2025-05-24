import * as dt from "./datatable.js";

document.addEventListener('DOMContentLoaded', e => {

    const createDatatables = () => {
        if(dt.info !== null && dt.info.currentRoute === 'app_admin_users_all') {
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
                            search: search
                        };
                    }
                },
                columns: [
                    {
                        data: 'username',
                        name: 'c.username',
                        orderable: false
                    },
                    {
                        data: 'firstname',
                        name: 'c.firstname',
                        orderable: false
                    },
                    {
                        data: 'lastname',
                        name: 'c.lastname',
                        orderable: false
                    },
                    {
                        data: 'status',
                        name: 'u.status',
                        orderable: false,
                        render: (data, type, row) => {

                            if(row.isDeleted === true){
                                return `<span class="${dt.status('deleted').color} ">${dt.status('deleted').status}</span>`
                            }

                            if(row.isBanned === true){
                                return `<span class="${dt.status('banned').color} ">${dt.status('banned').status}</span>`
                            }
                            if(row.isSuspended === true){
                                return `<span class="${dt.status('suspended').color} ">${dt.status('suspended').status}</span>`
                            }
                            if(row.isVerified === true){
                                return `<span class="${dt.status('validated').color} ">${dt.status('validated').status}</span>`
                            }
                            if(row.isVerified !== true){
                                return `<span class="${dt.status('noValid').color} ">${dt.status('noValid').status}</span>`
                            }

                        }
                    },
                    {
                        data: 'created',
                        name: 'c.created',
                        orderable: false,
                        render: data => {
                            return dt.formattedDate(data)
                        }
                    },
                    {
                        data: 'banned',
                        name: 'c.banned',
                        orderable: false,
                        render: data => {
                            return dt.formattedDate(data)
                        }
                    },
                    {
                        name: 'action',
                        orderable: false,
                        className: 'text-center',
                        render: (data, type, row) => {
                            return `<a href="${Routing.generate('app_admin_users_show', {id: row.id})}" class="text-center" title="Modifier/valider/rejette l'annonce"><i class="fa fa-peace"></i></a>`
                        }
                    },

                ]
            });

            dt.reloadFilters(table)
        }
    }
    createDatatables()

})
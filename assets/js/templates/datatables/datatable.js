// Les info des datatble (id, nom de la route pour ajax)
const infodatatable = () => {
    const dataTableInfo = document.querySelector("input[name='datatable-info']")

    if(!dataTableInfo) return null;

    return {
        id: dataTableInfo.dataset.id,
        pathname: dataTableInfo.dataset.pathname,
        currentRoute: dataTableInfo.dataset.currentRoute,
        paramRoute: dataTableInfo.dataset.paramsRoute !== '' ?
            JSON.parse(dataTableInfo.dataset.paramsRoute) : ''
    }
}
export const info = infodatatable()

// Tranformer un nombre au format monetaire (ex 1 000,92 pour 1000.92)
export const formattedMonary = montant =>{
    return new Intl.NumberFormat('fr-FR', {
            style: 'currency',
            currency: 'EUR'
        }).format(montant);
}

// en fonction de status
export const status = (data) => {

    const statusMap = {
        pending:   { color: 'text-warning', status: 'En attente' },
        validated: { color: 'text-success', status: 'Validé' },
        rejected:  { color: 'text-danger', status: 'Rejeté' },
        draft:     { color: 'text-info', status: 'Brouillon' }
    };

    return statusMap[data] || statusMap['pending']
}

export const formattedDate = (isoDate) => {

    if(isoDate === null) return '';
    const date = new Date(isoDate);

    return date.toLocaleString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        hour12: false,
        timeZone: 'Europe/Paris' // important pour le fuseau
    });
}


export const reloadFilters = table => {
    const  filters = document.querySelectorAll('.table .datatable-filters')
    if(!filters) return;

    filters.forEach(filter => {
        filter.addEventListener('change', () => {
            setTimeout(() => {
                table.ajax.reload();
            }, 1500)

            const url = new URL(window.location);
            url.searchParams.delete('status');

            window.history.replaceState({}, '', url);
        })
    })
}






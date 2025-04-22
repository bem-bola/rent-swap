import TomSelect from 'tom-select';
import 'tom-select/dist/css/tom-select.bootstrap5.css';

const selectCategory = () => {
    const selectElement = document.querySelector('#select-category');
    const categoriesDefault = document.querySelector('#default-categories');

    if (selectElement) {

        const defaultSelectedCategories = categoriesDefault ? JSON.parse(categoriesDefault.dataset.categories) : [];

        new TomSelect('#select-category', {
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            create: false,
            persist: true,
            dropdownParent: 'body',
            hideSelected: true,
            closeAfterSelect: true,
            openOnFocus: true,
            options: defaultSelectedCategories,
            items: defaultSelectedCategories !== [] ? defaultSelectedCategories.map(cat => cat.id) : [],
            load: (query, callback) => {
                if (!query.length) return callback();

                fetch(Routing.generate("app_category_search", { q: encodeURIComponent(query)}))
                    .then(response => response.json())
                    .then(data => callback(data))
                    .catch(() => callback());
            },
            plugins: ['remove_button']
        });
    }
}

document.addEventListener("DOMContentLoaded", () => {
    selectCategory()
})


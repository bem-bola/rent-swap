document.addEventListener('DOMContentLoaded', () => {
    const favoriteAction = async () => {
        const btnFavorite = document.getElementById("btn-favorite");

        if (!btnFavorite) return;

        const icon = btnFavorite.querySelector("i");
        const slug = btnFavorite.dataset.slug; // Utilisation de dataset pour une meilleure organisation

        btnFavorite.addEventListener("click", async (e) => {
            e.preventDefault();

            if (!slug) {
                return;
            }

            const url = Routing.generate("app_favorite_add_remove", { slug });

            btnFavorite.disabled = true; // Désactive le bouton pendant la requête

            try {
                const response = await fetch(url, {
                    method: "POST",
                    headers: {
                        Accept: "application/json",
                    },
                });

                if (!response.ok) {
                    throw new Error(`Erreur HTTP : ${response.status}`);
                }

                const json = await response.json();

                // Mise à jour des classes de l'icône
                icon.classList.toggle('bi-heart-fill', json.favorite)
                icon.classList.toggle('color-theme-yellow', json.favorite)
                icon.classList.toggle('bi-heart', !json.favorite)
                icon.classList.toggle('text-dark', !json.favorite)

            } catch (error) {
            } finally {
                btnFavorite.disabled = false; // Réactive le bouton après la requête
            }
        });
    };

    favoriteAction()
})
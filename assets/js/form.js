const regexMap = {
    email: /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/,
    password: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@#$%^&*!])[A-Za-z\d@#$%^&*!]{8,}$/,
    name: /^[A-Za-zÀ-ÖØ-öø-ÿ\s'-]{2,}$/, // Supporte les espaces et apostrophes
};

const togglePassword = () => {
    const btnToggles = document.querySelectorAll('.toggle-password');
    if (!btnToggles) return;
    btnToggles.forEach(btnToggle => {
        const contentPassword = btnToggle.closest('.content-password')
        if (!btnToggle.closest('.content-password')) return;
        const passwordInput = btnToggle.closest('.content-password').querySelector('input[type="password"], input[type="text"]');
        if (!passwordInput) return;

        const icon = btnToggle.querySelector('i');
        if (!icon) return;

        btnToggle.addEventListener('click', () => {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            icon.classList.replace(isPassword ? 'bi-eye' : 'bi-eye-slash', isPassword ? 'bi-eye-slash' : 'bi-eye');
        });
    });
};

// Vérifie si l'utilisateur est majeur
const isMajeur = (dateString) => {
    const birthDate = new Date(dateString);
    if (isNaN(birthDate)) return false;

    const today = new Date();
    const age = today.getFullYear() - birthDate.getFullYear();
    const monthDiff = today.getMonth() - birthDate.getMonth();
    const dayDiff = today.getDate() - birthDate.getDate();

    return age > 18 || (age === 18 && (monthDiff > 0 || (monthDiff === 0 && dayDiff >= 0)));
};

// Vérifie si les mots de passe correspondent
const checkPassWordRepeat = (container) => {
    const password = container.querySelector('.password');
    const passwordRepeat = container.querySelector('.password-repeat');
    const toggleButton = container.querySelector('.toggle-password');

    if (password && passwordRepeat) {
        const isMatching = password.value === passwordRepeat.value;
        passwordRepeat.classList.toggle('border-danger', !isMatching);
        toggleButton.classList.toggle('border-danger', !isMatching);
        return isMatching;
    }
    return true;
};

document.addEventListener('DOMContentLoaded', () => {
    const validateInput = (input) => {
        const pattern = input.getAttribute('data-type-pattern');
        const regex = regexMap[pattern];
        let isValid = true;

        if (pattern && regex) {
            isValid = regex.test(input.value.trim());
        } else if (input.type === 'date') {
            isValid = isMajeur(input.value.trim());
        }

        isValid = isValid && input.value.trim() !== '';

        // Afficher ou masquer le message d'erreur de pattern
        if (input.closest('.sub-section')) {
            const errorPattern = input.closest('.sub-section').querySelector('.input-error-pattern'); // Sélectionner le message d'erreur spécifique

            if (errorPattern) {
                if (!isValid) {
                    errorPattern.classList.remove('d-none');  // Afficher le message d'erreur si non valide
                } else {
                    errorPattern.classList.add('d-none');  // Masquer le message d'erreur si valide
                }
            }
        }

        input.classList.toggle('border-danger', !isValid);
        return isValid;
    };

    // Valide tous les champs d'un conteneur (section ou formulaire entier)
    const validateContainer = (container) => {
        const inputs = container.querySelectorAll('[required]');
        return Array.from(inputs).every(validateInput) && checkPassWordRepeat(container);
    };

    // Met à jour l'état du bouton en prenant en compte checkPassWordRepeat
    const updateButtonState = (container) => {
        const nextButton = container.querySelector('.btn-next-register, button[type="submit"]');
        if (nextButton) {
            const isValid = validateContainer(container);
            nextButton.disabled = !isValid;
            nextButton.classList.toggle('disabled', !isValid);
        }
    };

    // Gestion des formulaires
    document.querySelectorAll('form').forEach(form => {
        const sections = form.querySelectorAll('.section');

        if (sections.length > 0) {
            sections.forEach(section => {
                section.querySelectorAll('[required]').forEach(input => {
                    input.addEventListener('input', () => updateButtonState(section));
                });

                // Vérifie au chargement
                updateButtonState(section);
            });
        } else {
            form.querySelectorAll('[required]').forEach(input => {
                input.addEventListener('input', () => updateButtonState(form));
            });

            // Vérifie au chargement
            updateButtonState(form);
        }

        // Vérifie la correspondance des mots de passe en temps réel
        const password = form.querySelector('.password');
        const passwordRepeat = form.querySelector('.password-repeat');

        if (password && passwordRepeat) {
            passwordRepeat.addEventListener('input', () => {
                checkPassWordRepeat(form);
                updateButtonState(form);
            });

            password.addEventListener('input', () => {
                checkPassWordRepeat(form);
                updateButtonState(form);
            });
        }
    });

    togglePassword();

});





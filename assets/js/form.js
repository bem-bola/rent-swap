// document.addEventListener('DOMContentLoaded', () => {
//     // Mapping des regex pour valider les champs email et mot de passe
//     const regexMap = {
//         email: /^[letters-zA-Z0-9._%+-]+@[letters-zA-Z0-9.-]+\.[letters-zA-Z]{2,}$/,
//         password: /^(?=.*[letters-z])(?=.*[A-Z])(?=.*\d)(?=.*[@#$%^&*!])[A-Za-z\d@#$%^&*!]{8,}$/,
//         name: /^[A-Za-zÀ-ÖØ-öø-ÿ]{2,}$/
//     };
//
//     // Fonction pour afficher/masquer les mots de passe avec une icône
//     const togglePassword = () => {
//         const btnToggles = document.querySelectorAll('.toggle-password'); // Sélectionne tous les boutons de bascule
//         if(!btnToggles) return;
//         btnToggles.forEach(btnToggle => {
//             const passwordInput = btnToggle.closest('.input-group').querySelector('input[type="password"], input[type="text"]');
//             if (!passwordInput) return; // Sort si aucun champ mot de passe trouvé
//
//             const icon = btnToggle.querySelector('i');
//             if (!icon) return; // Sort si l'icône n'existe pas
//
//             btnToggle.addEventListener('click', () => {
//                 const isPassword = passwordInput.type === 'password';
//                 passwordInput.type = isPassword ? 'text' : 'password'; // Alterne entre texte et mot de passe
//                 icon.classList.replace(isPassword ? 'bi-eye' : 'bi-eye-slash', isPassword ? 'bi-eye-slash' : 'bi-eye'); // Change l'icône
//             });
//         });
//     };
//
//     // Fonction pour vérifier si l'input respecte le regex associé
//     const checkInputByRegex = (input) => {
//         const pattern = input.getAttribute('data-type-pattern'); // Récupère le type de validation
//         const regex = regexMap[pattern]; // Récupère la regex correspondante
//         return regex ? regex.test(input.value) : true; // Si aucune regex, considère valide
//     };
//
//     // Fonction pour vérifier si l'input est requis et non vide
//     const checkInputRequired = (input) => input.value.trim() !== '';
//
//     // Fonction pour valider un seul champ
//     const validateInput = (input) => {
//         const isValid = checkInputByRegex(input) && checkInputRequired(input);
//         input.classList.toggle('border-danger', !isValid); // Ajoute ou enlève la classe d'erreur
//         return isValid;
//     };
//
//     // Fonction pour valider tout le formulaire et gérer le bouton de soumission
//     const validateForm = (form, btn, event='input') => {
//         const inputs = form.querySelectorAll('[required]'); // Sélectionne tous les champs requis
//         let isFormValid = true;
//
//         inputs.forEach(input => {
//             input.addEventListener(event, () => {
//                 isFormValid = true;
//                 const isValid = validateInput(input);
//                 isFormValid = isFormValid && isValid; // Met à jour la validité globale
//             });
//         });
//
//         form.addEventListener(event, () => {
//             btn.disabled = !isFormValid; // Active/désactive le bouton
//             btn.classList.toggle('disabled', !isFormValid); // Ajoute/enlève la classe d'opacité
//
//         })
//     };
//
//     // Fonction pour initialiser la validation sur tous les formulaires de la page
//     const initForms = () => {
//         const forms = document.querySelectorAll('form');
//         forms.forEach(form => {
//             const btn = form.querySelector('button[type="submit"]');
//             if (btn) validateForm(form, btn); // Applique la validation si le bouton existe
//         });
//     };
//
//     // Initialisation des fonctionnalités
//     togglePassword();
//     initForms();
// });


// document.addEventListener('DOMContentLoaded', () => {
//     const regexMap = {
//         email: /^[letters-zA-Z0-9._%+-]+@[letters-zA-Z0-9.-]+\.[letters-zA-Z]{2,}$/,
//         password: /^(?=.*[letters-z])(?=.*[A-Z])(?=.*\d)(?=.*[@#$%^&*!])[A-Za-z\d@#$%^&*!]{8,}$/,
//         name: /^[A-Za-zÀ-ÖØ-öø-ÿ]{2,}$/
//     };
//
//     // Fonction pour valider un champ d'entrée
//     const validateInput = (input) => {
//         const pattern = input.getAttribute('data-type-pattern');
//         const regex = regexMap[pattern];
//         const isValid = (!regex || regex.test(input.value.trim())) && input.value.trim() !== '';
//
//         input.classList.toggle('border-danger', !isValid);
//         return isValid;
//     };
//
//     // Fonction pour valider tous les champs d'un conteneur (section ou formulaire)
//     const validateContainer = (container) => {
//         const inputs = container.querySelectorAll('[required]');
//         return Array.from(inputs).every(validateInput);
//     };
//
//     // Fonction pour mettre à jour l'état du bouton en fonction de la validation
//     const updateButtonState = (container) => {
//         const nextButton = container.querySelector('.btn-next-register, button[type="submit"]');
//         if (nextButton) {
//             const isValid = validateContainer(container);
//             nextButton.disabled = !isValid;
//             nextButton.classList.toggle('disabled', !isValid);
//         }
//     };
//
//     // Gère la validation pour tous les formulaires et sections
//     document.querySelectorAll('form').forEach(form => {
//         // Appliquer la validation sur chaque champ requis
//         form.querySelectorAll('[required]').forEach(input => {
//             input.addEventListener('input', () => updateButtonState(form));
//         });
//
//         // Vérifier initialement si le bouton doit être activé ou non
//         updateButtonState(form);
//     });
//
//     // Gestion des sections (si elles existent)
//     const sections = document.querySelectorAll('.section');
//     let currentSectionIndex = 0;
//
//     if (sections.length > 0) {
//
//         sections.forEach((section) => {
//             updateButtonState(section);
//         })
//
//     }
// });

//
// document.addEventListener('DOMContentLoaded', () => {
//     const regexMap = {
//         email: /^[letters-zA-Z0-9._%+-]+@[letters-zA-Z0-9.-]+\.[letters-zA-Z]{2,}$/,
//         password: /^(?=.*[letters-z])(?=.*[A-Z])(?=.*\d)(?=.*[@#$%^&*!])[A-Za-z\d@#$%^&*!]{8,}$/,
//         name: /^[A-Za-zÀ-ÖØ-öø-ÿ\s'-]{2,}$/, // Ajout des espaces et apostrophes
//     };
//
//     // Fonction pour valider un champ d'entrée
//     const validateInput = (input) => {
//         const pattern = input.getAttribute('data-type-pattern');
//         const regex = regexMap[pattern];
//         const isValid = (!regex || regex.test(input.value.trim())) && input.value.trim() !== '';
//
//         input.classList.toggle('border-danger', !isValid);
//         return isValid;
//     };
//
//     // Fonction pour valider tous les champs d'un conteneur (section ou formulaire)
//     const validateContainer = (container) => {
//         const inputs = container.querySelectorAll('[required]');
//         return Array.from(inputs).every(validateInput);
//     };
//
//     // Fonction pour mettre à jour l'état du bouton en fonction de la validation
//     const updateButtonState = (container) => {
//         const nextButton = container.querySelector('.btn-next-register, button[type="submit"]');
//         if (nextButton) {
//             const isValid = validateContainer(container);
//             nextButton.disabled = !isValid;
//             nextButton.classList.toggle('disabled', !isValid);
//         }
//     };
//
//     // Appliquer la validation sur chaque champ requis
//     document.querySelectorAll('[required]').forEach(input => {
//         input.addEventListener('input', () => {
//             const container = input.closest('form') || input.closest('.section');
//             if (container) updateButtonState(container);
//         });
//     });
//
//     // Vérifier initialement si les boutons doivent être activés ou non
//     document.querySelectorAll('form, .section').forEach(container => {
//         updateButtonState(container);
//     });
// });


// document.addEventListener('DOMContentLoaded', () => {
//     const regexMap = {
//         email: /^[letters-zA-Z0-9._%+-]+@[letters-zA-Z0-9.-]+\.[letters-zA-Z]{2,}$/,
//         password: /^(?=.*[letters-z])(?=.*[A-Z])(?=.*\d)(?=.*[@#$%^&*!])[A-Za-z\d@#$%^&*!]{8,}$/,
//         name: /^[A-Za-zÀ-ÖØ-öø-ÿ\s'-]{2,}$/, // Supporte les espaces et apostrophes
//     };
//
//     // Fonction pour valider un champ d'entrée
//     const validateInput = (input) => {
//         const pattern = input.getAttribute('data-type-pattern');
//         const regex = regexMap[pattern];
//         const isValid = (!regex || regex.test(input.value.trim())) && input.value.trim() !== '';
//
//         input.classList.toggle('border-danger', !isValid);
//         return isValid;
//     };
//
//     // Fonction pour valider tous les champs d'un conteneur (section ou formulaire entier)
//     const validateContainer = (container) => {
//         const inputs = container.querySelectorAll('[required]');
//         return Array.from(inputs).every(validateInput);
//     };
//
//     // Fonction pour mettre à jour l'état du bouton
//     const updateButtonState = (container) => {
//         const nextButton = container.querySelector('.btn-next-register, button[type="submit"]');
//         if (nextButton) {
//             const isValid = validateContainer(container);
//             nextButton.disabled = !isValid;
//             nextButton.classList.toggle('disabled', !isValid);
//         }
//     };
//
//     // Gestion des formulaires
//     document.querySelectorAll('form').forEach(form => {
//         // Vérifie si le formulaire contient des sections
//         const sections = form.querySelectorAll('.section');
//
//         if (sections.length > 0) {
//             // Cas d'un formulaire avec sections
//             sections.forEach(section => {
//                 section.querySelectorAll('[required]').forEach(input => {
//                     input.addEventListener('input', () => updateButtonState(section));
//                 });
//
//                 // Vérifie au chargement
//                 updateButtonState(section);
//             });
//         } else {
//             // Cas d'un formulaire sans sections
//             form.querySelectorAll('[required]').forEach(input => {
//                 input.addEventListener('input', () => updateButtonState(form));
//             });
//
//             // Vérifie au chargement
//             updateButtonState(form);
//         }
//     });
// });


// document.addEventListener('DOMContentLoaded', () => {
//     const regexMap = {
//         email: /^[letters-zA-Z0-9._%+-]+@[letters-zA-Z0-9.-]+\.[letters-zA-Z]{2,}$/,
//         password: /^(?=.*[letters-z])(?=.*[A-Z])(?=.*\d)(?=.*[@#$%^&*!])[A-Za-z\d@#$%^&*!]{8,}$/,
//         name: /^[A-Za-zÀ-ÖØ-öø-ÿ\s'-]{2,}$/, // Supporte les espaces et apostrophes
//     };
//
//     const togglePassword = () => {
//         const btnToggles = document.querySelectorAll('.toggle-password'); // Sélectionne tous les boutons de bascule
//         if(!btnToggles) return;
//         btnToggles.forEach(btnToggle => {
//             const passwordInput = btnToggle.closest('.input-group').querySelector('input[type="password"], input[type="text"]');
//             if (!passwordInput) return; // Sort si aucun champ mot de passe trouvé
//
//             const icon = btnToggle.querySelector('i');
//             if (!icon) return; // Sort si l'icône n'existe pas
//
//             btnToggle.addEventListener('click', () => {
//                 const isPassword = passwordInput.type === 'password';
//                 passwordInput.type = isPassword ? 'text' : 'password'; // Alterne entre texte et mot de passe
//                 icon.classList.replace(isPassword ? 'bi-eye' : 'bi-eye-slash', isPassword ? 'bi-eye-slash' : 'bi-eye'); // Change l'icône
//             });
//         });
//     };
//     // Fonction pour vérifier si une date correspond à un âge minimum
//     const isMajeur = (dateString) => {
//         const birthDate = new Date(dateString);
//         if (isNaN(birthDate)) return false; // Vérifie que c'est une date valide
//
//         const today = new Date();
//         const age = today.getFullYear() - birthDate.getFullYear();
//         const monthDiff = today.getMonth() - birthDate.getMonth();
//         const dayDiff = today.getDate() - birthDate.getDate();
//
//         // Vérifie si l'âge est bien 18 ans ou plus
//         return age > 18 || (age === 18 && (monthDiff > 0 || (monthDiff === 0 && dayDiff >= 0)));
//     };
//
//     // Fonction pour valider un champ d'entrée
//     const validateInput = (input) => {
//         const pattern = input.getAttribute('data-type-pattern');
//         const regex = regexMap[pattern];
//
//         let isValid = true;
//
//         if (pattern && regex) {
//             isValid = regex.test(input.value.trim());
//         } else if (input.type === 'date') {
//             isValid = isMajeur(input.value.trim());
//         }
//
//         isValid = isValid && input.value.trim() !== '';
//
//         input.classList.toggle('border-danger', !isValid);
//         return isValid;
//     };
//
//     // Fonction pour valider tous les champs d'un conteneur (section ou formulaire entier)
//     const validateContainer = (container) => {
//         const inputs = container.querySelectorAll('[required]');
//         return Array.from(inputs).every(validateInput);
//     };
//
//     // Fonction pour mettre à jour l'état du bouton
//     const updateButtonState = (container) => {
//         const nextButton = container.querySelector('.btn-next-register, button[type="submit"]');
//         if (nextButton) {
//             const isValid = validateContainer(container);
//             nextButton.disabled = !isValid;
//             nextButton.classList.toggle('disabled', !isValid);
//         }
//     };
//
//     const checkPassWordRepeat = () => {
//         const password = document.querySelector('.password')
//         const passwordRepeat = document.querySelector('.password-repeat')
//
//         if(password && passwordRepeat){
//             return password.value === passwordRepeat.value
//         }
//         return true;
//     }
//
//     // Gestion des formulaires
//     document.querySelectorAll('form').forEach(form => {
//         const sections = form.querySelectorAll('.section');
//
//         if (sections.length > 0) {
//             // Cas d'un formulaire avec sections
//             sections.forEach(section => {
//                 section.querySelectorAll('[required]').forEach(input => {
//                     input.addEventListener('input', () => updateButtonState(section));
//                 });
//
//                 // Vérifie au chargement
//                 updateButtonState(section);
//             });
//         } else {
//             // Cas d'un formulaire sans sections
//             form.querySelectorAll('[required]').forEach(input => {
//                 input.addEventListener('input', () => updateButtonState(form));
//             });
//
//             // Vérifie au chargement
//             updateButtonState(form);
//         }
//     });
//
//     togglePassword()
// });


document.addEventListener('DOMContentLoaded', () => {
    const regexMap = {
        email: /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/,
        password: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@#$%^&*!])[A-Za-z\d@#$%^&*!]{8,}$/,
        name: /^[A-Za-zÀ-ÖØ-öø-ÿ\s'-]{2,}$/, // Supporte les espaces et apostrophes
    };

    const togglePassword = () => {
        const btnToggles = document.querySelectorAll('.toggle-password');
        if (!btnToggles) return;
        btnToggles.forEach(btnToggle => {
            const passwordInput = btnToggle.closest('.input-group').querySelector('input[type="password"], input[type="text"]');
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

    // Valide un champ d'entrée
    // const validateInput = (input) => {
    //     const pattern = input.getAttribute('data-type-pattern');
    //     const regex = regexMap[pattern];
    //
    //     let isValid = true;
    //
    //     if (pattern && regex) {
    //         isValid = regex.test(input.value.trim());
    //     } else if (input.type === 'date') {
    //         isValid = isMajeur(input.value.trim());
    //     }
    //
    //     isValid = isValid && input.value.trim() !== '';
    //
    //     input.classList.toggle('border-danger', !isValid);
    //     return isValid;
    // };


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

//
//
// document.addEventListener('DOMContentLoaded', () => {
//     const regexMap = {
//         email: /^[letters-zA-Z0-9._%+-]+@[letters-zA-Z0-9.-]+\.[letters-zA-Z]{2,}$/,
//         password: /^(?=.*[letters-z])(?=.*[A-Z])(?=.*\d)(?=.*[@#$%^&*!])[A-Za-z\d@#$%^&*!]{8,}$/,
//         name: /^[A-Za-zÀ-ÖØ-öø-ÿ\s'-]{2,}$/, // Supporte les espaces et apostrophes
//     };
//
//     const togglePassword = () => {
//         const btnToggles = document.querySelectorAll('.toggle-password');
//         if (!btnToggles) return;
//         btnToggles.forEach(btnToggle => {
//             const passwordInput = btnToggle.closest('.input-group').querySelector('input[type="password"], input[type="text"]');
//             if (!passwordInput) return;
//
//             const icon = btnToggle.querySelector('i');
//             if (!icon) return;
//
//             btnToggle.addEventListener('click', () => {
//                 const isPassword = passwordInput.type === 'password';
//                 passwordInput.type = isPassword ? 'text' : 'password';
//                 icon.classList.replace(isPassword ? 'bi-eye' : 'bi-eye-slash', isPassword ? 'bi-eye-slash' : 'bi-eye');
//             });
//         });
//     };
//
//     // Vérifie si l'utilisateur est majeur
//     const isMajeur = (dateString) => {
//         const birthDate = new Date(dateString);
//         if (isNaN(birthDate)) return false;
//
//         const today = new Date();
//         const age = today.getFullYear() - birthDate.getFullYear();
//         const monthDiff = today.getMonth() - birthDate.getMonth();
//         const dayDiff = today.getDate() - birthDate.getDate();
//
//         return age > 18 || (age === 18 && (monthDiff > 0 || (monthDiff === 0 && dayDiff >= 0)));
//     };
//
//     // Vérifie si les mots de passe correspondent
//     const checkPassWordRepeat = (container) => {
//         const password = container.querySelector('.password');
//         const passwordRepeat = container.querySelector('.password-repeat');
//         const toggleButton = container.querySelector('.toggle-password');
//         const errorParagraph = container.querySelector('.input-error-pattern');
//
//         if (password && passwordRepeat) {
//             const isMatching = password.value === passwordRepeat.value;
//
//             // Affiche ou cache le message d'erreur
//             if (errorParagraph) {
//                 errorParagraph.classList.toggle('d-none', isMatching);
//             }
//
//             passwordRepeat.classList.toggle('border-danger', !isMatching);
//             toggleButton.classList.toggle('border-danger', !isMatching);
//
//             return isMatching;
//         }
//         return true;
//     };
//
//     // Valide un champ d'entrée
//     const validateInput = (input) => {
//         const pattern = input.getAttribute('data-type-pattern');
//         const regex = regexMap[pattern];
//         const errorParagraph = input.closest('.input-group').querySelector('.input-error-pattern');
//
//         let isValid = true;
//
//         if (pattern && regex) {
//             isValid = regex.test(input.value.trim());
//         } else if (input.type === 'date') {
//             isValid = isMajeur(input.value.trim());
//         }
//
//         isValid = isValid && input.value.trim() !== '';
//
//         // Affiche ou cache le message d'erreur
//         if (errorParagraph) {
//             errorParagraph.classList.toggle('d-none', isValid);
//         }
//
//         input.classList.toggle('border-danger', !isValid);
//         return isValid;
//     };
//
//     // Valide tous les champs d'un conteneur (section ou formulaire entier)
//     const validateContainer = (container) => {
//         const inputs = container.querySelectorAll('[required]');
//         return Array.from(inputs).every(validateInput) && checkPassWordRepeat(container);
//     };
//
//     // Met à jour l'état du bouton en prenant en compte checkPassWordRepeat
//     const updateButtonState = (container) => {
//         const nextButton = container.querySelector('.btn-next-register, button[type="submit"]');
//         if (nextButton) {
//             const isValid = validateContainer(container);
//             nextButton.disabled = !isValid;
//             nextButton.classList.toggle('disabled', !isValid);
//         }
//     };
//
//     // Gestion des formulaires
//     document.querySelectorAll('form').forEach(form => {
//         const sections = form.querySelectorAll('.section');
//
//         if (sections.length > 0) {
//             sections.forEach(section => {
//                 section.querySelectorAll('[required]').forEach(input => {
//                     input.addEventListener('input', () => updateButtonState(section));
//                 });
//
//                 // Vérifie au chargement
//                 updateButtonState(section);
//             });
//         } else {
//             form.querySelectorAll('[required]').forEach(input => {
//                 input.addEventListener('input', () => updateButtonState(form));
//             });
//
//             // Vérifie au chargement
//             updateButtonState(form);
//         }
//
//         // Vérifie la correspondance des mots de passe en temps réel
//         const password = form.querySelector('.password');
//         const passwordRepeat = form.querySelector('.password-repeat');
//
//         if (password && passwordRepeat) {
//             passwordRepeat.addEventListener('input', () => {
//                 checkPassWordRepeat(form);
//                 updateButtonState(form);
//             });
//
//             password.addEventListener('input', () => {
//                 checkPassWordRepeat(form);
//                 updateButtonState(form);
//             });
//         }
//     });
//
//     togglePassword();
// });





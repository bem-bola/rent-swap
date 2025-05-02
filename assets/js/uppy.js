import Uppy from '@uppy/core'
import Dashboard from '@uppy/dashboard'
import Webcam from '@uppy/webcam'
import French from '@uppy/locales/lib/fr_FR';
import XHRUpload from '@uppy/xhr-upload'

import '@uppy/core/dist/style.css'
import '@uppy/drag-drop/dist/style.min.css';
import '@uppy/dashboard/dist/style.min.css';
import '@uppy/webcam/dist/style.min.css';
import "../styles/packages/upp.scss";


const uploadXHR = (route) => {

    let paramUrl =  document.getElementById('uppy-dashboard')

    if(!paramUrl) return;

    let param = paramUrl ? JSON.parse(paramUrl.dataset.param) : null ;

    const uppy = new Uppy({
        autoProceed: false,
        locale: French
    })

    uppy.use(Dashboard, {
        target: '#uppy-dashboard',
        inline: true,
        // hideUploadButton: true
    })

    uppy.use(XHRUpload, {
        endpoint: Routing.generate(route, param),
        fieldName: 'files[]',
        formData: true,
        bundle: true,
        headers: {
            'HX-Request': true
        }
    })


    uppy.use(Webcam, {
        target: Dashboard
    })

    const checkLengthFile = () => {
        return uppy.getFiles().length > 0;
    }

    const textBtn = (uploadButton) => {
        return uploadButton.innerText = "Téleverser le(s) " + uppy.getFiles().length + " fichier(s) chargé(s)"
    }

    const uploadButton = document.querySelector('.uppy-StatusBar-actionBtn--upload')
    // const uploadButton = document.getElementById('submit-upload-file')

    if (uploadButton) {

        // Écouter quand plusieurs fichiers sont ajoutés d'un coup
        uppy.on('files-added', (files) => {
            textBtn(uploadButton)
            uploadButton.classList.toggle('d-none', !checkLengthFile())
        })
        uppy.on('file-removed', (files) => {
            uploadButton.classList.toggle('d-none', !checkLengthFile())
        })

        uploadButton.addEventListener('click', (event) => {

            event.defaultPrevented

            uppy.upload().then((result) => {
                if (result.failed.length > 0) {
                    alert("Une erreur s'est produite")
                } else {
                    // window.location.href(Routing.generate(result.url))

                    console.log(result.successful)
                }
            })
        })

    }

    return uppy
}

uploadXHR('app_user_upload_image_device')


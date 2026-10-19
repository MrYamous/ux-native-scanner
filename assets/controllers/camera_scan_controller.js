import { BridgeComponent } from "@hotwired/hotwire-native-bridge"

// Bridge Component "camera-scan" — côté Symfony/Stimulus.
// L'app native doit enregistrer un composant Swift/Kotlin nommé "camera-scan"
// qui répond au message "openCamera" et rappelle avec { imageData: "<base64>" }.
//
// En dehors d'une app native (navigateur web classique), ce controller se
// charge normalement mais this.enabled === false : les appels send() sont
// ignorés silencieusement, sans erreur.
export default class extends BridgeComponent {
    static component = "camera-scan"

    static targets = ["fileInput", "preview"]

    // Déclenché depuis le HTML : data-action="camera-scan#openCamera"
    openCamera() {
        if (!this.enabled) {
            // Hors contexte natif : rien à faire (le bouton ne devrait pas
            // apparaître ici grâce à ux_is_native() dans le template, mais
            // on garde le guard pour la robustesse)
            return
        }

        // Envoie le message "openCamera" au composant natif "camera-scan"
        this.send("openCamera", {}, ({ imageData }) => {
            // Callback appelé par l'app native avec les données base64 de l'image
            this.fileInputTarget.value = imageData
            this.previewTarget.src = `data:image/jpeg;base64,${imageData}`
            this.previewTarget.classList.remove("hidden")
        })
    }
}

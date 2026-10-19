import { BridgeComponent } from "@hotwired/hotwire-native-bridge"

// Bridge Component "native-action" — côté Symfony/Stimulus.
// L'app native doit enregistrer un composant Swift/Kotlin nommé "native-action"
// qui répond au message "connect" (en affichant un bouton dans la toolbar avec
// le titre fourni) et envoie le message "onTapAction" quand l'utilisateur tape.
//
// Usage dans un template Twig :
//   {% if ux_is_native() %}
//   <div
//     data-controller="native-action"
//     data-native-action-title-value="Sauvegarder"
//     data-native-action-action-value="contact:save"
//   ></div>
//   {% endif %}
export default class extends BridgeComponent {
    static component = "native-action"

    // Le titre affiché dans le bouton de la toolbar native
    static values = { title: String, action: String }

    connect() {
        super.connect()

        if (this.enabled) {
            // Demande à l'app native d'afficher un bouton dans sa toolbar
            this.send("connect", { title: this.titleValue })
        }
    }

    // Appelé par l'app native quand l'utilisateur tape le bouton toolbar.
    // On redispatche l'événement localement pour que d'autres controllers
    // Stimulus puissent y réagir.
    onTapAction() {
        this.element.dispatchEvent(
            new CustomEvent(this.actionValue, { bubbles: true, cancelable: true })
        )
    }
}

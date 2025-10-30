const messagesContainer = document.querySelector("div[data-topic]");
messagesContainer.scrollTop = messagesContainer.scrollHeight;

const currentUserId = messagesContainer.dataset.userId;

// Connection au hub mercure
const topic = messagesContainer.dataset.topic;
const mercureToken = messagesContainer.dataset.mercureToken;
const url = new URL('http://127.0.0.1:3000/.well-known/mercure');
url.searchParams.append('topic', topic);
url.searchParams.append('jwt', mercureToken);

// Ecoute la réception de message sur le hub
const eventSource = new EventSource(url);
// eventSource.onmessage = generateMessage;
eventSource.onmessage = manageMessage;

function manageMessage(event) {
    const data = JSON.parse(event.data);
    if (data.action === "create") {
        generateMessage(data);
    } else if (data.action === "delete") {
        document.querySelector(`#message${data.id}`).remove();
    }
}

// Génération de message
function generateMessage(data) {
    const messageContainer = document.createElement("div");
    messageContainer.classList.add("message");
    messageContainer.id = `message${data.message.id}`

    const messageHeader = document.createElement("div");
    messageHeader.classList.add("message-header");

    const messageAuthor = document.createElement("div");
    messageAuthor.classList.add("message-author");

    const profilePicture = document.createElement("img");
    profilePicture.classList.add("profil-picture");
    profilePicture.src = `/images/uploads/profil-pictures/${data.message.user.profilePicture}`;
    profilePicture.alt = `Photo de profil de ${data.message.user.firstName} ${data.message.user.lastName}`;

    const messageDetails = document.createElement("div");
    messageDetails.classList.add("message-details");

    const usernameP = document.createElement("p");
    const strongUsername = document.createElement("strong");
    strongUsername.textContent = `${data.message.user.firstName} ${data.message.user.lastName}`;

    const messageDate = document.createElement('time');
    messageDate.dateTime = data.message.createdAt;
    messageDate.textContent = new Date(data.message.createdAt).toLocaleString();

    const messageContent = document.createElement("p");
    messageContent.textContent = data.message.content;

    const noMessagesDiv = document.querySelector("#noMessages");
    if (noMessagesDiv) {
        noMessagesDiv.remove();
    }

    usernameP.appendChild(strongUsername);
    messageDetails.appendChild(usernameP);
    messageDetails.appendChild(messageDate);
    messageAuthor.appendChild(profilePicture);
    messageAuthor.appendChild(messageDetails);
    messageHeader.appendChild(messageAuthor);
    messageContainer.appendChild(messageHeader);
    messageContainer.appendChild(messageContent);
    messagesContainer.appendChild(messageContainer);

    if (data.message.user.id == currentUserId) {
        messageHeader.appendChild(generateDeleteForm(data.message.id, data.token));
    }

    messagesContainer.appendChild(messageContainer);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
};

// Génération du formulaire de suppression
const modalBackground = document.querySelector("#modal-background");
const confirmedDelete = document.querySelector(".delete-entity");
let currentForm = null; // formulaire en cours de suppression

function generateDeleteForm(messageId, tokenValue) {
    const form = document.createElement("form");
    form.method = "post";
    form.action = `/messages/delete/${messageId}`;
    form.classList.add("delete-form");
    form.dataset.delete = "message";

    const token = document.createElement("input");
    token.type = "hidden";
    token.name = "_token";
    token.value = tokenValue;

    const submit = document.createElement("button");
    submit.type = "submit";
    submit.classList.add("delete-button");
    submit.ariaLabel = `supprimer ${messageId}`;
    submit.textContent = "X";

    form.appendChild(token);
    form.appendChild(submit);

    form.addEventListener("submit", (e) => {
        e.preventDefault();
        currentForm = form; // on garde le formulaire actuel
        if (modalBackground) {
            modalBackground.classList.remove("hidden");
            modalBackground.classList.add("modal-background");
        }
    });

    return form;
}

// Listener unique pour le bouton de confirmation
if (confirmedDelete) {
    confirmedDelete.addEventListener("click", async () => {
        if (!currentForm) {
            return;
        }

        if (currentForm.dataset.delete === "message") {
            const formData = new FormData(currentForm);
            try {
                const response = await fetch(currentForm.action, {
                    method: "POST",
                    body: formData
                });
                if (!response.ok) alert("Erreur lors de la suppression");
            } catch (err) {
                console.error(err);
                alert("Erreur réseau");
            }
        }

        // Fermer la modal
        modalBackground.classList.remove("modal-background");
        modalBackground.classList.add("hidden");
        currentForm = null;
    });
}

// Ecoute de l'envoie de message
const messageForm = document.querySelector("#messageForm");
if (messageForm) {
    messageForm.addEventListener("submit", async (e) => {
        e.preventDefault();

        const data = {
            content: messageForm.message_content.value,
            event_id: messageForm.dataset.eventId,
            _csrf_token: messageForm.message__token.value
        };

        try {
            const response = await fetch('/messages/create', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            if (response.ok) {
                messageForm.reset();
            } else {
                alert("Impossible d’envoyer le message. Veuillez réessayer plus tard.");
                return;
            }

        } catch (e) {
            console.error(e);
            alert("Erreur réseau");
        }
    })
}
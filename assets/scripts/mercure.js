import { initDeleteSystem } from './deleteModal.js';

// Init delete system from the deleteModal.js file
const attachDeleteListener = initDeleteSystem();

const messagesContainer = document.querySelector("div[data-topic]");
messagesContainer.scrollTop = messagesContainer.scrollHeight;

const currentUserId = messagesContainer.dataset.userId;

// Connection to mercure hub
const topic = messagesContainer.dataset.topic;
const mercureToken = messagesContainer.dataset.mercureToken;
// En mode prod (l'une des deux suivantes) :
// const url = new URL('https://jeuxpartage.fr/.well-known/mercure');
const url = new URL('https://mercure.jeuxpartage.fr/.well-known/mercure');
// En mode dev :
// const url = new URL('http://127.0.0.1:3000/.well-known/mercure');
url.searchParams.append('topic', topic);
url.searchParams.append('jwt', mercureToken);

// Listen for new messages reception on the hub
const eventSource = new EventSource(url);
eventSource.onmessage = manageMessage;

function manageMessage(event) {
    const data = JSON.parse(event.data);
    if (data.action === "create") {
        generateMessage(data);
    } else if (data.action === "delete") {
        document.querySelector(`#message${data.id}`).remove();
    }
}

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

    // Adding a form to delete the message if connected user = sender
    if (data.message.user.id == currentUserId) {
        const deleteForm = generateDeleteForm(data.message.id, data.token);
        messageHeader.appendChild(deleteForm);

        // Adding function to delete the message from the deleteModal.js file
        attachDeleteListener(deleteForm);
    }

    messagesContainer.appendChild(messageContainer);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
};

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

    return form;
}

// Listening messages sent
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
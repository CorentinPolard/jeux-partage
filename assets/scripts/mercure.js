const messagesContainer = document.querySelector("div[data-topic]");
messagesContainer.scrollTop = messagesContainer.scrollHeight;

const currentUserId = messagesContainer.dataset.userId;

// Connection au hub mercure
const topic = messagesContainer.dataset.topic;
const mercureToken = messagesContainer.dataset.mercureToken;
const url = new URL('http://127.0.0.1:3000/.well-known/mercure');
url.searchParams.append('topic', topic);
url.searchParams.append('jwt', mercureToken);

const eventSource = new EventSource(url);
eventSource.onmessage = generateMessage;

function generateMessage(event) {
    const data = JSON.parse(event.data);

    const messageContainer = document.createElement("div");
    messageContainer.classList.add("message");

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

function generateDeleteForm(messageId, tokenValue) {
    const form = document.createElement("form");
    form.method = "post";
    form.action = `/messages/delete/${messageId}`;
    form.classList.add("delete-form")

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

    const modalBackground = document.querySelector("#modal-background");

    form.addEventListener("submit", (e) => {
        e.preventDefault();
        if (modalBackground) {
            modalBackground.classList.remove("hidden");
            modalBackground.classList.add("modal-background")

            const confirmedDelete = document.querySelector(".delete-entity");
            confirmedDelete.addEventListener("click", () => form.submit())
        }
    })

    return form;
}


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
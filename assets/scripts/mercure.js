const messagesContainer = document.querySelector("div[data-topic]");
const topic = messagesContainer.dataset.topic;
const currentUserId = messagesContainer.dataset.userId;
const url = new URL('http://127.0.0.1:3000/.well-known/mercure');
url.searchParams.append('topic', topic);

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
    profilePicture.src = `/images/uploads/profil-pictures/${data.user.profilePicture}`;
    profilePicture.alt = `Photo de profil de ${data.user.firstName} ${data.user.lastName}`;

    const messageDetails = document.createElement("div");
    messageDetails.classList.add("message-details");

    const usernameP = document.createElement("p");
    const strongUsername = document.createElement("strong");
    strongUsername.textContent = `${data.user.firstName} ${data.user.lastName}`;

    const messageDate = document.createElement('time');
    messageDate.dateTime = data.createdAt;
    messageDate.textContent = new Date(data.createdAt).toLocaleString();

    const messageContent = document.createElement("p");
    messageContent.textContent = data.content;

    usernameP.appendChild(strongUsername);
    messageDetails.appendChild(usernameP);
    messageDetails.appendChild(messageDate);
    messageAuthor.appendChild(profilePicture);
    messageAuthor.appendChild(messageDetails);
    messageHeader.appendChild(messageAuthor);
    messageContainer.appendChild(messageHeader);
    messageContainer.appendChild(messageContent);
    messagesContainer.appendChild(messageContainer);

    messagesContainer.appendChild(messageContainer);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
};


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
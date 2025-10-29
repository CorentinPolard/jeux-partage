const messagesContainer = document.querySelector("div[data-topic]");
const topic = messagesContainer.dataset.topic;
const url = new URL('http://127.0.0.1:3000/.well-known/mercure');
url.searchParams.append('topic', topic);

const eventSource = new EventSource(url);
eventSource.onmessage = (event) => {
    const data = JSON.parse(event.data);
    console.log('Nouveau message reçu:', data);
    const messageHTML = `
        <div class="message">
            <div class="message-header">
                <div class="message-author">
                    <img class="profil-picture" src="/images/uploads/profil-pictures/${data.user.profilePicture}" alt="Photo de profil de ${data.user.firstName} ${data.user.lastName}">
                    <div class="message-details">
                        <p><strong>${data.user.firstName} ${data.user.lastName}</strong></p>
                        <time datetime="${data.createdAt}">${new Date(data.createdAt).toLocaleString()}</time>
                    </div>
                </div>
            </div>
            <p>${data.content}</p>
        </div>
    `;

    messagesContainer.innerHTML += messageHTML;
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
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
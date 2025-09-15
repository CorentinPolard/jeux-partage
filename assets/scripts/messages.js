const chatForm = document.querySelector("#chat-form");
chatForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    const messageContent = document.querySelector("#message-content").value.trim();
    if (messageContent) {
        const eventUrl = window.location.pathname.split("/");
        const eventId = eventUrl[eventUrl.length - 1];

        const response = await fetch(`/messages/create/${eventId}`, {
            method: "POST",
            // Précise comment sont formatées les données (comme un formulaire classique ici)
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            // Contient les données envoyées par la requête
            body: new URLSearchParams({
                content: messageContent
            })
        });

        if (response.ok) {
            chatForm.reset();

            // Affichage du message
            const datas = await response.json();
            const messageDatas = datas.messageDatas

            const messagesContainer = document.querySelector(".messages-container");

            const message = document.createElement("div");
            message.classList.add("message");

            const messageAuthor = document.createElement("div");
            messageAuthor.classList.add("message-author");

            const profilePicture = document.createElement("img");
            profilePicture.classList.add("profil-picture");
            profilePicture.src = `/images/uploads/profil-pictures/${messageDatas.user.profilePicture}`;
            profilePicture.alt = `Photo de profil de ${messageDatas.user.username}`;

            const messageDetails = document.createElement("div");
            messageDetails.classList.add("message-details");

            const p = document.createElement("p");
            const strong = document.createElement("strong");
            strong.textContent = messageDatas.user.username;

            const time = document.createElement("time");
            time.setAttribute("datetime", messageDatas.createdAt);
            time.textContent = messageDatas.createdAt.replace(":", "h");

            const content = document.createElement("p");
            content.textContent = messageDatas.content;

            messagesContainer.appendChild(message);
            message.appendChild(messageAuthor);
            messageAuthor.appendChild(profilePicture);
            messageAuthor.appendChild(messageDetails);
            messageDetails.appendChild(p);
            p.appendChild(strong);
            messageDetails.appendChild(time);
            message.appendChild(content);

        } else {
            alert("Erreur lors de l'envoi du message.")
        }
    }
})
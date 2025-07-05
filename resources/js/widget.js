document.addEventListener("DOMContentLoaded", function () {
    // Create chat widget container if not already present
    if (!document.getElementById("chat-widget")) {
        const widget = document.createElement("div");
        widget.id = "chat-widget";
        widget.innerHTML = `
            <div id="chat-toggle" class="chat-toggle">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
            </div>
            <div id="chat-container" class="chat-container hidden">
                <div class="chat-header">
                    <h3>AI Assistant</h3>
                    <button id="chat-close" class="chat-close">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div id="chat-messages" class="chat-messages">
                    <div id="welcome-message" class="message bot-message">
                        <div class="message-content">
                            <div class="typing-indicator">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="chat-input-container">
                    <input type="text" id="chat-input" placeholder="Type your message..." class="chat-input">
                    <button id="chat-send" class="chat-send">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                    </button>
                </div>
            </div>
        `;
        document.body.appendChild(widget);
    }

    // Find or create chat button
    let button = document.getElementById("chat-button");
    if (!button) {
        button = document.createElement("button");
        button.id = "chat-button";
        button.textContent = "Chat";
        button.style.position = "fixed";
        button.style.bottom = "20px";
        button.style.right = "20px";
        button.style.zIndex = "10001";
        button.style.background = "#007bff";
        button.style.color = "#fff";
        button.style.border = "none";
        button.style.borderRadius = "50%";
        button.style.width = "56px";
        button.style.height = "56px";
        button.style.fontSize = "20px";
        button.style.boxShadow = "0 2px 8px rgba(0,0,0,0.2)";
        button.style.cursor = "pointer";
        document.body.appendChild(button);
    }

    const windowEl = document.getElementById("chat-window");
    const input = document.getElementById("chat-input");
    const sendBtn = document.getElementById("chat-send-btn");
    const messages = document.getElementById("chat-messages");

    // Toggle chat window
    button.onclick = () => {
        if (windowEl.style.display === "none" || windowEl.style.display === "") {
            windowEl.style.display = "flex";
            input.focus();
        } else {
            windowEl.style.display = "none";
        }
    };

    // Send message on Enter key
    input.addEventListener("keypress", function (e) {
        if (e.key === "Enter") {
            e.preventDefault();
            sendMessage();
        }
    });

    // Send message on button click
    sendBtn.addEventListener("click", function () {
        sendMessage();
    });

    function sendMessage() {
        const msg = input.value.trim();
        if (!msg) return;
        appendMessage("user", msg);
        input.value = "";
        input.disabled = true;
        sendBtn.disabled = true;

        // Get CSRF token from meta tag
        let csrfToken = null;
        const meta = document.querySelector('meta[name="csrf-token"]');
        if (meta) {
            csrfToken = meta.getAttribute('content');
        }

        fetch("/api/send-message", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                ...(csrfToken ? { "X-CSRF-TOKEN": csrfToken } : {})
            },
            body: JSON.stringify({
                message: msg,
            })
        })
            .then(res => res.json())
            .then(data => {
                // The backend returns { reply: ... }
                appendMessage("bot", data.reply || "Sorry, I could not process your request.");
            })
            .catch(() => {
                appendMessage("bot", "Sorry, there was an error sending your message.");
            })
            .finally(() => {
                input.disabled = false;
                sendBtn.disabled = false;
                input.focus();
            });
    }

    function appendMessage(sender, text) {
        const div = document.createElement("div");
        div.className = "chat-msg chat-" + (sender === "user" ? "user" : "bot");
        div.textContent = text;
        messages.appendChild(div);
        messages.scrollTop = messages.scrollHeight;
    }

    // Get elements
    const chatToggle = document.getElementById('chat-toggle');
    const chatContainer = document.getElementById('chat-container');
    const chatClose = document.getElementById('chat-close');
    const chatMessages = document.getElementById('chat-messages');
    const chatInput = document.getElementById('chat-input');
    const chatSend = document.getElementById('chat-send');
    const welcomeMessage = document.getElementById('welcome-message');

    // Load custom welcome message
    async function loadWelcomeMessage() {
        try {
            const response = await fetch('/api/welcome-message');
            const data = await response.json();

            // Update welcome message content
            const messageContent = welcomeMessage.querySelector('.message-content');
            messageContent.innerHTML = data.message;

            // Remove typing indicator
            const typingIndicator = messageContent.querySelector('.typing-indicator');
            if (typingIndicator) {
                typingIndicator.remove();
            }
        } catch (error) {
            console.error('Error loading welcome message:', error);
            // Fallback welcome message
            const messageContent = welcomeMessage.querySelector('.message-content');
            messageContent.innerHTML = 'Hello! How can I help you today?';
            const typingIndicator = messageContent.querySelector('.typing-indicator');
            if (typingIndicator) {
                typingIndicator.remove();
            }
        }
    }

    // Toggle chat
    chatToggle.addEventListener('click', function () {
        chatContainer.classList.toggle('hidden');
        if (!chatContainer.classList.contains('hidden')) {
            loadWelcomeMessage();
        }
    });

    chatClose.addEventListener('click', function () {
        chatContainer.classList.add('hidden');
    });

    // Send message
    function sendMessage() {
        const message = chatInput.value.trim();
        if (!message) return;

        // Add user message
        addMessage(message, 'user');
        chatInput.value = '';

        // Show typing indicator
        const typingMessage = addMessage('', 'bot', true);

        // Send to server
        fetch('/api/send-message', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ message: message })
        })
            .then(response => response.json())
            .then(data => {
                // Remove typing indicator and add response
                typingMessage.remove();
                addMessage(data.reply, 'bot');
            })
            .catch(error => {
                console.error('Error:', error);
                typingMessage.remove();
                addMessage('Sorry, I encountered an error. Please try again.', 'bot');
            });
    }

    // Add message to chat
    function addMessage(content, sender, isTyping = false) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${sender}-message`;

        const messageContent = document.createElement('div');
        messageContent.className = 'message-content';

        if (isTyping) {
            messageContent.innerHTML = `
                <div class="typing-indicator">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            `;
        } else {
            messageContent.textContent = content;
        }

        messageDiv.appendChild(messageContent);
        chatMessages.appendChild(messageDiv);

        // Scroll to bottom
        chatMessages.scrollTop = chatMessages.scrollHeight;

        return messageDiv;
    }

    // Event listeners
    chatSend.addEventListener('click', sendMessage);
    chatInput.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });

    // Load welcome message when widget is first shown
    if (!chatContainer.classList.contains('hidden')) {
        loadWelcomeMessage();
    }
});

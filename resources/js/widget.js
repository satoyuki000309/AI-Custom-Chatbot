document.addEventListener("DOMContentLoaded", function () {
    // Create chat widget container if not already present
    if (!document.getElementById("chat-widget")) {
        const widget = document.createElement("div");
        widget.id = "chat-widget";
        widget.innerHTML = `
            <style>
                #chat-window {
                    display: none;
                    position: fixed;
                    bottom: 80px;
                    right: 20px;
                    width: 300px;
                    height: 400px;
                    background: white;
                    border: 1px solid #ccc;
                    border-radius: 8px;
                    box-shadow: 0 0 10px rgba(0,0,0,0.2);
                    font-family: sans-serif;
                    z-index: 10000;
                    display: flex;
                    flex-direction: column;
                }
                #chat-header {
                    background: #333;
                    color: #fff;
                    padding: 10px;
                    border-top-left-radius: 8px;
                    border-top-right-radius: 8px;
                    font-size: 16px;
                }
                #chat-messages {
                    flex: 1;
                    overflow-y: auto;
                    padding: 10px;
                    font-size: 14px;
                    background: #fafafa;
                }
                .chat-msg { margin: 5px 0; }
                .chat-user { text-align: right; color: #333; }
                .chat-bot { text-align: left; color: #007bff; }
                #chat-input-area {
                    padding: 10px;
                    border-top: 1px solid #eee;
                    background: #fff;
                    display: flex;
                    gap: 5px;
                }
                #chat-input {
                    flex: 1;
                    padding: 6px;
                    font-size: 14px;
                    border: 1px solid #ccc;
                    border-radius: 4px;
                }
                #chat-send-btn {
                    padding: 6px 12px;
                    background: #007bff;
                    color: #fff;
                    border: none;
                    border-radius: 4px;
                    cursor: pointer;
                    font-size: 14px;
                }
            </style>
            <div id="chat-window">
                <div id="chat-header">AI Chatbot</div>
                <div id="chat-messages"></div>
                <div id="chat-input-area">
                    <input type="text" id="chat-input" placeholder="Type your message..." autocomplete="off"/>
                    <button id="chat-send-btn">Send</button>
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
});

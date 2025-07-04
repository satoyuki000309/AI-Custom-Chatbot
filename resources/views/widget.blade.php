<div id="chat-widget"
     style="position:fixed;bottom:20px;right:20px;width:300px;height:400px;border:1px solid #ccc;border-radius:8px;box-shadow:0 0 10px rgba(0,0,0,0.2);background:#fff;font-family:sans-serif;z-index:9999;">
    <div id="chat-header"
         style="background:#333;color:#fff;padding:10px;border-top-left-radius:8px;border-top-right-radius:8px;">
        AI Chatbot
    </div>
    <div id="chat-messages"
         style="height:300px;overflow-y:auto;padding:10px;font-size:14px;">
    </div>
    <div id="chat-input" style="padding:10px;">
        <input type="text" id="chat-text" placeholder="Type your message..."
               style="width:70%;padding:6px;font-size:14px;">
        <button onclick="sendMessage()"
                style="padding:6px 10px;font-size:14px;">Send</button>
    </div>
</div>

<script>
function sendMessage(){
    var text = document.getElementById('chat-text').value.trim();
    if(text === '') return;
    
    var chat = document.getElementById('chat-messages');
    chat.innerHTML += "<div><b>You:</b> " + text + "</div>";
    document.getElementById('chat-text').value = "";
    chat.scrollTop = chat.scrollHeight;

    fetch("/api/send-message", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({ message: text })
    })
    .then(response => response.json())
    .then(data => {
        const botMsg = document.createElement('div');
        botMsg.innerHTML = "<b>Bot:</b> ";
        chat.appendChild(botMsg);

        let i = 0;
        let text = data.reply;
        let typingInterval = setInterval(() => {
            if (i < text.length) {
                botMsg.innerHTML += text.charAt(i);
                i++;
                chat.scrollTop = chat.scrollHeight;
            } else {
                clearInterval(typingInterval);
            }
        }, 20);
    });
    .catch(error => {
        chat.innerHTML += "<div style='color:red;'>Error contacting server.</div>";
    });
}
</script>

<!DOCTYPE html>
<html>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        #chat-widget {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 300px;
            height: 400px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
            background: #fff;
            font-family: sans-serif;
            z-index: 9999;
        }
        #chat-header {
            background: #333;
            color: #fff;
            padding: 10px;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }
        #chat-messages {
            height: 300px;
            overflow-y: auto;
            padding: 10px;
            font-size: 14px;
        }
        #chat-input {
            padding: 10px;
        }
    </style>
</head>
<body>
<div id="chat-widget">
    <div id="chat-header">AI Chatbot</div>
    <div id="chat-messages"></div>
    <div id="chat-input">
        <input type="text" id="chat-text" placeholder="Type your message..." style="width:70%;padding:6px;">
        <button onclick="sendMessage()">Send</button>
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

    fetch("{{ url('/api/send-message') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
</body>
</html>

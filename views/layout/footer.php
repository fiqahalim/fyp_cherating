<?php
$base_url = '/fyp_cherating';
// $base_url = "http://localhost:8000/FYP/fyp_cherating"; //for macbook
$isLoggedIn = !empty($_SESSION['is_logged_in']);
$isAdmin = $isLoggedIn && ($_SESSION['auth_type'] ?? '') === 'admin';
$isCustomer = $isLoggedIn && ($_SESSION['auth_type'] ?? '') === 'customer';
?>

<?php if (!$isAdmin): ?>
<footer>
    <div class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-10">
                    <h3>Contact US</h3>
                    <ul class="conta">
                    <li><i class="fas fa-map-marker" aria-hidden="true"></i> 4/1000 Kampung Budaya, Jalan Kampung Cherating Lama, 26080 Kuantan, Pahang</li>
                    <li><i class="fas fa-mobile" aria-hidden="true"></i> +6011 1103 4533</li>
                    <li> <i class="fas fa-envelope" aria-hidden="true"></i><a href="#"> cherating_guesthouse@gmail.com</a></li>
                    </ul>
                </div>
                <div class="col-md-2">
                    <h3>Menu Link</h3>
                    <ul class="link_menu">
                    <li><a class="nav-link" href="<?= $base_url ?>/">Home</a></li>
                    <li><a class="nav-link" href="<?= $base_url ?>/about">About</a></li>
                    <li><a class="nav-link" href="<?= $base_url ?>/rooms">Our Rooms</a></li>
                    <li><a class="nav-link" href="<?= $base_url ?>/contact">Contact Us</a></li>
                    </ul>
                </div>
                <!-- <div class="col-md-4">
                    <h3>Our Media Social</h3>
                    <ul class="social_icon">
                    <li><a href="#"><i class="fab fa-facebook" aria-hidden="true"></i></a></li>
                    <li><a href="#"><i class="fab fa-twitter" aria-hidden="true"></i></a></li>
                    <li><a href="#"><i class="fab fa-linkedin" aria-hidden="true"></i></a></li>
                    <li><a href="#"><i class="fab fa-youtube-play" aria-hidden="true"></i></a></li>
                    </ul>
                </div> -->
            </div>
        </div>
        <div class="copyright">
            <div class="container">
                <div class="row">
                    <div class="col-md-10 offset-md-1">
                    <p>
                    © 2025 All Rights Reserved. Design by <a href="#"> Amelia</a>
                    </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
<?php endif; ?>

<?php if (!$isAdmin): ?>
    <div id="chat-wrapper" style="position: fixed; bottom: 30px; right: 30px; z-index: 9999; font-family: 'Poppins', sans-serif;">
        <button onclick="toggleChat()" class="btn btn-primary rounded-circle shadow-lg" style="width: 60px; height: 60px; border: none; display: flex; align-items: center; justify-content: center;">
            <i class="fa-solid fa-robot" id="chat-icon" style="font-size: 28px;"></i>
        </button>

        <div id="chat-box" style="display: none; width: 350px; height: 450px; background: white; border-radius: 15px; flex-direction: column; position: absolute; bottom: 80px; right: 0; overflow: hidden; border: 1px solid rgba(0,0,0,0.1);">
            <div class="bg-dark text-white p-3 d-flex justify-content-between align-items-center">
                <span><i class="fas fa-robot"></i> Cherating Guest House AI</span>
                <div>
                    <button onclick="resetChat()" class="btn btn-sm text-dark border-0" title="Reset Chat">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                    <button onclick="toggleChat()" class="btn btn-sm text-dark border-0"><i class="fas fa-times"></i></button>
                </div>
            </div>
            
            <div id="chat-messages" style="flex: 1; overflow-y: auto; padding: 15px; background: #f8f9fa; font-size: 14px;">
                <div class="mb-2"><strong>AI:</strong> How can I help you with your booking today?</div>
            </div>

            <div class="p-3 border-top bg-white">
                <div class="input-group">
                    <input type="text" id="chat-input" class="form-control" placeholder="Type a message..." style="border-radius: 20px 0 0 20px;">
                    <button class="btn btn-primary" onclick="sendMessage()" style="border-radius: 0 20px 20px 0;">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Javascript files-->
<script src="<?= $base_url ?>/vendor/jquery/jquery.min.js"></script>
<script src="<?= $base_url ?>/vendor/bootstrap/js/bootstrap.min.js"></script>
<!-- Other Plugins -->
<script src="<?= $base_url ?>/assets/js/jquery.mCustomScrollbar.concat.min.js"></script>
<script src="<?= $base_url ?>/assets/js/custom.js"></script>
<script>
    function toggleChat() {
        const chatBox = document.getElementById('chat-box');
        const icon = document.getElementById('chat-icon');
        const chatMsg = document.getElementById('chat-messages');
        
        if (chatBox.style.display === 'none' || chatBox.style.display === '') {
            chatBox.style.display = 'flex';
            icon.classList.remove('fa-robot');
            icon.classList.add('fa-times');

            if (chatMsg.children.length <= 1) {
                setTimeout(() => {
                    appendMessage('AI', "Welcome! 👋 I'm your Cherating Assistant. I can help you with <b>room prices</b>, <b>check-in times</b>, and <b>facilities</b>. What would you like to know?");
                }, 500);
            }
        } else {
            chatBox.style.display = 'none';
            icon.classList.remove('fa-times');
            icon.classList.add('fa-robot');
        }
    }

    function sendMessage() {
        const input = document.getElementById('chat-input');
        const message = input.value.trim();
        if (!message) return;

        appendMessage('You', message);
        input.value = '';

        // Show typing indicator
        const typingId = 'typing-' + Date.now();
        const chatMsg = document.getElementById('chat-messages');
        chatMsg.innerHTML += `<div id="${typingId}" class="mb-2 text-muted italic"><strong>AI:</strong> Let me think… 🤔💭</div>`;
        chatMsg.scrollTop = chatMsg.scrollHeight;

        // Send to Controller
        fetch('<?= $base_url ?>/chatbot/ask', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'Accept': 'application/json' 
            },
            body: JSON.stringify({ message: message })
        })
        .then(res => res.json())
        .then(data => {
            setTimeout(() => {
                const typingElem = document.getElementById(typingId);
                if(typingElem) typingElem.remove();
                appendMessage('AI', data.reply);
            }, 1500);
        })
        .catch(err => {
            setTimeout(() => {
                const typingElem = document.getElementById(typingId);
                if(typingElem) typingElem.remove();
                appendMessage('AI', 'Sorry, I am having trouble connecting to the server.');
            }, 1000);
        });
    }

    function sendQuickMsg(text) {
        document.getElementById('chat-input').value = text;
        sendMessage();
    }

    function appendMessage(sender, text) {
        const chatMsg = document.getElementById('chat-messages');
        const div = document.createElement('div');
        div.className = 'mb-2';
        div.innerHTML = `<strong>${sender}:</strong> ${text}`;
        chatMsg.appendChild(div);
        chatMsg.scrollTop = chatMsg.scrollHeight;
    }

    function resetChat() {
        const chatMsg = document.getElementById('chat-messages');
        chatMsg.innerHTML = '';
        appendMessage('AI', "Chat reset! How else can I help you with your booking today? 👋");
    }

    // Allow "Enter" key to send message
    document.getElementById('chat-input').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') sendMessage();
    });
</script>
</body>
</html>
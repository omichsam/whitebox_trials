<!-- Back to Top Button -->
<button onclick="window.scrollTo({top: 0, behavior: 'smooth'})" id="back-to-top"
    class="fixed bottom-6 centre-6 w-12 h-12 ml-4 gradient-bgg  bg-accent text-white rounded-full shadow-lg flex items-center justify-center hover:shadow-xl transition duration-300 z-40">
    <i class="fas fa-chevron-up"></i>
</button>



<style>
    @keyframes typingAnimation {

        0%,
        60%,
        100% {
            transform: translateY(0);
        }

        30% {
            transform: translateY(-4px);
        }
    }

    /* Add these styles to your CSS */
    @keyframes gentle-pulse {

        0%,
        100% {
            box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4);
        }

        50% {
            box-shadow: 0 0 0 10px rgba(59, 130, 246, 0);
        }
    }

    .animate-gentle-pulse {
        animation: gentle-pulse 3s infinite;
    }

    @keyframes bounce-slow {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-5px);
        }
    }

    .animate-bounce-slow {
        animation: bounce-slow 2s infinite;
    }

    @keyframes float-gentle {

        0%,
        100% {
            transform: translateY(0) rotate(0deg);
        }

        33% {
            transform: translateY(-5px) rotate(1deg);
        }

        66% {
            transform: translateY(5px) rotate(-1deg);
        }
    }

    .group:hover .animate-gentle-pulse {
        animation: float-gentle 3s ease-in-out infinite;
    }

    /* Smooth transitions */
    .transition-all {
        transition-property: all;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Image fallback styles */
    img[src*="giphy"] {
        image-rendering: -webkit-optimize-contrast;
        image-rendering: crisp-edges;
    }

    /* Floating animation */


    .animate-float {
        animation: float 3s ease-in-out infinite;
    }

    /* Slow pulse animation */
    @keyframes pulse-slow {

        0%,
        100% {
            opacity: 0.2;
        }

        50% {
            opacity: 0.4;
        }
    }

    .animate-pulse-slow {
        animation: pulse-slow 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    /* Smooth transitions */
    .transition-all {
        transition-property: all;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Shadow utilities */
    .shadow-xl {
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .hover\:shadow-2xl:hover {
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }

    .typing-dot {
        animation: typingAnimation 1.4s infinite ease-in-out;
    }

    .typing-dot:nth-child(1) {
        animation-delay: 0s;
    }

    .typing-dot:nth-child(2) {
        animation-delay: 0.2s;
    }

    .typing-dot:nth-child(3) {
        animation-delay: 0.4s;
    }

    .chat-bg-pattern {
        background-color: #e6ebf0;
        background-image:
            radial-gradient(#d1d9e0 1px, transparent 1px),
            radial-gradient(#d1d9e0 1px, transparent 1px);
        background-size: 20px 20px;
        background-position: 0 0, 10px 10px;
    }
</style>


<!-- Chatbot Icon -->
<!-- <div class="fixed bottom-6 right-6 w-14 h-14 bg-accent/80 rounded-full flex items-center justify-center text-white shadow-lg cursor-pointer z-50 transition-transform hover:scale-110" id="chatbot-icon">
        <i class="fas  fa-comment-dots text-xl fas-2x"></i>
        <div class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hidden" id="notification-badge">1</div>
    </div> -->
<!-- <div class="fixed bottom-6 right-6 w-16 h-16 bg-gradient-to-r from-accent to-blue-500 rounded-full flex items-center justify-center text-white shadow-xl cursor-pointer z-50 transition-all duration-300 hover:scale-110 hover:shadow-2xl animate-float"
    id="chatbot-icon">
    <div class="absolute inset-0 rounded-full bg-accent animate-pulse-slow opacity-70"></div>
    <i class="fas fa-comment-dots text-2xl relative z-10"></i>
    < !-- <div class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold animate-pulse shadow-md"
        id="notification-badge">3</div> - ->
</div> -->


<!-- code -->
<div class="fixed bottom-6 right-6 w-16 h-16 rounded-full flex items-center justify-center cursor-pointer z-50 transition-all duration-300 hover:scale-110 hover:shadow-2xl shadow-xl group"
    id="chatbot-icon">
    
    <!-- Background with shine -->
    <div class="absolute inset-0 rounded-full bg-gradient-to-br from-blue-600 to-accent shadow-inner"></div>
    <div class="absolute top-0 left-0 w-full h-1/2 rounded-t-full bg-gradient-to-b from-white/30 to-transparent"></div>
    
    <!-- Professional avatar -->
    <div class="relative z-10">
        <!-- Avatar circle -->
        <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-md">
            <!-- Professional person icon -->
            <div class="relative">
                <!-- Head with professional look -->
                <div class="w-8 h-8 rounded-full bg-gradient-to-b from-gray-100 to-gray-200 flex flex-col items-center justify-center">
                    <!-- Glasses/smart look -->
                    <div class="flex items-center justify-center space-x-3 mt-1">
                        <div class="w-2 h-2 bg-gray-700 rounded-full"></div>
                        <div class="w-2 h-2 bg-gray-700 rounded-full"></div>
                    </div>
                    <!-- Professional smile -->
                    <div class="w-4 h-0.5 bg-gray-600 mt-1 rounded-full"></div>
                </div>
                <!-- Tie/formal indication -->
                <div class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-1 h-2 bg-blue-600 rounded-b-full"></div>
            </div>
        </div>
        
        <!-- Chat notification -->
        <div class="absolute -top-2 -right-2">
            <div class="relative">
                <!-- Outer ring -->
                <div class="w-7 h-7 bg-white rounded-full flex items-center justify-center shadow-lg">
                    <!-- Speech bubble icon -->
                    <div class="w-5 h-5 bg-gradient-to-r from-accent to-blue-500 rounded-full flex items-center justify-center">
                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                </div>
                <!-- Live indicator -->
                <div class="absolute -bottom-1 -right-1 w-3 h-3 bg-green-400 rounded-full border-2 border-white"></div>
            </div>
        </div>
    </div>
    
    <!-- Hover label -->
    <div class="absolute -top-14 right-0 bg-white text-gray-800 px-4 py-2 rounded-lg shadow-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 min-w-max">
        <div class="flex items-center gap-2 font-medium">
            <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
            <span class="text-sm">Chat with IctaBot</span>
        </div>
        <div class="absolute w-3 h-3 bg-white transform rotate-45 -bottom-1.5 right-5"></div>
    </div>
    
    <!-- Subtle pulse animation -->
    <div class="absolute inset-0 rounded-full border border-white/20 animate-pulse-slow"></div>
</div>
<!-- code -->
<script>
    // Add this JavaScript to make the face interactive
    document.addEventListener('DOMContentLoaded', function () {
        const chatbotIcon = document.getElementById('chatbot-icon');
        const mainFace = document.getElementById('main-face');
        const thinkingFace = document.getElementById('thinking-face');
        const listeningFace = document.getElementById('listening-face');

        // Handle GIF errors
        const faces = [mainFace, thinkingFace, listeningFace];
        faces.forEach(face => {
            face.onerror = function () {
                this.style.display = 'none';
                document.getElementById('svg-face-fallback').style.display = 'flex';
            };
        });

        // Interactive face expressions on hover
        let expressionTimeout;

        chatbotIcon.addEventListener('mouseenter', function () {
            // Switch to listening face
            mainFace.style.opacity = '0';
            listeningFace.style.opacity = '1';

            // After 2 seconds, show thinking face
            clearTimeout(expressionTimeout);
            expressionTimeout = setTimeout(() => {
                listeningFace.style.opacity = '0';
                thinkingFace.style.opacity = '1';
            }, 2000);
        });

        chatbotIcon.addEventListener('mouseleave', function () {
            // Reset to main face
            clearTimeout(expressionTimeout);
            thinkingFace.style.opacity = '0';
            listeningFace.style.opacity = '0';
            mainFace.style.opacity = '1';
        });

        // Click to chat
        chatbotIcon.addEventListener('click', function () {
            // Add click animation
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = 'scale(1.1)';
            }, 100);

            // Open chat window (your implementation)
            openChatWindow();
        });

        // Random face blinking effect
        setInterval(() => {
            if (!chatbotIcon.matches(':hover')) {
                mainFace.style.opacity = '0.8';
                setTimeout(() => {
                    mainFace.style.opacity = '1';
                }, 200);
            }
        }, 5000);
    });
</script>


<!-- Chatbot Container -->
<div class="fixed bottom-24 border border-2 border-light right-6 w-80 h-[500px] bg-white rounded-xl shadow-xl flex flex-col z-60 opacity-0 translate-y-5 pointer-events-none transition-all duration-300"
    id="chatbot-container">
    <!-- Header -->
    <div class="bg-accent text-white p-2 rounded-t-xl flex justify-between items-center">
        <div class="flex items-center">
            <div class="w-10 h-10 rounded-full bg-white bg-opacity-20 flex items-center justify-center mr-3">
                <i class="fas fa-robot"></i>
            </div>
            <div>
                <h4 class="font-semibold">Whitebox Assistant</h4>
                <p class="text-xs opacity-90 flex items-center">
                    <span class="w-2 h-2 bg-green-400 rounded-full mr-1"></span>
                    <span id="status-text">Online</span>
                </p>
            </div>
        </div>
        <div class="flex space-x-2">
            <button class="w-8 h-8 rounded-full hover:bg-white hover:bg-opacity-10 flex items-center justify-center"
                id="menu-toggle" title="Menu">
                <i class="fas fa-bars"></i>
            </button>
            <button class="w-8 h-8 rounded-full hover:bg-white hover:bg-opacity-10 flex items-center justify-center"
                id="format-toggle" title="Formatting">
                <i class="fas fa-text-height"></i>
            </button>
            <button class="w-8 h-8 rounded-full hover:bg-white hover:bg-opacity-10 flex items-center justify-center"
                id="fullscreen-button" title="Expand">
                <i class="fas fa-expand"></i>
            </button>
            <button class="w-8 h-8 rounded-full hover:bg-white hover:bg-opacity-10 flex items-center justify-center"
                id="minimize-button" title="Minimize">
                <i class="fas fa-minus"></i>
            </button>
            <button class="w-8 h-8 rounded-full hover:bg-white hover:bg-opacity-10 flex items-center justify-center"
                id="chatbot-close" title="Close">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <!-- Menu System -->
    <div class="bg-whatsapp-menubg border-b border-gray-200 p-3 hidden" id="menu-container">
        <div class="flex justify-between items-center mb-2">
            <h4 class="font-semibold text-whatsapp-green">Quick Actions</h4>
            <button class="text-red-500 text-sm flex items-center" id="clear-chat">
                <i class="fas fa-trash  mr-1"></i> Clear Chat
            </button>
        </div>
        <div class="grid grid-cols-2 gap-2">
            <div class="bg-white rounded-lg p-2 text-center cursor-pointer border border-gray-200 hover:border-whatsapp-green transition-colors menu-option"
                data-query="What services do you offer?">
                <i class="fas fa-concierge-bell text-whatsapp-green mb-1"></i>
                <p class="text-xs font-medium">Services</p>
            </div>
            <div class="bg-white rounded-lg p-2 text-center cursor-pointer border border-gray-200 hover:border-whatsapp-green transition-colors menu-option"
                data-query="How do I submit a document?">
                <i class="fas fa-file-upload text-whatsapp-green mb-1"></i>
                <p class="text-xs font-medium">Documents</p>
            </div>
            <div class="bg-white rounded-lg p-2 text-center cursor-pointer border border-gray-200 hover:border-whatsapp-green transition-colors menu-option"
                data-query="What are your operating hours?">
                <i class="fas fa-clock text-whatsapp-green mb-1"></i>
                <p class="text-xs font-medium">Hours</p>
            </div>
            <div class="bg-white rounded-lg p-2 text-center cursor-pointer border border-gray-200 hover:border-whatsapp-green transition-colors menu-option"
                data-query="Contact support">
                <i class="fas fa-headset text-whatsapp-green mb-1"></i>
                <p class="text-xs font-medium">Support</p>
            </div>
        </div>
    </div>

    <!-- Formatting Toolbar -->
    <div class="bg-whatsapp-menubg border-b border-gray-200 p-2 hidden flex-wrap" id="format-toolbar">
        <button class="w-8 h-8 rounded flex items-center justify-center hover:bg-gray-200 format-button" data-tag="b"
            title="Bold">
            <i class="fas fa-bold"></i>
        </button>
        <button class="w-8 h-8 rounded flex items-center justify-center hover:bg-gray-200 format-button" data-tag="i"
            title="Italic">
            <i class="fas fa-italic"></i>
        </button>
        <button class="w-8 h-8 rounded flex items-center justify-center hover:bg-gray-200 format-button" data-tag="u"
            title="Underline">
            <i class="fas fa-underline"></i>
        </button>
        <button class="w-8 h-8 rounded flex items-center justify-center hover:bg-gray-200 format-button" data-tag="ol"
            title="Numbered List">
            <i class="fas fa-list-ol"></i>
        </button>
        <button class="w-8 h-8 rounded flex items-center justify-center hover:bg-gray-200 format-button" data-tag="ul"
            title="Bulleted List">
            <i class="fas fa-list-ul"></i>
        </button>
    </div>

    <!-- Chat Messages -->
    <div class="flex-1 overflow-y-auto p-4 chat-bg-pattern" id="chat-messages">
        <div class="flex mb-3">
            <div
                class="w-8 h-8 rounded-full bg-whatsapp-green flex items-center justify-center text-white mr-2 flex-shrink-0">
                <i class="fas fa-robot text-sm text-primary"></i>
            </div>
            <div class="bg-white rounded-lg p-3 shadow max-w-[70%]">
                <div class="text-sm">
                    <p>Hello! 👋 I'm your <strong>Whitebox Assistant</strong>.</p>
                    <p class="mt-1">I can help with:</p>
                    <ul class="list-disc pl-5 mt-1">
                        <li>Whitebox Application</li>
                        <li>Onboarding</li>
                        <li>Entrepreneurship</li>
                        <li>Coaching & more</li>
                    </ul>
                    <p class="mt-1">How can I assist you today?</p>
                </div>
                <div class="text-xs text-primary text-right mt-1"></div>
            </div>
        </div>
    </div>

    <!-- Chat Input -->
    <div class="border-t border-gray-200 p-3 bg-gray-50">
        <div class="flex items-center">
            <div class="flex">
                <button
                    class="w-9 h-9 rounded-full flex items-center justify-center text-gray-500 hover:text-whatsapp-green"
                    id="emoji-button">
                    <i class="far fa-smile"></i>
                </button>
                <button
                    class="w-9 h-9 rounded-full flex items-center justify-center text-gray-500 hover:text-whatsapp-green"
                    id="attach-button">
                    <i class="fas fa-paperclip"></i>
                </button>
            </div>
            <textarea id="message-input"
                class="flex-1 border rounded-full py-2 px-4 mx-2 resize-none focus:outline-none focus:ring-1 focus:ring-whatsapp-green"
                placeholder="Type a message..." rows="1"></textarea>
            <button class="bg-primary w-9 h-9 rounded-full flex items-center justify-center text-white"
                id="send-button">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // DOM Elements
        const chatbotIcon = document.getElementById('chatbot-icon');
        const chatbotContainer = document.getElementById('chatbot-container');
        const chatMessages = document.getElementById('chat-messages');
        const messageInput = document.getElementById('message-input');
        const sendButton = document.getElementById('send-button');
        const closeButton = document.getElementById('chatbot-close');
        const minimizeButton = document.getElementById('minimize-button');
        const fullscreenButton = document.getElementById('fullscreen-button');
        const menuToggle = document.getElementById('menu-toggle');
        const formatToggle = document.getElementById('format-toggle');
        const menuContainer = document.getElementById('menu-container');
        const formatToolbar = document.getElementById('format-toolbar');
        const clearChatButton = document.getElementById('clear-chat');
        const statusText = document.getElementById('status-text');
        const notificationBadge = document.getElementById('notification-badge');
        const emojiButton = document.getElementById('emoji-button');
        const menuOptions = document.querySelectorAll('.menu-option');
        const formatButtons = document.querySelectorAll('.format-button');

        // State variables
        let isChatOpen = false;
        let isFullscreen = false;
        let isMenuVisible = false;
        let isFormatVisible = false;
        let sessionId = generateSessionId();
        //const API_BASE_URL = 'http://localhost:8007/api';
        const API_BASE_URL = 'http://10.241.18.19:8006/api';

        // Generate a unique session ID
        function generateSessionId() {
            return 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        }

        // Get current time for timestamps
        function getCurrentTime() {
            const now = new Date();
            return now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        }

        // Toggle chat visibility
        function toggleChat() {
            isChatOpen = !isChatOpen;
            if (isChatOpen) {
                chatbotContainer.classList.remove('opacity-0', 'translate-y-5', 'pointer-events-none');
                chatbotContainer.classList.add('opacity-100', 'translate-y-0', 'pointer-events-auto');
                messageInput.focus();
                notificationBadge.classList.add('hidden');
            } else {
                chatbotContainer.classList.remove('opacity-100', 'translate-y-0', 'pointer-events-auto');
                chatbotContainer.classList.add('opacity-0', 'translate-y-5', 'pointer-events-none');
                hideMenu();
                hideFormatToolbar();
            }
        }

        // Toggle fullscreen mode
        function toggleFullscreen() {
            isFullscreen = !isFullscreen;
            if (isFullscreen) {
                chatbotContainer.classList.add('w-[calc(100%-3rem)]', 'h-[calc(100%-6rem)]', 'bottom-6', 'right-6');
                fullscreenButton.innerHTML = '<i class="fas fa-compress "></i>';
                fullscreenButton.setAttribute('title', 'Restore');
            } else {
                chatbotContainer.classList.remove('w-[calc(100%-3rem)]', 'h-[calc(100%-6rem)]', 'bottom-6', 'left-6');
                fullscreenButton.innerHTML = '<i class="fas fa-expand"></i>';
                fullscreenButton.setAttribute('title', 'Expand');
            }
        }

        // Toggle menu visibility
        function toggleMenu() {
            isMenuVisible = !isMenuVisible;
            if (isMenuVisible) {
                menuContainer.classList.remove('hidden');
                hideFormatToolbar();
            } else {
                menuContainer.classList.add('hidden');
            }
        }

        // Hide menu
        function hideMenu() {
            isMenuVisible = false;
            menuContainer.classList.add('hidden');
        }

        // Toggle format toolbar visibility
        function toggleFormatToolbar() {
            isFormatVisible = !isFormatVisible;
            if (isFormatVisible) {
                formatToolbar.classList.remove('hidden');
                formatToolbar.classList.add('flex');
                hideMenu();
            } else {
                formatToolbar.classList.add('hidden');
                formatToolbar.classList.remove('flex');
            }
        }

        // Hide format toolbar
        function hideFormatToolbar() {
            isFormatVisible = false;
            formatToolbar.classList.add('hidden');
            formatToolbar.classList.remove('flex');
        }

        // Clear chat history
        function clearChat() {
            // Keep only the first message (the welcome message)
            while (chatMessages.children.length > 1) {
                chatMessages.removeChild(chatMessages.lastChild);
            }

            // Generate a new session ID
            sessionId = generateSessionId();

            // Add a confirmation message
            addMessage("Chat history cleared. How can I help you?", false);

            // Hide menu
            hideMenu();
        }

        // Check API health status
        async function checkAPIHealth() {
            try {
                const response = await fetch(`${API_BASE_URL}/health`);
                const data = await response.json();

                if (data.status === 'healthy') {
                    statusText.textContent = 'Online';
                } else {
                    statusText.textContent = 'Limited functionality';
                }
            } catch (error) {
                console.error('Health check failed:', error);
                statusText.textContent = 'Offline';
            }
        }

        // Add message to chat
        function addMessage(content, isUser = false, timestamp = null) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `flex mb-3 ${isUser ? 'justify-end' : ''}`;

            if (!isUser) {
                messageDiv.innerHTML = `
                        <div class="w-8 h-8 rounded-full bg-whatsapp-green flex items-center justify-center text-white mr-2 flex-shrink-0">
                            <i class="fas fa-robot text-sm text-primary"></i>
                        </div>
                    `;
            }

            const messageContent = document.createElement('div');
            messageContent.className = `bg-${isUser ? 'whatsapp-sent' : 'white'} rounded-lg p-3 shadow max-w-[70%]`;

            // Check if content needs formatting
            const messageText = document.createElement('div');
            messageText.className = 'text-sm';

            if (content.includes('*') || content.includes('- ') || content.includes('1.')) {
                messageText.innerHTML = formatText(content);
            } else {
                messageText.textContent = content;
            }

            messageContent.appendChild(messageText);

            // Add timestamp
            const timeDiv = document.createElement('div');
            timeDiv.className = `text-xs text-gray-500 ${isUser ? 'text-right' : ''} mt-1`;
            timeDiv.textContent = timestamp || getCurrentTime();
            messageContent.appendChild(timeDiv);

            if (isUser) {
                messageDiv.appendChild(messageContent);
            } else {
                const avatarDiv = messageDiv.firstChild;
                messageDiv.appendChild(messageContent);
                messageDiv.insertBefore(avatarDiv, messageContent);
            }

            // For user messages, add after the typing indicator if present
            const typingIndicator = document.querySelector('.typing-indicator');
            if (typingIndicator && isUser) {
                chatMessages.insertBefore(messageDiv, typingIndicator.parentNode);
            } else {
                chatMessages.appendChild(messageDiv);
            }

            scrollToBottom();
        }

        // Format text with markdown-like syntax
        function formatText(text) {
            // Replace *text* with <strong>text</strong>
            text = text.replace(/\*(.*?)\*/g, '<strong>$1</strong>');

            // Replace _text_ with <em>text</em>
            text = text.replace(/_(.*?)_/g, '<em>$1</em>');

            // Replace numbered lists
            text = text.replace(/^(\d+)\.\s+(.*)$/gm, '<li>$2</li>');
            if (text.includes('<li>')) {
                text = text.replace(/(<li>.*<\/li>)/s, '<ol class="list-decimal pl-5 mt-1">$1</ol>');
            }

            // Replace bullet points with dashes
            text = text.replace(/^-\s+(.*)$/gm, '<li>$1</li>');
            if (text.includes('<li>') && !text.includes('<ol')) {
                text = text.replace(/(<li>.*<\/li>)/s, '<ul class="list-disc pl-5 mt-1">$1</ul>');
            }

            // Replace line breaks with paragraphs
            text = text.replace(/\n\n/g, '</p><p class="mt-1">');
            text = '<p>' + text + '</p>';
            text = text.replace(/<p><\/p>/g, '');

            return text;
        }

        // Apply formatting to selected text
        function applyFormatting(tag) {
            const start = messageInput.selectionStart;
            const end = messageInput.selectionEnd;
            const selectedText = messageInput.value.substring(start, end);

            if (selectedText) {
                let formattedText = '';

                switch (tag) {
                    case 'b':
                        formattedText = `*${selectedText}*`;
                        break;
                    case 'i':
                        formattedText = `_${selectedText}_`;
                        break;
                    case 'u':
                        formattedText = `<u>${selectedText}</u>`;
                        break;
                    case 'ol':
                        formattedText = `1. ${selectedText.replace(/\n/g, '\n1. ')}`;
                        break;
                    case 'ul':
                        formattedText = `- ${selectedText.replace(/\n/g, '\n- ')}`;
                        break;
                    default:
                        formattedText = selectedText;
                }

                messageInput.value = messageInput.value.substring(0, start) +
                    formattedText +
                    messageInput.value.substring(end);
            } else {
                // If no text is selected, insert tags at cursor position
                let emptyTags = '';

                switch (tag) {
                    case 'b':
                        emptyTags = '**';
                        break;
                    case 'i':
                        emptyTags = '__';
                        break;
                    case 'u':
                        emptyTags = '<u></u>';
                        break;
                    case 'ol':
                        emptyTags = '1. ';
                        break;
                    case 'ul':
                        emptyTags = '- ';
                        break;
                }

                messageInput.value = messageInput.value.substring(0, start) +
                    emptyTags +
                    messageInput.value.substring(end);
                messageInput.selectionStart = start + (tag === 'u' ? 4 : 1);
                messageInput.selectionEnd = messageInput.selectionStart;
            }

            messageInput.focus();
        }

        // Show typing indicator
        function showTypingIndicator() {
            removeTypingIndicator(); // Remove any existing indicator

            const typingDiv = document.createElement('div');
            typingDiv.className = 'flex mb-3';

            typingDiv.innerHTML = `
                    <div class="w-8 h-8 rounded-full bg-whatsapp-green flex items-center justify-center text-white mr-2 flex-shrink-0">
                        <i class="fas fa-robot text-sm text-primary"></i>
                    </div>
                    <div class="typing-indicator bg-white rounded-lg p-3 shadow flex items-center">
                        <div class="typing-dot w-2 h-2 bg-gray-500 rounded-full mx-1"></div>
                        <div class="typing-dot w-2 h-2 bg-gray-500 rounded-full mx-1"></div>
                        <div class="typing-dot w-2 h-2 bg-gray-500 rounded-full mx-1"></div>
                    </div>
                `;

            chatMessages.appendChild(typingDiv);
            scrollToBottom();
        }

        // Remove typing indicator
        function removeTypingIndicator() {
            const typingIndicator = document.querySelector('.typing-indicator');
            if (typingIndicator) {
                typingIndicator.parentNode.remove();
            }
        }

        // Scroll to bottom of chat
        function scrollToBottom() {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        // Animate message typing
        function typeMessage(message, delay = 30) {
            return new Promise(resolve => {
                let i = 0;
                const messageDiv = document.createElement('div');
                messageDiv.className = 'flex mb-3';

                messageDiv.innerHTML = `
                        <div class="w-8 h-8 rounded-full bg-whatsapp-green flex items-center justify-center text-white mr-2 flex-shrink-0">
                            <i class="fas fa-robot text-sm text-primary"></i>
                        </div>
                        <div class="bg-white rounded-lg p-3 shadow max-w-[70%]">
                            <div class="text-sm message-content"></div>
                        </div>
                    `;

                // Remove typing indicator first if it exists
                removeTypingIndicator();
                chatMessages.appendChild(messageDiv);

                const messageContent = messageDiv.querySelector('.message-content');
                const typeInterval = setInterval(() => {
                    if (i < message.length) {
                        messageContent.innerHTML = formatText(message.substring(0, i + 1));
                        i++;
                        scrollToBottom();
                    } else {
                        clearInterval(typeInterval);

                        // Add timestamp
                        const timeDiv = document.createElement('div');
                        timeDiv.className = 'text-xs text-gray-500 mt-1';
                        timeDiv.textContent = getCurrentTime();
                        messageContent.parentNode.appendChild(timeDiv);

                        resolve();
                    }
                }, delay);
            });
        }

        // Send message to API
        async function sendMessage(message) {
            showTypingIndicator();

            try {
                const response = await fetch(`${API_BASE_URL}/chat`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        question: message,
                        session_id: sessionId
                    })
                });

                if (!response.ok) {
                    throw new Error(`API error: ${response.status}`);
                }

                const data = await response.json();

                // Remove typing indicator
                removeTypingIndicator();

                // Type out the response with animation
                await typeMessage(data.answer);

            } catch (error) {
                console.error('Error sending message:', error);
                removeTypingIndicator();
                await typeMessage("I'm having trouble connecting to the service. Please try again later.");
            }
        }

        // Event listeners
        chatbotIcon.addEventListener('click', function () {
            toggleChat();
        });

        closeButton.addEventListener('click', function () {
            toggleChat();
        });

        minimizeButton.addEventListener('click', function () {
            toggleChat();
        });

        fullscreenButton.addEventListener('click', function () {
            toggleFullscreen();
        });

        menuToggle.addEventListener('click', function () {
            toggleMenu();
        });

        formatToggle.addEventListener('click', function () {
            toggleFormatToolbar();
        });

        clearChatButton.addEventListener('click', function () {
            clearChat();
        });

        sendButton.addEventListener('click', handleSendMessage);

        messageInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                handleSendMessage();
            }
        });

        // Auto-resize textarea
        messageInput.addEventListener('input', function () {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });

        // Menu option handlers
        menuOptions.forEach(option => {
            option.addEventListener('click', function () {
                const query = this.getAttribute('data-query');
                addMessage(query, true);
                sendMessage(query);
                hideMenu();
            });
        });

        // Format button handlers
        formatButtons.forEach(button => {
            button.addEventListener('click', function () {
                const tag = this.getAttribute('data-tag');
                applyFormatting(tag);
            });
        });

        // Emoji button (placeholder functionality)
        emojiButton.addEventListener('click', function () {
            addMessage("😊 Emoji selection would appear here in a full implementation!", false);
        });

        function handleSendMessage() {
            const message = messageInput.value.trim();
            if (message) {
                addMessage(message, true);
                messageInput.value = '';
                messageInput.style.height = 'auto';
                sendMessage(message);
                hideFormatToolbar();
            }
        }

        // Initial API health check
        checkAPIHealth();

        // Periodically check API health (every 60 seconds)
        setInterval(checkAPIHealth, 60000);
    });
</script>
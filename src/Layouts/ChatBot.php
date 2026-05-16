<!-- Floating ChatBot Toggle Button -->
<div id="chatbot-toggle-wrapper" class="chatbot-toggle-wrapper">
    <div class="chatbot-welcome-bubble">
        ¿Hola! ¿En qué puedo ayudarte?
        <div class="bubble-arrow"></div>
    </div>
    <div id="chatbot-toggle" class="chatbot-toggle" onclick="toggleChat()">
        <img src="/assets/img/chat.png" alt="Chat" class="chat-icon-img">
        <div class="notification-badge">1</div>
    </div>
</div>

<!-- ChatBot Container -->
<div id="chatbot-container" class="chatbot-widget-container">
    <div class="chat-container">
        <!-- Header -->
        <div class="header">
            <div class="header-avatar">
                <img src="/assets/img/chat.png" alt="Bot" style="width: 25px; height: 25px; object-fit: contain;">
            </div>
            <div class="header-info">
                <h1>StarTraining AI</h1>
                <div class="status">
                    <div class="status-dot"></div>
                    En línea · IA Asistente
                </div>
            </div>
            <div class="header-actions">
                <button title="Cerrar chat" onclick="toggleChat()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <!-- Chat Box -->
        <div class="chat-box" id="chatBox">
            <div class="date-separator">
                <span>Hoy</span>
            </div>
            <div class="message-wrapper bot" style="animation-delay:.2s">
                <div class="msg-avatar">
                    <img src="/assets/img/chat.png" alt="Bot" style="width: 32px; height: 32px; object-fit: contain;">
                </div>
                <div class="msg-content">
                    <div class="message">¡Hola! 👋 Soy el asistente de <strong>StarTraining</strong>. ¿En qué puedo asistirte hoy?</div>
                    <span class="msg-time" id="botInitTime"></span>
                </div>
            </div>
        </div>

        <!-- Sugerencias -->
        <div class="suggestions" id="suggestions">
            <div class="suggestion-chip" onclick="enviarSugerencia('¿Qué cursos tienen?')">📚 Cursos</div>
            <div class="suggestion-chip" onclick="enviarSugerencia('¿Cómo me registro?')">✍️ Registro</div>
            <div class="suggestion-chip" onclick="enviarSugerencia('Ayuda')">🆘 Ayuda</div>
        </div>

        <!-- Input Area -->
        <div class="input-area">
            <div class="input-wrapper">
                <input 
                    type="text" 
                    id="mensaje" 
                    placeholder="Escribe aquí..."
                    autocomplete="off"
                >
            </div>
            <button class="send-btn" id="sendBtn" onclick="enviarMensaje()">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>
</div>

<style>
/* ── Estilos del Widget Flotante ── */
.chatbot-toggle-wrapper {
    position: fixed;
    bottom: 30px;
    right: 30px;
    z-index: 10000;
    display: flex;
    align-items: center;
    gap: 15px;
}

.chatbot-welcome-bubble {
    background: #ffffff;
    color: #1e293b;
    padding: 12px 20px;
    border-radius: 15px;
    font-size: 13px;
    font-weight: 700;
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    position: relative;
    white-space: nowrap;
    animation: bubbleFloat 3s ease-in-out infinite;
    border: 1px solid rgba(0,0,0,0.05);
}

.bubble-arrow {
    position: absolute;
    right: -8px;
    top: 50%;
    transform: translateY(-50%);
    width: 0; height: 0;
    border-top: 8px solid transparent;
    border-bottom: 8px solid transparent;
    border-left: 10px solid #ffffff;
}

@keyframes bubbleFloat {
    0%, 100% { transform: translateX(0); }
    50% { transform: translateX(-5px); }
}

.chatbot-toggle {
    width: 75px;
    height: 75px;
    background: transparent;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.chatbot-toggle:hover {
    transform: scale(1.15) rotate(10deg);
}

.chat-icon-img {
    width: 65px;
    height: 65px;
    object-fit: contain;
    filter: drop-shadow(0 5px 15px rgba(0,0,0,0.2));
}

.notification-badge {
    position: absolute;
    top: 5px;
    right: 5px;
    background: #ef4444;
    color: white;
    font-size: 10px;
    font-weight: 900;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid white;
    box-shadow: 0 2px 10px rgba(0,0,0,0.2);
}

.chatbot-widget-container {
    position: fixed;
    bottom: 30px;
    right: 120px;
    z-index: 10000;
    display: none;
    opacity: 0;
    transform: translateX(20px) scale(0.95);
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}

.chatbot-widget-container.active {
    display: block;
    opacity: 1;
    transform: translateY(0) scale(1);
}

/* ── Adaptación del CSS del usuario ── */
.chat-container {
    width: 320px;
    height: 480px;
    background: #111827;
    border-radius: 20px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    box-shadow: 0 20px 50px rgba(0,0,0,0.6);
    border: 1px solid rgba(255,255,255,0.1);
}

.header {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    padding: 12px 15px;
    display: flex;
    align-items: center;
    gap: 10px;
    color: white;
}

.header-avatar {
    width: 34px; height: 34px;
    background: rgba(255,255,255,0.2);
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
}

.header-info h1 { font-size: 14px; margin: 0; font-weight: 800; color: #ffffff; }
.status { font-size: 11px; opacity: 0.9; display: flex; align-items: center; gap: 5px; color: #e2e8f0; }
.status-dot { width: 7px; height: 7px; background: #10b981; border-radius: 50%; box-shadow: 0 0 6px #10b981; }

.chat-box {
    flex: 1;
    padding: 15px;
    overflow-y: auto;
    background: #0a0e1a;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.message-wrapper { display: flex; gap: 10px; max-width: 88%; margin-bottom: 2px; }
.message-wrapper.user { align-self: flex-end; flex-direction: row-reverse; }
.message-wrapper.bot { align-self: flex-start; }

.message {
    padding: 10px 14px;
    border-radius: 16px;
    font-size: 13px;
    line-height: 1.5;
    color: #ffffff !important; /* Force white text for readability */
}

.bot .message { background: #1e293b; border-bottom-left-radius: 4px; border: 1px solid rgba(255,255,255,0.05); }
.user .message { background: #4f46e5; border-bottom-right-radius: 4px; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2); }

.msg-time { font-size: 10px; color: #64748b; margin-top: 4px; opacity: 0.8; }

.suggestions { display: flex; gap: 6px; padding: 10px; flex-wrap: wrap; background: #0a0e1a; border-top: 1px solid rgba(255,255,255,0.05); }
.suggestion-chip {
    padding: 6px 12px;
    background: rgba(99,102,241,0.15);
    border: 1px solid rgba(99,102,241,0.3);
    border-radius: 15px;
    color: #a78bfa;
    font-size: 11.5px;
    cursor: pointer;
    transition: 0.3s;
    font-weight: 500;
}

.input-area {
    padding: 12px 15px;
    background: #111827;
    display: flex;
    gap: 10px;
    border-top: 1px solid rgba(255,255,255,0.08);
}

.input-wrapper { flex: 1; position: relative; }
.input-wrapper input {
    width: 100%;
    padding: 10px 14px;
    background: #1a2035;
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 12px;
    color: white;
    outline: none;
    font-size: 13px;
}

.send-btn {
    width: 40px; height: 40px;
    background: #4f46e5;
    border: none;
    border-radius: 12px;
    color: white;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
}

/* Animations and Responsive */
@media (max-width: 480px) {
    .chat-container { width: calc(100vw - 40px); height: 500px; }
}
</style>

<script>
const CHAT_API_URL = "https://chatbot-phyton.onrender.com/chat";

function toggleChat() {
    const container = document.getElementById('chatbot-container');
    const badge = document.querySelector('.notification-badge');
    const bubble = document.querySelector('.chatbot-welcome-bubble');
    
    if (container.style.display === 'block') {
        container.style.opacity = '0';
        container.style.transform = 'translateX(20px) scale(0.95)';
        setTimeout(() => {
            container.style.display = 'none';
            if (bubble) bubble.style.display = 'block'; // Show bubble again when closed
        }, 400);
    } else {
        container.style.display = 'block';
        if (bubble) bubble.style.display = 'none'; // Hide welcome bubble
        
        setTimeout(() => {
            container.classList.add('active');
            container.style.opacity = '1';
            container.style.transform = 'translateX(0) scale(1)';
        }, 10);
        if (badge) badge.style.display = 'none';
    }
}

async function enviarMensaje() {
    const input = document.getElementById('mensaje');
    const texto = input.value.trim();
    if (!texto) return;

    input.value = '';
    agregarChatMsg(texto, 'user');
    
    // Typing...
    const loading = agregarChatTyping();
    
    try {
        const res = await fetch(CHAT_API_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ mensaje: texto })
        });
        const data = await res.json();
        loading.remove();
        agregarChatMsg(data.respuesta, 'bot');
    } catch (err) {
        loading.remove();
        agregarChatMsg("Lo siento, hubo un error de conexión. 😢", "bot");
    }
}

function agregarChatMsg(texto, tipo) {
    const box = document.getElementById('chatBox');
    const wrap = document.createElement('div');
    wrap.className = `message-wrapper ${tipo}`;
    wrap.innerHTML = `
        <div class="msg-content">
            <div class="message">${texto}</div>
        </div>
    `;
    box.appendChild(wrap);
    box.scrollTop = box.scrollHeight;
}

function agregarChatTyping() {
    const box = document.getElementById('chatBox');
    const wrap = document.createElement('div');
    wrap.className = 'message-wrapper bot';
    wrap.innerHTML = '<div class="message">...</div>';
    box.appendChild(wrap);
    box.scrollTop = box.scrollHeight;
    return wrap;
}

function enviarSugerencia(t) {
    document.getElementById('mensaje').value = t;
    enviarMensaje();
}

function limpiarChat() {
    document.getElementById('chatBox').innerHTML = '<div class="date-separator"><span>Hoy</span></div>';
    agregarChatMsg("¡Chat reiniciado! ✨ ¿En qué puedo ayudarte?", "bot");
}

document.getElementById('mensaje')?.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') enviarMensaje();
});

document.getElementById('botInitTime').textContent = new Date().toLocaleTimeString('es-ES', {hour:'2-digit', minute:'2-digit'});
</script>

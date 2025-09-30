<?php
session_start();
require_once 'includes/database.inc.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$receiver_id = $_GET['user_id'] ?? 0;

// Set the anonymous state for the conversation
// This is the key change: it checks if 'anonymous' is in the URL OR the session.
// If it's in the URL, it sets the session, otherwise it uses the existing session value.
$is_anonymous_from_url = $_GET['anonymous'] ?? null;

if ($is_anonymous_from_url !== null) {
    $_SESSION['anonymous_chat'][$receiver_id] = $is_anonymous_from_url == 1;
}

$is_anonymous = $_SESSION['anonymous_chat'][$receiver_id] ?? 0;

// Initialize receiver variable
$receiver = null;

if ($receiver_id > 0) {
    $stmt = $pdo->prepare(
        "SELECT id, FirstName, LastName, profile_picture FROM users WHERE id = ?"
    );
    $stmt->execute([$receiver_id]);
    $receiver = $stmt->fetch(PDO::FETCH_ASSOC);
}

// If receiver not found, redirect or show error
if (!$receiver) {
    echo "<script>alert('User not found.'); window.location.href = 'messages_list.php';</script>";
    exit;
}

// Fetch messages between current user and receiver for initial page load
$msgQuery = "
    SELECT m.*,
           CONCAT(u.FirstName,' ',u.LastName) AS sender_name
    FROM messages m
    JOIN users u ON m.sender_id = u.id
    WHERE (sender_id = :me AND receiver_id = :them)
       OR (sender_id = :them AND receiver_id = :me)
    ORDER BY m.created_at ASC";
$stmt = $pdo->prepare($msgQuery);
$stmt->execute(['me' => $user_id, 'them' => $receiver_id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get the last message ID for the JavaScript
$lastMessageId = !empty($messages) ? end($messages)['id'] : 0;
?>
<?php
// === Decide once if the whole conversation should hide the header ===
// 1) If current user started the conversation anonymously (session flag)
$conversation_is_anonymous = false;
if (!empty($_SESSION['anonymous_chat'][$receiver_id])) {
    $conversation_is_anonymous = true;
}

// 2) Or if there exists any anonymous message between the two users (DB check)
// This covers cases where the other participant sent anonymously.
if (!$conversation_is_anonymous && $receiver_id > 0) {
    $anonCheck = $pdo->prepare(
        "SELECT 1 FROM messages
         WHERE ((sender_id = :me AND receiver_id = :them)
            OR (sender_id = :them AND receiver_id = :me))
           AND is_anonymous = 1
         LIMIT 1"
    );
    $anonCheck->execute(['me' => $user_id, 'them' => $receiver_id]);
    if ($anonCheck->fetchColumn()) {
        $conversation_is_anonymous = true;
    }
}

// OPTIONAL debug (remove after testing):
// echo '<!-- conversation_is_anonymous = ' . ($conversation_is_anonymous ? '1' : '0') . ' -->';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - Ensiuniverse</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="messages.css">
</head>
<body>
<nav class="navbar">
    <div class="nav-brand">
        <div class="logo">E</div>
        <h1>Ensi<span>universe</span></h1>
    </div>
     <p style="color:white;opacity:0.4; font-family:cursive ;   animation: glow 2s ease-in-out infinite;">  If the screen looks wrong, try Ctrl + or Ctrl - </p>
    <div class="user-profile">
        <a href="index.php" class="logout-btn">Logout</a>
    </div>
</nav>

<div class="main-layout">
    <aside class="sidebar">
        <div class="nav-menu">
            <a href="dashboard.php" class="nav-item"><i class="fa-solid fa-house"></i> Home</a>
            <a href="profile.php" class="nav-item"><i class="fa-solid fa-user"></i> Profile</a>
            <a href="#" class="nav-item active"><i class="fa-solid fa-envelope"></i> Messages</a>
        </div>
    </aside>
  <div class="messages-content">
    <?php if (!$conversation_is_anonymous): ?>
        <div class="messages-header">
            <div class="recipient-avatar">
                <?php if (!empty($receiver['profile_picture'])): ?>
                    <img class="user-avatar" id="topAvatar"
                         src="uploads/profiles/<?= htmlspecialchars($receiver['profile_picture']) ?>"
                         alt="User Avatar"
                         style="width:100%; height:100%; object-fit:cover;">
                <?php else: ?>
                    <div class="user-avatar" id="topAvatar"
                         style="width:100%; height:100%; display:flex; align-items:center; justify-content:center;">
                        <?= strtoupper(htmlspecialchars($receiver['FirstName'][0] ?? 'U')) ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="recipient-info">
                <h2><?= htmlspecialchars($receiver['FirstName'].' '.$receiver['LastName']) ?></h2>
                <div class="connection-status">
                    <span class="status-indicator online"></span>
                    <span class="status-text">Connected to this website</span>
                </div>
            </div>
        </div>
    <?php endif; ?>



        <div class="messages-container" id="messagesContainer">
            <?php foreach ($messages as $m): 
                $isSent = $m['sender_id'] == $user_id;
                $classes = $isSent ? 'message sent' : 'message received';
                $senderName = $m['is_anonymous'] && !$isSent ? 'Anonymous' : $m['sender_name'];
            ?>
            <div class="<?= $classes ?>" data-message-id="<?= $m['id'] ?>">
                <div class="message-sender"><?=htmlspecialchars($senderName)?></div>
                <?php if ($m['message_type'] === 'text'): ?>
                    <div class="message-content"><?=nl2br(htmlspecialchars($m['message']))?></div>
                <?php elseif ($m['message_type'] === 'image'): ?>
                    <div class="message-media"><img src="<?=htmlspecialchars($m['media_url'])?>" alt=""></div>
                <?php elseif ($m['message_type'] === 'video'): ?>
                    <div class="message-media"><video controls src="<?=htmlspecialchars($m['media_url'])?>"></video></div>
                <?php endif; ?>
                <div class="message-time"><?=date('H:i', strtotime($m['created_at']))?></div>
            </div>
            <?php endforeach; ?>
        </div>

        <form class="message-input-container" id="messageForm" enctype="multipart/form-data">
    <input type="hidden" name="receiver_id" value="<?= $receiver_id ?>">
    <input type="hidden" name="is_anonymous" value="<?= $is_anonymous ?>">

    <input type="file" id="imageInput" name="image" accept="image/*" style="display:none;">

    <button type="button" class="action-btn" id="imageBtn">
        <i class="fas fa-image"></i>
    </button>
    <textarea
        name="message"
        id="messageInput"
        class="message-input"
        placeholder="Type your message..."
        rows="1"
    ></textarea>

    <button type="submit" class="send-btn" id="sendButton">
        <i class="fas fa-paper-plane"></i>
    </button>
</form>
    </div>
</div>
<script>
// Configuration
const isAnonymous = <?= json_encode($is_anonymous) ?>;
const receiverId = <?= json_encode($receiver_id) ?>;
const userId = <?= json_encode($user_id) ?>;
let lastMessageId = <?= !empty($messages) ? end($messages)['id'] : 0 ?>;
let isSending = false;
let fetchInterval;

// DOM Elements
const messagesContainer = document.getElementById('messagesContainer');
const messageForm = document.getElementById('messageForm');
const messageInput = document.getElementById('messageInput');
const sendButton = document.getElementById('sendButton');

// Auto-resize textarea
messageInput.addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = (this.scrollHeight) + 'px';
});
// Send message via AJAX
async function sendMessage(messageText) {
    if (isSending) return;
    
    isSending = true;
    
    // Update UI immediately for better UX
    const tempId = 'temp-' + Date.now();
    addMessageToUI({
        id: tempId,
        sender_id: userId,
        sender_name: 'You',
        message: messageText,
        message_type: 'text',
        created_at: new Date().toISOString(),
        is_anonymous: isAnonymous ? 1 : 0  // Show if message is anonymous
    });
    
    // Disable send button during request
    sendButton.disabled = true;
    sendButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    
    try {
        const formData = new FormData();
        formData.append('receiver_id', receiverId);
        formData.append('message', messageText);
        formData.append('is_anonymous', isAnonymous ? '1' : '0'); // Send anonymous flag
        
        // Use dedicated API endpoint
        const response = await fetch('send_message.php', {
            method: 'POST',
            body: formData
        });
        
        // Check if response is JSON
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            const data = await response.json();
            
            if (data.success) {
                // Update the temporary message with the real ID
                const tempMessage = document.querySelector(`[data-message-id="${tempId}"]`);
                if (tempMessage) {
                    tempMessage.dataset.messageId = data.message_id;
                    tempMessage.querySelector('.message-time').textContent = data.created_at;
                    lastMessageId = data.message_id;
                }
                
                // Clear input
                messageInput.value = '';
                messageInput.style.height = '45px';
                
                // Immediately fetch new messages after sending
                fetchNewMessages();
            } else {
                // Remove the temporary message if sending failed
                const tempMessage = document.querySelector(`[data-message-id="${tempId}"]`);
                if (tempMessage) {
                    tempMessage.remove();
                }
                
                alert('Error sending message: ' + (data.error || 'Unknown error'));
            }
        } else {
            // Response is not JSON, likely an error
            const text = await response.text();
            console.error('Non-JSON response:', text);
            
            // Remove the temporary message
            const tempMessage = document.querySelector(`[data-message-id="${tempId}"]`);
            if (tempMessage) {
                tempMessage.remove();
            }
            
            alert('Server error. Please check the console for details.');
        }
    } catch (error) {
        console.error('Error:', error);
        
        // Remove the temporary message
        const tempMessage = document.querySelector(`[data-message-id="${tempId}"]`);
        if (tempMessage) {
            tempMessage.remove();
        }
        
        alert('Error sending message. Please check your connection.');
    } finally {
        // Re-enable send button
        sendButton.disabled = false;
        sendButton.innerHTML = '<i class="fas fa-paper-plane"></i>';
        isSending = false;
    }
}

// Fetch new messages
async function fetchNewMessages() {
    try {
        // Use dedicated API endpoint
        const response = await fetch(`fetch_messages.php?receiver_id=${receiverId}&last_id=${lastMessageId}`);
        
        // Check if response is JSON
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            const messages = await response.json();
            
            if (messages.length > 0) {
                // Add new messages to UI
                messages.forEach(message => {
                    // Check if message already exists (prevents duplicates)
                    if (!document.querySelector(`[data-message-id="${message.id}"]`)) {
                        addMessageToUI(message);
                        lastMessageId = Math.max(lastMessageId, message.id);
                    }
                });
                
                // Scroll to bottom if new messages were added
                if (messages.length > 0) {
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;
                }
            }
        } else {
            // Response is not JSON, likely an error
            const text = await response.text();
            console.error('Non-JSON response from fetch:', text);
        }
    } catch (error) {
        console.error('Error fetching messages:', error);
    }
}

// Add a message to the UI
function addMessageToUI(message) {
    const isSent = message.sender_id == userId;
    const classes = isSent ? 'message sent' : 'message received';
    const senderName = message.is_anonymous && !isSent ? 'Anonymous' : message.sender_name;
    
    let content = '';
    if (message.message_type === 'text') {
        content = `<div class="message-content">${escapeHtml(message.message)}</div>`;
    } else if (message.message_type === 'image') {
        content = `<div class="message-media"><img src="${escapeHtml(message.media_url)}" alt=""></div>`;
    } else if (message.message_type === 'video') {
        content = `<div class="message-media"><video controls src="${escapeHtml(message.media_url)}"></video></div>`;
    }
    
    const messageEl = document.createElement('div');
    messageEl.className = classes;
    messageEl.dataset.messageId = message.id;
    messageEl.innerHTML = `
        <div class="message-sender">${escapeHtml(senderName)}</div>
        ${content}
        <div class="message-time">${formatTime(message.created_at)}</div>
    `;
    
    messagesContainer.appendChild(messageEl);
    
    // Scroll to bottom if this is a new message from the current user
    if (isSent) {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
}

// Utility function to escape HTML
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Format time from timestamp
function formatTime(timestamp) {
    const date = new Date(timestamp);
    return date.toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'});
}

// Form submission handler
messageForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const message = messageInput.value.trim();
    if (message) {
        sendMessage(message);
    }
});

// Initial setup when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Scroll to bottom on initial load
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
    
    // Set up interval for fetching new messages
    fetchInterval = setInterval(fetchNewMessages, 2000);
    
    // Also fetch when user focuses on the window (for immediate updates)
    window.addEventListener('focus', fetchNewMessages);
});

// Clean up interval when page is closed or navigated away from
window.addEventListener('beforeunload', function() {
    if (fetchInterval) {
        clearInterval(fetchInterval);
    }
});
</script>
<script>
    // IMAGE: open file dialog and upload
document.getElementById('imageBtn').addEventListener('click', () => {
    document.getElementById('imageInput').click();
});

document.getElementById('imageInput').addEventListener('change', async e => {
    const file = e.target.files[0];
    if (!file) return;

    const fd = new FormData();
    fd.append('receiver_id', receiverId);
    fd.append('is_anonymous', isAnonymous ? '1' : '0');
    fd.append('image', file);

    try {
        const res = await fetch('upload_image.php', { method: 'POST', body: fd });
        const data = await res.json();
        if (data.success) {
            addMessageToUI({
                id: data.message_id,
                sender_id: userId,
                sender_name: 'You',
                message_type: 'image',
                media_url: data.url,
                created_at: data.created_at,
                is_anonymous: isAnonymous ? 1 : 0
            });
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        } else {
            alert(data.error || 'Image upload failed.');
        }
    } catch (err) {
        console.error(err);
        alert('Image upload failed.');
    }
});
</script>
</body>
</html>
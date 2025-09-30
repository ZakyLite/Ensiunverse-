<?php
session_start();
require_once 'includes/database.inc.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Get the requested user ID from URL or use current user
$profile_user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : $_SESSION['user_id'];

// Fetch user data
$sql = "SELECT id, FirstName, LastName, email, bio, profile_picture, cover_photo, registration_date
        FROM users WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$profile_user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: dashboard.php");
    exit;
}

// Fetch user's posts
$sql_posts = "SELECT p.*, p.category,
               CONCAT(u.FirstName,' ',u.LastName) AS author,
               u.profile_picture as author_profile_picture,
               (SELECT COUNT(*) FROM post_likes ps WHERE ps.post_id = p.id) AS supports,
               (SELECT COUNT(*) FROM post_loves pl WHERE pl.post_id = p.id) AS loves,
               (SELECT COUNT(*) FROM comments c WHERE c.post_id = p.id) AS comments
        FROM posts p
        JOIN users u ON p.user_id = u.id
        WHERE p.user_id = ? AND p.is_anonymous = 0
        ORDER BY p.created_at DESC";
$stmt_posts = $pdo->prepare($sql_posts);
$stmt_posts->execute([$profile_user_id]);
$user_posts = $stmt_posts->fetchAll(PDO::FETCH_ASSOC);

// Check if current user is viewing their own profile
$is_own_profile = ($profile_user_id == $_SESSION['user_id']);

// Get current user data for navbar
$current_user_id = $_SESSION['user_id'];
$sql_current = "SELECT FirstName, LastName, profile_picture FROM users WHERE id = ?";
$stmt_current = $pdo->prepare($sql_current);
$stmt_current->execute([$current_user_id]);
$current_user = $stmt_current->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width,initial-scale=1.0" />
<title><?php echo htmlspecialchars($user['FirstName'] . ' ' . $user['LastName']); ?> - Ensiuniverse</title>
<link rel="stylesheet" href="dashboard.css">
<link rel="stylesheet" href="profile.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
/* Additional styles for post avatars */
.post-header .user-avatar, 
.post-header .anonymous-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    color: white;
}

.post-header .anonymous-avatar {
    background-color: #6c757d;
}

.post-header .user-avatar.initials {
    font-size: 1.2rem;
}

.post-card {
    margin-bottom: 20px;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.post-header {
    display: flex;
    align-items: center;
    padding: 15px;
    gap: 12px;
}

.post-meta {
    flex-grow: 1;
}

.post-meta h4 {
    margin: 0;
    font-size: 1rem;
}

.timestamp {
    color: #6c757d;
    font-size: 0.85rem;
}

.post-content {
    padding: 0 15px 15px;
}

.post-actions {
    display: flex;
    border-top: 1px solid #e9ecef;
    padding: 10px 15px;
    gap: 15px;
}

.post-actions button {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 0.9rem;
    color: #6c757d;
    display: flex;
    align-items: center;
    gap: 5px;
}
.post-actions button:hover {
    color: #495057;
}

.post-category-badge {
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 0.8rem;
    background-color: #e9ecef;
    display: flex;
    align-items: center;
    gap: 5px;
}

.post-category-badge[data-category="academic"] {
    background-color: #e8f4ff;
    color: #0066cc;
}

.post-category-badge[data-category="social"] {
    background-color: #fff2e8;
    color: #cc5500;
}

.post-category-badge[data-category="campus"] {
    background-color: #e8f7f0;
    color: #00875a;
}
</style>
</head>
<body>
<nav class="navbar">
  <div class="nav-container">
    <div class="nav-brand">
       <div class="logo">E</div>
      <a href="dashboard.php" style="text-decoration:none; color:black"><h1>Ensi<span>universe</span></a></h1>
    </div>
    <div class="nav-actions">
      <?php if (!empty($current_user['profile_picture'])): ?>
        <img class="user-avatar" id="navbarAvatar"
             src="uploads/profiles/<?php echo htmlspecialchars($current_user['profile_picture']); ?>"
             alt="User Avatar">
      <?php else: ?>
        <div class="user-avatar" id="navbarAvatar">
          <?php echo strtoupper(htmlspecialchars($current_user['FirstName'][0] ?? 'U')); ?>
        </div>
      <?php endif; ?>
      <a href="index.php" class="logout-btn">Logout</a>
    </div>
  </div>
</nav>

<div class="main-layout">
  <aside class="sidebar">
    <div class="nav-menu">
      <a href="dashboard.php" class="nav-item" style="text-decoration: none;"><i class="fa-solid fa-house"></i> Home</a>
      <a href="profile.php" class="nav-item active " style="text-decoration: none;"><i class="fa-solid fa-user"></i> Profile</a>
    <div id="messagesDropdown" class="messages-dropdown" style="display:none;">
      <div class="messages-list" id="messagesList">
      </div>
    </div>
    </div>
  </aside>

  <main class="main-content">
    <!-- Profile Header -->
    <div class="profile-header card">
      <div class="cover-photo">
        <?php if (!empty($user['cover_photo'])): ?>
          <img src="uploads/covers/<?php echo htmlspecialchars($user['cover_photo']); ?>" alt="Cover photo">
        <?php else: ?>
          <div class="default-cover"></div>
        <?php endif; ?>
      </div>
      
      <div class="profile-info">
        <div class="profile-picture">
          <?php if (!empty($user['profile_picture'])): ?>
            <img src="uploads/profiles/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile picture">
          <?php else: ?>
            <div class="default-avatar"><?php echo strtoupper(htmlspecialchars($user['FirstName'][0] . ($user['LastName'][0] ?? ''))); ?></div>
          <?php endif; ?>
        </div>
        
        <div class="profile-details">
          <h1><?php echo htmlspecialchars($user['FirstName'] . ' ' . $user['LastName']); ?></h1>
          <p class="profile-bio">
            <?php echo !empty($user['bio']) ? htmlspecialchars($user['bio']) : 'No bio yet.'; ?>
          </p>
          <div class="profile-stats">
            <div class="stat">
              <span class="stat-number"><?php echo count($user_posts); ?></span>
              <span class="stat-label">Posts</span>
            </div>
            <div class="stat">
              <span class="stat-number">0</span>
              <span class="stat-label">Friends</span>
            </div>
            <div class="stat">
              <span class="stat-number"><?php echo date('M Y', strtotime($user['registration_date'])); ?></span>
              <span class="stat-label">Joined</span>
            </div>
          </div>
          
          <?php if ($is_own_profile): ?>
            <button class="edit-profile-btn"><i class="fas fa-edit"></i> Edit Profile</button>
          <?php else: ?>
          <a class="message-btn" href="message_options.php?user_id=<?php echo $user['id']; ?>">
         <i class="fas fa-envelope"></i> Message
         </a>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Profile Content -->
    <div class="profile-content">
      <!-- About Section -->
      <div class="profile-section card">
        <h2>About</h2>
        <div class="about-details">
          <div class="about-item">
            <i class="fas fa-envelope"></i>
            <span><?php echo htmlspecialchars($user['email']); ?></span>
          </div>
          <div class="about-item">
            <i class="fas fa-calendar-alt"></i>
            <span>Joined <?php echo date('F Y', strtotime($user['registration_date'])); ?></span>
          </div>
        </div>
      </div>

      <!-- User's Posts -->
      <div class="profile-section card">
        <h2>Posts</h2>
        <div class="posts-feed" id="posts-container">
          <?php if (count($user_posts) > 0): ?>
            <?php foreach ($user_posts as $post): ?>
              <div class="post-card card">
                <div class="post-header">
                  <?php
                  $isAnonymous = !empty($post['is_anonymous']) && $post['is_anonymous'] == 1;
                  $displayAuthor = $isAnonymous ? "Anonymous User" : htmlspecialchars($post['author']);
                  
                  if ($isAnonymous) {
                      // Anonymous post - show question mark
                      echo '<div class="anonymous-avatar">?</div>';
                  } else if (!empty($post['author_profile_picture'])) {
                      // Post with profile picture
                      echo '<img class="user-avatar" src="uploads/profiles/' . htmlspecialchars($post['author_profile_picture']) . '" alt="' . htmlspecialchars($post['author']) . '">';
                  } else {
                      // Post without profile picture - show initials with colored background Message
                      $initials = strtoupper(($post['author'][0] ?? '?'));
                  }
                  ?>
                  <div class="post-meta">
                    <h4><?php echo $displayAuthor; ?></h4>
                    <span class="timestamp"><?php echo date("M d, Y H:i", strtotime($post['created_at'])); ?></span>
                  </div>
                  <div class="post-category-badge" data-category="<?php echo htmlspecialchars($post['category'] ?? 'social'); ?>">
                    <?php 
                    $category = $post['category'] ?? 'academic';
                    $icon = '';
                    if ($category === 'academic') {
                        $icon = '<i class="fas fa-graduation-cap"></i>';
                    } elseif ($category === 'social') {
                        $icon = '<i class="fas fa-users"></i>';
                    } elseif ($category === 'campus') {
                        $icon = '<i class="fas fa-building"></i>';
                    }
                    echo $icon . ' ' . htmlspecialchars(ucfirst($category)); 
                    ?>
                  </div>
                </div>
                    <!-- Style -->
                   
                <div class="post-content">
                  <p><?php echo htmlspecialchars($post['content']); ?></p>
                </div>
                <div class="post-actions">
                    <button class="like-btn" data-id="<?= $post['id'] ?>">
                      👍 <span class="like-count"><?= $post['likes'] ?></span>
                    </button>
                    <button class="love-btn" data-post-id="<?= $post['id'] ?>">
                      ❤️ <span class="love-count"><?= $post['loves'] ?></span>
                    </button>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <p class="no-posts">No posts yet.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </main>
</div>

<!-- Edit Profile Modal -->
<div id="editProfileModal" class="modal" style="display: none;">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h2>Edit Profile</h2>
    <form id="editProfileForm" action="update_profile.php" method="POST" enctype="multipart/form-data">
      <div class="form-group">
        <label for="bio">Bio</label>
        <textarea id="bio" name="bio" placeholder="Tell us about yourself"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
      </div>
      <div class="form-group">
        <label for="profile_picture">Profile Picture</label>
        <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
      </div>
      <div class="form-group">
        <label for="cover_photo">Cover Photo</label>
        <input type="file" id="cover_photo" name="cover_photo" accept="image/*">
      </div>
      <button type="submit" class="btn-primary">Save Changes</button>
    </form>
      <p>Please note: small image sizes are recommended to conserve server space.</p>
      <p>Limit size : <span style="color:red">3MB</span></p>
  </div>
</div>

<script>
// Function to generate random colors for avatar backgrounds
function getRandomColor() {
    return '#' + Math.floor(Math.random() * 16777215).toString(16).padStart(6, '0');
}

// Apply random colors to all avatar placeholders
document.addEventListener('DOMContentLoaded', function() {
    const avatarPlaceholders = document.querySelectorAll('.user-avatar.initials');
    avatarPlaceholders.forEach(avatar => {
        avatar.style.backgroundColor = getRandomColor();
    });
    
    // Edit Profile Modal functionality
    document.querySelector('.edit-profile-btn')?.addEventListener('click', function() {
        document.getElementById('editProfileModal').style.display = 'block';
    });

    document.querySelector('.close')?.addEventListener('click', function() {
        document.getElementById('editProfileModal').style.display = 'none';
    });

    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('editProfileModal');
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
});
</script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const messagesBtn = document.getElementById('messagesBtn');
    const messagesDropdown = document.getElementById('messagesDropdown');
    const messagesList = document.getElementById('messagesList');

    messagesBtn.addEventListener('click', () => {
        if (messagesDropdown.style.display === 'none' || messagesDropdown.style.display === '') {
            // Fetch conversations
            fetch('get_conversations.php')
                .then(response => response.json())
                .then(data => {
                    messagesList.innerHTML = ''; // clear old messages

                    if (data.length === 0) {
                        messagesList.innerHTML = '<p class="no-messages">No messages yet.</p>';
                    } else {
                        data.forEach(conv => {
                            const div = document.createElement('div');
                            div.classList.add('message-item');
                            div.innerHTML = `
                                <img src="uploads/profiles/${conv.sender_avatar || 'default.png'}" alt="avatar">
                                <div class="message-info">
                                    <strong>${conv.sender_name}</strong>
                                    <p>${conv.last_message}</p>
                                </div>`;
                            messagesList.appendChild(div);
                        });
                    }

                    messagesDropdown.style.display = 'block';
                })
                .catch(err => console.error('Error fetching messages:', err));
        } else {
            messagesDropdown.style.display = 'none';
        }
    });

    // Close dropdown when clicking outside
    window.addEventListener('click', function(e) {
        if (!messagesDropdown.contains(e.target) && e.target !== messagesBtn) {
            messagesDropdown.style.display = 'none';
        }
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const messagesBtn = document.getElementById('messagesBtn');
    const messagesDropdown = document.getElementById('messagesDropdown');
    const messagesList = document.getElementById('messagesList');

    messagesBtn.addEventListener('click', () => {
        if (messagesDropdown.style.display === 'none' || messagesDropdown.style.display === '') {
            // Fetch conversations
            fetch('load_messages.php')
                .then(response => response.json())
                .then(data => {
                    messagesList.innerHTML = ''; // clear old messages

                    if (data.length === 0) {
                        messagesList.innerHTML = '<p class="no-messages">No messages yet.</p>';
                    } else {
                        data.forEach(conv => {
                            const div = document.createElement('div');
                            div.classList.add('message-item');

                            const avatar = conv.profile_picture !== 'default.png' 
                                ? `<img src="uploads/profiles/${conv.profile_picture}" alt="avatar">`
                                : `<div class="avatar-placeholder" style="background-color:${conv.color}">${conv.initials}</div>`;

                            div.innerHTML = `
                                ${avatar}
                                <div class="message-info">
                                    <strong>${conv.name}</strong>
                                    <p>${conv.last_message}</p>
                                </div>
                            `;
                            messagesList.appendChild(div);
                        });
                    }

                    messagesDropdown.style.display = 'block';
                })
                .catch(err => {
                    messagesList.innerHTML = '<p>Error loading messages.</p>';
                    console.error(err);
                });
        } else {
            messagesDropdown.style.display = 'none';
        }
    });

    // Close dropdown when clicking outside 
    window.addEventListener('click', function(e) {
        if (!messagesDropdown.contains(e.target) && e.target !== messagesBtn) {
            messagesDropdown.style.display = 'none';
        }
    });
});
</script>
<style>
  /* Messages dropdown */
.messages-dropdown {
    position: absolute;
    top: 80px; /* below navbar */
    right: 20px;
    width: 300px;
    max-height: 400px;
    overflow-y: auto;
    background: #fff;
    border: 1px solid #ddd;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    border-radius: 8px;
    z-index: 1000;
}

/* Each message item */
.message-item {
    display: flex;
    align-items: center;
    padding: 8px 12px;
    gap: 10px;
    cursor: pointer;
    transition: background 0.2s;
}

.message-item:hover {
    background-color: #f2f2f2;
}

/* Avatar inside message item */
.message-item img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    flex-shrink: 0;
}

/* Message info */
.message-info {
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.message-info strong {
    font-size: 0.95rem;
    color: #333;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.message-info p {
    font-size: 0.85rem;
    color: #666;
    margin: 2px 0 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* No messages text */
.no-messages {
    padding: 10px;
    text-align: center;
    color: #888;
    font-size: 0.9rem;
}

</style>
<script>
  function loadConversations() {
    messagesList.innerHTML = '<div class="loading">Loading conversations...</div>';

    fetch('ajax_messages_list.php')
        .then(res => res.json())
        .then(data => {
            messagesList.innerHTML = '';
            if (!data.length) {
                messagesList.innerHTML = '<div class="no-conversations">No conversations yet.</div>';
                return;
            }

            data.forEach(conv => {
                const item = document.createElement('a');
                item.href = `messages.php?user_id=${conv.user_id}&anonymous=${conv.is_anonymous}`;
                item.className = 'conversation-item';
                if (conv.unread_count > 0) item.classList.add('unread');

                const avatar = document.createElement('div');
                avatar.className = 'conversation-avatar';

                if (conv.is_anonymous) {
                    avatar.textContent = '?';
                    avatar.classList.add('anonymous-avatar');
                } else if (conv.profile_picture) {
                    const img = document.createElement('img');
                    img.src = 'uploads/profiles/' + conv.profile_picture;
                    img.alt = conv.FirstName;
                    avatar.appendChild(img);
                } else {
                    avatar.textContent = conv.FirstName.charAt(0).toUpperCase();
                }

                const info = document.createElement('div');
                info.className = 'conversation-info';

                const name = document.createElement('span');
                name.className = 'conversation-name';
                name.textContent = conv.is_anonymous ? 'Anonymous User' : conv.FirstName + ' ' + conv.LastName;

                const message = document.createElement('span');
                message.className = 'conversation-last-message';
                let msgText = conv.last_message || 'No messages yet';
                if (msgText.length > 30) msgText = msgText.substring(0, 30) + '...';
                message.textContent = msgText;

                info.appendChild(name);
                info.appendChild(message);
                item.appendChild(avatar);
                item.appendChild(info);
                messagesList.appendChild(item);
            });
        })
        .catch(err => {
            console.error(err);
            messagesList.innerHTML = '<div class="error">Error loading conversations.</div>';
        });
}

</script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const container = document.getElementById('posts-container');

  container.addEventListener('click', e => {
    const btn = e.target.closest('button');
    if (!btn) return;
    e.preventDefault();

    // 👍 LIKE
    if (btn.classList.contains('like-btn')) {
      const id = btn.dataset.id;
      const count = btn.querySelector('.like-count');
      btn.disabled = true;
      fetch('support_post.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'post_id=' + encodeURIComponent(id)
      })
      .then(r => r.json())
      .then(d => { if (d.success) count.textContent = d.likes; })
      .finally(() => btn.disabled = false);
    }

    // ❤️ LOVE
    if (btn.classList.contains('love-btn')) {
      const id = btn.dataset.postId;
      const count = btn.querySelector('.love-count');
      btn.disabled = true;
      fetch('love_post.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'post_id=' + encodeURIComponent(id)
      })
      .then(r => r.json())
      .then(d => {
        if (d.success) {
          count.textContent = d.loves;
          btn.classList.toggle('active', d.action === 'loved');
        }
      })
      .finally(() => btn.disabled = false);
    }
  });
});
</script>
</body>
</html>
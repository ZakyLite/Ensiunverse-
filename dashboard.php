<?php
session_start();
require_once 'includes/database.inc.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Fetch current user's data
$sql_user = "SELECT FirstName, profile_picture FROM users WHERE id = ?";
$stmt_user = $pdo->prepare($sql_user);
$stmt_user->execute([$_SESSION['user_id']]);
$current_user = $stmt_user->fetch(PDO::FETCH_ASSOC);

// Define user's name and profile picture path
$user_name = $current_user['FirstName'] ?? $_SESSION['user_name'] ?? 'U';
$profilePic = !empty($profile_picture) ? '/uploads/' . htmlspecialchars($profile_picture) : null;
// Fetch posts from database
$sql = "SELECT p.*, 
               u.profile_picture,
               (SELECT COUNT(*) FROM post_likes ps WHERE ps.post_id = p.id) AS supports,
               (SELECT COUNT(*) FROM post_loves pl WHERE pl.post_id = p.id) AS loves,
               (SELECT COUNT(*) FROM comments c WHERE c.post_id = p.id) AS comments
        FROM posts p
        LEFT JOIN users u ON p.user_id = u.id
        ORDER BY p.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width,initial-scale=1.0" />
<title>Ensiuniverse Dashboard</title>
<link rel="stylesheet" href="dashboard.css">
<link rel="stylesheet" href="comments.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<?php
// Fix user profile data
$user_name = $current_user['FirstName'] ?? 'U';
$profile_picture = $current_user['profile_picture'] ?? null;
$profilePicPath = $profile_picture ? 'uploads/profiles/' . htmlspecialchars($profile_picture) : null;
?>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const profilePic = <?php echo $profilePic ? json_encode($profilePic) : 'null'; ?>;

    if (profilePic) {
        document.querySelectorAll('.user-avatar').forEach(img => {
            img.src = profilePic;
        });
    }
});
</script>
<body>
<nav class="navbar">
  <div class="nav-container">
    <div class="nav-brand">
      <div class="logo">E</div>
      <a href="dashboard.php" style="text-decoration:none; color:black"><h1>Ensi<span>universe</span></a></h1>
    </div>
    <div class="search-container">
    <form action="search.php" method="GET">
        <input type="text" name="query" placeholder="Search students, teachers..." class="search-input" required>
        <button type="submit" style="display:none">Search</button>
    </form>
</div>
    <div class="nav-actions">
    <button class="anonymous-toggle" id="anonymousToggle">
        <span class="toggle-text">Public</span>
    </button>

    <?php
    $userId = (int)$_SESSION['user_id']; // current logged-in user id
    if (!empty($profile_picture)): ?>
        <a href="profile.php?user_id=<?php echo $userId; ?>">
            <img class="user-avatar" id="topAvatar"
                 src="uploads/profiles/<?php echo htmlspecialchars($profile_picture); ?>"
                 alt="User Avatar">
        </a>
    <?php else: ?>
        <a href="profile.php?user_id=<?php echo $userId; ?>">
            <div class="user-avatar" id="topAvatar">
                <?php echo strtoupper(htmlspecialchars($user_name[0] ?? 'U')); ?>
            </div>
        </a>
    <?php endif; ?>

    <a href="index.php" class="logout-btn">Logout</a>
</div>
  </div>
</nav>

<div class="main-layout">
 <aside class="sidebar">
  <div class="nav-menu">
    <a href="dashboard.php" class="nav-item active" style="text-decoration:none"><i class="fa-solid fa-house"></i> Home</a>
    <a href="profile.php" class="nav-item" style="text-decoration:none"><i class="fa-solid fa-user"></i> Profile</a>
    <a href="#" id="messagesBtn" class="nav-item" style="text-decoration:none"><i class="fa-solid fa-envelope"></i> Messages</a>
    <div id="messagesDropdown" class="messages-dropdown" style="display:none;">
      <div class="messages-list" id="messagesList">
        <p>Loading...</p>
      </div>
    </div>
  </div>
  <div class="nav-menu">
  <button id="notifBell">
  <i class="fa-solid fa-bell"></i>
  <span id="notifDot" class="notif-dot"></span>
  <span id="notifCount" class="badge">Notifications</span>
</button>
</div>
<!-- Notifs drop down -->
<div id="notifDropdown" class="notif-dropdown" style="display:none;">
  <ul id="notifList">
    <!-- JS will fill this with notifications -->
  </ul>
</div>
  <!-- EVENTS CARD -->
  <div class="events-card card">
    <h3>Upcoming Events</h3>
    <div class="events-list" id="sidebarEventsList">
      <p>Loading events...</p>
    </div>
    <hr>
    <form id="sidebarAddEventForm">
      <input type="text" name="title" placeholder="Event title" required>
      <input type="date" name="event_date" required>
      <button type="submit">Add Event</button>
    </form>
  </div>
</aside>


  <main class="main-content">
    <!-- Create Post -->
    <div class="create-post-card card">
      <form id="postForm" method="POST" action="create_posts.php">
        <div class="post-creator">
          <span class="post-username"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
          <?php if (!empty($profile_picture)): ?>
    <img class="user-avatar" id="creatorAvatar"
         src="uploads/profiles/<?php echo htmlspecialchars($profile_picture); ?>"
         alt="User Avatar">
  <?php else: ?>
    <div class="user-avatar" id="topAvatar">
        <?php echo strtoupper(htmlspecialchars($user_name[0] ?? 'U')); ?>
    </div>
    <?php endif; ?>
          <textarea name="content" placeholder="What's on your mind?" class="post-textarea" required></textarea>
          <input type="hidden" name="is_anonymous" id="is_anonymous" value="0">
        </div>
        <div class="post-categories">
          <button type="button" class="category-btn" data-category="academic">Academic</button>
          <button type="button" class="category-btn" data-category="social">Social</button>
          <button type="button" class="category-btn" data-category="campus">Campus</button>
        </div>
        <input type="hidden" name="category" id="selectedCategory" value="Social">
        <div class="post-actions">
          <button type="submit" class="post-btn btn-primary">Post</button>
        </div>
      </form>
    </div>
    <script>
        document.querySelectorAll('.category-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                // set hidden input
                document.getElementById('selectedCategory').value = btn.dataset.category;

                // optional highlight
                document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
            });
        });
  </script>

    <!-- Posts Feed -->
    <div class="posts-feed" id="postsFeed">
       <div id="posts-container">
      <?php foreach ($posts as $post): ?>
        <div class="post-card card">
      <div class="post-header">
  <?php
  $isAnonymous = !empty($post['is_anonymous']) && $post['is_anonymous'] == 1;
  $displayAuthor = $isAnonymous ? "Anonymous User" : htmlspecialchars($post['author']);
  
  if ($isAnonymous) {
      echo '<div class="anonymous-avatar">?</div>';
  } else if (!empty($post['profile_picture'])) {
      echo '<a href="profile.php?user_id=' . (int)$post['user_id'] . '">
            <img class="user-avatar"
                 src="uploads/profiles/' . htmlspecialchars($post['profile_picture']) . '"
                 alt="' . htmlspecialchars($post['author']) . '">
          </a>';
  } else {
      $initials = strtoupper(($post['author'][0] ?? 'U'));
    echo '<a href="profile.php?user_id=' . (int)$post['user_id'] . '">
            <div class="user-avatar initials">' . $initials . '</div>
          </a>';
  }
  ?>
  <div class="post-meta">
    <h4><?php echo $displayAuthor; ?></h4>
    <span class="timestamp"><?php echo date("M d, Y H:i", strtotime($post['created_at'])); ?></span>
  </div>
 <div class="post-category-badge"
     data-category="<?= htmlspecialchars(strtolower($post['category'] ?? 'academic')) ?>">
    <?= htmlspecialchars(ucfirst($post['category'] ?? 'academic')) ?>
    <?php
        $category = strtolower($post['category'] ?? 'academic');
        $icon = '';
        if ($category === 'academic') {
            $icon = '<i class="fas fa-graduation-cap"></i>';
        } elseif ($category === 'social') {
            $icon = '<i class="fas fa-users"></i>';
        } elseif ($category === 'campus') {
            $icon = '<i class="fas fa-building"></i>';
        }
        echo $icon;
    ?>
</div>

</div>


          <div class="post-content" id="post-content-<?php echo $post['id']; ?>">
            <p><?php echo htmlspecialchars($post['content']); ?></p>
          </div>
<div id="posts-container">
  <!-- each post -->
  <div class="post-actions">
    <button class="like-btn" data-id="<?= $post['id'] ?>">
      🤝 <span class="like-count"><?= $post['likes'] ?></span>
    </button>
    <button class="love-btn" data-post-id="<?= $post['id'] ?>">
      ❤️ <span class="love-count"><?= $post['loves'] ?></span>
    </button>
</div>
            <div class="comments-section" id="comments-<?php echo $post['id']; ?>">
              <!-- Comment Button -->
              <button type="button" class="comment-btn" data-post-id="<?php echo $post['id']; ?>">
                💬 <?php echo $post['comments']; ?>
              </button>
              <?php if ($isAnonymous): ?>
          <!-- Anonymous post: force anonymous messaging -->
          <a href="messages.php?user_id=<?php echo $post['user_id']; ?>&anonymous=1" 
            class="message-btn anonymous">
            Message Anonymously
          </a>
          <style>
            .message-btn {
            display: inline-block;
            padding: 6px 12px;
            margin-left: 200px;
            background-color: #4a6baf;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;}
            .message-btn.anonymous {
                background-color: #6c757d;}
            .message-btn:hover { background-color: #505050ff; transition:0.3s }
          </style>
          <p style="font-size:12px;color:gray; text-align:right;">
              To talk publicly, leave a comment with your username.
          </p>
      <?php endif; ?>

              <!-- Comment Form (hidden initially) -->
              <form class="comment-form" data-post-id="<?php echo $post['id']; ?>" style="display:none;">
                <input type="text" name="comment" placeholder="Write a comment..." required>

              <!-- Anonymous toggle -->
            <button type="button" class="anonymous-toggle">
              <span class="toggle-text">Public</span>
            </button>
            <input type="hidden" name="is_anonymous" value="0">

            <button type="submit">Send</button>
            </form>
              <!-- Existing comments -->
              <div class="comments-list" id="comments-list-<?php echo $post['id']; ?>">
                <?php
                $sqlComments = "SELECT c.*, u.FirstName, u.LastName
                FROM comments c
                JOIN users u ON c.user_id = u.id
                WHERE c.post_id = ?
                ORDER BY c.created_at ASC";
                $stmtComments = $pdo->prepare($sqlComments);
                $stmtComments->execute([$post['id']]);
                $comments = $stmtComments->fetchAll(PDO::FETCH_ASSOC);
                foreach ($comments as $comment): ?>
                  <div class="comment-item"
                       data-comment-id="<?php echo (int)$comment['id']; ?>"
                       data-comment-user-id="<?php echo (int)$comment['user_id']; ?>">
                    <span class="comment-author">
  <?php
    if (!empty($comment['is_anonymous']) && $comment['is_anonymous']) {
        echo 'Anonymous User';
    } else {
        echo htmlspecialchars($comment['FirstName'] . ' ' . $comment['LastName']);
    }
  ?>:
</span>
                    <span class="comment-text"><?php echo htmlspecialchars($comment['content']); ?></span>

                    <?php if ($comment['user_id'] == $_SESSION['user_id']): ?>
                      
                      <button class="delete-comment-btn">🗑</button>
                    <?php endif; ?>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>

            <?php if ($post['user_id'] == $_SESSION['user_id']): ?>
              <div class="post-edit-actions">
                <button class="btn-edit" data-post-id="<?php echo $post['id']; ?>">✏ Edit</button>
                <div class="edit-form" id="edit-form-<?php echo $post['id']; ?>" style="display:none;">
                  <form class="inline-edit-form" data-post-id="<?php echo $post['id']; ?>">
                    <textarea name="content" class="edit-active" required><?php echo htmlspecialchars($post['content']); ?></textarea>
                    <button class="save-btn" type="submit">Save</button>
                  </form>
                </div>
                <button type="button" class="btn-delete" data-post-id="<?php echo $post['id']; ?>">🗑 Delete</button>
                <p class="warning-text">This will delete your post</p>
              </div>
           <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </main>
</div>
<script>
/* current user id used to decide whether to show edit/delete for newly added comments */
const currentUserId = <?php echo json_encode((int)$_SESSION['user_id']); ?>;

console.log('Dashboard JavaScript loaded!'); // Debug log
console.log('Current user ID:', currentUserId); // Debug log

// Test: Check if forms exist
setTimeout(() => {
  const commentForms = document.querySelectorAll('.comment-form');
  const editForms = document.querySelectorAll('.inline-edit-form');
  console.log('Found comment forms:', commentForms.length);
  console.log('Found edit forms:', editForms.length);
  
  commentForms.forEach((form, index) => {
    console.log(`Comment form ${index}:`, form);
  });
  
  editForms.forEach((form, index) => {
    console.log(`Edit form ${index}:`, form);
  });
}, 1000);

/* Toggle comment form */
document.addEventListener('click', e => {
  console.log('ANY click:', e.target); // Debug log
  
  if (e.target.classList.contains('comment-btn')) {
    console.log('Comment button clicked!'); // Debug log
    const postId = e.target.dataset.postId;
    const form = document.querySelector(`.comment-form[data-post-id="${postId}"]`);
    const list = document.getElementById(`comments-list-${postId}`);
    const isHidden = window.getComputedStyle(form).display === 'none';
    form.style.display = isHidden ? 'block' : 'none';
    list.style.display = isHidden ? 'block' : 'none';
  }
  
  // Handle anonymous toggle in comment forms
  if (e.target.classList.contains('anonymous-toggle') && e.target.closest('.comment-form')) {
    const hidden = e.target.nextElementSibling; // the hidden input
    const anon = hidden.value === "1";
    hidden.value = anon ? "0" : "1";
    e.target.querySelector('.toggle-text').textContent = anon ? "Public" : "Anonymous";
    e.target.classList.toggle('active', !anon);
  }
  
  // Test: Check if submit button is clicked
  if (e.target.type === 'submit') {
    console.log('Submit button clicked!', e.target); // Debug log
    console.log('Submit button form:', e.target.form); // Debug log
    
    // Handle comment form submission directly
    if (e.target.form && e.target.form.classList.contains('comment-form')) {
      console.log('Handling comment form submission directly!'); // Debug log
      e.preventDefault();
      const form = e.target.form;
      const postId = form.dataset.postId;
      const input = form.querySelector('input[name="comment"]');
      const commentText = input.value.trim();
      
      console.log('Post ID:', postId, 'Comment:', commentText); // Debug log
      
      if (!commentText) {
        console.log('No comment text, returning'); // Debug log
        return;
      }

      const anonValue = form.querySelector('input[name="is_anonymous"]').value;
      console.log('Sending request to add_comment_ajax.php'); // Debug log
      
      fetch('add_comment_ajax.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `post_id=${encodeURIComponent(postId)}&comment=${encodeURIComponent(commentText)}&is_anonymous=${encodeURIComponent(anonValue)}`
      })
      .then(res => {
        console.log('Response received:', res); // Debug log
        return res.json();
      })
      .then(data => {
        console.log('Response data:', data); // Debug log
        if (!data.success) return alert(data.message || 'Could not add comment');

        const list = document.getElementById(`comments-list-${postId}`);
        const div = document.createElement('div');
        div.className = 'comment-item';
        div.dataset.commentId = data.comment_id;
        div.dataset.commentUserId = data.comment_user_id;

        const authorSpan = document.createElement('span');
        authorSpan.className = 'comment-author';
        authorSpan.textContent = data.comment_author + ':';

        const textSpan = document.createElement('span');
        textSpan.className = 'comment-text';
        textSpan.textContent = data.comment_text;

        div.appendChild(authorSpan);
        div.appendChild(textSpan);
        
        if (data.comment_user_id == currentUserId) {
          const editBtn = document.createElement('button');
          editBtn.className = 'edit-comment-btn';
          editBtn.textContent = '';
          div.appendChild(editBtn);

          const delBtn = document.createElement('button');
          delBtn.className = 'delete-comment-btn';
          delBtn.textContent = '🗑';
          div.appendChild(delBtn);
        }

        list.appendChild(div);
        input.value = '';
        console.log('Comment added successfully'); // Debug log
      })
      .catch(err => {
        console.error('Error:', err);
        alert('Network error');
      });
    }
    
    // Handle edit form submission directly
    if (e.target.form && e.target.form.classList.contains('inline-edit-form')) {
      console.log('Handling edit form submission directly!'); // Debug log
      e.preventDefault();
      const form = e.target.form;
      const postId = form.dataset.postId;
      const content = form.querySelector('textarea[name="content"]').value;
      
      console.log('Post ID:', postId, 'Content:', content); // Debug log
      console.log('Sending request to edit_post_ajax.php'); // Debug log
      
      fetch('edit_post_ajax.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `post_id=${encodeURIComponent(postId)}&content=${encodeURIComponent(content)}`
      })
      .then(res => {
        console.log('Edit response received:', res); // Debug log
        return res.json();
      })
      .then(data => {
        console.log('Edit response data:', data); // Debug log
        if (data.success) {
          document.getElementById('post-content-' + postId).querySelector('p').textContent = data.content;
          document.getElementById('edit-form-' + postId).style.display = 'none';
          document.getElementById('post-content-' + postId).style.display = 'block';
          document.querySelector(`.btn-edit[data-post-id="${postId}"]`).style.display = 'inline';
          console.log('Post edited successfully'); // Debug log
        } else {
          alert(data.message);
        }
      })
      .catch(err => {
        console.error('Edit error:', err);
        alert('Network error');
      });
    }
  }
});

/* Add comment via AJAX - using event delegation */
document.addEventListener('submit', e => {
  console.log('ANY form submitted:', e.target); // Debug log
  console.log('Form classes:', e.target.classList); // Debug log
  console.log('Form tag name:', e.target.tagName); // Debug log
  
  if (!e.target.classList.contains('comment-form')) {
    console.log('Not a comment form, ignoring'); // Debug log
    return;
  }
  
  console.log('Comment form submitted!'); // Debug log
  e.preventDefault();
  const form = e.target;
  const postId = form.dataset.postId;
  const input = form.querySelector('input[name="comment"]');
  const commentText = input.value.trim();
  
  console.log('Post ID:', postId, 'Comment:', commentText); // Debug log
  
  if (!commentText) {
    console.log('No comment text, returning'); // Debug log
    return;
  }

  const anonValue = form.querySelector('input[name="is_anonymous"]').value;
  console.log('Sending request to add_comment_ajax.php'); // Debug log
  
  fetch('add_comment_ajax.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: `post_id=${encodeURIComponent(postId)}&comment=${encodeURIComponent(commentText)}&is_anonymous=${encodeURIComponent(anonValue)}`
  })
  .then(res => {
    console.log('Response received:', res); // Debug log
    return res.json();
  })
  .then(data => {
    console.log('Response data:', data); // Debug log
    if (!data.success) return alert(data.message || 'Could not add comment');

    const list = document.getElementById(`comments-list-${postId}`);
    const div = document.createElement('div');
    div.className = 'comment-item';
    div.dataset.commentId = data.comment_id;
    div.dataset.commentUserId = data.comment_user_id;

    const authorSpan = document.createElement('span');
    authorSpan.className = 'comment-author';
    authorSpan.textContent = data.comment_author + ':';

    const textSpan = document.createElement('span');
    textSpan.className = 'comment-text';
    textSpan.textContent = data.comment_text;

    div.appendChild(authorSpan);
    div.appendChild(textSpan);
// only show edit/delete for the creator (regardless of anonymous status)
if (data.comment_user_id == currentUserId) {
  const editBtn = document.createElement('button');
  editBtn.className = 'edit-comment-btn';
  editBtn.textContent = '✏';
  div.appendChild(editBtn);

  const delBtn = document.createElement('button');
  delBtn.className = 'delete-comment-btn';
  delBtn.textContent = '🗑';
  div.appendChild(delBtn);
}

    list.appendChild(div);
    input.value = '';
    console.log('Comment added successfully'); // Debug log
  })
  .catch(err => {
    console.error('Error:', err);
    alert('Network error');
  });
});

/* ---------- DELETE COMMENT & POST (delegation) ---------- */
document.addEventListener('click', e => {
  // DELETE comment
  if (e.target.classList.contains('delete-comment-btn')) {
    const commentItem = e.target.closest('.comment-item');
    const commentId   = commentItem.dataset.commentId;

    if (!confirm('Delete this comment?')) return;

    fetch('delete_comment_ajax.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: `comment_id=${encodeURIComponent(commentId)}`
    })
    .then(r => r.json())
    .then(data => data.success ? commentItem.remove() : alert(data.message || 'Delete failed'))
    .catch(err => { console.error(err); alert('Network error'); });
  }

  // DELETE post
  if (e.target.classList.contains('btn-delete')) {
    const postId   = e.target.dataset.postId;
    const postCard = e.target.closest('.post-card');

    if (!confirm('Delete this post?')) return;

    fetch('delete_post_ajax.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: `post_id=${encodeURIComponent(postId)}`
    })
    .then(r => r.json())
    .then(data => data.success ? postCard.remove() : alert(data.message || 'Delete failed'))
    .catch(err => { console.error(err); alert('Network error'); });
  }
});

/* Handle edit comment form submit (delegated) */
document.addEventListener('submit', e => {
  if (!e.target.classList.contains('edit-comment-form')) return;
  e.preventDefault();
  e.stopPropagation(); // Prevent other handlers from interfering
  const form = e.target;
  const commentItem = form.closest('.comment-item');
  const commentId = commentItem.dataset.commentId;
  const newContent = form.querySelector('textarea[name="content"]').value.trim();
  if (!newContent) return alert('Content required');

  fetch('edit_comment_ajax.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: `comment_id=${encodeURIComponent(commentId)}&content=${encodeURIComponent(newContent)}`
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      const textEl = commentItem.querySelector('.comment-text');
      textEl.textContent = data.content;
      textEl.style.display = 'inline';
      form.style.display = 'none';
    } else {
      alert(data.message || 'Edit failed');
    }
  })
  .catch(err => {
    console.error(err);
    alert('Network error');
  });
});

// delegated submit for all edit-comment forms
document.addEventListener('submit', e => {
  if (!e.target.classList.contains('edit-comment-form')) return;
  e.preventDefault();

  const form        = e.target;
  const commentItem = form.closest('.comment-item');
  const commentId   = commentItem.dataset.commentId;
  const textEl      = commentItem.querySelector('.comment-text');
  const newContent  = form.querySelector('textarea[name="content"]').value.trim();

  if (!newContent) return alert('Content cannot be empty');

  fetch('edit_comment_ajax.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: `comment_id=${encodeURIComponent(commentId)}&content=${encodeURIComponent(newContent)}`
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      textEl.textContent = data.content;
      textEl.style.display = 'inline';
      form.style.display   = 'none';
    } else {
      alert(data.message || 'Failed to edit comment');
    }
  })
  .catch(err => { console.error('Network error:', err); alert('Network error'); });
});

/* Post inline edit handlers - using event delegation */
document.addEventListener('click', e => {
  if (e.target.classList.contains('btn-edit')) {
    const postId = e.target.dataset.postId;
    e.target.style.display = 'none';
    document.getElementById('post-content-' + postId).style.display = 'none';
    document.getElementById('edit-form-' + postId).style.display = 'block';
  }
});

document.addEventListener('submit', e => {
  console.log('Form submitted:', e.target); // Debug log
  console.log('Form classes:', e.target.classList); // Debug log
  
  if (!e.target.classList.contains('inline-edit-form')) {
    console.log('Not an inline edit form, ignoring'); // Debug log
    return;
  }
  
  console.log('Edit form submitted!'); // Debug log
  e.preventDefault();
  const form = e.target;
  const postId = form.dataset.postId;
  const content = form.querySelector('textarea[name="content"]').value;
  
  console.log('Post ID:', postId, 'Content:', content); // Debug log
  console.log('Sending request to edit_post_ajax.php'); // Debug log
  
  fetch('edit_post_ajax.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: `post_id=${encodeURIComponent(postId)}&content=${encodeURIComponent(content)}`
  })
  .then(res => {
    console.log('Edit response received:', res); // Debug log
    return res.json();
  })
  .then(data => {
    console.log('Edit response data:', data); // Debug log
    if (data.success) {
      document.getElementById('post-content-' + postId).querySelector('p').textContent = data.content;
      document.getElementById('edit-form-' + postId).style.display = 'none';
      document.getElementById('post-content-' + postId).style.display = 'block';
      document.querySelector(`.btn-edit[data-post-id="${postId}"]`).style.display = 'inline';
      console.log('Post edited successfully'); // Debug log
    } else {
      alert(data.message);
    }
  })
  .catch(err => {
    console.error('Edit error:', err);
    alert('Network error');
  });
});

/* UI: anonymous toggle & category buttons */
let isAnonymous = false;
const toggle = document.getElementById('anonymousToggle');
const isAnonymousInput = document.getElementById('is_anonymous');
const creatorAvatar = document.getElementById('creatorAvatar');

if (toggle) {
  toggle.addEventListener('click', () => {
    isAnonymous = !isAnonymous;
    toggle.classList.toggle('anonymous', isAnonymous);
    toggle.querySelector('.toggle-text').textContent = isAnonymous ? 'Anonymous' : 'Public';
    isAnonymousInput.value = isAnonymous ? "1" : "0";
    
    // Update the avatar display
    if (isAnonymous) {
      if (creatorAvatar.tagName === 'IMG') {
        // Replace image with anonymous avatar
        const newAvatar = document.createElement('div');
        newAvatar.className = 'anonymous-avatar';
        newAvatar.textContent = '?';
        creatorAvatar.parentNode.replaceChild(newAvatar, creatorAvatar);
      } else {
        // Update existing div avatar
        creatorAvatar.textContent = '?';
        creatorAvatar.className = 'anonymous-avatar';
      }
    } else {
      // Restore original avatar
      <?php if (!empty($profile_picture)): ?>
        const imgAvatar = document.createElement('img');
        imgAvatar.className = 'user-avatar';
        imgAvatar.src = 'uploads/profiles/<?php echo htmlspecialchars($profile_picture); ?>';
        imgAvatar.alt = 'User Avatar';
        creatorAvatar.parentNode.replaceChild(imgAvatar, creatorAvatar);
      <?php else: ?>
        creatorAvatar.textContent = '<?php echo strtoupper(htmlspecialchars($user_name[0] ?? 'U')); ?>';
        creatorAvatar.className = 'user-avatar';
      <?php endif; ?>
    }
  });
}
</script>
<script>
  function getRandomColor() {
  return '#' + Math.floor(Math.random() * 16777215).toString(16).padStart(6, '0');
}

// Assign random color to all avatars without images
document.querySelectorAll('.user-avatar:not(:has(img)), .anonymous-avatar').forEach(el => {
  el.style.backgroundColor = getRandomColor();
});
</script>
<script>
const messagesBtn = document.getElementById('messagesBtn');
const messagesDropdown = document.getElementById('messagesDropdown');
const messagesList = document.getElementById('messagesList');

messagesBtn.addEventListener('click', (e) => {
    e.preventDefault();
    e.stopPropagation();
    
    if (messagesDropdown.style.display === 'none') {
        messagesDropdown.style.display = 'block';
        loadConversations();
    } else {
        messagesDropdown.style.display = 'none';
    }
});

function loadConversations() {
    // Show loading state
    messagesList.innerHTML = '<div class="loading">Loading conversations...</div>';
    
    // Fetch conversations via AJAX
    fetch('ajax_messages_list.php')
        .then(res => {
            if (!res.ok) {
                throw new Error('Network response was not ok');
            }
            return res.json();
        })
        .then(data => {
            messagesList.innerHTML = ''; // clear previous
            
            if (!data.length) {
                messagesList.innerHTML = '<div class="no-conversations">No conversations yet.</div>';
                return;
            }
            
            data.forEach(conv => {
                const item = document.createElement('a');
                item.href = `messages.php?user_id=${conv.user_id}`;
                item.className = 'conversation-item';
                if (conv.unread_count > 0) item.classList.add('unread');
                
                const avatar = document.createElement('div');
                avatar.className = 'conversation-avatar';
                if (conv.profile_picture) {
                    const img = document.createElement('img');
                    img.src = 'uploads/profiles/' + conv.profile_picture;
                    img.alt = conv.FirstName;
                    avatar.appendChild(img);
                } else {
                    const initials = document.createElement('div');
                    initials.className = 'avatar-initials';
                    initials.textContent = conv.FirstName.charAt(0).toUpperCase();
                    avatar.appendChild(initials);
                }
                
                // Add unread badge if needed
                if (conv.unread_count > 0) {
                    const badge = document.createElement('span');
                    badge.className = 'unread-badge';
                    badge.textContent = conv.unread_count;
                    avatar.appendChild(badge);
                }
                
                const info = document.createElement('div');
                info.className = 'conversation-info';
                
                const name = document.createElement('span');
                name.className = 'conversation-name';
                name.textContent = `${conv.FirstName} ${conv.LastName}`;
                
                const message = document.createElement('span');
                message.className = 'conversation-last-message';
                
                // Truncate long messages
                let messageText = conv.last_message || 'No messages yet';
                if (messageText.length > 30) {
                    messageText = messageText.substring(0, 30) + '...';
                }
                message.textContent = messageText;
                
                info.appendChild(name);
                info.appendChild(message);
                
                item.appendChild(avatar);
                item.appendChild(info);
                messagesList.appendChild(item);
            });
        })
        .catch(err => {
            console.error('Error loading conversations:', err);
            messagesList.innerHTML = '<div class="error">Error loading conversations. Please try again.</div>';
        });
}

// Close dropdown when clicking outside
document.addEventListener('click', (e) => {
    if (messagesDropdown && !messagesDropdown.contains(e.target) && !messagesBtn.contains(e.target)) {
        messagesDropdown.style.display = 'none';
    }
});
</script>
<script>
const sidebarEventsList = document.getElementById('sidebarEventsList');
const sidebarAddEventForm = document.getElementById('sidebarAddEventForm');

// Load events from server
function loadSidebarEvents() {
    fetch('ajax_events_list.php')
        .then(res => res.json())
        .then(data => {
            sidebarEventsList.innerHTML = '';
            if (!data.length) {
                sidebarEventsList.innerHTML = '<p>No upcoming events</p>';
                return;
            }

            data.forEach(event => {
                const div = document.createElement('div');
                div.className = 'event-item';
                div.dataset.eventId = event.id;
                div.innerHTML = `
                    <span>${event.title} - ${event.event_date}</span>
                    <button class="remove-event-btn">🗑</button>
                `;
                sidebarEventsList.appendChild(div);
            });
        });
}

// Add Event
sidebarAddEventForm.addEventListener('submit', e => {
    e.preventDefault();
    const formData = new FormData(sidebarAddEventForm);

    fetch('add_event_ajax.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) return alert(data.message || 'Failed to add event');

        // Append the new event instantly
        const div = document.createElement('div');
        div.className = 'event-item';
        div.dataset.eventId = data.event_id;
        div.innerHTML = `
            <span>${data.title} - ${data.event_date}</span>
            <button class="remove-event-btn">🗑</button>
        `;
        sidebarEventsList.appendChild(div);
        sidebarAddEventForm.reset();
        loadSidebarEvents(); // reload to ensure consistency
    });
});

// Remove Event (delegation)
sidebarEventsList.addEventListener('click', e => {
    if (!e.target.classList.contains('remove-event-btn')) return;

    const eventId = e.target.closest('.event-item').dataset.eventId;

    fetch('remove_event_ajax.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `event_id=${encodeURIComponent(eventId)}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            e.target.closest('.event-item').remove();
        } else {
            alert(data.message || 'Failed to remove event');
        }
    });
});

// Initialize sidebar events on page load
document.addEventListener('DOMContentLoaded', loadSidebarEvents);
</script>
<script>
    const notifDot = document.getElementById('notifDot');
    const notifBell = document.getElementById('notifBell');
    const notifDropdown = document.getElementById('notifDropdown');
    const notifList = document.getElementById('notifList');

    async function fetchNotifications() {
        try {
            const res = await fetch('fetch_notifications.php');
            const data = await res.json();
            
            if (data.unread_count > 0) {
                notifDot.style.display = 'block';
            } else {
                notifDot.style.display = 'none';
            }

            notifList.innerHTML = '';
            if (data.notifications.length === 0) {
                notifList.innerHTML = '<li>No notifications yet.</li>';
            } else {
                data.notifications.forEach(notif => {
                    const li = document.createElement('li');
                    li.className = 'notification-item';
                    if (notif.seen == 0) {
                        li.classList.add('unread');
                    }
                    li.innerHTML = `
                        <span>${notif.message}</span>
                        <span class="timestamp">${notif.created_at}</span>
                    `;
                    notifList.appendChild(li);
                });
            }
        } catch (err) {
            console.error('Notification fetch error:', err);
        }
    }

    notifBell.addEventListener('click', () => {
        if (notifDropdown.style.display === 'block') {
            notifDropdown.style.display = 'none';
        } else {
            fetchNotifications();
            notifDropdown.style.display = 'block';

            fetch('mark_notifications_as_seen.php', { method: 'POST' })
                .then(() => {
                    notifDot.style.display = 'none';
                    document.querySelectorAll('.unread').forEach(item => {
                        item.classList.remove('unread');
                    });
                })
                .catch(err => console.error('Mark read error:', err));
        }
    });

    window.addEventListener('click', e => {
        if (!notifBell.contains(e.target) && !notifDropdown.contains(e.target)) {
            notifDropdown.style.display = 'none';
        }
    });

    fetchNotifications();
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

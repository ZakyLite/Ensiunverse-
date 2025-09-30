<?php
session_start();
require_once 'includes/database.inc.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Get search query
$searchQuery = isset($_GET['query']) ? trim($_GET['query']) : '';

if (!empty($searchQuery)) {
    // Search for users (no distinction between students/teachers)
   $sql = "SELECT id, FirstName, LastName, profile_picture 
        FROM users 
        WHERE (FirstName LIKE ? OR LastName LIKE ?) 
        AND id != ?
        ORDER BY FirstName, LastName";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["%$searchQuery%", "%$searchQuery%", $_SESSION['user_id']]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Results - Ensiuniverse</title>
    <link rel="stylesheet" href="dashboard.css">
    <style>
    .search-results {
        padding: 20px;
    }
    .user-result {
        display: flex;
        align-items: center;
        padding: 15px;
        border-bottom: 1px solid #eee;
        transition: background-color 0.2s;
    }
    .user-result:hover {
        background-color: #f9f9f9;
    }
    .user-link {
        display: flex;
        align-items: center;
        text-decoration: none;
        color: inherit;
        flex-grow: 1;
    }
    .user-info {
        margin-left: 15px;
    }
    .user-name {
        font-weight: 600;
        margin-bottom: 5px;
    }
    .no-results {
        text-align: center;
        padding: 40px;
        color: #666;
    }
    .message-user-btn {
        padding: 8px 15px;
        background-color: #4a6baf;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
    .message-user-btn:hover {
        background-color: #3a5a9f;
    }
    </style>
</head>
<body>
   <nav class="navbar">
  <div class="nav-container">
    <div class="nav-brand">
      <div class="logo">E</div>
      <h1>Ensi<span>universe</span></h1>
    </div>
    <div class="search-container">
    <form action="search.php" method="GET">
        <input type="text" name="query" placeholder="Search students, teachers..." class="search-input" required>
        <button type="submit" style="display:none">Search</button>
    </form>
</div>
      <a href="index.php" class="logout-btn">Logout</a>
    </div>
  </div>
</nav>
    <div class="main-layout">
          <aside class="sidebar">
    <div class="nav-menu">
  <a href="dashboard.php" class="nav-item active" style="text-decoration:none"><i class="fa-solid fa-house"></i> Home</a>
  <a href="profile.php" class="nav-item" style="text-decoration:none"><i class="fa-solid fa-user"></i> Profile</a>
  <a href="#" class="nav-item" style="text-decoration:none"><i class="fa-solid fa-envelope"></i> Messages</a>
</div>
  </aside>
        <main class="main-content">
            <div class="search-results card">
                <h2>Search Results for "<?php echo htmlspecialchars($searchQuery); ?>"</h2>
                <?php if (!empty($users)): ?>
                    <div class="users-results">
                        <?php foreach ($users as $user): ?>
                            <div class="user-result">
                                <a href="profile.php?user_id=<?php echo $user['id']; ?>" class="user-link">
                                    <?php if (!empty($user['profile_picture'])): ?>
                                        <img src="uploads/profiles/<?php echo $user['profile_picture']; ?>" 
                                             alt="<?php echo $user['FirstName']; ?>" 
                                             class="user-avatar" 
                                             style="width: 100px; height: 100px; border-radius: 50%; box-shadow: 1px 2px 5px rgba(0, 0, 0, 0.5); ">
                                    <?php else: ?>
                                        <div class="user-avatar" 
                                             style="width: 50px; height: 50px; border-radius: 50%; background-color: #4a6baf; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                                            <?php echo strtoupper($user['FirstName'][0] . ($user['LastName'][0] ?? '')); ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="user-info">
                                        <div class="user-name" style="font-size: 1.2rem;">
                                            <?php echo htmlspecialchars($user['FirstName'] . ' ' . $user['LastName']); ?>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="no-results">
                        <i class="fas fa-search" style="font-size: 3rem; margin-bottom: 15px;"></i>
                        <p>No users found matching "<?php echo htmlspecialchars($searchQuery); ?>"</p>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- Message Modal -->
    <div id="messageModal" class="modal" style="display: none; margin-left: 100px;">
        <div class="modal-content">
            <span class="close" style="cursor:pointer;">&times;</span>
            <h2 style="padding: 10px;">Send Message to <span id="messageRecipientName"></span></h2>
            <form action="send_message.php" method="POST">
                <input type="hidden" name="receiver_id" id="messageRecipientId">
                <div class="form-group">
                    <textarea name="message" placeholder="Type your message here..." required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; resize: vertical;"></textarea>
                </div>
                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 8px; font-family: Arial, Helvetica, sans-serif; font-weight: 600; padding: 10px; color:#666">
                        <input type="checkbox" name="is_anonymous" value="1"> Send anonymously
                    </label>
                </div>
                <button type="submit" class="btn-primary" style="padding: 10px 20px; background-color: #4a6baf; color: white; border: none; border-radius: 5px; cursor: pointer;">Send Message</button>
            </form>
        </div>
    </div>

    <script>
    // Message Modal functionality
    document.querySelectorAll('.message-user-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const userId = this.dataset.userId;
            const userName = this.dataset.userName;
            
            document.getElementById('messageRecipientId').value = userId;
            document.getElementById('messageRecipientName').textContent = userName;
            document.getElementById('messageModal').style.display = 'block';
        });
    });

    // Close modal
    document.querySelector('#messageModal .close').addEventListener('click', function() {
        document.getElementById('messageModal').style.display = 'none';
    });

    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('messageModal');
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
    </script>
</body>
</html>
<?php
session_start();
require_once 'includes/database.inc.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$currentUserId = $_SESSION['user_id'];

// Fetch all conversations
$sql = "
SELECT 
    u.id AS user_id,
    u.FirstName,
    u.LastName,
    u.profile_picture,
    m.content AS last_message,
    m.created_at AS last_message_time,
    SUM(CASE WHEN m.receiver_id = ? AND m.is_read = 0 THEN 1 ELSE 0 END) AS unread_count
FROM users u
JOIN (
    SELECT * FROM messages 
    WHERE sender_id = ? OR receiver_id = ?
) m ON (u.id = m.sender_id OR u.id = m.receiver_id) AND u.id != ?
GROUP BY u.id
ORDER BY last_message_time DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$currentUserId, $currentUserId, $currentUserId, $currentUserId]);
$conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Messages - Ensiuniverse</title>
<link rel="stylesheet" href="dashboard.css">
<style>
.messages-list { max-width: 600px; margin: 20px auto; }
.conversation-item { display: flex; align-items: center; padding: 10px; border-bottom: 1px solid #eee; text-decoration: none; color: inherit; }
.conversation-item:hover { background: #f5f5f5; }
.conversation-avatar { width: 50px; height: 50px; border-radius: 50%; overflow: hidden; background: #ccc; display: flex; align-items: center; justify-content: center; font-size: 20px; margin-right: 10px; }
.conversation-avatar img { width: 100%; height: 100%; object-fit: cover; }
.conversation-info { flex: 1; display: flex; flex-direction: column; }
.conversation-name { font-weight: bold; }
.conversation-last-message { font-size: 14px; color: #555; }
.unread-badge { background: #ff3b3b; color: white; font-size: 12px; padding: 2px 6px; border-radius: 12px; align-self: flex-start; margin-top: 4px; }
.conversation-item.unread .conversation-name { font-weight: 900; }
</style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="messages-list">
  <h2>Your Conversations</h2>

  <?php if (!$conversations): ?>
      <p>You don't have any messages yet. Start a conversation from a user's profile!</p>
  <?php else: ?>
      <?php foreach ($conversations as $conv): ?>
          <a href="messages.php?user_id=<?php echo $conv['user_id']; ?>" class="conversation-item <?php echo $conv['unread_count'] > 0 ? 'unread' : ''; ?>">
              <div class="conversation-avatar">
                  <?php if ($conv['profile_picture']): ?>
                      <img src="uploads/profiles/<?php echo htmlspecialchars($conv['profile_picture']); ?>" alt="Avatar">
                  <?php else: ?>
                      <?php echo strtoupper($conv['FirstName'][0]); ?>
                  <?php endif; ?>
              </div>
              <div class="conversation-info">
                  <span class="conversation-name"><?php echo htmlspecialchars($conv['FirstName'] . ' ' . $conv['LastName']); ?></span>
                  <span class="conversation-last-message"><?php echo htmlspecialchars($conv['last_message']); ?></span>
                  <?php if ($conv['unread_count'] > 0): ?>
                      <span class="unread-badge"><?php echo $conv['unread_count']; ?></span>
                  <?php endif; ?>
              </div>
          </a>
      <?php endforeach; ?>
  <?php endif; ?>
</div>
</body>
</html>

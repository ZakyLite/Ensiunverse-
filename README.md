# Ensiunverse-

[![PHP](https://img.shields.io/badge/Language-PHP-blue.svg)](https://www.php.net/)
[![JavaScript](https://img.shields.io/badge/Language-JavaScript-yellow.svg)](https://www.javascript.com/)
[![License: Unlicensed](https://img.shields.io/badge/license-Unlicensed-red)](./LICENSE)

A social networking platform for ENSIA students.

## 🌟 Description
Ensiuniverse- is a PHP-based web application designed as a social platform exclusively for students of ENSIA (École Nationale Supérieure d'Informatique et d'Analyse). It enables students to connect with peers, share ideas, and engage within a secure and private online community.

## 📑 Table of Contents
1.  [Features](#-features)
2.  [Tech Stack](#-tech-stack)
3.  [Installation](#-installation)
4.  [Usage](#-usage)
5.  [Project Structure](#-project-structure)
6.  [API Reference](#-api-reference)
7.  [Contributing](#-contributing)
8.  [License](#-license)
9. [Important links](#important-links)
10. [Footer](#footer)

## ✨ Features

*   **User Authentication**: Secure login and registration with email verification using PHPMailer.
*   **Profile Management**: Users can create and edit profiles, including uploading profile pictures and cover photos.
*   **Posts Creation**: Users can create posts, categorized under 'Academic', 'Social', and 'Campus'. Posts can be created anonymously.
*   **Posts Feed**: Display of posts with like and love reactions.
*   **Commenting System**: Users can comment on posts, with the option to comment anonymously.
*   **Real-time Messaging**: Implements one-on-one messaging with the option for anonymous messaging.
*   **Notifications**: Users receive notifications for likes and supports on their posts.
*   **Events**: Display of upcoming events with the ability to add and remove events from a sidebar.
*   **Dark/Light Mode**: Toggle for switching between dark and light themes.
*   **Search Functionality**: Users can search for other users by name.

## 🛠️ Tech Stack
*   **Backend**: PHP
*   **Frontend**: JavaScript, CSS, HTML
*   **Database**: MySQL
*   **Frameworks/Libraries**: PHPMailer, Font Awesome

## ⚙️ Installation

1.  **Clone the repository**:
    ```bash
    git clone https://github.com/ZakyLite/Ensiunverse-.git
    cd Ensiunverse-
    ```
2.  **Import Database**: Import `if0_39935832_ensia_connect` database to your local system with phpMyAdmin or any other MySQL client.
3.  **Database Connection**: Edit `includes/database.inc.php` with your database credentials:
    ```php
    <?php
    $host = "your_host";
    $port = 3306; 
    $dbname = "your_dbname";
    $user = "your_dbuser";
    $pass = "your_dbpassword";
    
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    
    try {
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    
        // ✅ Set MySQL session time zone to Algeria (UTC+1)
        $pdo->exec("SET time_zone = '+01:00'");
    
        // ✅ Make sure PHP uses the same time zone
        date_default_timezone_set('Africa/Algiers');
    
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
    ?>
    ```
4.  **PHPMailer Setup**: Ensure PHPMailer is correctly configured for email functionality, especially for password reset and email verification. Update the credentials in `includes/ForgottenpwdHandler.php` and `includes/RegisterHandler.php`.
    ```php
    $mail = new PHPMailer(true);
    try {
        // Server settings for Gmail
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'your_email@gmail.com';
        $mail->Password   = 'your_app_password';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;
    
    ```
5.  **Install Composer Dependencies**: Run `composer install` to install PHPMailer.

## 💻 Usage

1.  **Web Server**: Deploy the project files to a PHP-compatible web server (e.g., Apache, Nginx).
2.  **Base URL**: Access the application through your web browser.
3.  **Registration and Login**: New users can register via the registration form on the landing page (`index.php`), while existing users can log in.
4.  **Dashboard**: Once logged in, users are redirected to the dashboard (`dashboard.php`), where they can create posts, view the posts feed, and manage their profiles.
5.  **Real-world Use Cases**: For new students, Ensiuniverse serves as a virtual orientation platform, while senior students can leverage it for networking and sharing opportunities.

### How To Use

*   **Creating Posts**: Use the post creation form on the dashboard to share thoughts, ideas, or media. Choose a category and decide whether to post anonymously.
    ```html
    <form id="postForm" method="POST" action="create_posts.php">
    ```
*   **Engaging with Posts**: Like, love, or comment on posts in the feed. React to posts to show support or appreciation.
    ```php
    <button class="like-btn" data-id="<?= $post['id'] ?>">
      🤝 <span class="like-count"><?= $post['likes'] ?></span>
    </button>
    ```
*   **Messaging**: Visit user profiles and use the message button to start a private chat.
    ```html
    <a class="message-btn" href="message_options.php?user_id=<?php echo $user['id']; ?>">
      <i class="fas fa-envelope"></i> Message
    </a>
    ```

## 📂 Project Structure

```
Ensiunverse-/
├── README.md
├── index.js
├── ColorMods.js
├── Forgottenpwd.css
├── Forgottenpwd.php
├── Register.css
├── add_comment_ajax.php
├── add_event_ajax.php
├── ajax_events_list.php
├── ajax_messages_list.php
├── ajax_notification.php
├── comments.css
├── create_posts.php
├── dashboard.css
├── dashboard.php
├── debug_messages.php
├── delete_comment_ajax.php
├── delete_post_ajax.php
├── edit_comment.php
├── edit_comment_ajax.php
├── edit_post.php
├── edit_post_ajax.php
├── fetch_messages.php
├── fetch_notifications.php
├── get_conversations.php
├── includes
│   ├── ForgottenpwdHandler.php
│   ├── LoginHandler.php
│   ├── RegisterHandler.php
│   ├── database.inc.php
│   └── verify.php
├── index.css
├── index.php
├── load_messages.php
├── login.css
├── love_post.php
├── mark_notifications_as_seen.php
├── message_list.php
├── message_options.php
├── messages.css
├── messages.php
├── profile.css
├── profile.php
├── remove_event_ajax.php
├── reset_password.php
├── search.php
├── send_message.php
├── session_test.php
├── support_post.php
├── update_profile.php
├── upload_image.php
└── upload_voice.php
```

## 🛡️ API Reference

| Endpoint                     | Method | Description                                            | Authentication | Data Format | Example Usage                                                                                    |
| ---------------------------- | ------ | ------------------------------------------------------ | -------------- | ----------- | ---------------------------------------------------------------------------------------------- |
| `add_comment_ajax.php`       | POST   | Adds a comment to a post                               | Session        | JSON        | `post_id`, `comment`, `is_anonymous`                                                         |
| `add_event_ajax.php`         | POST   | Adds an event to the sidebar                            | Session        | JSON        | `title`, `event_date`                                                                          |
| `ajax_events_list.php`       | GET    | Retrieves a list of events for the sidebar              | Session        | JSON        | None                                                                                           |
| `ajax_messages_list.php`     | GET    | Retrieves a list of conversations for the current user   | Session        | JSON        | None                                                                                           |
| `ajax_notification.php`       | POST   | Creates a notification (e.g., for likes)                | Session        | JSON        | `targetUserId`, `postId`, `message`                                                          |
| `delete_comment_ajax.php`    | POST   | Deletes a comment                                      | Session        | JSON        | `comment_id`                                                                                   |
| `delete_post_ajax.php`       | POST   | Deletes a post                                         | Session        | JSON        | `post_id`                                                                                      |
| `edit_comment_ajax.php`      | POST   | Edits a comment                                        | Session        | JSON        | `comment_id`, `content`                                                                        |
| `edit_post_ajax.php`         | POST   | Edits a post                                           | Session        | JSON        | `post_id`, `content`                                                                           |
| `fetch_messages.php`         | GET    | Retrieves messages for a conversation                 | Session        | JSON        | `receiver_id`, `last_id`                                                                       |
| `fetch_notifications.php`    | GET    | Retrieves notifications for the current user            | Session        | JSON        | None                                                                                           |
| `load_messages.php`          | GET    | Retrieves message data for display                     | Session        | JSON        | None                                                                                           |
| `love_post.php`             | POST   | Adds or removes a 
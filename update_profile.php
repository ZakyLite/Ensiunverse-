<?php
session_start();
require_once 'includes/database.inc.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bio = trim($_POST['bio'] ?? '');
    
    // Handle file uploads
    $profile_picture = null;
    $cover_photo = null;
    $upload_errors = [];
    
    // Upload profile picture
    if (!empty($_FILES['profile_picture']['name']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $profile_picture = uploadFile($_FILES['profile_picture'], 'profiles', $upload_errors);
    } elseif (!empty($_FILES['profile_picture']['name'])) {
        $upload_errors[] = "Profile picture upload error: " . getUploadError($_FILES['profile_picture']['error']);
    }
    
    // Upload cover photo
    if (!empty($_FILES['cover_photo']['name']) && $_FILES['cover_photo']['error'] === UPLOAD_ERR_OK) {
        $cover_photo = uploadFile($_FILES['cover_photo'], 'covers', $upload_errors);
    } elseif (!empty($_FILES['cover_photo']['name'])) {
        $upload_errors[] = "Cover photo upload error: " . getUploadError($_FILES['cover_photo']['error']);
    }
    
    // Update database
    try {
        // Start building the query
        $sql = "UPDATE users SET bio = ?";
        $params = [$bio];
        
        if ($profile_picture) {
            $sql .= ", profile_picture = ?";
            $params[] = $profile_picture;
        }
        
        if ($cover_photo) {
            $sql .= ", cover_photo = ?";
            $params[] = $cover_photo;
        }
        
        $sql .= " WHERE id = ?";
        $params[] = $user_id;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        // Redirect with appropriate message
        if (!empty($upload_errors)) {
            header("Location: profile.php?success=1&warnings=" . urlencode(implode(', ', $upload_errors)));
        } else {
            header("Location: profile.php?success=1");
        }
        exit;
    } catch (PDOException $e) {
        error_log("Profile update error: " . $e->getMessage());
        header("Location: profile.php?error=1");
        exit;
    }
}

function uploadFile($file, $type, &$errors = []) {
    $upload_dir = "uploads/$type/";
    
    // Create directory if it doesn't exist
    if (!file_exists($upload_dir)) {
        if (!mkdir($upload_dir, 0755, true)) {
            $errors[] = "Failed to create directory: $upload_dir";
            return null;
        }
    }
    
    // Check if directory is writable
    if (!is_writable($upload_dir)) {
        $errors[] = "Directory is not writable: $upload_dir";
        return null;
    }
    
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $file_name = uniqid() . '.' . $file_extension;
    $file_path = $upload_dir . $file_name;
    
    // Check file type
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mime_type, $allowed_types)) {
        $errors[] = "Invalid file type. Allowed: JPEG, PNG, GIF, WEBP";
        return null;
    }
    
    // Check file size (max 5MB)
    if ($file['size'] > 3 * 1024 * 1024) {
        $errors[] = "File too large. Maximum size is 3MB";
        return null;
    }
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "Upload error: " . getUploadError($file['error']);
        return null;
    }
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $file_path)) {
        // Check if the file is a valid image
        if (!getimagesize($file_path)) {
            unlink($file_path); // Delete invalid file
            $errors[] = "Uploaded file is not a valid image";
            return null;
        }
        return $file_name;
    } else {
        $errors[] = "Failed to move uploaded file";
        return null;
    }
}

function getUploadError($error_code) {
    switch ($error_code) {
        case UPLOAD_ERR_INI_SIZE:
            return "File exceeds upload_max_filesize directive in php.ini";
        case UPLOAD_ERR_FORM_SIZE:
            return "File exceeds MAX_FILE_SIZE directive in form";
        case UPLOAD_ERR_PARTIAL:
            return "File was only partially uploaded";
        case UPLOAD_ERR_NO_FILE:
            return "No file was uploaded";
        case UPLOAD_ERR_NO_TMP_DIR:
            return "Missing temporary folder";
        case UPLOAD_ERR_CANT_WRITE:
            return "Failed to write file to disk";
        case UPLOAD_ERR_EXTENSION:
            return "File upload stopped by extension";
        default:
            return "Unknown upload error";
    }
}
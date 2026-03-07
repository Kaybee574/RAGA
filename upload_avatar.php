<?php
ob_start(); // Buffer output to prevent warnings from leaking
require_once 'DatabaseConnection.php';
require_once 'session_helper.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
        $userType = getUserType();
        
        $file = $_FILES['avatar'];
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errMsgs = [
                UPLOAD_ERR_INI_SIZE   => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
                UPLOAD_ERR_FORM_SIZE  => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
                UPLOAD_ERR_PARTIAL    => 'The uploaded file was only partially uploaded.',
                UPLOAD_ERR_NO_FILE    => 'No file was uploaded.',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
                UPLOAD_ERR_EXTENSION  => 'A PHP extension stopped the file upload.',
            ];
            throw new Exception($errMsgs[$file['error']] ?? 'Unknown upload error.');
        }

        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception('Invalid file type. Only JPG, PNG, GIF, and WEBP are allowed.');
        }

        $fileName = time() . '_' . preg_replace("/[^a-zA-Z0-9._-]/", "_", basename($file['name']));
        $uploadDir = 'uploads/avatars/';
        
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0777, true)) {
                throw new Exception('Failed to create upload directory.');
            }
        }
        
        $targetPath = $uploadDir . $fileName;
        
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            $_SESSION['avatar_url'] = $targetPath;
            
            if ($userType === 'seller' || $userType === 'both') {
                $email = $_SESSION['email'] ?? $_SESSION['user_id'];
                $updateSeller = "UPDATE sellers SET avatar_url = '" . mysqli_real_escape_string($conn, $targetPath) . "' WHERE email = '" . mysqli_real_escape_string($conn, $email) . "'";
                mysqli_query($conn, $updateSeller);
            }
            
            if ($userType === 'buyer' || $userType === 'both') {
                $studentNum = $_SESSION['student_number'] ?? $_SESSION['user_id'];
                $updateBuyer = "UPDATE buyers SET avatar_url = '" . mysqli_real_escape_string($conn, $targetPath) . "' WHERE student_number = '" . mysqli_real_escape_string($conn, $studentNum) . "'";
                mysqli_query($conn, $updateBuyer);
            }
            
            ob_end_clean();
            echo json_encode(['success' => true, 'avatar_url' => $targetPath]);
        } else {
            throw new Exception('Failed to move uploaded file to target directory.');
        }
    } else {
        throw new Exception('Invalid request.');
    }
} catch (Exception $e) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>

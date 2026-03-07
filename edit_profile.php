<?php
require_once 'session_helper.php';
require_once 'DatabaseConnection.php';

if (!isLoggedIn()) {
    header("Location: SignIn.html");
    exit();
}

$user_type = getUserType();

// Fetch current data
if ($user_type === 'seller') {
    $email = $_SESSION['email'];
    $query = "SELECT * FROM sellers WHERE email = '$email'";
} elseif ($user_type === 'buyer') {
    $student_number = $_SESSION['student_number'];
    $query = "SELECT * FROM buyers WHERE student_number = '$student_number'";
} else {
    // For 'both', we can fetch from both, but usually we just need one at a time.
    // Let's default to buyer for now or allow switching.
    // Actually, let's just use student_number as primary if 'both'
    $student_number = $_SESSION['student_number'];
    $query = "SELECT * FROM buyers WHERE student_number = '$student_number'";
}

$result = mysqli_query($conn, $query);
$user_data = mysqli_fetch_assoc($result);

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    
    if ($user_type === 'seller') {
        $email = $_SESSION['email'];
        $contact = mysqli_real_escape_string($conn, $_POST['contact_number']);
        $update_query = "UPDATE sellers SET full_name = '$full_name', address = '$address', contact_number = '$contact' WHERE email = '$email'";
    } else {
        $student_number = $_SESSION['student_number'];
        $update_query = "UPDATE buyers SET full_name = '$full_name', address = '$address' WHERE student_number = '$student_number'";
    }

    if (mysqli_query($conn, $update_query)) {
        $_SESSION['name'] = $full_name; // Update session name
        $message = "Profile updated successfully!";
        // Refresh data
        $result = mysqli_query($conn, $query);
        $user_data = mysqli_fetch_assoc($result);
    } else {
        $message = "Error updating profile: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile · Raga</title>
    <link rel="stylesheet" href="mainCss.css">
    <style>
        .edit-container {
            max-width: 600px;
            margin: 40px auto;
            padding: 30px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
            color: #555;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 12px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 1rem;
            box-sizing: border-box;
        }
        .form-group textarea {
            height: 100px;
            resize: vertical;
        }
        .msg {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 6px;
            text-align: center;
        }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        .btn-save {
            width: 100%;
            background: #ffaa50;
            color: white;
            border: none;
            padding: 15px;
            border-radius: 6px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: normal;
        }
        .btn-save:hover { background: #e68f3c; }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #7e3285;
            text-decoration: none;
        }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<main>
    <div class="edit-container">
        <h1>Edit Profile</h1>
        
        <?php if ($message): ?>
            <div class="msg <?php echo strpos($message, 'Error') === false ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form action="edit_profile.php" method="POST">
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user_data['full_name']); ?>" required>
            </div>

            <?php if ($user_type === 'seller'): ?>
            <div class="form-group">
                <label for="contact_number">Contact Number</label>
                <input type="text" id="contact_number" name="contact_number" value="<?php echo htmlspecialchars($user_data['contact_number'] ?? ''); ?>">
            </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="address">Address</label>
                <textarea id="address" name="address"><?php echo htmlspecialchars($user_data['address'] ?? ''); ?></textarea>
            </div>

            <button type="submit" class="btn-save">Save Changes</button>
        </form>

        <a href="<?php echo $user_type === 'seller' ? 'SellerDashboard.php' : 'BuyerDashboard.php'; ?>" class="back-link">← Back to Dashboard</a>
    </div>
</main>

</body>
</html>

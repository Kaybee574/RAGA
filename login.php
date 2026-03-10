<?php
/**
 * Login Page for Raga Marketplace
 * Minimalist Editorial Style (MANCLUB inspired)
 */
require_once 'config/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = "";

// Handle Logic BEFORE HTML output to prevent 'Headers already sent'
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['role']; 
    $password = $_POST['password'];

    if ($role === 'buyer') {
        $student_number = sanitize($conn, $_POST['student_number']);
        $sql = "SELECT * FROM buyers WHERE student_number = '$student_number'";
        $result = mysqli_query($conn, $sql);
        
        if ($user = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['student_number'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_role'] = 'buyer';
                header("Location: index.php");
                exit();
            } else {
                $error = "PASS WRONG-";
            }
        } else {
            $error = "ID NOT FOUND-";
        }
    } else if ($role === 'seller') {
        $email = sanitize($conn, $_POST['email']);
        $sql = "SELECT * FROM sellers WHERE email = '$email'";
        $result = mysqli_query($conn, $sql);
        
        if ($user = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['seller_id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_role'] = 'seller';
                header("Location: seller/dashboard.php");
                exit();
            } else {
                $error = "PASS WRONG-";
            }
        } else {
            $error = "EMAIL NOT FOUND-";
        }
    }
}

// Now include header for display
include 'includes/header.php';
?>

<div class="auth-container">
    <div style="text-align: center; margin-bottom: 4rem;">
        <h1 style="font-size: 3rem; letter-spacing: -2px;">IDENTIFY-</h1>
        <p style="font-size: 0.7rem; letter-spacing: 2px; color: #888;">LOG IN TO ACCESS-</p>
    </div>

    <?php if ($error): ?>
        <div style="margin-bottom: 2rem; border: 1px solid black; padding: 1rem; text-align: center; font-weight: 700; color: red;">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <div class="form-group">
            <label style="font-size: 0.6rem; font-weight: 700;">USER TYPE-</label>
            <select name="role" id="role" onchange="toggleLoginFields(this.value)" required>
                <option value="buyer">BUYER (CAMPUS)</option>
                <option value="seller">SELLER (STORE)</option>
            </select>
        </div>

        <div class="form-group" id="student_input">
            <input type="text" name="student_number" id="student_number" placeholder="STUDENT ID-">
        </div>

        <div class="form-group" id="email_input" style="display: none;">
            <input type="email" name="email" id="email" placeholder="SELLER EMAIL-">
        </div>

        <div class="form-group">
            <input type="password" name="password" id="password" placeholder="PASSWORD-" required>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1.5rem; font-weight: 900; margin-top: 2rem;">LOG IN-</button>
        
        <p style="text-align: center; margin-top: 2rem; font-size: 0.7rem;">
            NEW TO RAGA-? <a href="register.php" style="color: black; font-weight: 700;">JOIN NOW-</a>
        </p>
    </form>
</div>

<script>
function toggleLoginFields(role) {
    if (role === 'buyer') {
        document.getElementById('student_input').style.display = 'block';
        document.getElementById('email_input').style.display = 'none';
        document.getElementById('student_number').required = true;
        document.getElementById('email').required = false;
    } else {
        document.getElementById('student_input').style.display = 'none';
        document.getElementById('email_input').style.display = 'block';
        document.getElementById('student_number').required = false;
        document.getElementById('email').required = true;
    }
}
// Initial run
toggleLoginFields(document.getElementById('role').value);
</script>

<?php include 'includes/footer.php'; ?>

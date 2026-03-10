<?php
/**
 * Registration Page for Raga Marketplace
 * Minimalist Editorial Style
 */
require_once 'config/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = "";
$success = "";

// Handle Registration Logic BEFORE any HTML output
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['role']; 
    $full_name = sanitize($conn, $_POST['full_name']);
    $password = $_POST['password']; 
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    $address = sanitize($conn, $_POST['address']);

    if ($role === 'buyer') {
        $student_number = sanitize($conn, $_POST['student_number']);
        if (empty($student_number) || empty($full_name) || empty($password)) {
            $error = "FIELDS REQUIRED-";
        } else {
            $check = mysqli_query($conn, "SELECT student_number FROM buyers WHERE student_number = '$student_number'");
            if (mysqli_num_rows($check) > 0) {
                $error = "ID EXISTS-";
            } else {
                $sql = "INSERT INTO buyers (student_number, full_name, address, password) VALUES ('$student_number', '$full_name', '$address', '$hashed_password')";
                if (mysqli_query($conn, $sql)) {
                    // Auto-login after successful registration
                    $_SESSION['user_id'] = $student_number;
                    $_SESSION['user_name'] = $full_name;
                    $_SESSION['user_role'] = 'buyer';
                    header("Location: index.php");
                    exit();
                } else {
                    $error = "REG FAIL-";
                }
            }
        }
    } else if ($role === 'seller') {
        $email = sanitize($conn, $_POST['email']);
        $contact = sanitize($conn, $_POST['contact_number']);

        if (empty($email) || empty($full_name) || empty($password)) {
            $error = "FIELDS REQUIRED-";
        } else {
            $check = mysqli_query($conn, "SELECT seller_id FROM sellers WHERE email = '$email'");
            if (mysqli_num_rows($check) > 0) {
                $error = "EMAIL EXISTS-";
            } else {
                $sql = "INSERT INTO sellers (full_name, email, contact_number, address, password) VALUES ('$full_name', '$email', '$contact', '$address', '$hashed_password')";
                if (mysqli_query($conn, $sql)) {
                    $new_id = mysqli_insert_id($conn);
                    $_SESSION['user_id'] = $new_id;
                    $_SESSION['user_name'] = $full_name;
                    $_SESSION['user_role'] = 'seller';
                    header("Location: seller/store_setup.php");
                    exit();
                } else {
                    $error = "REG FAIL-";
                }
            }
        }
    }
}

// Now include header for display
include 'includes/header.php';
?>

<div class="auth-container">
    <div style="text-align: center; margin-bottom: 4rem;">
        <h1 style="font-size: 3rem; letter-spacing: -2px;">JOIN-</h1>
        <p style="font-size: 0.7rem; letter-spacing: 2px; color: #888;">SELECT YOUR PATH</p>
    </div>

    <?php if ($error): ?>
        <div style="margin-bottom: 2rem; border: 1px solid black; padding: 1rem; text-align: center; font-weight: 700; color: red;">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="register.php">
        <div class="form-group">
            <label style="font-size: 0.6rem; font-weight: 700;">ROLE-</label>
            <select name="role" id="role" onchange="toggleFields(this.value)" required>
                <option value="buyer">BUYER (CAMPUS)</option>
                <option value="seller">SELLER (STORE)</option>
            </select>
        </div>

        <div class="form-group" id="student_field">
            <input type="text" name="student_number" id="student_number" placeholder="STUDENT ID (G23M...)">
        </div>

        <div class="form-group" id="email_field" style="display: none;">
            <input type="email" name="email" id="email" placeholder="EMAIL ADDRESS-">
        </div>

        <div class="form-group">
            <input type="text" name="full_name" id="full_name" placeholder="FULL LEGAL NAME-" required>
        </div>

        <div class="form-group" id="contact_field" style="display: none;">
            <input type="text" name="contact_number" id="contact_number" placeholder="CONTACT NUMBER-">
        </div>

        <div class="form-group">
            <input type="text" name="address" id="address" placeholder="PHYSICAL ADDRESS- (OPTIONAL)">
        </div>

        <div class="form-group">
            <input type="password" name="password" id="password" placeholder="CREATE PASSWORD-" required>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1.5rem; font-weight: 900; margin-top: 2rem;">SIGN UP-</button>
        
        <p style="text-align: center; margin-top: 2rem; font-size: 0.7rem;">
            ALREADY VALID? <a href="login.php" style="color: black; font-weight: 700;">LOG IN-</a>
        </p>
    </form>
</div>

<script>
function toggleFields(role) {
    const sField = document.getElementById('student_field');
    const eField = document.getElementById('email_field');
    const cField = document.getElementById('contact_field');
    
    if (role === 'buyer') {
        sField.style.display = 'block';
        eField.style.display = 'none';
        cField.style.display = 'none';
        document.getElementById('student_number').required = true;
        document.getElementById('email').required = false;
    } else {
        sField.style.display = 'none';
        eField.style.display = 'block';
        cField.style.display = 'block';
        document.getElementById('student_number').required = false;
        document.getElementById('email').required = true;
    }
}
// Initial run
toggleFields(document.getElementById('role').value);
</script>

<?php include 'includes/footer.php'; ?>

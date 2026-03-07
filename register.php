<?php
require_once 'DatabaseConnection.php';

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect specific variables mapped from the form inputs
    $fullName = mysqli_real_escape_string($conn, $_POST['name']);
    $userType = mysqli_real_escape_string($conn, $_POST['user_type']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    // Optional variables depending on role
    $email = isset($_POST['email']) ? mysqli_real_escape_string($conn, $_POST['email']) : null;
    $studentNumber = isset($_POST['student_number']) ? mysqli_real_escape_string($conn, $_POST['student_number']) : null;
    $contactNumber = isset($_POST['contact_number']) ? mysqli_real_escape_string($conn, $_POST['contact_number']) : null;

    if ($password !== $confirmPassword) {
        die("Passwords do not match. <a href='SignUp.html'>Go back</a>");
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Using explicitly atomic Transactions if user is registering for both
    mysqli_begin_transaction($conn);

    try {
        if ($userType === 'buyer' || $userType === 'both') {
            if (empty($studentNumber)) {
                throw new Exception("Student number is required for buyers.");
            }
            // Check if already exists just to be safe
            $check = mysqli_query($conn, "SELECT student_number FROM buyers WHERE student_number = '$studentNumber'");
            if (mysqli_num_rows($check) > 0) {
                throw new Exception("A buyer account with this student number already exists.");
            }

            $queryBuyer = "INSERT INTO buyers (student_number, full_name, address, password) 
                           VALUES ('$studentNumber', '$fullName', '$address', '$hashedPassword')";

            if (!mysqli_query($conn, $queryBuyer)) {
                throw new Exception("Error creating buyer account: " . mysqli_error($conn));
            }
        }

        if ($userType === 'seller' || $userType === 'both') {
            if (empty($email) || empty($contactNumber)) {
                throw new Exception("Email and contact number are required for sellers.");
            }
            // Check if already exists
            $check = mysqli_query($conn, "SELECT email FROM sellers WHERE email = '$email'");
            if (mysqli_num_rows($check) > 0) {
                throw new Exception("A seller account with this email already exists.");
            }

            $querySeller = "INSERT INTO sellers (full_name, email, contact_number, password, address) 
                            VALUES ('$fullName', '$email', '$contactNumber', '$hashedPassword', '$address')";

            if (!mysqli_query($conn, $querySeller)) {
                throw new Exception("Error creating seller account: " . mysqli_error($conn));
            }
        }

        // If all operations are successful, commit the transaction.
        mysqli_commit($conn);

        // Set session variables for auto-login
        $_SESSION['user_type'] = $userType;
        $_SESSION['name'] = $fullName;
        
        if ($userType === 'buyer' || $userType === 'both') {
            $_SESSION['student_number'] = $studentNumber;
        }
        if ($userType === 'seller' || $userType === 'both') {
            $_SESSION['email'] = $email;
        }
        
        // user_id for generic use
        $_SESSION['user_id'] = ($userType === 'buyer') ? $studentNumber : $email;

        // Success message and Redirect
        echo "<h1>Registration Successful!</h1>";
        echo "<p>Welcome to RAGA, $fullName! You have successfully registered as a <strong>$userType</strong>.</p>";
        echo "<p>Redirecting you to your dashboard in 3 seconds...</p>";

        $dashboard = ($userType === 'seller') ? 'SellerDashboard.php' : 'BuyerDashboard.php';
        header("refresh:3;url=$dashboard");
    } catch (Exception $e) {
        // Rollback any partial inserts if error occurs
        mysqli_rollback($conn);
        echo "<h1>Registration Failed</h1>";
        echo "<p>Error: " . $e->getMessage() . "</p>";
        echo "<p><a href='SignUp.html'>Go back and try again</a></p>";
    }
} else {
    // If not POST request, send back to form
    header("Location: SignUp.html");
    exit();
}

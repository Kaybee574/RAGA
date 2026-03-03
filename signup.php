<?php
// signup.php
// Basic PHP handler for signup form sumbission

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"] ?? 'User';
    $email = $_POST["email"] ?? '';
    $user_type = $_POST["user_type"] ?? 'buyer';
    
    // Server-side validation: must be @campus.ru.ac.za
    if (!preg_match('/^[^\s@]+@campus\.ru\.ac\.za$/', $email)) {
        die("Invalid email domain. Only @campus.ru.ac.za emails are allowed.");
    }
    
    // We would hash the password and insert to DB here.
    
    // Simple output response
    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Signup Processed</title>
        <link rel='stylesheet' href='global.css'>
    </head>
    <body style='display:flex; justify-content:center; align-items:center; height:100vh;'>
        <div style='background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); text-align:center;'>
            <h2 style='color:#1877f2'>Account Created Successfully!</h2>
            <p>Hello <strong>" . htmlspecialchars($name) . "</strong> (" . htmlspecialchars($user_type) . ").</p>
            <p>Your account for <em>" . htmlspecialchars($email) . "</em> has been registered.</p>
            <p><em>(Note: Database integration is coming in Prac 4)</em></p>
            <a href='SignIn.html' class='btn' style='margin-top:20px; display:inline-block;'>Go to Login</a>
        </div>
    </body>
    </html>";
} else {
    // Redirect back if accessed without POST
    header("Location: SignUp.html");
    exit();
}
?>

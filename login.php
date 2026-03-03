<?php
// login.php
// Basic PHP handler for login form sumbission

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"] ?? '';
    
    // Server-side validation: must be @campus.ru.ac.za
    if (!preg_match('/^[^\s@]+@campus\.ru\.ac\.za$/', $email)) {
        die("Invalid email domain. Only @campus.ru.ac.za emails are allowed.");
    }
    // For a real application, we would check the database and setup a session here.
    
    // Simple output response
    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Login Processed</title>
        <link rel='stylesheet' href='global.css'>
    </head>
    <body style='display:flex; justify-content:center; align-items:center; height:100vh;'>
        <div style='background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); text-align:center;'>
            <h2 style='color:#42b72a'>Login Successful!</h2>
            <p>Welcome back, <strong>" . htmlspecialchars($email) . "</strong>.</p>
            <p><em>(Note: Database integration is coming in Prac 4)</em></p>
            <a href='index.html' class='btn' style='margin-top:20px; display:inline-block;'>Return to App</a>
        </div>
    </body>
    </html>";
} else {
    // Redirect back if accessed without POST
    header("Location: SignIn.html");
    exit();
}
?>

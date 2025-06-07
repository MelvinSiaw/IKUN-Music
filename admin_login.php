<?php
session_start();

$login_error = '';

// Define hardcoded admin credentials
$admin_username = "admin";
$admin_password = password_hash("admin@123", PASSWORD_DEFAULT); // Hashed password

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['name'];
    $password = $_POST['password'];

    // Verify admin credentials
    if ($username === $admin_username && password_verify($password, $admin_password)) {
        $_SESSION['admin_id'] = 1; // Example admin ID
        header("Location: Admin_dashboard.php");
        exit();
    } else {
        $login_error = 'Invalid username or password.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IKUN - Admin Login</title>
    <link rel="stylesheet" href="assets/css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="logo-container">
            <img src="assets/pic/Inspirational_Quote_Instagram_Post_1.png" alt="Logo" class="logo-image">
            <span style="color: black;">IKUN MUSIC</span>
        </div>
        <div class="form-container">
            <div class="form-toggle">
            </div>
            <div id="signup-form" class="form-content">   
            </div>
            <div id="login-form" class="form-content">
                <h2>Admin Login</h2>
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" placeholder="Admin Name" required>
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="********" required>
                    <button type="submit" style="font-family: Poppins;">Log In</button>
                    <?php if (!empty($login_error)) : ?>
                        <p class="error-message"><?php echo $login_error; ?></p>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
    <script>
        function showLogin() {
            document.getElementById('signup-form').style.display = 'none';
            document.getElementById('login-form').style.display = 'block';
            document.getElementById('signup-btn').classList.remove('active');
            document.getElementById('login-btn').classList.add('active');
        }

        window.onload = function() {
            showLogin();
        }
    </script>
</body>
</html>

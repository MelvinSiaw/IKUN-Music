<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="assets/css/login.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <style>
        .toast {
            visibility: hidden;
            min-width: 300px;
            margin-left: -150px;
            background-color: #d4edda;
            color: #155724;
            text-align: left;
            border-radius: 5px;
            padding: 16px;
            position: fixed;
            z-index: 1001;
            left: 50%;
            bottom: 30px;
            font-size: 17px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
        }
        .toast.show {
            visibility: visible;
            -webkit-animation: fadein 0.5s, fadeout 0.5s 3.5s;
            animation: fadein 0.5s, fadeout 0.5s 3.5s;
        }
        @-webkit-keyframes fadein {
            from {bottom: 0; opacity: 0;} 
            to {bottom: 30px; opacity: 1;}
        }
        @keyframes fadein {
            from {bottom: 0; opacity: 0;}
            to {bottom: 30px; opacity: 1;}
        }
        @-webkit-keyframes fadeout {
            from {bottom: 30px; opacity: 1;} 
            to {bottom: 0; opacity: 0;}
        }
        @keyframes fadeout {
            from {bottom: 30px; opacity: 1;}
            to {bottom: 0; opacity: 0;}
        }
        .toast .icon {
            margin-right: 10px;
            font-size: 20px;
        }
        .toast .close {
            margin-left: auto;
            cursor: pointer;
        }
        .back-to-login {
            display: none;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2>Reset Password</h2>
            <form id="reset-password-form">
                <input type="hidden" name="token" id="token" value="<?php echo htmlspecialchars($_GET['token']); ?>" style="padding: 10px; border: none; border-radius: 5px; margin-bottom: 20px;">
                <label for="password">New Password</label>
                <input type="password" id="password" name="password" placeholder="********" required style="padding: 10px; border: none; border-radius: 5px; margin-bottom: 20px;">
                <br>
                <label for="password_confirmation">Repeat New Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" placeholder="********" required style="padding: 10px; border: none; border-radius: 5px; margin-bottom: 20px;">
                <button type="submit" id="reset-button" style="padding: 10px; border: none; border-radius: 5px; background-color: #6200ea; color: #fff; font-size: 16px; cursor: pointer; font-family: Poppins, sans-serif;">Reset Password</button>
            </form>
            <!-- Add a button to redirect to login.php -->
            <div class="back-to-login" id="back-to-login">
                <form action="login.php" method="get">
                    <button type="submit" style="padding: 10px; border: none; border-radius: 5px; background-color: #6200ea; color: #fff; font-size: 16px; cursor: pointer; font-family: Poppins, sans-serif;">Back to Login</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="toast">
        <span class="icon"><i class="fas fa-check-circle"></i></span>
        <span class="message"></span>
        <span class="close" onclick="hideToast()">&times;</span>
    </div>

    <script>
    function showToast(message) {
        var toast = document.getElementById("toast");
        toast.querySelector(".message").innerText = message;
        toast.className = "toast show";
        setTimeout(function(){ hideToast(); }, 4000); // Show toast for 4 seconds
    }

    function hideToast() {
        var toast = document.getElementById("toast");
        toast.className = toast.className.replace("show", "");
    }

    document.getElementById('reset-password-form').addEventListener('submit', function(event) {
        event.preventDefault();

        let token = document.getElementById('token').value;
        let password = document.getElementById('password').value;
        let password_confirmation = document.getElementById('password_confirmation').value;
        let resetButton = document.getElementById('reset-button');

        // Show loading spinner and disable button
        resetButton.disabled = true;
        resetButton.innerHTML = 'Resetting...<span class="spinner" id="spinner"></span>';
        document.getElementById('spinner').style.display = 'inline-block';
        
        // AJAX request to update_password.php
        let xhr = new XMLHttpRequest();
        xhr.open('POST', 'update_password.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                showToast(xhr.responseText);
                // Hide loading spinner and enable button
                resetButton.disabled = false;
                resetButton.innerHTML = 'Reset Password';
                if (xhr.responseText.trim() === 'Password has been reset successfully') {
                    document.getElementById('back-to-login').style.display = 'block';
                }
            }
        };
        xhr.send('token=' + encodeURIComponent(token) + '&password=' + encodeURIComponent(password) + '&password_confirmation=' + encodeURIComponent(password_confirmation));
    });
    </script>
</body>
</html>
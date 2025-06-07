<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IKUN - Sign Up & Login</title>
    <link rel="stylesheet" href="assets/css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <script src="/js/validation.js" defer></script>
    <style>
        /* Style for OTP Popup */
        .otp-popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            z-index: 1000;
        }
        .otp-popup h2 {
            font-size: 1.5em;
            margin-bottom: 10px;
            text-align: center;
        }
        .otp-input-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .otp-input-container input {
            width: 40px;
            height: 40px;
            text-align: center;
            font-size: 1.5em;
            border: 1px solid #ddd;
            border-radius: 4px;
            outline: none;
            margin-right: 5px;
        }
        .otp-input-container input:focus {
            border-color: #6200ea;
            box-shadow: 0 0 5px rgba(98, 0, 234, 0.5);
        }
        .otp-popup button {
            display: block;
            width: 100%;
            padding: 10px;
            font-size: 1em;
            font-weight: bold;
            color: white;
            background-color: #6912db;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .otp-popup button:hover {
            background-color: #b991ec;
        }
        /* Loading Spinner */
        .spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 2px solid #fff;
            border-top: 2px solid #6200ea;
            border-radius: 50%;
            animation: spin 0.5s linear infinite;
            margin-left: 10px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        /* Toast Notification */
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
    </style>
</head>
<body>
    <div class="container">
        <div class="logo-container">
            <img src="assets/pic/Inspirational_Quote_Instagram_Post_1.png" alt="Logo" class="logo-image">
            <span style="color: black;">IKUN MUSIC</span>
        </div>
        <div class="form-container">
            <div class="form-toggle">
                <button id="signup-btn" onclick="showSignup()">Sign Up</button>
                <button id="login-btn" onclick="showLogin()">Log In</button>
            </div>
            <div id="signup-form" class="form-content">
                <h2>Sign Up</h2>
                <form id="signup-form" action="process_signup.php" method="post">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" placeholder="Your Name" required>
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="hello@gmail.com" required>
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="********" required>
                    <label for="password_confirmation">Repeat password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="********" required>
                    <button type="button" onclick="sendOTP();" id="otp-button" style="padding: 10px; border: none; border-radius: 5px; background-color: #6200ea; color: #fff; font-size: 16px; cursor: pointer; font-family: Poppins, sans-serif;">Send OTP<span class="spinner" id="spinner"></span></button>
                </form>
                <p>Already a member? <a href="#" onclick="showLogin()">Log in</a></p>
            </div>
            <div id="login-form" class="form-content">
                <h2>Log In</h2>
                <form action="process_login.php" method="post">
                    <label for="login_email">Email</label>
                    <input type="email" id="login_email" name="email" placeholder="hello@gmail.com" required>
                    <label for="login_password">Password</label>
                    <input type="password" id="login_password" name="password" placeholder="********" required>
                    <button type="submit">Log In</button>
                </form>
                <p>Don’t have an account? <a href="#" onclick="showSignup()">Sign up</a></p>
                <p><a href="forgot_password.php">Forgot Password?</a></p>
            </div>
        </div>
    </div>

    <!-- OTP Popup -->
    <div id="otp-popup" class="otp-popup">
        <h2 style="color: black;">Enter OTP</h2>
        <form id="otp-form" onsubmit="verifyOTP(); return false;">
            <div class="otp-input-container">
                <input type="text" maxlength="1" oninput="moveToNext(this, 'otp-input-2')" id="otp-input-1" required>
                <input type="text" maxlength="1" oninput="moveToNext(this, 'otp-input-3')" id="otp-input-2" required>
                <input type="text" maxlength="1" oninput="moveToNext(this, 'otp-input-4')" id="otp-input-3" required>
                <input type="text" maxlength="1" oninput="moveToNext(this, 'otp-input-5')" id="otp-input-4" required>
                <input type="text" maxlength="1" oninput="moveToNext(this, 'otp-input-6')" id="otp-input-5" required>
                <input type="text" maxlength="1" id="otp-input-6" required>
            </div>
            <button type="submit">Verify OTP</button>
        </form>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="toast">
        <span class="icon"><i class="fas fa-check-circle"></i></span>
        <span class="message"></span>
        <span class="close" onclick="hideToast()">&times;</span>
    </div>

    <script>
    function showSignup() {
        document.getElementById('signup-form').style.display = 'block';
        document.getElementById('login-form').style.display = 'none';
        document.getElementById('signup-btn').classList.add('active');
        document.getElementById('login-btn').classList.remove('active');
    }

    function showLogin() {
        document.getElementById('signup-form').style.display = 'none';
        document.getElementById('login-form').style.display = 'block';
        document.getElementById('signup-btn').classList.remove('active');
        document.getElementById('login-btn').classList.add('active');
    }

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

    function moveToNext(current, nextFieldID) {
        if (current.value.length >= current.maxLength) {
            if (nextFieldID) {
                document.getElementById(nextFieldID).focus();
            }
        }
    }

    function sendOTP() {
        let name = document.getElementById('name').value;
        let email = document.getElementById('email').value;
        let password = document.getElementById('password').value;
        let password_confirmation = document.getElementById('password_confirmation').value;
        let otpButton = document.getElementById('otp-button');
        let spinner = document.getElementById('spinner');
        
        // Validate inputs (simplified, add more validation as needed)
        if (!name || !password || !password_confirmation) {
            showToast("All fields are required");
            return;
        }
        if (password !== password_confirmation) {
            showToast("Passwords must match");
            return;
        }
        
        // Show loading spinner and disable button
        otpButton.disabled = true;
        spinner.style.display = 'inline-block';
        otpButton.style.cursor = 'not-allowed';

        // AJAX request to process_signup.php for database insertion
        let xhr = new XMLHttpRequest();
        xhr.open('POST', 'process_signup.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    // Database insertion successful, now send OTP
                    sendOTPToEmail(email);
                } else {
                    showToast('Error: ' + xhr.responseText);
                    // Hide loading spinner and enable button
                    otpButton.disabled = false;
                    spinner.style.display = 'none';
                    otpButton.style.cursor = 'pointer';
                }
            }
        };
        xhr.send('name=' + encodeURIComponent(name) + '&email=' + encodeURIComponent(email) + '&password=' + encodeURIComponent(password));
    }

    function sendOTPToEmail(email) {
        let xhr = new XMLHttpRequest();
        xhr.open('POST', 'send_otp.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                let otpButton = document.getElementById('otp-button');
                let spinner = document.getElementById('spinner');
                if (xhr.status === 200) {
                    let response = JSON.parse(xhr.responseText);
                    showToast(response.message); // Show toast message from send_otp.php
                    if (response.status === 'success') {
                        document.getElementById('otp-popup').style.display = 'block'; // Display OTP popup
                    }
                } else {
                    showToast('Error: ' + xhr.responseText);
                }
                // Hide loading spinner and enable button
                otpButton.disabled = false;
                spinner.style.display = 'none';
                otpButton.style.cursor = 'pointer';
            }
        };
        xhr.send('email=' + encodeURIComponent(email));
    }

    function verifyOTP() {
        let otp = '';
        for (let i = 1; i <= 6; i++) {
            otp += document.getElementById(`otp-input-${i}`).value;
        }
        let email = document.getElementById('email').value;
        
        // Example of AJAX call to verify the OTP
        let xhr = new XMLHttpRequest();
        xhr.open('POST', 'verify_otp.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                let response = xhr.responseText;
                showToast(response); // Show toast message from verify_otp.php
                if (response.trim() === 'OTP verified') {
                    document.getElementById('otp-popup').style.display = 'none'; // Hide OTP popup on success
                }
            }
        };
        xhr.send('otp=' + encodeURIComponent(otp) + '&email=' + encodeURIComponent(email));
    }
    </script>
</body>
</html>

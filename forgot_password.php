<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
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
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2>Forgot Password</h2>
            <form id="forgot-password-form">
                <label for="email">Enter your email address</label>
                <input type="email" id="email" name="email" placeholder="hello@gmail.com" required style="padding: 10px; border: none; border-radius: 5px; margin-bottom: 20px;">
                <button type="submit" id="reset-button" style="padding: 10px; border: none; border-radius: 5px; background-color: #6200ea; color: #fff; font-size: 16px; cursor: pointer; font-family: Poppins, sans-serif;">
                    Send Reset Link
                    <span class="spinner" id="spinner"></span>
                </button>
            </form>
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

    document.getElementById('forgot-password-form').addEventListener('submit', function(event) {
        event.preventDefault();
        
        let email = document.getElementById('email').value;
        let resetButton = document.getElementById('reset-button');
        let spinner = document.getElementById('spinner');
        
        // Show loading spinner and disable button
        resetButton.disabled = true;
        spinner.style.display = 'inline-block';
        resetButton.style.cursor = 'not-allowed';
        
        // AJAX request to send_reset_link.php
        let xhr = new XMLHttpRequest();
        xhr.open('POST', 'send_reset_link.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                showToast(xhr.responseText);
                // Hide loading spinner and enable button
                resetButton.disabled = false;
                spinner.style.display = 'none';
                resetButton.style.cursor = 'pointer';
            }
        };
        xhr.send('email=' + encodeURIComponent(email));
    });
    </script>
</body>
</html>
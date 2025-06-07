<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ikun Music</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
</head>
<style>
    body {
    font-family: Poppins, sans-serif;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background-color: #f9f9f9;
    background-image: url(assets/pic/background.png);
}
.container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    text-align: left;
    max-width: 1200px;
    width: 100%;
    padding: 20px;
}
.content {
    max-width: 600px;
}
.logo-title {
    display: flex;
    align-items: flex-end; /* Align to the bottom */
    gap: 20px;
    margin-bottom: 20px;
}
.logo {
    width: 150px;
    margin-bottom: 10px; /* Add margin-bottom to adjust position */
}
.title {
    font-size: 2.5em;
    font-weight: bold;
    color: #333;
}
.subtitle {
    font-size: 1.2em;
    color: #666;
    margin-bottom: 40px;
}
.buttons {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.buttons-horizontal {
    display: flex;
    flex-direction: row;
    gap: 20px;
    align-items: center;
}
.admin-section {
    display: flex;
    align-items: center;
    gap: 10px;
}
.admin-section-horizontal {
    display: flex;
    align-items: center;
    gap: 10px;
}
.btn {
    display: inline-block;
    padding: 10px 20px;
    border: 2px solid #333;
    border-radius: 5px;
    text-decoration: none;
    color: #333;
    font-weight: bold;
    background-color: #fff;
    transition: background-color 0.3s, color 0.3s;
}
.btn:hover {
    background-color: #333;
    color: #fff;
}
.get-started {
    padding: 10px 20px;
    width: auto;
}
.get-started:hover {
    background-color: white;
    color: purple;
    border: 2px solid purple;
}
.admin-link {
    color: #666;
}
.admin-link:hover {
    color: #000;

}
.animation {
    width: 400px;
    height: 400px;
}
</style>
<body>
    <div class="container">
        <div class="content">
            <div class="logo-title">
                <img src="assets/pic/Inspirational_Quote_Instagram_Post_1.png" alt="Ikun Music Logo" class="logo">
                <h1 class="title">Ikun Music</h1>
            </div>
            <p class="subtitle">Discover and enjoy music from the Ikun community. A place where artists share their unique sounds and creations with the world.</p>
            <div class="buttons-horizontal">
                <a href="Login.php" class="btn get-started">Get Started</a>
                <div class="admin-section-horizontal">
                    <p class="admin-link">Are You An Admin?</p>
                    <a href="admin_login.php" class="btn">Click Here For Admin Login</a>
                </div>
            </div>
        </div>
        <div class="animation">
            <!-- Include your Lottie animation here -->
            <lottie-player src="assets/lottie-animation/index_music_animation.json" background="transparent" speed="1" style="width: 100%; height: 100%;" loop autoplay></lottie-player>
        </div>
    </div>

    <!-- Include Lottie script -->
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
</body>
</html>
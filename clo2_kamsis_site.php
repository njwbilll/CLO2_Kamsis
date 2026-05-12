<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli("localhost", "admin_kamsis", "Kamsis186!", "kamsis_db");

if ($conn->connect_error) {
    die("Koneksi DB Error: " . $conn->connect_error);
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: clo2_kamsis_site.php");
    exit;
}

$login_error = "";
if (isset($_POST['login']) && !isset($_SESSION['logged_in'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (strlen($username) > 20 || strlen($password) > 20) {
        $login_error = "DI TOLAK! Input terlalu panjang (Potensi Buffer Overflow).";
    } else {
        $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if (password_verify($password, $row['password'])) {
                $_SESSION['logged_in'] = true;
                $s_SESSION['username']  = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');
                header("Location: clo2_kamsis_site.php");
                exit;
            } else {
                sleep(2);
                $login_error = "Password salah!";
            }
        } else {
            $login_error = "Username tidak ditemukan di database!";
        }
        $stmt->close();
    }
}

$comment_display = "";
if (isset($_SESSION['logged_in']) && isset($_POST['send_comment'])) {
    $comment_display = htmlspecialchars($_POST['comment'], ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Portal 1998 - Projek CLO 2</title>
    <link rel="stylesheet" type="text/css" href="clo2_kamsis_site.css">
</head>
<body>

<div class="site-banner">
    <h1>PORTAL 1998</h1>
    <marquee scrollamount="8">-- SELAMAT DATANG DI SERVER AMAN NAJWA BILQIS -- BEST VIEWED IN 800x600 --</marquee>
</div>

<div class="main-content">

<?php if (!isset($_SESSION['logged_in'])): ?>
    <div class="login-gate">
        <div class="login-box">
            <div class="titlebar">
                <span>LOGIN_SERVER.EXE</span>
                <div class="win-buttons">
                    <div class="win-btn">_</div>
                    <div class="win-btn">□</div>
                    <div class="win-btn">x</div>
                </div>
            </div>
            <div class="login-body">
                <?php if ($login_error): ?>
                    <p class="login-error">⚠ <?= $login_error ?></p>
                <?php endif; ?>
                <p>Silahkan masukkan otorisasi untuk melanjutkan:</p>
                <form method="POST">
                    <label>USER:</label>
                    <input type="text" name="username" placeholder="Enter username..." required maxlength="20" autocomplete="off">
                    <label>PASS:</label>
                    <input type="password" name="password" placeholder="Enter password..." required maxlength="20">
                    <button type="submit" name="login" class="btn-login">[ ENTER ]</button>
                </form>
            </div>
        </div>
        <p style="color:#aaa; font-size:11px; margin-top:10px; text-align:center;">
            ⛔ Guestbook hanya dapat diakses setelah login.
        </p>
    </div>

<?php else: ?>

    <div class="session-bar">
        <span>SESSION: <span class="status"><?= $_SESSION['username'] ?></span> — AUTHENTICATED ✔</span>
        <a href="?logout=1" style="text-decoration:none;">
            <button class="btn-logout">LOGOUT</button>
        </a>
    </div>

    <hr class="rainbow">

    <div class="comment-section">
        <div class="section-header">GUESTBOOK.TXT</div>
        <div class="comment-form">
            <form method="POST">
                <label style="color:#000; font-weight:bold; font-size:11px;">PESAN KAMU:</label>
                <textarea name="comment" placeholder="Write your message here..." required></textarea>
                <button type="submit" name="send_comment" class="btn-comment">SEND!</button>
            </form>
        </div>

        <div class="comment-list">
            <?php if ($comment_display): ?>
            <div class="comment-item">
                <span class="comment-time"><?= date("H:i:s") ?></span>
                <span class="comment-author"><?= $_SESSION['username'] ?></span>
                <div class="comment-text"><?= $comment_display ?></div>
            </div>
            <?php endif; ?>

            <div class="comment-item">
                <span class="comment-time">12:00:01</span>
                <span class="comment-author">System_Admin</span>
                <div class="comment-text">Selamat datang! Server ini sudah diproteksi dari serangan SQLi dan XSS.</div>
            </div>
        </div>
    </div>

<?php endif; ?>

    <div class="under-construction">★ SITE UNDER SECURITY AUDIT 2026 ★</div>
</div>

<div class="footer-badges">
    <span class="badge-88x31">MADE IN RI</span>
    <span class="badge-88x31">PHP SECURE</span>
    <span class="badge-88x31">SSL ACTIVE</span>
</div>

</body>
</html>

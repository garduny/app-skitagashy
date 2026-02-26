<?php
if (file_exists('../server/init.php')) {
    require_once '../server/init.php';
}
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
function user()
{
    global $admin_user;
    return $admin_user;
}
$current_page = basename($_SERVER['PHP_SELF']);
$public_pages = ['login.php', 'forgetpassword.php', 'resetpassword.php'];
$token = $_SESSION['token'] ?? $_COOKIE['auth_token'] ?? null;
$admin_user = null;
if ($token) {
    $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown', 0, 255);
    $session = findQuery(" SELECT * FROM user_sessions WHERE token='$token' AND expires_at>NOW() ");
    if ($session) {
        if ($session['user_agent'] === $ua) {
            $admin_user = findQuery(" SELECT u.*,r.name as role_name,r.slug as role_slug FROM users u JOIN roles r ON u.role_id=r.id WHERE u.id={$session['user_id']} AND u.is_active=1 ");
            if ($admin_user) {
                if (empty($_SESSION['user_id'])) {
                    $_SESSION['user_id'] = $session['user_id'];
                    $_SESSION['token'] = $token;
                }
                if (in_array($current_page, $public_pages)) {
                    header("Location: app.php");
                    exit;
                }
            } else {
                destroyAuth();
            }
        } else {
            destroyAuth();
        }
    } else {
        destroyAuth();
    }
} else {
    if (!in_array($current_page, $public_pages)) {
        header("Location: login.php");
        exit;
    }
}
if (isset($_GET['logout'])) {
    if ($token) {
        execute(" DELETE FROM user_sessions WHERE token='$token' ");
    }
    destroyAuth();
}
function destroyAuth()
{
    setcookie('auth_token', '', time() - 3600, '/', '', true, true);
    session_unset();
    session_destroy();
    global $current_page, $public_pages;
    if (!in_array($current_page, $public_pages)) {
        header("Location: login.php");
        exit;
    }
}

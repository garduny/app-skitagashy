<?php
require_once 'init.php';
if (post('submit_otp')) {
    $email = request('email', 'post');
    $code = request('otp_code', 'post');
    $u = findQuery(" SELECT * FROM users WHERE email='$email' ");
    if (!empty($u) && $u['otp_code'] === $code && strtotime($u['otp_expires']) > time()) {
        $token = bin2hex(random_bytes(32));
        $ip = $_SERVER['REMOTE_ADDR'];
        $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown', 0, 255);
        $exp = date('Y-m-d H:i:s', strtotime('+30 days'));
        execute(" INSERT INTO user_sessions (user_id,token,ip_address,user_agent,expires_at) VALUES ({$u['id']},'$token','$ip','$ua','$exp') ");
        $_SESSION['user_id'] = $u['id'];
        $_SESSION['token'] = $token;
        setcookie('auth_token', $token, time() + (86400 * 30), "/", "", true, true);
        execute(" UPDATE users SET otp_code=NULL, otp_expires=NULL, updated_at=NOW() WHERE id={$u['id']} ");
        header("Location: app.php");
        exit;
    } else {
        $error = "Invalid or expired code.";
        $step = 2;
    }
} elseif (post('submit_login')) {
    $email = request('email', 'post');
    $pass = $_POST['password'] ?? '';
    $u = findQuery(" SELECT id,is_active,password FROM users WHERE email='$email' ");
    if (!empty($u) && password_verify($pass, $u['password'])) {
        if ($u['is_active'] == 1) {
            $otp = rand(100000, 999999);
            $exp = date('Y-m-d H:i:s', strtotime('+60 minutes'));
            execute(" UPDATE users SET otp_code='$otp', otp_expires='$exp' WHERE id={$u['id']} ");
            $subject = "Your Admin Login Code";
            $body = "<div style='padding:20px;color:#333'><h2 style='color:#00d48f'>Admin Access</h2><p>OTP Code:</p><h1 style='font-size:30px'>$otp</h1></div>";
            if (function_exists('mailer')) {
                mailer($subject, $body, "Gashy Security", $email);
            }
            $demo_otp = $otp;
            $step = 2;
        } else {
            $error = "Account inactive.";
        }
    } else {
        $error = "Invalid credentials.";
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Admin Login | Gashy Bazaar</title>
    <link rel="shortcut icon" href="../public/img/logo.png" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        dark: {
                            900: '#0B0E14',
                            800: '#151A23',
                            700: '#1E2532'
                        },
                        primary: {
                            500: '#00ffaa',
                            600: '#00d48f'
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0B0E14
        }
    </style>
</head>

<body class="bg-gray-50 dark:bg-dark-900 text-gray-900 dark:text-white flex items-center justify-center min-h-screen transition-colors">
    <div class="w-full max-w-md p-8 space-y-6 bg-white dark:bg-dark-800 rounded-2xl shadow-xl border border-gray-200 dark:border-white/5">
        <div class="text-center">
            <h1 class="text-3xl font-black tracking-tighter mb-2">GASHY<span class="text-primary-500">ADMIN</span></h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Secure Access Portal</p>
        </div>
        <?php if (isset($error)): ?><div class="p-4 bg-red-100 dark:bg-red-500/20 border border-red-200 dark:border-red-500/30 text-red-600 dark:text-red-400 text-sm rounded-lg font-medium text-center"><?= $error ?></div><?php endif; ?>
        <?php if (isset($step) && $step == 2): ?>
            <form method="POST" action="" class="space-y-5"><input type="hidden" name="email" value="<?= $email ?>">
                <div><label class="block text-xs font-bold text-gray-500 uppercase mb-2">Enter 6-Digit Code</label><input type="text" name="otp_code" required class="w-full px-4 py-3 bg-gray-50 dark:bg-black/20 border border-gray-200 dark:border-white/10 rounded-xl focus:border-primary-500 outline-none text-gray-900 dark:text-white text-center font-mono tracking-[0.5em] text-xl" autofocus></div><button type="submit" name="submit_otp" value="1" class="w-full py-3.5 bg-primary-600 hover:bg-primary-500 text-white font-bold rounded-xl transition-all shadow-lg shadow-primary-500/25 transform hover:-translate-y-0.5">Verify & Login</button>
            </form>
        <?php else: ?>
            <form method="POST" action="" class="space-y-5">
                <div><label class="block text-xs font-bold text-gray-500 uppercase mb-2">Email Address</label><input type="email" name="email" required class="w-full px-4 py-3 bg-gray-50 dark:bg-black/20 border border-gray-200 dark:border-white/10 rounded-xl focus:border-primary-500 outline-none text-gray-900 dark:text-white transition-all"></div>
                <div>
                    <div class="flex justify-between items-center mb-2"><label class="block text-xs font-bold text-gray-500 uppercase">Password</label><a href="forgetpassword.php" class="text-xs text-primary-500 hover:underline">Forgot?</a></div><input type="password" name="password" required class="w-full px-4 py-3 bg-gray-50 dark:bg-black/20 border border-gray-200 dark:border-white/10 rounded-xl focus:border-primary-500 outline-none text-gray-900 dark:text-white transition-all">
                </div><button type="submit" name="submit_login" value="1" class="w-full py-3.5 bg-primary-600 hover:bg-primary-500 text-white font-bold rounded-xl shadow-lg shadow-primary-500/25 transition-all transform hover:-translate-y-0.5">Continue</button>
            </form>
        <?php endif; ?>
        <div class="text-center">
            <p class="text-xs text-gray-400">Restricted Area. Unauthorized access is monitored.</p>
        </div>
    </div>
</body>

</html>
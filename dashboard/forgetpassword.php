<?php
require_once 'init.php';
if (post('submit_forget')) {
    $email = request('email', 'post');
    $u = findQuery(" SELECT id FROM users WHERE email='$email' AND is_active=1 ");
    if ($u) {
        $code = rand(100000, 999999);
        $exp = date('Y-m-d H:i:s', strtotime('+15 minutes'));
        execute(" INSERT INTO users_forget (email,code,expires_at) VALUES ('$email','$code','$exp') ");
        redirect("resetpassword.php?email=$email&demo_code=$code");
    } else {
        $error = "Email address not found or inactive.";
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Forgot Password | Gashy Admin</title>
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
            <h1 class="text-2xl font-black tracking-tighter mb-2">Recovery</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Enter your admin email to reset password</p>
        </div>
        <?php if (isset($error)): ?><div class="p-4 bg-red-100 dark:bg-red-500/20 border border-red-200 dark:border-red-500/30 text-red-600 dark:text-red-400 text-sm rounded-lg font-medium text-center"><?= $error ?></div><?php endif; ?>
        <form method="POST" action="" class="space-y-5">
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Email Address</label>
                <input type="email" name="email" required class="w-full px-4 py-3 bg-gray-50 dark:bg-black/20 border border-gray-200 dark:border-white/10 rounded-xl focus:border-primary-500 focus:ring-1 focus:ring-primary-500 outline-none text-gray-900 dark:text-white transition-all">
            </div>
            <button type="submit" name="submit_forget" value="1" class="w-full py-3.5 bg-primary-600 hover:bg-primary-500 text-dark-900 font-bold rounded-xl shadow-lg shadow-primary-500/25 transition-all transform hover:-translate-y-0.5">Send Code</button>
        </form>
        <div class="text-center">
            <a href="login.php" class="text-sm text-gray-500 hover:text-primary-500 transition-colors">Back to Login</a>
        </div>
    </div>
</body>

</html>
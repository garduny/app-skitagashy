<?php
__FILE__ === realpath($_SERVER['SCRIPT_FILENAME'] ?? '') && exit(header('HTTP/1.1 403 Forbidden') . '403 Forbidden');
$isDashboard = basename(dirname($_SERVER['SCRIPT_FILENAME'])) === 'dashboard';
$manifest = $isDashboard ? $vite('dashboardpwamanifest') : $vite('clientpwamanifest');
$themeColor = $isDashboard ? '#625BFF' : '#00ff88';
$appName = $setting[$lang . '_appname'];
$iconSizes = [72, 96, 128, 144, 152, 192, 384, 512];
$splashes = [
  '640x1136' => '(device-width: 320px) and (device-height: 568px) and (-webkit-device-pixel-ratio: 2)',
  '750x1334' => '(device-width: 375px) and (device-height: 667px) and (-webkit-device-pixel-ratio: 2)',
  '1242x2208' => '(device-width: 621px) and (device-height: 1104px) and (-webkit-device-pixel-ratio: 3)',
  '1125x2436' => '(device-width: 375px) and (device-height: 812px) and (-webkit-device-pixel-ratio: 3)',
  '828x1792' => '(device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 2)',
  '1242x2688' => '(device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 3)',
  '1536x2048' => '(device-width: 768px) and (device-height: 1024px) and (-webkit-device-pixel-ratio: 2)',
  '1668x2224' => '(device-width: 834px) and (device-height: 1112px) and (-webkit-device-pixel-ratio: 2)',
  '1668x2388' => '(device-width: 834px) and (device-height: 1194px) and (-webkit-device-pixel-ratio: 2)',
  '2048x2732' => '(device-width: 1024px) and (device-height: 1366px) and (-webkit-device-pixel-ratio: 2)',
];
?>
<link rel="manifest" href="<?= $manifest ?>">
<meta name="theme-color" content="<?= $themeColor ?>">
<meta name="mobile-web-app-capable" content="yes">
<meta name="application-name" content="<?= $appName ?>">
<link rel="icon" sizes="512x512" href="<?= $vite('pwa-icon-512x512') ?>">
<?php foreach ($iconSizes as $size): ?>
  <link rel="apple-touch-icon" href="<?= $vite("pwa-icon-{$size}x{$size}") ?>" sizes="<?= $size ?>x<?= $size ?>"><?php endforeach; ?>
<link rel="mask-icon" href="<?= $vite('pwa-icon-512x512') ?>" color="<?= $themeColor ?>">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="white">
<meta name="apple-mobile-web-app-title" content="<?= $appName ?>">
<?php foreach ($splashes as $size => $media): ?>
  <link href="<?= $vite("pwa-splash-{$size}") ?>" media="<?= $media ?>" rel="apple-touch-startup-image"><?php endforeach; ?>
<meta name="msapplication-TileColor" content="white">
<meta name="msapplication-TileImage" content="<?= $vite('pwa-icon-512x512') ?>">
<meta name="display" content="standalone">
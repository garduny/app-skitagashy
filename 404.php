<?php
include_once("./server/init.php");
$serialized = md5(serialize($_GET));
$cacheKey = "dashboardpage_{$actPage}_lang_{$lang}_{$serialized}";
cache_start($cacheKey);
?>
<!DOCTYPE html>
<html lang="<?= $trans['LANG']; ?>" dir="<?= $trans['DIRECTIONALITY']; ?>">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" href="./server/uploads/setting/<?= setting('favicon')['favicon']; ?>">
	<link rel="preload" href="<?= $vite('public/fonts/rabar.ttf'); ?>" as="font" type="font/ttf" crossorigin="anonymous">
	<link rel="prefetch" href="<?= $vite('public/fonts/tabler-icons.ttf'); ?>" as="font" type="font/ttf" crossorigin="anonymous">
	<link rel="prefetch" href="<?= $vite('public/fonts/tabler-icons.woff'); ?>" as="font" type="font/woff" crossorigin="anonymous">
	<link rel="prefetch" href="<?= $vite('public/fonts/tabler-icons.woff2'); ?>" as="font" type="font/woff2" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="<?= $vite('appcss'); ?>">
	<link rel="stylesheet" type="text/css" href="<?= $vite('dashboardcss'); ?>">
	<link rel="stylesheet" type="text/css" href="<?= $vite('icons'); ?>">
	<link rel="icon" href="<?= $vite('notfoundimg'); ?>">
	<title>404</title>
	<style>
		* {
			margin: 0;
			padding: 0;
			box-sizing: border-box;
		}

		html,
		body {
			height: 100%;
			overflow-x: hidden;
		}

		body.light-theme {
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			height: 100vh;
			font-family: 'Inter', -apple-system, BlinkMacSystemFont, system-ui, sans-serif;
			position: relative;
			display: flex;
			align-items: center;
			justify-content: center;
		}

		body.dark-theme {
			background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
			height: 100vh;
			display: flex;
			align-items: center;
			justify-content: center;
		}

		body::before {
			content: '';
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background:
				radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
				radial-gradient(circle at 80% 20%, rgba(255, 119, 198, 0.15) 0%, transparent 50%),
				radial-gradient(circle at 40% 40%, rgba(120, 219, 226, 0.1) 0%, transparent 50%);
			pointer-events: none;
			z-index: -1;
			animation: backgroundShift 20s ease-in-out infinite;
		}

		@keyframes backgroundShift {

			0%,
			100% {
				opacity: 1;
				transform: scale(1) rotate(0deg);
			}

			50% {
				opacity: 0.8;
				transform: scale(1.1) rotate(180deg);
			}
		}

		.main-container {
			position: absolute;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%);
			width: 100%;
			max-width: 500px;
			padding: 0 1rem;
			z-index: 2;
		}

		.card-container {
			backdrop-filter: blur(20px);
			border-radius: 30px;
			padding: 2rem 1rem;
			background: rgba(255, 255, 255, 0.08);
			border: 1px solid rgba(255, 255, 255, 0.15);
			box-shadow:
				0 20px 60px 0 rgba(31, 38, 135, 0.2),
				0 8px 32px 0 rgba(102, 126, 234, 0.1),
				inset 0 1px 0 rgba(255, 255, 255, 0.1);
			animation: containerFloat 6s ease-in-out infinite;
			text-align: center;
			transform-style: preserve-3d;
			position: relative;
		}

		.dark-theme .card-container {
			background: rgba(0, 0, 0, 0.15);
			border: 1px solid rgba(255, 255, 255, 0.08);
		}

		@keyframes containerFloat {

			0%,
			100% {
				transform: translateY(0px);
			}

			50% {
				transform: translateY(-10px);
			}
		}

		.error-code {
			font-size: 8rem;
			font-weight: 900;
			background: linear-gradient(45deg, #667eea, #764ba2, #f093fb, #667eea);
			background-size: 400% 400%;
			background-clip: text;
			-webkit-background-clip: text;
			-webkit-text-fill-color: transparent;
			animation: gradientMove 4s ease infinite;
			margin-bottom: 1rem;
			line-height: 0.8;
			letter-spacing: -0.05em;
			will-change: background-position;
		}

		@keyframes gradientMove {
			0% {
				background-position: 0% 50%;
			}

			50% {
				background-position: 100% 50%;
			}

			100% {
				background-position: 0% 50%;
			}
		}

		.error-message {
			font-size: 1.8rem;
			font-weight: 300;
			opacity: 0.95;
			font-family: rabar;
			margin-bottom: 3rem;
			animation: fadeInUp 1s ease-out 0.5s both;
			letter-spacing: 0.02em;
			color: rgba(255, 255, 255, 0.95);
			text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
		}

		@keyframes fadeInUp {
			from {
				opacity: 0;
				transform: translateY(30px);
			}

			to {
				opacity: 0.95;
				transform: translateY(0);
			}
		}

		.image-container {
			position: relative;
			display: flex;
			justify-content: center;
			align-items: center;
			margin: 2rem 0;
		}

		.lazyload {
			transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
			filter: drop-shadow(0 10px 30px rgba(0, 0, 0, 0.3));
			animation: imageGlow 3s ease-in-out infinite alternate;
			max-width: 300px;
			width: 100%;
			height: auto;
		}

		@keyframes imageGlow {
			0% {
				filter: drop-shadow(0 10px 30px rgba(0, 0, 0, 0.3)) brightness(1);
				transform: scale(1);
			}

			100% {
				filter: drop-shadow(0 15px 40px rgba(102, 126, 234, 0.4)) brightness(1.05);
				transform: scale(1.02);
			}
		}

		.lazyload:hover {
			transform: scale(1.05) rotate(2deg);
			filter: drop-shadow(0 20px 40px rgba(102, 126, 234, 0.5));
		}

		.pulse-ring {
			position: absolute;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%);
			width: 120%;
			height: 120%;
			border: 2px solid rgba(255, 255, 255, 0.3);
			border-radius: 50%;
			animation: pulse 2s cubic-bezier(0.455, 0.03, 0.515, 0.955) infinite;
			pointer-events: none;
		}

		@keyframes pulse {
			0% {
				transform: translate(-50%, -50%) scale(0.8);
				opacity: 1;
			}

			100% {
				transform: translate(-50%, -50%) scale(1.2);
				opacity: 0;
			}
		}

		.back-button {
			background: linear-gradient(45deg, #667eea, #764ba2);
			border: none;
			color: white;
			font-weight: 600;
			letter-spacing: 0.5px;
			position: fixed;
			bottom: 2rem;
			left: 50%;
			transform: translateX(-50%);
			overflow: hidden;
			transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
			backdrop-filter: blur(15px);
			box-shadow:
				0 10px 30px rgba(102, 126, 234, 0.5),
				inset 0 1px 0 rgba(255, 255, 255, 0.2);
			z-index: 1000;
			will-change: transform;
			width: 100px;
			height: 60px;
			border-radius: 50px;
			display: flex;
			align-items: center;
			justify-content: center;
			cursor: pointer;
		}

		.dark-theme .back-button {
			background: linear-gradient(45deg, #4facfe, #00f2fe);
			box-shadow:
				0 10px 30px rgba(79, 172, 254, 0.5),
				inset 0 1px 0 rgba(255, 255, 255, 0.2);
		}

		.back-button::before {
			content: '';
			position: absolute;
			top: 0;
			left: -100%;
			width: 100%;
			height: 100%;
			background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
			transition: left 0.5s;
		}

		.back-button:hover::before {
			left: 100%;
		}

		.back-button:hover {
			transform: translateX(-50%) translateY(-5px) scale(1.05);
			box-shadow:
				0 20px 40px rgba(102, 126, 234, 0.7),
				inset 0 1px 0 rgba(255, 255, 255, 0.3);
		}

		.back-button:active {
			transform: translateX(-50%) translateY(-2px) scale(1.02);
		}

		.ti-arrow-badge-left,
		.ti-arrow-badge-right {
			transition: transform 0.3s ease;
			display: inline-block;
			font-size: 2rem;
		}

		.back-button:hover .ti-arrow-badge-left,
		.ti-arrow-badge-right {
			transform: translateX(-5px);
		}

		@media (max-width: 768px) {
			.main-container {
				padding: 0 1rem;
				max-width: calc(100% - 2rem);
			}

			.card-container {
				padding: 3rem 2rem;
			}

			.error-code {
				font-size: 5rem;
			}

			.error-message {
				font-size: 1.4rem;
				margin-bottom: 2.5rem;
			}

			.lazyload {
				max-width: 250px;
			}

			.back-button {
				bottom: 1.5rem;
				width: 90px;
				height: 55px;
			}

			.ti-arrow-badge-left,
			.ti-arrow-badge-right {
				font-size: 1.8rem;
			}
		}

		@media (max-width: 480px) {
			.card-container {
				padding: 2.5rem 1.5rem;
			}

			.error-code {
				font-size: 4rem;
			}

			.error-message {
				font-size: 1.2rem;
				margin-bottom: 2rem;
			}

			.lazyload {
				max-width: 200px;
			}

			.back-button {
				width: 80px;
				height: 50px;
				bottom: 1rem;
			}

			.ti-arrow-badge-left,
			.ti-arrow-badge-right {
				font-size: 1.5rem;
			}
		}

		@media (max-height: 600px) {
			.card-container {
				padding: 2rem 2.5rem;
			}

			.error-code {
				font-size: 5rem;
				margin-bottom: 0.5rem;
			}

			.error-message {
				font-size: 1.3rem;
				margin-bottom: 1.5rem;
			}

			.lazyload {
				max-width: 200px;
			}

			.image-container {
				margin: 1rem 0;
			}
		}

		.card-container,
		.back-button,
		.lazyload {
			transform: translate3d(0, 0, 0);
			backface-visibility: hidden;
		}

		@media (prefers-reduced-motion: reduce) {
			* {
				animation-duration: 0.01ms !important;
				animation-iteration-count: 1 !important;
				transition-duration: 0.01ms !important;
			}

			.pulse-ring {
				display: none;
			}
		}

		.container,
		.row,
		.col-md-12 {
			margin: 0 !important;
			padding: 0 !important;
		}

		.centered,
		.m-auto,
		.mx-auto,
		.text-center {
			position: static !important;
			margin: 0 !important;
		}
	</style>
</head>

<body class="light-theme">
	<script src="<?= $vite('appjs'); ?>"></script>
	<div class="main-container">
		<div class="card-container">
			<div class="error-code">404</div>
			<div class="error-message"><?= $trans['NORESULT']; ?></div>
			<div class="image-container">
				<div class="pulse-ring"></div>
				<img data-src="<?= $vite('notfoundimg'); ?>"
					data-preview="<?= $vite('lazyimg'); ?>"
					class="lazyload component-theme outline-theme rounded-5 p-2"
					alt="404 Not Found">
			</div>
		</div>
	</div>
	<button onclick="window.history.back()" class="back-button">
		<span class="ti ti-arrow-badge-<?= $trans['START']; ?>"></span>
	</button>
</body>

</html>
<?php
cache_end();
?>
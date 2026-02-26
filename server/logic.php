<?php
const CACHE_DIR = __DIR__ . '/../.cache/';
const CACHE_QUERIES_DIR = CACHE_DIR . 'queries/';
const CACHE_TTL = 86400; // 1 day

function cache_path(string $key, bool $isQuery = false): string
{
    $baseDir = $isQuery ? CACHE_QUERIES_DIR : CACHE_DIR;
    if ($isQuery && !is_dir($baseDir)) {
        mkdir($baseDir, 0755, true);
    }
    $hash = substr(md5($key), 0, 8);
    $clean = preg_replace('/[^a-z0-9\-_\.]/i', '', $key);
    return $baseDir . $hash . $clean . '.cache';
}

function cache_get(string $key, int $ttl = CACHE_TTL, bool $isQuery = false): ?string
{
    $file = cache_path($key, $isQuery);
    if (!is_file($file)) return null;

    try {
        $data = json_decode(file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);
    } catch (JsonException) {
        @unlink($file);
        return null;
    }

    if (($data['expires'] ?? 0) < time()) {
        @unlink($file);
        return null;
    }

    foreach (($data['sources'] ?? []) as $src => $cachedMtime) {
        if (!is_file($src) || filemtime($src) > $cachedMtime) {
            @unlink($file);
            return null;
        }
    }

    return $data['content'] ?? null;
}

function cache_set(string $key, string $content, int $ttl = CACHE_TTL, bool $isQuery = false): void
{
    $baseDir = $isQuery ? CACHE_QUERIES_DIR : CACHE_DIR;
    if (!is_dir($baseDir)) {
        mkdir($baseDir, 0755, true);
    }

    $sources = [];
    foreach (get_included_files() as $src) {
        if (is_file($src)) {
            $sources[$src] = filemtime($src);
        }
    }

    $data = [
        'expires' => time() + $ttl,
        'sources' => $sources,
        'content' => $content,
    ];

    file_put_contents(cache_path($key, $isQuery), json_encode($data, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE));
}

function cache_start(string $key = '', int $ttl = CACHE_TTL): void
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') return;
    $key = $key ?: basename($_SERVER['SCRIPT_FILENAME']);
    if (($cached = cache_get($key, $ttl)) !== null) {
        echo $cached;
        defined('__CACHE_HIT__') || define('__CACHE_HIT__', true);
        exit;
    }
    static $skeleton = <<<HTML
       <style>
            .light-theme .skeleton,
            .dark-theme .skeleton {
                position: relative;
                overflow: hidden;
                background-size: 200% 100%;
                animation: skeleton-loading 1.5s infinite;
                pointer-events: none;
                color: transparent;
                border: none;
            }
            .skeleton * {
                visibility: hidden;
            }
            @keyframes skeleton-loading {
                0% {
                    background-position: -200% 0;
                }
                100% {
                    background-position: 200% 0;
                }
            }
            .light-theme .skeleton {
                background: linear-gradient(90deg, #e0e0e0 25%, #f5f5f5 50%, #e0e0e0 75%);
                background-size: 200% 100%;
                animation: skeleton-loading 1.5s infinite;
                box-shadow: 0px 0px 1.3px 0px #444B56;
            }
            .dark-theme .skeleton {
                background: linear-gradient(90deg, #171F2F 25%, #101828 50%, #171F2F 75%);
                background-size: 200% 100%;
                animation: skeleton-loading 1.5s infinite;
                outline: 1px solid #2F3844;
            }
        </style>
HTML;
    echo $skeleton;
    ob_start();
    defined('__CACHE_KEY__') || define('__CACHE_KEY__', $key);
    defined('__CACHE_TTL__') || define('__CACHE_TTL__', $ttl);
}


function cache_end(): void
{
    if (defined('__CACHE_HIT__') || !defined('__CACHE_KEY__')) return;
    $html = ob_get_clean();
    echo $html;
    cache_set(__CACHE_KEY__, $html, __CACHE_TTL__);
}


function cache_once(string $key, callable $callback, int $ttl = CACHE_TTL, bool $isQuery = true): mixed
{
    $cached = cache_get($key, $ttl, $isQuery);
    if ($cached !== null) {
        $data = @unserialize($cached);
        if (is_object($data) || is_array($data) || $data === false || is_scalar($data)) {
            return $data;
        }
    }

    $result = $callback();
    $serialized = serialize($result);
    cache_set($key, $serialized, $ttl, $isQuery);
    return $result;
}

function cache_invalidate_contains(string $partialName): void
{
    // Main cache dir
    foreach (glob(CACHE_DIR . '*.cache') as $file) {
        if (str_contains(basename($file), $partialName)) {
            @unlink($file);
        }
    }
    // Queries cache dir
    foreach (glob(CACHE_QUERIES_DIR . '*.cache') as $file) {
        if (str_contains(basename($file), $partialName)) {
            @unlink($file);
        }
    }
}

function cache_delete_by_prefix(string $prefix): void
{
    foreach (glob(CACHE_DIR . '*') as $file) {
        if (str_starts_with(basename($file), $prefix)) {
            @unlink($file);
        }
    }
    foreach (glob(CACHE_QUERIES_DIR . '*') as $file) {
        if (str_starts_with(basename($file), $prefix)) {
            @unlink($file);
        }
    }
}


function cache_clear_all(): void
{
    $dirs = [CACHE_DIR, CACHE_QUERIES_DIR];
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) continue;
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($iterator as $fileinfo) {
            if ($fileinfo->isFile() && str_ends_with($fileinfo->getFilename(), '.cache')) {
                @unlink($fileinfo->getPathname());
            }
        }
    }
}










// const LANGUAGES = ['ku', 'ar', 'en'];

// function setLang(): void
// {
//     $_SESSION['lang'] ??= setting('mainlang')['mainlang'] ?? 'en';
// }

// function changeLang(): void
// {
//     $lang = $_REQUEST['lang'] ?? null;
//     if ($lang && in_array($lang, LANGUAGES, true)) {
//         $_SESSION['lang'] = $lang;
//         // optional: also set cookie for persistence
//         setcookie('lang', $lang, time() + 86400 * 30, '/');
//         back();
//     }
// }

// function initLang(): string
// {
//     setLang();

//     if (isset($_REQUEST['lang'])) {
//         changeLang();
//     } elseif (!isset($_SESSION['lang']) && isset($_COOKIE['lang'])) {
//         $_SESSION['lang'] = $_COOKIE['lang'];
//     }

//     return $_SESSION['lang'];
// }

// function lang(): string
// {
//     static $lang = null;
//     return $lang ??= initLang();
// }

// function langArray(): array
// {
//     static $langData = null;
//     if ($langData !== null) return $langData;

//     $path = __DIR__ . "/../lang/" . lang() . ".json";

//     if (!is_file($path)) {
//         $langData = [];
//     } else {
//         try {
//             $json = file_get_contents($path);
//             $langData = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
//         } catch (Throwable) {
//             $langData = [];
//         }
//     }

//     return $langData;
// }

// function trans(?string $key = null): array|string|null
// {
//     $lang = langArray();
//     return $key ? ($lang[$key] ?? $key) : $lang;
// }

// $lang = lang();
// $trans = trans();






function apiMode()
{
    $input = file_get_contents('php://input');
    $json = json_decode($input, true);
    return (!empty($json) && $json !== null) || hasRequest('apimode', 'any');
}

function encode($data)
{
    echo json_encode($data, JSON_NUMERIC_CHECK);
    exit;
}

function decode($data)
{
    echo json_decode($data);
    exit;
}

function post($key)
{
    // if (!checkCsrfToken(request('csrf_token', 'post'))) {
    //     dnd('Invalid CSRF Token', 400);
    // }
    return isset($_POST[$key]);
}

function get($key)
{
    return isset($_GET[$key]);
}

function secure($data)
{
    return addslashes(str_replace("'", "''", htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8')));
}

function escapeSecure($data)
{
    return str_replace("'", "''", $data);
}

function escapeStr($data)
{
    return addslashes(str_replace("'", "''", $data));
}



$requestPost = $_POST ?? [];
$requestGet = $_GET ?? [];
$requestAny = $_REQUEST ?? [];
$requestFiles = $_FILES ?? [];
global $body;

function hasRequest($key, $type = 'body')
{
    global $body, $requestPost, $requestGet, $requestAny, $requestFiles;
    $type = strtolower($type);

    return match ($type) {
        'body' => isset($body->$key),
        'post', 'put', 'delete' => isset($requestPost[$key]),
        'get' => isset($requestGet[$key]),
        'any' => isset($requestAny[$key]),
        'files' => isset($requestFiles[$key]),
        default => false,
    };
}

function hasRequestMustTrue($key, $type = 'body')
{
    global $body, $requestPost, $requestGet, $requestAny, $requestFiles;
    $type = strtolower($type);

    return match ($type) {
        'body' => isset($body->$key) && $body->$key == true,
        'post', 'put', 'delete' => isset($requestPost[$key]) && $requestPost[$key] == true,
        'get' => isset($requestGet[$key]) && $requestGet[$key] == true,
        'any' => isset($requestAny[$key]) && $requestAny[$key] == true,
        'files' => isset($requestFiles[$key]) && $requestFiles[$key] == true,
        default => false,
    };
}

function request($key, $type = 'body')
{
    global $body, $requestPost, $requestGet, $requestAny, $requestFiles;
    $type = strtolower($type);

    return match ($type) {
        'body' => isset($body->$key) ? secure($body->$key) : null,
        'post', 'put', 'delete' => isset($requestPost[$key]) ? secure($requestPost[$key]) : null,
        'get' => isset($requestGet[$key]) ? secure($requestGet[$key]) : null,
        'any' => isset($requestAny[$key]) ? secure($requestAny[$key]) : null,
        'files' => $requestFiles[$key] ?? null,
        default => null,
    };
}

function filterRequest(array $allowedParams, $type = 'body', $denyOthers = true)
{
    global $body, $requestPost, $requestGet;
    $params = match ($type) {
        'body' => (array)$body,
        'get' => $requestGet,
        'post' => $requestPost,
        default => [],
    };

    foreach ($allowedParams as $key) {
        if (empty(request($key, $type))) {
            dnd('INVALID_REQUEST', 400);
        }
    }

    if ($denyOthers) {
        foreach ($params as $key => $_) {
            if (!in_array($key, $allowedParams)) {
                dnd('INVALID_REQUEST', 400);
            }
        }
    }
}



function generate_token(): string
{
    return bin2hex(random_bytes(32));
}

function generate_mailer_code(): int
{
    return random_int(100000, 999999);
}

function checkAuthToken($where = null, $request = 'body')
{
    existOrFail("users_tokens", "token", request('authtoken', $request), $where, "token", 'NOT_AUTHORIZED', 401);
}

function unlinkFile($fileAndPath)
{
    if (is_file($fileAndPath)) {
        unlink($fileAndPath);
    }
}

function execute($sql)
{
    Builder::execute($sql);
}

function findQuery($sql)
{
    return Builder::findQuery($sql);
}

function countQuery($sql)
{
    return Builder::countQuery($sql);
}

function getQuery($sql)
{
    return Builder::getQuery($sql);
}

function existOrFail($table, $field, $value, $extraWhere = null, $fields = '*', $error = 'DATA_NOT_FOUND', $code = 404)
{
    $where = "$field = '" . addslashes($value) . "'";
    if ($extraWhere) {
        $where .= " AND $extraWhere";
    }
    $data = findQuery(" SELECT $fields FROM $table WHERE $where ");

    if (empty($data) || $data === false) {
        apiMode() ? dnd($error, $code) : redirect('404');
    }
    return $data;
}

function exist($table, $field, $value, $extraWhere = null, $fields = '*')
{
    $where = "$field = '" . addslashes($value) . "'";
    if ($extraWhere) {
        $where .= " AND $extraWhere";
    }
    return findQuery(" SELECT $fields FROM $table WHERE $where");
}

// function setting($fields = "*")
// {
//     return findQuery(" SELECT $fields FROM setting ");
// }

function upload($name, $path, $validate = false)
{
    global $config, $trans;
    $file = $_FILES[$name] ?? null;

    if (empty($file['name'] ?? null)) {
        return $validate ? (apiMode() ? $trans['FILE_IS_EMPTY'] : alert('Upload', 'FILE_IS_EMPTY', 'warning')) : null;
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $config['file']['extensions'])) {
        return $validate ? (apiMode() ? $trans['FILE_TYPE_NOT_ALLOWED'] : alert('Upload', 'FILE_TYPE_NOT_ALLOWED', 'warning')) : null;
    }

    if ($file['size'] >= $config['file']['max_size']) {
        return $validate ? (apiMode() ? $trans['FILE_IS_TO_BIG'] : alert('Upload', 'FILE_IS_TO_BIG', 'warning')) : null;
    }

    $newName = uniqid('', true) . '.' . $ext;
    if (move_uploaded_file($file['tmp_name'], $path . $newName)) {
        return $newName;
    }

    return $validate ? (apiMode() ? $trans['UPLOAD_FAILED'] : alert('Upload', 'UPLOAD_FAILED', 'warning')) : null;
}

function uploadMultiple($name, $path, $validate = false)
{
    global $config, $trans;

    $files = $_FILES[$name] ?? null;
    if (empty($files['name']) || !is_array($files['name'])) {
        return $validate ? (apiMode() ? $trans['FILE_IS_EMPTY'] : alert('Upload', 'FILE_IS_EMPTY', 'warning')) : [];
    }

    $uploaded = [];
    foreach ($files['name'] as $i => $fileName) {
        if (empty($fileName)) {
            if ($validate) return apiMode() ? $trans['FILE_IS_EMPTY'] : alert('Upload', 'FILE_IS_EMPTY', 'warning');
            continue;
        }

        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if (!in_array($ext, $config['file']['extensions'])) {
            return $validate ? (apiMode() ? $trans['FILE_TYPE_NOT_ALLOWED'] : alert('Upload', 'FILE_TYPE_NOT_ALLOWED', 'warning')) : [];
        }

        if ($files['size'][$i] >= $config['file']['max_size']) {
            return $validate ? (apiMode() ? $trans['FILE_IS_TO_BIG'] : alert('Upload', 'FILE_IS_TO_BIG', 'warning')) : [];
        }

        if ($files['error'][$i] !== 0) {
            return $validate ? (apiMode() ? $trans['UPLOAD_FAILED'] : alert('Upload', 'UPLOAD_FAILED', 'warning')) : [];
        }

        $newName = uniqid('', true) . '.' . $ext;
        if (move_uploaded_file($files['tmp_name'][$i], $path . $newName)) {
            $uploaded[] = $newName;
        }
    }

    return $uploaded;
}


function mailer($subject, $body, $from, $to)
{
    global $config;
    $mail = new PHPMailer();
    $mail->CharSet = 'UTF-8';
    $mail->isSMTP();
    $mail->isHTML(true);
    $mail->SMTPAuth = $config['mailer']['smtp_auth'];
    $mail->SMTPSecure = $config['mailer']['smtp_secure'];
    $mail->Host = $config['mailer']['host'];
    $mail->Port = $config['mailer']['port'];
    $mail->Username = $config['mailer']['username'];
    $mail->Password = $config['mailer']['password'];
    $mail->From = $config['mailer']['from'];
    $mail->FromName = $from;
    $mail->Sender = $config['mailer']['sender'];
    $mail->addReplyTo($config['mailer']['reply']);
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->addAddress($to);

    return $mail->send();
}

function authToken($auth, $auth_id)
{
    return bin2hex(random_bytes(32)) . '_' . $auth . '_' . $auth_id;
}

function authDevice()
{
    $agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $client = new Client();
    $info = $client->getAll($agent);

    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    return implode('|', [$ip, $info['device_type'], $info['os_title'], $info['browser_title']]);
}



function paginationButtons(int $totalItems, int $currentPage = 1, int $perPage = 10, string $url = '?'): string
{
    global $trans;
    $totalPages = (int) ceil($totalItems / $perPage);
    if ($totalPages <= 1) return '';
    $pagination = '<div class="col-12 text-center m-auto scrollbar">
    <ul class="pagination d-flex flex-wrap pagination-lg bg-component p-2 mt-3 rounded-1 text-center w-auto">';
    if ($currentPage > 1) {
        $pagination .= '<li class="page-item">
            <a class="page-link btn component-theme outline-theme text-theme m-1 rounded-5" href="' . $url . 'page=' . ($currentPage - 1) . '">' . $trans['PREVIOUS'] . '</a>
        </li>';
    }
    for ($i = 1; $i <= $totalPages; $i++) {
        $active = $currentPage === $i
            ? 'bg-main text-decoration-none'
            : 'component-theme outline-theme text-theme';
        $pagination .= '<li class="page-item">
            <a class="page-link btn ' . $active . ' m-1 rounded-5" href="' . $url . 'page=' . $i . '">' . $i . '</a>
        </li>';
    }
    if ($currentPage < $totalPages) {
        $pagination .= '<li class="page-item">
            <a class="page-link btn component-theme outline-theme text-theme m-1 rounded-5" href="' . $url . 'page=' . ($currentPage + 1) . '">' . $trans['NEXT'] . '</a>
        </li>';
    }
    return $pagination . '</ul></div>';
}

function paginationButtonsUrl(): string
{
    $url = strtok($_SERVER['REQUEST_URI'], '?');
    $query = $_GET;
    unset($query['page']);
    $queryString = http_build_query($query);
    return $url . ($queryString ? '?' . $queryString . '&' : '?');
}

function paginationPage(): int
{
    $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
    return $page > 0 ? $page : 1;
}

function paginationStart(int $perPage): int
{
    return ($page = paginationPage() - 1) * $perPage;
}

function pagination(int $totalItems, int $perPage, ?string $url = null): void
{
    echo paginationButtons($totalItems, paginationPage(), $perPage, $url ?? paginationButtonsUrl());
}

function apiPaginationPage(): int
{
    $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
    return $page > 0 ? $page : 1;
}

function apiPaginationPerPage(int $default): int
{
    $perPage = filter_input(INPUT_GET, 'perpage', FILTER_VALIDATE_INT);
    return $perPage > 0 ? $perPage : $default;
}

function apiPaginationStart(int $perPage): int
{
    return (apiPaginationPage() - 1) * $perPage;
}

function redirect(string $page): void
{
    header("Location: $page");
    exit;
}

function rootUrl(string $dir = __DIR__): string
{
    $scheme = !empty($_SERVER['HTTPS']) ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $dir = str_replace('\\', '/', realpath($dir));
    $base = !empty($_SERVER['CONTEXT_PREFIX'])
        ? $_SERVER['CONTEXT_PREFIX'] . substr($dir, strlen($_SERVER['CONTEXT_DOCUMENT_ROOT']))
        : substr($dir, strlen($_SERVER['DOCUMENT_ROOT']));
    return "$scheme://$host$base/";
}

function root(): string
{
    return (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/';
}

function fullUrl(): string
{
    return root() . ltrim($_SERVER['REQUEST_URI'], '/');
}

function actUrl(): string
{
    return $_SERVER['REQUEST_URI'];
}

function actPage(): string
{
    return basename(parse_url(strtolower($_SERVER['REQUEST_URI']), PHP_URL_PATH));
}
$actPage = actPage();

function realPage(): string
{
    return str_replace('.php', '', basename($_SERVER['PHP_SELF']));
}

function urlParamPrefix(string $request, string $param): string
{
    $url = actUrl();
    $url = preg_replace("/[?&]$request=" . preg_quote(request($request, 'get'), '/') . "/", '', $url);
    return (count($_GET) > 0 ? "$url&$param" : "$url?$param");
}

function back(): void
{
    if (!empty($_SERVER["HTTP_REFERER"])) {
        redirect($_SERVER["HTTP_REFERER"]);
    }
}

function backWithReplace(string $replace): void
{
    if (!empty($_SERVER["HTTP_REFERER"])) {
        $url = str_replace($replace, '', $_SERVER["HTTP_REFERER"]);
        redirect($url);
    }
}

function checkAuth(string $authtype, string $fields = '*', bool $returnData = false): bool|array
{
    $id = $_COOKIE["{$authtype}auth_id"] ?? null;
    $token = $_COOKIE["{$authtype}auth_token"] ?? null;
    if (!$id || !$token) {
        killAuthCredentials($authtype);
        return $returnData ? ['isauthorized' => false] : false;
    }
    $safeId = (int) $id;
    $safeToken = addslashes($token);
    $user = findQuery(" SELECT $fields FROM `$authtype` WHERE id = $safeId LIMIT 1 ");
    if (!$user) {
        killAuthCredentials($authtype);
        return $returnData ? ['isauthorized' => false] : false;
    }
    $exists = countQuery(" SELECT 1 FROM `{$authtype}_tokens` WHERE auth_id = $safeId AND token = '$safeToken' LIMIT 1 ");
    if ($exists) {
        return $returnData ? ['auth' => $user, 'isauthorized' => true] : true;
    }
    killAuthCredentials($authtype);
    return $returnData ? ['isauthorized' => false] : false;
}

function checkAuthWithRedirect(string $authtype, string $page): void
{
    if (!checkAuth($authtype)) {
        killAuthCredentials($authtype);
        redirect($page);
    }
}

function checkAuthorized(string $authtype): bool
{
    $authorized = checkAuth($authtype);
    if (!$authorized) killAuthCredentials($authtype);
    return $authorized;
}

function checkAuthorizedWithRedirect(string $authtype, string $page): void
{
    checkAuthorized($authtype) ? redirect($page) : killAuthCredentials($authtype);
}

function lastId(string $table, string $column): mixed
{
    $row = findQuery(" SELECT $column FROM $table ORDER BY $column DESC LIMIT 1 ");
    return $row[$column] ?? null;
}




function killAuthCredentials(string $authtype): void
{
    $prefix = $authtype . 'auth_';
    $authId = $_COOKIE[$prefix . 'id'] ?? null;
    $device = authDevice();

    if ($authId) {
        execute("DELETE FROM {$authtype}_tokens WHERE auth_id='{$authId}' AND device='{$device}'");
    }

    // Clear session data
    foreach (['id', 'name', 'email', 'token'] as $field) {
        unset($_SESSION[$prefix . $field]);
    }

    // Clear cookies
    foreach (['id', 'name', 'email', 'token'] as $field) {
        setcookie($prefix . $field, '', time() - 86400, '/');
    }
}

function createAuthCredentials(
    string $authtype,
    string $token,
    string $authId,
    string $userName,
    string $userEmail,
    string $rememberUsername,
    string $rememberPassword
): void {
    killAuthCredentials($authtype);

    $prefix = $authtype . 'auth_';

    // Set session
    $_SESSION[$prefix . 'token'] = $authId;
    $_SESSION[$prefix . 'id'] = $authId;
    $_SESSION[$prefix . 'name'] = $userName;
    $_SESSION[$prefix . 'email'] = $userEmail;

    // Set cookies (30 days)
    $expiry = time() + (86400 * 30);
    foreach (
        [
            'token' => $token,
            'id' => $authId,
            'name' => $userName,
            'email' => $userEmail
        ] as $key => $value
    ) {
        setcookie($prefix . $key, $value, $expiry, '/');
    }

    // Clear & set remember cookies
    setcookie($authtype . 'remember_username', '', time() - 86400, '/');
    setcookie($authtype . 'remember_password', '', time() - 86400, '/');
    setcookie($authtype . 'remember_username', $rememberUsername, $expiry, '/');
    setcookie($authtype . 'remember_password', $rememberPassword, $expiry, '/');
}

function auth(string $auth, string $fields = '*'): ?array
{
    $authId = cookie($auth . 'auth_id');
    return $authId ? findQuery(" SELECT $fields FROM $auth WHERE id='{$authId}'") : null;
}





function createSession(string $name, mixed $value): void
{
    $_SESSION[$name] = $value;
}

function existSession(string $name): bool
{
    return isset($_SESSION[$name]);
}

function killSession(string $name): void
{
    unset($_SESSION[$name]);
}

function session(string $name): mixed
{
    return $_SESSION[$name] ?? null;
}

function cookie(string $name): mixed
{
    return $_COOKIE[$name] ?? null;
}




function dnd($data, int $code): void
{
    if (apiMode()) {
        encode([
            'status' => false,
            'code' => $code,
            'msg' => $data
        ]);
    } else {
        ob_get_clean();
        http_response_code($code);
        echo <<<HTML
        <style>
            .centered {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
            }
        </style>
        <div class="centered"><h1>{$data}</h1></div>
        HTML;
        exit();
    }
}

function get_client_ip(): string
{
    foreach (
        [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ] as $key
    ) {
        if ($ip = getenv($key)) {
            return $ip;
        }
    }
    return 'UNKNOWN';
}

function isLiveServer(): bool
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    return !(
        $ip === '127.0.0.1' || $ip === '::1' ||
        str_starts_with($ip, '192.168.') ||
        str_starts_with($ip, '10.') ||
        str_starts_with($ip, '172.16.')
    );
}

function alert(string $title, string $text, string $type = 'info', int $timer = 3): void
{
    foreach (['title' => $title, 'text' => $text, 'timer' => $timer] as $key => $value) {
        killSession("alert_$key");
        createSession("alert_$key", $value);
    }

    $mappedType = match ($type) {
        'success' => 'success',
        'danger'  => 'danger',
        'warning' => 'warning',
        'question' => 'info',
        default   => 'info',
    };

    killSession('alert_type');
    createSession('alert_type', $mappedType);
}

function killAlerts(): void
{
    foreach (['title', 'text', 'type', 'timer'] as $key) {
        killSession("alert_$key");
    }
}

function checkAccountActivation(): bool
{
    return auth("users", "activated")['activated'] === 'yes';
}

function checkAccountActivationWithRedirect(): void
{
    if (!checkAccountActivation()) {
        redirect('activate');
    }
}

function generateCsrfToken(): string
{
    if (!existSession('csrf_token')) {
        createSession('csrf_token', rtrim(base64_encode(random_bytes(32)), '='));
    }
    return session('csrf_token');
}

function checkCsrfToken(?string $token): bool
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return true;
    }
    $sessionToken = session('csrf_token');
    return $token !== null && $token === $sessionToken;
}



function csrf(): void
{
    echo '<input type="hidden" readonly name="csrf_token" value="' . generateCsrfToken() . '">';
}

function formatTimestamp($timestamp, $format): ?string
{
    if ($timestamp) {
        $date = new DateTime($timestamp);
        return $date->format($format);
    }
    return null;
}

function thisMonthDays(): string
{
    return (new DateTime())->format('j');
}

function thisYearDays(): int
{
    return (int)(new DateTime())->format('z') + 1;
}

function countDates($date1, $date2): int
{
    $diff = date_diff(date_create($date1), date_create($date2));
    return (int)$diff->format('%d') + 1;
}

function csrfProtection(): void
{
    $csrf = generateCsrfToken();
?>
    <script>
        setTimeout(() => {
            document.querySelectorAll('input[name="csrf_token"]').forEach(input => {
                input.value = '<?= $csrf; ?>';
            });
        }, 100);
    </script>
    <?php
}



function flashMessagesAndValidations(): void
{
    global $trans;
    $alert = [
        'title' => session('alert_title'),
        'text'  => session('alert_text'),
        'type'  => session('alert_type'),
        'timer' => session('alert_timer')
    ];
    if ($alert['title'] && $alert['text'] && $alert['type'] && $alert['timer']) {
    ?>
        <div class="toast fadeThis text-dark show border-0 shadow-none bottom-center-fixed bg-<?= $alert['type']; ?>-theme" style="z-index: 1055"
            role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body w-100 fs-5">
                    <span class="text-wrap"><?= $alert['text']; ?></span>
                </div>
                <div class="m<?= $trans['S_OR_E']; ?>-1 m-auto text-danger">
                    <button type="button" class="btn-close rounded-pill bg-white" aria-label="Close Toast Message"></button>
                </div>
            </div>
        </div>
    <?php
    }
    killAlerts();
    ?>
    <script>
        document.querySelectorAll(".needs-validation").forEach((form) => {
            form.addEventListener(
                "submit",
                (e) => {
                    if (!form.checkValidity()) {
                        e.preventDefault();
                        e.stopPropagation();
                    }
                    form.classList.add("was-validated");
                },
                false
            );
        });
    </script>
<?php
}

function csrfToken(): ?string
{
    return session('csrf_token');
}

function createSeo($url, $title, $description, $image): void
{
    echo <<<HTML
    <script>
        document.title = "{$title}";

        document.getElementById('meta_url')?.setAttribute('content', "{$url}");
        document.getElementById('meta_title')?.setAttribute('content', "{$title}");
        document.getElementById('meta_description')?.setAttribute('content', "{$description}");
        document.getElementById('meta_image')?.setAttribute('content', "{$image}");
    </script>
    HTML;
}

function createHeadTag($tag): void
{
    echo <<<HTML
    <script>
        document.head.insertAdjacentHTML("beforeend", '{$tag}');
    </script>
    HTML;
}

function linkify($value, $protocols = ['http', 'mail'], array $attributes = []): string
{
    $attr = '';
    foreach ($attributes as $key => $val) {
        $attr .= ' ' . $key . '="' . htmlentities($val) . '"';
    }

    $links = [];

    $value = preg_replace_callback('~(<a .*?>.*?</a>|<.*?>)~i', function ($match) use (&$links) {
        return '<' . array_push($links, $match[1]) . '>';
    }, $value);

    foreach ($protocols as $protocol) {
        switch ($protocol) {
            case 'http':
            case 'https':
                $value = preg_replace_callback('~(?:(https?)://([^\s<]+)|(www\.[^\s<]+?\.[^\s<]+))(?<![\.,:])~i', function ($m) use (&$links, $attr) {
                    $proto = $m[1] ?: 'http';
                    $link = $m[2] ?? $m[3];
                    return '<' . array_push($links, "<a target='_blank'$attr href=\"$proto://$link\">$link</a>") . '>';
                }, $value);
                break;
            case 'mail':
                $value = preg_replace_callback('~([^\s<]+?@[^\s<]+?\.[^\s<]+)(?<![\.,:])~', function ($m) use (&$links, $attr) {
                    return '<' . array_push($links, "<a target='_blank'$attr href=\"mailto:{$m[1]}\">{$m[1]}</a>") . '>';
                }, $value);
                break;
            case 'twitter':
                $value = preg_replace_callback('~(?<!\w)[@#](\w++)~', function ($m) use (&$links, $attr) {
                    $url = $m[0][0] === '@' ? "https://twitter.com/{$m[1]}" : "https://twitter.com/search/%23{$m[1]}";
                    return '<' . array_push($links, "<a target='_blank'$attr href=\"$url\">{$m[0]}</a>") . '>';
                }, $value);
                break;
            default:
                $value = preg_replace_callback('~' . preg_quote($protocol, '~') . '://([^\s<]+?)(?<![\.,:])~i', function ($m) use ($protocol, &$links, $attr) {
                    return '<' . array_push($links, "<a target='_blank'$attr href=\"$protocol://{$m[1]}\">{$m[1]}</a>") . '>';
                }, $value);
        }
    }

    return preg_replace_callback('/<(\d+)>/', fn($m) => $links[$m[1] - 1], $value);
}







function formatHours($hours, $format = 'h:m long')
{
    global $trans;
    $wholeHours = floor($hours);
    $minutes = round(($hours - $wholeHours) * 60); // Round minutes to nearest integer

    switch ($format) {
        case 'h:m':
            return sprintf('%02d:%02d', $wholeHours, $minutes);
        case 'h hours':
            return sprintf('%d %s', $wholeHours, $trans['HOUR']);
        case 'h:m long':
            return sprintf('%d %s, %02d %s', $wholeHours, $trans['HOUR'], $minutes, $trans['MINUTE']);
        default:
            return sprintf('%d %s', $wholeHours, $trans['HOUR']);
    }
}

function IQD($amount)
{
    global $trans;
    return number_format($amount + 0, 0, '.', ',') . ' ' . $trans['IQD'];
}

function USD($amount)
{
    static $formatter = null;
    if ($formatter === null) {
        $formatter = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
        $formatter->setAttribute(\NumberFormatter::FRACTION_DIGITS, 0);
    }
    return $formatter->formatCurrency($amount + 0, 'USD');
}

function toUSD($amountIqd, $exchangeRate = 1)
{
    if ($exchangeRate <= 0) return 'Invalid exchange rate';
    return USD($amountIqd / $exchangeRate);
}

function toIQD($amountUsd, $exchangeRate = 1)
{
    if ($exchangeRate <= 0) return 'Invalid exchange rate';
    return IQD($amountUsd * $exchangeRate);
}

function sendNotificationToTopic($topic, $title, $message)
{
    global $config;
    $serverKey = $config['firebase']['server_key'];
    $headers = [
        'Authorization: key=' . $serverKey,
        'Content-Type: application/json',
    ];
    $payload = [
        'to' => '/topics/' . $topic,
        'notification' => [
            'title' => $title,
            'body' => $message,
            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
        ],
    ];

    $curl = curl_init('https://fcm.googleapis.com/fcm/send');
    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_HTTPHEADER => $headers,
    ]);

    $response = curl_exec($curl);
    curl_close($curl);

    return $response;
}

function timestamp($timestamp, $format = 'm / j, Y  - \a\t g:i A')
{
    try {
        $date = new DateTime($timestamp);
        return '<span dir="ltr">' . $date->format($format) . '</span>';
    } catch (Exception $e) {
        return '';
    }
}

function numberToWords($number, $lang = 'ku', $currency = 'usd')
{
    $data = [
        'ku' => [
            'zero' => 'سفر',
            'minus' => 'سالب',
            'ones' => [0 => 'سفر', 1 => 'یەک', 2 => 'دوو', 3 => 'سێ', 4 => 'چوار', 5 => 'پێنج', 6 => 'شەش', 7 => 'حەوت', 8 => 'هەشت', 9 => 'نۆ'],
            'tens' => [10 => 'دە', 20 => 'بیست', 30 => 'سی', 40 => 'چل', 50 => 'پەنجا', 60 => 'شەست', 70 => 'حەفتا', 80 => 'هەشتا', 90 => 'نەوەد'],
            'hundred' => 'سەد',
            'thousand' => 'هەزار',
            'million' => 'ملیۆن',
            'and' => 'و',
            'currency' => [
                'usd' => ['main' => 'دۆلار', 'fraction' => 'سێنت'],
                'iqd' => ['main' => 'دینار', 'fraction' => 'فلس'],
            ],
        ],
        'ar' => [
            'zero' => 'صفر',
            'minus' => 'ناقص',
            'ones' => [0 => 'صفر', 1 => 'واحد', 2 => 'اثنان', 3 => 'ثلاثة', 4 => 'أربعة', 5 => 'خمسة', 6 => 'ستة', 7 => 'سبعة', 8 => 'ثمانية', 9 => 'تسعة'],
            'tens' => [10 => 'عشرة', 20 => 'عشرون', 30 => 'ثلاثون', 40 => 'أربعون', 50 => 'خمسون', 60 => 'ستون', 70 => 'سبعون', 80 => 'ثمانون', 90 => 'تسعون'],
            'hundred' => 'مئة',
            'thousand' => 'ألف',
            'million' => 'مليون',
            'and' => 'و',
            'currency' => [
                'usd' => ['main' => 'دولار', 'fraction' => 'سنت'],
                'iqd' => ['main' => 'دينار', 'fraction' => 'فلس'],
            ],
        ],
        'en' => [
            'zero' => 'zero',
            'minus' => 'minus',
            'ones' => [0 => 'zero', 1 => 'one', 2 => 'two', 3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six', 7 => 'seven', 8 => 'eight', 9 => 'nine'],
            'tens' => [10 => 'ten', 20 => 'twenty', 30 => 'thirty', 40 => 'forty', 50 => 'fifty', 60 => 'sixty', 70 => 'seventy', 80 => 'eighty', 90 => 'ninety'],
            'hundred' => 'hundred',
            'thousand' => 'thousand',
            'million' => 'million',
            'and' => 'and',
            'currency' => [
                'usd' => ['main' => 'dollar', 'fraction' => 'cent'],
                'iqd' => ['main' => 'dinar', 'fraction' => 'fils'],
            ],
        ],
    ];

    if (!isset($data[$lang], $data[$lang]['currency'][$currency])) {
        return 'Unsupported language or currency';
    }

    $langData = $data[$lang];
    $currencyLabels = $langData['currency'][$currency];

    $whole = intval($number);
    $fraction = (int) round(($number - $whole) * 100);

    $text = convertNumber($whole, $langData);
    $result = $text . ' ' . $currencyLabels['main'];

    if ($fraction > 0) {
        $fractionText = convertNumber($fraction, $langData);
        $result .= ' ' . $langData['and'] . ' ' . $fractionText . ' ' . $currencyLabels['fraction'];
    }

    return $result;
}

function convertNumber($number, $langData)
{
    // Handle negative numbers
    if ($number < 0) {
        return ($langData['minus'] ?? 'minus') . ' ' . convertNumber(abs($number), $langData);
    }

    if ($number === 0) {
        return $langData['zero'] ?? 'zero';
    }

    if ($number < 10) {
        return $langData['ones'][$number];
    } elseif ($number < 100) {
        $ten = intval($number / 10) * 10;
        $one = $number % 10;
        if ($one === 0) {
            return $langData['tens'][$ten];
        }
        return $langData['tens'][$ten] . ' ' . $langData['and'] . ' ' . $langData['ones'][$one];
    } elseif ($number < 1000) {
        $hundred = intval($number / 100);
        $rest = $number % 100;
        $hundredText = $langData['ones'][$hundred] . ' ' . $langData['hundred'];
        return $rest === 0
            ? $hundredText
            : $hundredText . ' ' . $langData['and'] . ' ' . convertNumber($rest, $langData);
    } elseif ($number < 1000000) {
        $thousand = intval($number / 1000);
        $rest = $number % 1000;
        $thousandText = ($thousand === 1 && ($langData['lang'] ?? '') !== 'en')
            ? $langData['thousand']
            : convertNumber($thousand, $langData) . ' ' . $langData['thousand'];
        return $rest === 0
            ? $thousandText
            : $thousandText . ' ' . $langData['and'] . ' ' . convertNumber($rest, $langData);
    } elseif ($number < 1000000000) {
        $million = intval($number / 1000000);
        $rest = $number % 1000000;
        $millionText = ($million === 1 && ($langData['lang'] ?? '') !== 'en')
            ? $langData['million']
            : convertNumber($million, $langData) . ' ' . $langData['million'];
        return $rest === 0
            ? $millionText
            : $millionText . ' ' . $langData['and'] . ' ' . convertNumber($rest, $langData);
    } else {
        return 'too large';
    }
}




function imageResizer(string $source, string $destination, $size, ?int $quality = null): void
{
    // Validate source file existence and extension
    if (!file_exists($source)) {
        throw new Exception("Source image file not found: $source");
    }

    $ext = strtolower(pathinfo($source, PATHINFO_EXTENSION));
    $allowed = ["bmp", "gif", "jpg", "jpeg", "png", "webp"];
    if (!in_array($ext, $allowed, true)) {
        throw new Exception("Invalid image file type: $ext");
    }

    // Get original image dimensions
    $dimensions = getimagesize($source);
    if (!$dimensions) {
        throw new Exception("Unable to get image size for: $source");
    }
    [$width, $height] = $dimensions;

    // Calculate new dimensions
    if (is_array($size) && count($size) === 2) {
        [$new_width, $new_height] = $size;
    } elseif (is_numeric($size)) {
        $new_width = (int)ceil(($size / 100) * $width);
        $new_height = (int)ceil(($size / 100) * $height);
    } else {
        throw new Exception("Invalid size parameter");
    }

    // Prepare image create and output functions
    $createFunc = "imagecreatefrom" . ($ext === "jpg" ? "jpeg" : $ext);
    $outputFunc = "image" . ($ext === "jpg" ? "jpeg" : $ext);

    if (!function_exists($createFunc) || !function_exists($outputFunc)) {
        throw new Exception("Unsupported image functions for type: $ext");
    }

    $original = @$createFunc($source);
    if (!$original) {
        throw new Exception("Failed to create image resource from source");
    }

    $resized = imagecreatetruecolor($new_width, $new_height);

    // Handle transparency for PNG, GIF, WEBP
    if (in_array($ext, ['png', 'gif', 'webp'], true)) {
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        $transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
        imagefilledrectangle($resized, 0, 0, $new_width, $new_height, $transparent);
    }

    // Resize image
    imagecopyresampled(
        $resized,
        $original,
        0,
        0,
        0,
        0,
        $new_width,
        $new_height,
        $width,
        $height
    );

    // Output image with quality if applicable
    if (is_int($quality)) {
        if (in_array($ext, ['jpg', 'jpeg', 'webp'], true)) {
            $quality = max(0, min(100, $quality));
        } elseif ($ext === 'png') {
            $quality = max(-1, min(9, $quality));
        }
        $success = $outputFunc($resized, $destination, $quality);
    } else {
        $success = $outputFunc($resized, $destination);
    }

    imagedestroy($original);
    imagedestroy($resized);

    if (!$success) {
        throw new Exception("Failed to save resized image to $destination");
    }
}

// function generatePWA(): void
// {
//     $logoSetting = setting('brandimage')['brandimage'] ?? null;
//     if (!$logoSetting) {
//         throw new Exception("Logo setting not found");
//     }

//     $lightmodelogo = "../" . $logoSetting;

//     // Validate extension before calling imageResizer
//     $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
//     $ext = strtolower(pathinfo($lightmodelogo, PATHINFO_EXTENSION));

//     if (!in_array($ext, $allowedExtensions)) {
//         throw new Exception("Invalid image file type for PWA generation: " . $ext);
//     }

//     $sizes = [
//         ["../server/pwa/maskable_icon.png", [512, 512]],
//         ["../server/pwa/apple-touch-icon.png", [128, 128]],
//         ["../server/pwa/icon-512x512.png", [512, 512]],
//         ["../server/pwa/icon-384x384.png", [384, 384]],
//         ["../server/pwa/icon-192x192.png", [192, 192]],
//         ["../server/pwa/icon-152x152.png", [152, 152]],
//         ["../server/pwa/icon-144x144.png", [144, 144]],
//         ["../server/pwa/icon-128x128.png", [128, 128]],
//         ["../server/pwa/icon-96x96.png", [96, 96]],
//         ["../server/pwa/icon-72x72.png", [72, 72]],
//         ["../server/pwa/splash-640x1136.png", [640, 1136]],
//         ["../server/pwa/splash-750x1334.png", [750, 1334]],
//         ["../server/pwa/splash-1242x2208.png", [1242, 2208]],
//         ["../server/pwa/splash-1125x2436.png", [1125, 2436]],
//         ["../server/pwa/splash-828x1792.png", [828, 1792]],
//         ["../server/pwa/splash-1242x2688.png", [1242, 2688]],
//         ["../server/pwa/splash-1536x2048.png", [1536, 2048]],
//         ["../server/pwa/splash-1668x2224.png", [1668, 2224]],
//         ["../server/pwa/splash-1668x2388.png", [1668, 2388]],
//         ["../server/pwa/splash-2048x2732.png", [2048, 2732]],
//     ];

//     foreach ($sizes as [$dest, $dim]) {
//         imageResizer($lightmodelogo, $dest, $dim);
//     }
// }







function css(string $file, string $cdn): void
{
    echo isLiveServer() ? $cdn : $file;
}

function js(string $file, string $cdn): void
{
    echo isLiveServer() ? $cdn : $file;
}

function dashboardActions(): array
{
    global $actPage;
    if (!in_array($actPage, ['login', 'forget', 'reset'], true)) {
        checkAuthWithRedirect('users', 'login');
    } else {
        checkAuthorizedWithRedirect('users', 'app');
    }
    if (in_array($actPage, ['header', 'footer', 'pwa'], true)) {
        redirect('app');
        exit;
    }
    return [
        'authorizing' => checkAuth('users', 'id,fullname,username,email,password,avatar,role_id,branch_id', true)
    ];
}

function dashboardHeaderPage(string $page, string $actroute): array
{
    static $cache = [];
    $key = $page . '|' . $actroute;
    if (isset($cache[$key])) {
        return $cache[$key];
    }
    if ($actroute === $page) {
        $cache[$key] = ['class' => 'component-main active', 'text' => 'text-white'];
    } else {
        $cache[$key] = ['class' => '', 'text' => 'text-theme'];
    }
    return $cache[$key];
}

function rolePermissions($role): string
{
    static $cache = [];
    if (!$role) {
        return '';
    }
    $roleSafe = (string)$role;
    if (isset($cache[$roleSafe])) {
        return $cache[$roleSafe];
    }
    $result = findQuery(" SELECT permissions FROM roles WHERE id = '" . addslashes($roleSafe) . "' LIMIT 1 ");
    $cache[$roleSafe] = $result['permissions'] ?? '';
    return $cache[$roleSafe];
}

function rolePermissionsArrayForSql($role): string
{
    $permissions = rolePermissions($role);
    $permissions = trim($permissions, " ',");

    if ($permissions === '') {
        return "''";
    }

    $permissionsArray = array_filter(array_map('trim', explode(',', $permissions)));
    $quotedPermissions = array_map(fn($p) => "'" . addslashes($p) . "'", $permissionsArray);

    return implode(',', $quotedPermissions);
}

function hasAccess(string $permissionCode, string $rolePermissions): bool
{
    if (!$rolePermissions) {
        return false;
    }
    $permissions = array_map('trim', explode(',', $rolePermissions));
    return in_array($permissionCode, $permissions, true);
}

function hasAccessClass(string $permissionCode, string $rolePermissions): string
{
    return hasAccess($permissionCode, $rolePermissions) ? '' : 'd-none';
}

function hasAccessWithRedirect(string $permissionCode, string $rolePermissions, string $redirect = '404'): void
{
    if (!hasAccess($permissionCode, $rolePermissions)) {
        redirect($redirect);
        exit;
    }
}


function removeLeadingComma(string $text): string
{
    $text = ltrim($text);
    return (isset($text[0]) && $text[0] === ',') ? substr($text, 1) : $text;
}


function vite(string $filename, $endpoint = 'file')
{
    static $manifest = null, $dot = null;
    if ($dot === null) {
        $dot = str_contains($_SERVER['REQUEST_URI'] ?? '', '/dashboard') ? '..' : '.';
    }
    $manifestPath = "$dot/dist/manifest.json";
    if ($manifest === null) {
        if (is_file($manifestPath)) {
            $json = @file_get_contents($manifestPath);
            $manifest = $json ? json_decode($json, true) ?? [] : [];
        } else {
            $manifest = [];
        }
    }
    $realfile = is_array($manifest[$filename][$endpoint]) ? $manifest[$filename][$endpoint][0] : $manifest[$filename][$endpoint];
    return isset($manifest[$filename][$endpoint])
        ? "$dot/dist/" . $realfile
        : $filename;
}
$vite = fn(string $filename, string $endpoint = 'file'): string => vite($filename, $endpoint);



function preventRoute(string $route): void
{
    $requestPath = $_SERVER['REQUEST_URI'] ?? '';
    $pos = strrpos($requestPath, '/');
    $lastSegment = ($pos === false) ? $requestPath : substr($requestPath, $pos + 1);
    if (strcasecmp($lastSegment, $route) === 0) {
        header('Location: app');
        exit;
    }
}

function checkEven(int $number): bool
{
    return ($number & 1) === 0;
}

function checkOdd(int $number): bool
{
    return ($number & 1) !== 0;
}

function allPermissions(): string
{
    $rows = getQuery(" SELECT code FROM permissions");
    if (empty($rows)) {
        return '';
    }
    return implode(',', array_column($rows, 'code'));
}

function columns(string $table, string $except = ""): string
{
    global $config;
    $dbName = addslashes($config['db']['name']);
    $tableName = addslashes($table);
    $whereNotIn = "";
    if ($except) {
        $exceptArray = array_map(function ($col) {
            return "'" . trim($col) . "'";
        }, explode(',', $except));

        $exceptList = implode(', ', $exceptArray);
        $whereNotIn = " AND COLUMN_NAME NOT IN ($exceptList)";
    }
    $query = "
        SELECT GROUP_CONCAT(COLUMN_NAME SEPARATOR ', ')
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = '$dbName'
          AND TABLE_NAME = '$tableName'
          $whereNotIn
    ";
    $result = getQuery($query);

    if (!$result) {
        return '';
    }
    return array_values($result[0])[0] ?? '';
}


function console($value)
{
    echo <<<JS
        <script>
        console.log('{$value}')
        </scirpt>    
    JS;
}

function plusTreasure($treasure_id, $amountIqd, $amountUsd)
{
    execute(" UPDATE treasures SET iqd_amount=iqd_amount+'{$amountIqd}',usd_amount=usd_amount+'{$amountUsd}' WHERE id='{$treasure_id}' ");
}
function minusTreasure($treasure_id, $amountIqd, $amountUsd)
{
    execute(" UPDATE treasures SET iqd_amount=iqd_amount-'{$amountIqd}',usd_amount=usd_amount-'{$amountUsd}' WHERE id='{$treasure_id}' ");
}



function buyAndSaleContractLastNumber()
{
    static $cached = null;
    if ($cached !== null) {
        return $cached;
    }
    $result = findQuery(" SELECT contractnumber FROM buyandsalecontracts ORDER BY contractnumber DESC LIMIT 1 ");
    $cached = isset($result['contractnumber']) ? ($result['contractnumber'] + 0) : 0;
    return $cached;
}
function rentContractLastNumber()
{
    static $cached = null;
    if ($cached !== null) {
        return $cached;
    }
    $result = findQuery(" SELECT contractnumber FROM rentcontracts ORDER BY contractnumber DESC LIMIT 1 ");
    $cached = isset($result['contractnumber']) ? ($result['contractnumber'] + 0) : 0;
    return $cached;
}

function buyAndSaleInvoiceLastNumber($typestatus): string
{
    static $cached = [];
    if (isset($cached[$typestatus])) {
        return $cached[$typestatus];
    }
    $result = findQuery(" SELECT invoicenumber FROM buyandsaleinvoices WHERE type_status='{$typestatus}' ORDER BY invoicenumber DESC LIMIT 1 ");
    $cached[$typestatus] = isset($result['invoicenumber']) ? ($result['invoicenumber'] + 0) : 0;
    return $cached[$typestatus];
}

function rentInvoiceLastNumber($typestatus): string
{
    static $cached = [];
    if (isset($cached[$typestatus])) {
        return $cached[$typestatus];
    }
    $result = findQuery(" SELECT invoicenumber FROM rentinvoices WHERE type_status='{$typestatus}' ORDER BY invoicenumber DESC LIMIT 1 ");
    $cached[$typestatus] = isset($result['invoicenumber']) ? ($result['invoicenumber'] + 0) : 0;
    return $cached[$typestatus];
}



function getSecurityInvoiceLastNumber($table)
{
    static $cache = [];
    if (isset($cache[$table])) {
        return $cache[$table];
    }

    $result = findQuery(" SELECT invoicenumber FROM {$table} ORDER BY invoicenumber DESC LIMIT 1");
    $cache[$table] = isset($result['invoicenumber']) ? ($result['invoicenumber'] + 0) : 0;
    return $cache[$table];
}

function buyAndSaleSecurityInvoiceLastNumber()
{
    return getSecurityInvoiceLastNumber('buyandsalesecurityinvoices');
}

function rentSecurityInvoiceLastNumber()
{
    return getSecurityInvoiceLastNumber('rentsecurityinvoices');
}



function getSupplierDebt(int $supplier_id): array
{
    $expense = findQuery("
        SELECT 
            COALESCE(SUM(amount_iqd), 0) AS total_iqd, 
            COALESCE(SUM(amount_usd), 0) AS total_usd
        FROM expenses 
        WHERE supplier_id = {$supplier_id} AND invoicetype = 'debt'
    ");
    $returnDebt = findQuery("
        SELECT 
            COALESCE(SUM(amount_iqd), 0) AS total_iqd, 
            COALESCE(SUM(amount_usd), 0) AS total_usd
        FROM returndebts 
        WHERE supplier_id = {$supplier_id}
    ");
    return [
        'iqd' => $expense['total_iqd'] - $returnDebt['total_iqd'],
        'usd' => $expense['total_usd'] - $returnDebt['total_usd'],
    ];
}


function getAllSupplierDebts(): array
{
    $debts = [];
    $expenseDebts = getQuery("
        SELECT supplier_id, 
               COALESCE(SUM(amount_iqd),0) AS total_iqd, 
               COALESCE(SUM(amount_usd),0) AS total_usd 
        FROM expenses 
        WHERE invoicetype = 'debt'
        GROUP BY supplier_id
    ");
    $returnDebts = getQuery("
        SELECT supplier_id, 
               COALESCE(SUM(amount_iqd),0) AS total_iqd, 
               COALESCE(SUM(amount_usd),0) AS total_usd 
        FROM returndebts
        GROUP BY supplier_id
    ");
    $returnDebtMap = [];
    foreach ($returnDebts as $rd) {
        $returnDebtMap[$rd['supplier_id']] = $rd;
    }
    foreach ($expenseDebts as $ed) {
        $sid = $ed['supplier_id'];
        $ret = $returnDebtMap[$sid] ?? ['total_iqd' => 0, 'total_usd' => 0];
        $debts[$sid] = [
            'iqd' => $ed['total_iqd'] - $ret['total_iqd'],
            'usd' => $ed['total_usd'] - $ret['total_usd'],
        ];
    }
    return $debts;
}










function generateBarcode($code, $height = 50, $widthFactor = 2)
{
    $code = strtoupper($code);
    $code = '*' . $code . '*'; // Add start/stop

    // Define bar patterns including '*'
    $bars = [
        '0' => '101001101101',
        '1' => '110100101011',
        '2' => '101100101011',
        '3' => '110110010101',
        '4' => '101001101011',
        '5' => '110100110101',
        '6' => '101100110101',
        '7' => '101001011011',
        '8' => '110100101101',
        '9' => '101100101101',
        'A' => '110101001011',
        'B' => '101101001011',
        'C' => '110110100101',
        'D' => '101011001011',
        'E' => '110101100101',
        'F' => '101101100101',
        'G' => '101010011011',
        'H' => '110101001101',
        'I' => '101101001101',
        'J' => '101011001101',
        'K' => '110101010011',
        'L' => '101101010011',
        'M' => '110110101001',
        'N' => '101011010011',
        'O' => '110101101001',
        'P' => '101101101001',
        'Q' => '101010110011',
        'R' => '110101011001',
        'S' => '101101011001',
        'T' => '101011011001',
        'U' => '110010101011',
        'V' => '100110101011',
        'W' => '110011010101',
        'X' => '100101101011',
        'Y' => '110010110101',
        'Z' => '100110110101',
        '-' => '100101011011',
        '.' => '110010101101',
        ' ' => '100110101101',
        '*' => '100101101101',
        '$' => '100100100101',
        '/' => '100100101001',
        '+' => '100101001001',
        '%' => '101001001001'
    ];

    // Build the full barcode pattern
    $pattern = '';
    for ($i = 0; $i < strlen($code); $i++) {
        $char = $code[$i];
        if (!isset($bars[$char])) {
            throw new Exception("Invalid character for Code39 barcode: $char");
        }
        $pattern .= $bars[$char] . '0'; // add narrow space between characters
    }

    // Create the image
    $img = imagecreate(strlen($pattern) * $widthFactor, $height);
    $white = imagecolorallocate($img, 255, 255, 255);
    $black = imagecolorallocate($img, 0, 0, 0);

    for ($i = 0; $i < strlen($pattern); $i++) {
        $color = ($pattern[$i] == '1') ? $black : $white;
        imagefilledrectangle(
            $img,
            $i * $widthFactor,
            0,
            ($i + 1) * $widthFactor - 1,
            $height,
            $color
        );
    }

    // Capture the image output into a variable
    ob_start();
    imagepng($img);
    $imageData = ob_get_clean();

    imagedestroy($img);

    // Return as base64 for direct HTML embedding
    return 'data:image/png;base64,' . base64_encode($imageData);
}



function invoiceLastNumber(string $table, int $branch_id): int
{
    $result = findQuery(" SELECT invoicenumber FROM {$table} WHERE branch_id='{$branch_id}' ORDER BY invoicenumber DESC LIMIT 1");
    return isset($result['invoicenumber']) ? (int)$result['invoicenumber'] : 0;
}

function checkView(string $table, ?int $id, array $caches): void
{
    $cookieName = "viewed_{$table}_" . ($id ?? 'all');
    if (!empty($_COOKIE[$cookieName])) {
        return;
    }
    $where = $id ? " WHERE id={$id}" : '';
    execute("UPDATE {$table} SET views = views + 1{$where}");
    setcookie($cookieName, '1', time() + 31536000, '/');
    $_COOKIE[$cookieName] = '1';

    foreach ($caches as $cache) {
        cache_invalidate_contains($cache);
    }
}



function renderPickers()
{
    global $trans;
?>
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css">
    </noscript>
    <script>
        const PickerManager = {
            selectInstances: new Map(),
            isInitialized: false,
            debounce(func, wait) {
                let timeout;
                return function(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args)
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait)
                }
            },
            getOptimizedConfig(element) {
                const optionCount = element.options.length,
                    isLarge = optionCount > 100;
                return {
                    create: false,
                    sortField: {
                        field: "text",
                        direction: "asc"
                    },
                    dropdownParent: "body",
                    searchField: ["text"],
                    placeholder: "<?= $trans['CHOOSE']; ?>",
                    maxOptions: isLarge ? 50 : null,
                    loadThrottle: 100,
                    searchOn: ["text"],
                    load: isLarge ? this.debounce(function(query, callback) {
                        if (query.length < 2) {
                            callback();
                            return
                        }
                        const filtered = Array.from(element.options).filter(opt => opt.textContent.toLowerCase().includes(query.toLowerCase())).slice(0, 50).map(opt => ({
                            value: opt.value,
                            text: opt.textContent
                        }));
                        callback(filtered)
                    }, 150) : null,
                    render: {
                        option: (data, escape) => `<div class="option" data-value="${escape(data.value)}">${escape(data.text)}</div>`,
                        item: (data, escape) => `<div class="item" data-value="${escape(data.value)}">${escape(data.text)}</div>`
                    },
                    onInitialize() {
                        this.control.style.willChange = "transform"
                    },
                    onDropdownOpen() {
                        this.dropdown.style.transform = "translateZ(0)";
                        requestAnimationFrame(() => {
                            const firstOption = this.dropdown.querySelector(".option");
                            if (firstOption) firstOption.classList.add("active")
                        })
                    },
                    onDropdownClose() {
                        this.control.style.willChange = "auto"
                    }
                }
            },
            initializeSelects() {
                if (this.isInitialized) return;
                const selects = document.querySelectorAll("select:not(.notpicker)");
                const initBatch = (startIndex = 0) => {
                    const batchSize = 5,
                        endIndex = Math.min(startIndex + batchSize, selects.length);
                    for (let i = startIndex; i < endIndex; i++) {
                        const select = selects[i];
                        if (!this.selectInstances.has(select)) {
                            try {
                                const config = this.getOptimizedConfig(select);
                                const instance = new TomSelect(select, config);
                                this.selectInstances.set(select, instance)
                            } catch (error) {
                                console.warn("Failed to initialize select:", select, error)
                            }
                        }
                    }
                    if (endIndex < selects.length) {
                        requestAnimationFrame(() => initBatch(endIndex))
                    } else {
                        this.isInitialized = true
                    }
                };
                requestAnimationFrame(() => initBatch())
            },
            initializeDatePickers() {
                const dateInputs = document.querySelectorAll("input[type='date']:not(.notpicker)");
                dateInputs.forEach((input, index) => {
                    setTimeout(() => {
                        flatpickr(input, {
                            dateFormat: "Y-m-d",
                            allowInput: false,
                            animate: false,
                            clickOpens: true,
                            allowInvalidPreload: false,
                            onReady: function(selectedDates, dateStr, instance) {
                                instance.calendarContainer.style.transform = "translateZ(0)"
                            }
                        })
                    }, index * 10)
                })
            },
            cleanup() {
                this.selectInstances.forEach((instance, select) => {
                    try {
                        instance.destroy()
                    } catch (error) {
                        console.warn("Failed to destroy select instance:", error)
                    }
                });
                this.selectInstances.clear();
                this.isInitialized = false
            }
        };
        const loadScript = (src) => new Promise((resolve, reject) => {
            const script = document.createElement("script");
            script.src = src;
            script.async = true;
            script.onload = resolve;
            script.onerror = reject;
            document.head.appendChild(script)
        });
        document.addEventListener("DOMContentLoaded", async () => {
            try {
                await Promise.all([loadScript("https://cdn.jsdelivr.net/npm/flatpickr"), loadScript("https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js")]);
                if ("requestIdleCallback" in window) {
                    requestIdleCallback(() => {
                        PickerManager.initializeDatePickers();
                        PickerManager.initializeSelects()
                    })
                } else {
                    setTimeout(() => {
                        PickerManager.initializeDatePickers();
                        PickerManager.initializeSelects()
                    }, 0)
                }
            } catch (error) {
                console.error("Failed to load picker scripts:", error);
                const script1 = document.createElement("script");
                script1.src = "https://cdn.jsdelivr.net/npm/flatpickr";
                document.head.appendChild(script1);
                const script2 = document.createElement("script");
                script2.src = "https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js";
                document.head.appendChild(script2);
                script2.onload = () => {
                    PickerManager.initializeDatePickers();
                    PickerManager.initializeSelects()
                }
            }
        });
        window.PickerManager = PickerManager;
    </script>
    <style>
        .light-theme .ts-control,
        .light-theme .ts-control.has-items,
        .light-theme .ts-dropdown {
            background-color: var(--light-theme-component) !important;
            color: var(--color-theme) !important;
            transform: translateZ(0);
            will-change: transform
        }

        .dark-theme .ts-control,
        .light-theme .ts-control {
            padding: 4px !important;
            font-size: 1rem;
            cursor: pointer;
            text-align: center;
            width: 100%;
            transition: border-color .15s ease-out;
            contain: layout style
        }

        .light-theme .ts-control {
            border: none !important;
            border-radius: 6px;
            box-shadow: none !important
        }

        .light-theme .ts-dropdown {
            border: 1px solid var(--main-theme) !important;
            border-radius: 6px;
            box-shadow: 0 2px 6px rgba(37, 150, 190, .3);
            z-index: 1000;
            transform: translateZ(0);
            will-change: transform, opacity
        }

        .dark-theme .ts-dropdown .option,
        .light-theme .ts-dropdown .option {
            padding: .5rem 1rem;
            font-size: 1rem;
            cursor: pointer;
            color: var(--color-theme) !important;
            transform: translateZ(0)
        }

        .light-theme .ts-dropdown .active,
        .light-theme .ts-dropdown .active:focus,
        .light-theme .ts-dropdown .active:hover {
            background-color: var(--hover-theme) !important;
            color: var(--light-theme-component) !important;
            transition: background-color .1s ease-out
        }

        .light-theme .ts-control .ts-input {
            background-color: var(--light-theme-component) !important;
            color: var(--color-theme) !important;
            border: none !important;
            padding: 5px !important;
            border-radius: 4px !important;
            width: 100% !important
        }

        .dark-theme .ts-control,
        .dark-theme .ts-control.has-items,
        .dark-theme .ts-dropdown {
            background-color: var(--dark-theme-component) !important;
            color: var(--color-theme) !important;
            transform: translateZ(0);
            will-change: transform
        }

        .dark-theme .ts-control {
            border: none !important;
            border-radius: 6px;
            box-shadow: none !important
        }

        .dark-theme .ts-dropdown {
            border: 1px solid var(--main-theme) !important;
            border-radius: 6px;
            box-shadow: 0 2px 6px rgba(37, 150, 190, .3);
            z-index: 1000;
            transform: translateZ(0);
            will-change: transform, opacity
        }

        .dark-theme .ts-dropdown .active,
        .dark-theme .ts-dropdown .active:focus,
        .dark-theme .ts-dropdown .active:hover {
            background-color: var(--hover-theme) !important;
            color: var(--dark-theme-component) !important;
            transition: background-color .1s ease-out
        }

        .dark-theme .ts-control .ts-input {
            background-color: var(--dark-theme-component) !important;
            color: var(--color-theme) !important;
            border: none !important;
            box-shadow: none !important;
            padding: 5px !important;
            border-radius: 4px !important;
            width: 100% !important;
            transition: background-color .15s ease-out
        }

        .ts-placeholder {
            color: var(--main-theme) !important;
            opacity: .7
        }

        .ts-dropdown .list {
            max-height: 200px;
            overflow-y: auto;
            scroll-behavior: smooth;
            contain: layout style;
            transform: translateZ(0)
        }

        .light-theme .ts-input input,
        .light-theme input {
            color: #101828
        }

        .dark-theme .ts-input input,
        .dark-theme input {
            color: #f5f5f5
        }

        .ts-dropdown {
            backface-visibility: hidden;
            perspective: 1000px
        }

        .ts-dropdown .option {
            backface-visibility: hidden
        }

        .ts-dropdown .list::-webkit-scrollbar {
            width: 6px
        }

        .ts-dropdown .list::-webkit-scrollbar-thumb {
            background-color: var(--main-theme);
            border-radius: 3px
        }

        .ts-loading {
            opacity: .7;
            pointer-events: none
        }
    </style>
<?php
}



function updateQuestProgress($accountId, $actionType, $amount)
{
    $quest = findQuery(" SELECT id, target_count FROM quests WHERE action_type='$actionType' AND is_active=1 LIMIT 1 ");
    if ($quest) {
        $qid = $quest['id'];
        execute(" INSERT INTO account_quests (account_id, quest_id, progress) VALUES ($accountId, $qid, $amount) 
                  ON DUPLICATE KEY UPDATE progress = progress + $amount ");
    }
}

function logActivity($type, $userId, $action, $details = '')
{
    $ip = $_SERVER['REMOTE_ADDR'];
    $details = $details;
    execute(" INSERT INTO activity_log (user_type, user_id, action, details, ip_address, created_at) VALUES ('$type', $userId, '$action', '$details', '$ip', NOW()) ");
}


function sellerStats($sellerId)
{
    $sellerId = (int)$sellerId;

    $row = findQuery("
        SELECT
            COALESCE(SUM(oi.quantity),0) total_units,
            COALESCE(SUM(oi.quantity*oi.price_at_purchase),0) total_sale,
            COALESCE(SUM(oi.quantity*oi.price_at_purchase*(1-(s.commission_rate/100))),0) total_revenue
        FROM order_items oi
        JOIN orders o ON o.id=oi.order_id
        JOIN products p ON p.id=oi.product_id
        JOIN sellers s ON s.account_id=p.seller_id
        WHERE p.seller_id=$sellerId
        AND o.status IN('completed','processing')
    ");

    $fmt = function ($n) {
        return rtrim(rtrim(number_format((float)$n, 3, '.', ''), '0'), '.');
    };

    return [
        'total_units' => (int)($row['total_units'] ?? 0),
        'total_sale' => $fmt($row['total_sale'] ?? 0),
        'total_revenue' => $fmt($row['total_revenue'] ?? 0)
    ];
}

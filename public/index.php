<?php
session_start();

// Autoload if composer installed
$vendorAutoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($vendorAutoload)) {
    require_once $vendorAutoload;
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/ClassController.php';
require_once __DIR__ . '/../controllers/NoteController.php';
require_once __DIR__ . '/../controllers/EventController.php';
require_once __DIR__ . '/../controllers/MessageController.php';
require_once __DIR__ . '/../controllers/HomeworkController.php';
require_once __DIR__ . '/../controllers/AdminUserController.php';

// URL helpers
function base_url(): string {
    $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    return $base === '/' ? '' : $base;
}

function url(string $path): string {
    return base_url() . $path;
}

function redirect(string $path): void {
    header('Location: ' . url($path));
    exit;
}

// Helper to render a view
function render($view, $params = []) {
    $base = base_url();
    extract($params);
    include __DIR__ . "/views/{$view}.php";
}

// Compute path from REQUEST_URI
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
$reqUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = '/' . ltrim(substr($reqUri, strlen($scriptName)), '/');

// Remove trailing slash (except root)
if ($path !== '/' && substr($path, -1) === '/') {
    $path = rtrim($path, '/');
}

// Routing
switch ($path) {
    case '/':
    case '/login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            (new AuthController())->login();
        } else {
            (new AuthController())->showLogin();
        }
        break;
    case '/register':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            (new AuthController())->register();
        } else {
            (new AuthController())->showRegister();
        }
        break;
    case '/logout':
        (new AuthController())->logout();
        break;
    case '/dashboard':
        (new AuthController())->dashboard();
        break;

    // Classes
    case '/classes':
        (new ClassController())->index();
        break;
    case '/classes/create':
        (new ClassController())->create();
        break;
    default:
        // Dynamic routes: /classes/edit/{id}, /classes/delete/{id}, /notes..., /notes/edit/{id}
        if (preg_match('#^/classes/edit/(\d+)$#', $path, $m)) {
            (new ClassController())->edit((int)$m[1]);
            break;
        }
        if (preg_match('#^/classes/delete/(\d+)$#', $path, $m)) {
            (new ClassController())->delete((int)$m[1]);
            break;
        }
        if ($path === '/notes') {
            (new NoteController())->index();
            break;
        }
        if ($path === '/notes/create') {
            (new NoteController())->create();
            break;
        }
        if (preg_match('#^/notes/edit/(\d+)$#', $path, $m)) {
            (new NoteController())->edit((int)$m[1]);
            break;
        }
        if (preg_match('#^/notes/delete/(\d+)$#', $path, $m)) {
            (new NoteController())->delete((int)$m[1]);
            break;
        }
        // Homeworks
        if ($path === '/homeworks') {
            (new HomeworkController())->index();
            break;
        }
        if ($path === '/homeworks/create') {
            (new HomeworkController())->create();
            break;
        }
        if (preg_match('#^/homeworks/edit/(\d+)$#', $path, $m)) {
            (new HomeworkController())->edit((int)$m[1]);
            break;
        }
        if (preg_match('#^/homeworks/delete/(\d+)$#', $path, $m)) {
            (new HomeworkController())->delete((int)$m[1]);
            break;
        }
        // Events
        if ($path === '/events') {
            (new EventController())->index();
            break;
        }
        if ($path === '/events/create') {
            (new EventController())->create();
            break;
        }
        if (preg_match('#^/events/edit/(\d+)$#', $path, $m)) {
            (new EventController())->edit((int)$m[1]);
            break;
        }
        if (preg_match('#^/events/delete/(\d+)$#', $path, $m)) {
            (new EventController())->delete((int)$m[1]);
            break;
        }
        // Messaging
        if ($path === '/messages') {
            (new MessageController())->index();
            break;
        }
        if ($path === '/messages/create') {
            (new MessageController())->create();
            break;
        }
        if (preg_match('#^/messages/thread/(\d+)$#', $path, $m)) {
            (new MessageController())->thread((int)$m[1]);
            break;
        }
        // Admin users
        if ($path === '/admin/users') {
            (new AdminUserController())->index();
            break;
        }
        if ($path === '/admin/users/create') {
            (new AdminUserController())->create();
            break;
        }
        if (preg_match('#^/admin/users/edit/(\d+)$#', $path, $m)) {
            (new AdminUserController())->edit((int)$m[1]);
            break;
        }
        if (preg_match('#^/admin/users/delete/(\d+)$#', $path, $m)) {
            (new AdminUserController())->delete((int)$m[1]);
            break;
        }
        http_response_code(404);
        echo '<h1>Page non trouvée</h1><p>Route: ' . htmlspecialchars($path) . '</p>';
}

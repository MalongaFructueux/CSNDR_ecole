<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/ClassModel.php';

class AuthController {
    private string $jwtSecret;

    public function __construct() {
        $configPath = __DIR__ . '/../config/app.php';
        $cfg = file_exists($configPath) ? (require $configPath) : [];
        $this->jwtSecret = $cfg['jwt_secret'] ?? (getenv('JWT_SECRET') ?: 'CHANGE_ME_JWT_SECRET');
    }

    public function showLogin() {
        render('login');
    }

    public function login() {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $errors = [];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email invalide';
        if (strlen($password) < 6) $errors[] = 'Mot de passe trop court';
        if ($errors) return render('login', ['errors' => $errors, 'old' => compact('email')]);

        $user = User::findByEmail($email);
        if (!$user || !password_verify($password, $user['password'])) {
            return render('login', ['errors' => ['Identifiants invalides'], 'old' => compact('email')]);
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        if (class_exists(JWT::class)) {
            $payload = [
                'sub' => $user['id'],
                'role' => $user['role'],
                'iat' => time(),
                'exp' => time() + 3600*8,
            ];
            $token = JWT::encode($payload, $this->jwtSecret, 'HS256');
            $_SESSION['token'] = $token;
        }
        redirect('/dashboard');
    }

    public function showRegister() {
        $classes = ClassModel::getAll();
        $parents = User::getAll('parent');
        render('register', ['classes' => $classes, 'parents' => $parents]);
    }

    public function register() {
        $data = [
            'nom' => trim($_POST['nom'] ?? ''),
            'prenom' => trim($_POST['prenom'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'role' => $_POST['role'] ?? 'eleve',
            'classe_id' => $_POST['classe_id'] !== '' ? (int)$_POST['classe_id'] : null,
            'parent_id' => $_POST['parent_id'] !== '' ? (int)$_POST['parent_id'] : null,
        ];
        $errors = User::validateRegistration($data);
        if ($errors) {
            $classes = ClassModel::getAll();
            $parents = User::getAll('parent');
            return render('register', ['errors' => $errors, 'old' => $data, 'classes' => $classes, 'parents' => $parents]);
        }
        if (User::emailExists($data['email'])) {
            $classes = ClassModel::getAll();
            $parents = User::getAll('parent');
            return render('register', ['errors' => ['Email déjà utilisé'], 'old' => $data, 'classes' => $classes, 'parents' => $parents]);
        }
        User::create($data);
        header('Location: /CSNDR/public/login');
        exit;
    }

    public function logout() {
        session_destroy();
        redirect('/login');
    }

    public function dashboard() {
        $this->requireAuth();
        $user = User::findById($_SESSION['user_id']);
        $role = $user['role'];
        require_once __DIR__ . '/../models/Note.php';
        require_once __DIR__ . '/../models/Homework.php';
        if ($role === 'eleve') {
            $notes = Note::getByStudent($user['id']);
            $classe_id = (int)($user['classe_id'] ?? 0);
            $homeworks = $classe_id ? Homework::getByClass($classe_id) : [];
            return render('dashboard', ['user' => $user, 'notes' => $notes, 'homeworks' => $homeworks]);
        }
        if ($role === 'parent') {
            $childrenNotes = Note::getByParent($user['id']);
            $homeworks = Homework::getByParent($user['id']);
            return render('dashboard', ['user' => $user, 'childrenNotes' => $childrenNotes, 'homeworks' => $homeworks]);
        }
        if ($role === 'professeur') {
            return render('dashboard', ['user' => $user]);
        }
        if ($role === 'admin') {
            return render('dashboard', ['user' => $user]);
        }
        render('dashboard', ['user' => $user]);
    }

    private function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            redirect('/login');
        }
    }
}

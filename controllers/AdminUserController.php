<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/ClassModel.php';

class AdminUserController {
    private function requireAdmin() {
        if (!isset($_SESSION['user_id'])) { header('Location: /CSNDR/public/login'); exit; }
        if (($_SESSION['role'] ?? '') !== 'admin') { http_response_code(403); echo 'Accès refusé'; exit; }
    }

    public function index() {
        $this->requireAdmin();
        $users = User::getAll();
        render('users', ['users' => $users]);
    }

    public function create() {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nom' => trim($_POST['nom'] ?? ''),
                'prenom' => trim($_POST['prenom'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'password' => $_POST['password'] ?? '',
                'role' => $_POST['role'] ?? 'student',
                'classe_id' => (int)($_POST['classe_id'] ?? 0),
                'parent_id' => (int)($_POST['parent_id'] ?? 0),
            ];
            $errors = User::validateRegistration($data);
            if (User::emailExists($data['email'])) $errors[] = 'Email déjà utilisé';
            if ($errors) return render('user_form', ['errors' => $errors, 'old' => $data, 'classes' => ClassModel::getAll(), 'users' => User::getAll()]);
            User::create($data);
            header('Location: /CSNDR/public/admin/users');
            exit;
        }
        render('user_form', ['classes' => ClassModel::getAll(), 'users' => User::getAll()]);
    }

    public function edit(int $id) {
        $this->requireAdmin();
        $u = User::findById($id);
        if (!$u) { http_response_code(404); echo 'Utilisateur introuvable'; return; }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nom' => trim($_POST['nom'] ?? ''),
                'prenom' => trim($_POST['prenom'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'password' => $_POST['password'] ?? '',
                'role' => $_POST['role'] ?? 'student',
                'classe_id' => (int)($_POST['classe_id'] ?? 0),
                'parent_id' => (int)($_POST['parent_id'] ?? 0),
            ];
            // Minimal validation for update
            $errs = [];
            if ($data['nom'] === '') $errs[] = 'Nom requis';
            if ($data['prenom'] === '') $errs[] = 'Prénom requis';
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errs[] = 'Email invalide';
            if (!in_array($data['role'], ['student','teacher','parent','admin'], true)) $errs[] = 'Rôle invalide';
            if ($errs) return render('user_form', ['errors' => $errs, 'old' => $data, 'user' => $u, 'classes' => ClassModel::getAll(), 'users' => User::getAll()]);
            User::update($id, $data);
            header('Location: /CSNDR/public/admin/users');
            exit;
        }
        render('user_form', ['user' => $u, 'classes' => ClassModel::getAll(), 'users' => User::getAll()]);
    }

    public function delete(int $id) {
        $this->requireAdmin();
        User::delete($id);
        header('Location: /CSNDR/public/admin/users');
        exit;
    }
}

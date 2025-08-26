<?php
require_once __DIR__ . '/../models/Homework.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/ClassModel.php';

class HomeworkController {
    private function requireAuth() {
        if (!isset($_SESSION['user_id'])) { redirect('/login'); }
    }
    private function requireEditor() {
        $this->requireAuth();
        if (!in_array($_SESSION['role'], ['professeur','admin'], true)) { http_response_code(403); echo 'Accès refusé'; exit; }
    }

    public function index() {
        $this->requireAuth();
        $role = $_SESSION['role'] ?? '';
        if ($role === 'eleve') {
            $user = User::findById((int)$_SESSION['user_id']);
            $classe_id = (int)($user['classe_id'] ?? 0);
            $homeworks = $classe_id ? Homework::getByClass($classe_id) : [];
        } else if ($role === 'parent') {
            // Show only homeworks for classes of the parent's children
            $homeworks = Homework::getByParent((int)$_SESSION['user_id']);
        } else {
            $homeworks = Homework::getAll();
        }
        render('homeworks', ['homeworks' => $homeworks, 'role' => $role]);
    }

    public function create() {
        $this->requireEditor();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'title' => trim($_POST['title'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'due_at' => $_POST['due_at'] ?? '',
                'classe_id' => (int)($_POST['classe_id'] ?? 0),
                'created_by' => (int)$_SESSION['user_id'],
            ];
            $errors = Homework::validate($data);
            if ($errors) return render('homework_form', ['errors' => $errors, 'old' => $data, 'classes' => ClassModel::getAll()]);
            Homework::create($data);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Devoir créé avec succès.'];
            redirect('/homeworks');
        }
        render('homework_form', ['classes' => ClassModel::getAll()]);
    }

    public function edit(int $id) {
        $this->requireEditor();
        $hw = Homework::findById($id);
        if (!$hw) { http_response_code(404); echo 'Devoir introuvable'; return; }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'title' => trim($_POST['title'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'due_at' => $_POST['due_at'] ?? '',
                'classe_id' => (int)($_POST['classe_id'] ?? 0),
            ];
            $errors = Homework::validate($data);
            if ($errors) return render('homework_form', ['errors' => $errors, 'old' => $data, 'homework' => $hw, 'classes' => ClassModel::getAll()]);
            Homework::update($id, $data);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Devoir mis à jour avec succès.'];
            redirect('/homeworks');
        }
        render('homework_form', ['homework' => $hw, 'classes' => ClassModel::getAll()]);
    }

    public function delete(int $id) {
        $this->requireEditor();
        Homework::delete($id);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Devoir supprimé avec succès.'];
        redirect('/homeworks');
    }
}

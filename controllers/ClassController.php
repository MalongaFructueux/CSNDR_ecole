<?php
require_once __DIR__ . '/../models/ClassModel.php';
require_once __DIR__ . '/../models/User.php';

class ClassController {
    private function requireAdmin() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            http_response_code(403);
            echo 'Accès refusé';
            exit;
        }
    }

    public function index() {
        if (!isset($_SESSION['user_id'])) { header('Location: /CSNDR/public/login'); exit; }
        $classes = ClassModel::getAll();
        render('classes', ['classes' => $classes]);
    }

    public function create() {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = trim($_POST['nom'] ?? '');
            $errors = [];
            if ($nom === '') $errors[] = 'Le nom est requis';
            if ($errors) return render('class_form', ['errors' => $errors, 'old' => ['nom' => $nom]]);
            ClassModel::create($nom);
            header('Location: /CSNDR/public/classes');
            exit;
        }
        render('class_form');
    }

    public function edit(int $id) {
        $this->requireAdmin();
        $class = ClassModel::findById($id);
        if (!$class) { http_response_code(404); echo 'Classe introuvable'; return; }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = trim($_POST['nom'] ?? '');
            if ($nom === '') return render('class_form', ['errors' => ['Le nom est requis'], 'old' => ['nom' => $nom], 'class' => $class]);
            ClassModel::update($id, $nom);
            header('Location: /CSNDR/public/classes');
            exit;
        }
        render('class_form', ['class' => $class]);
    }

    public function delete(int $id) {
        $this->requireAdmin();
        ClassModel::delete($id);
        header('Location: /CSNDR/public/classes');
        exit;
    }
}

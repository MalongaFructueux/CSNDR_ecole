<?php
require_once __DIR__ . '/../models/Note.php';
require_once __DIR__ . '/../models/User.php';

class NoteController {
    private function requireRole($roles) {
        if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], (array)$roles, true)) {
            http_response_code(403);
            echo 'Accès refusé';
            exit;
        }
    }

    public function index() {
        if (!isset($_SESSION['user_id'])) { redirect('/login'); }
        $role = $_SESSION['role'];
        if ($role === 'eleve') {
            $notes = Note::getByStudent($_SESSION['user_id']);
            return render('notes', ['notes' => $notes, 'role' => $role]);
        }
        if ($role === 'parent') {
            $notes = Note::getByParent($_SESSION['user_id']);
            return render('notes', ['notes' => $notes, 'role' => $role]);
        }
        if ($role === 'professeur') {
            $notes = Note::getByTeacher($_SESSION['user_id']);
            return render('notes', ['notes' => $notes, 'role' => $role]);
        }
        return render('notes', ['notes' => [], 'role' => $role]);
    }

    public function create() {
        $this->requireRole(['professeur','admin']);
        $students = User::getAll('eleve');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'eleve_id' => (int)($_POST['eleve_id'] ?? 0),
                'matiere' => trim($_POST['matiere'] ?? ''),
                'note' => (float)($_POST['note'] ?? 0),
                'type' => $_POST['type'] ?? 'Devoir',
                'professeur_id' => (int)$_SESSION['user_id'],
            ];
            $errors = Note::validate($data);
            if ($errors) return render('note_form', ['errors' => $errors, 'old' => $data, 'students' => $students]);
            Note::create($data);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Note créée avec succès.'];
            redirect('/notes');
        }
        render('note_form', ['students' => $students]);
    }

    public function edit(int $id) {
        $this->requireRole(['professeur','admin']);
        $note = Note::findById($id);
        if (!$note) { http_response_code(404); echo 'Note introuvable'; return; }
        $students = User::getAll('eleve');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'eleve_id' => (int)($_POST['eleve_id'] ?? 0),
                'matiere' => trim($_POST['matiere'] ?? ''),
                'note' => (float)($_POST['note'] ?? 0),
                'type' => $_POST['type'] ?? 'Devoir',
            ];
            $errors = Note::validate($data);
            if ($errors) return render('note_form', ['errors' => $errors, 'old' => $data, 'students' => $students, 'note' => $note]);
            Note::update($id, $data);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Note mise à jour avec succès.'];
            redirect('/notes');
        }
        render('note_form', ['note' => $note, 'students' => $students]);
    }

    public function delete(int $id) {
        $this->requireRole(['professeur','admin']);
        Note::delete($id);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Note supprimée avec succès.'];
        redirect('/notes');
    }
}

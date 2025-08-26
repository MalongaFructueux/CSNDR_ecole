<?php
require_once __DIR__ . '/../models/Event.php';
require_once __DIR__ . '/../models/User.php';

class EventController {
    private function requireAuth() {
        if (!isset($_SESSION['user_id'])) { redirect('/login'); }
    }
    private function requireEditor() {
        $this->requireAuth();
        if (!in_array($_SESSION['role'], ['professeur','admin'], true)) {
            http_response_code(403); echo 'Accès refusé'; exit;
        }
    }

    public function index() {
        $this->requireAuth();
        $events = Event::getAll();
        render('events', ['events' => $events, 'role' => $_SESSION['role'] ?? '']);
    }

    public function create() {
        $this->requireEditor();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'title' => trim($_POST['title'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'start_at' => $_POST['start_at'] ?? '',
                'end_at' => $_POST['end_at'] ?? '',
                'created_by' => (int)$_SESSION['user_id'],
            ];
            $errors = Event::validate($data);
            if ($errors) return render('event_form', ['errors' => $errors, 'old' => $data]);
            Event::create($data);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Événement créé avec succès.'];
            redirect('/events');
        }
        render('event_form');
    }

    public function edit(int $id) {
        $this->requireEditor();
        $event = Event::findById($id);
        if (!$event) { http_response_code(404); echo 'Événement introuvable'; return; }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'title' => trim($_POST['title'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'start_at' => $_POST['start_at'] ?? '',
                'end_at' => $_POST['end_at'] ?? '',
            ];
            $errors = Event::validate($data);
            if ($errors) return render('event_form', ['errors' => $errors, 'old' => $data, 'event' => $event]);
            Event::update($id, $data);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Événement mis à jour avec succès.'];
            redirect('/events');
        }
        render('event_form', ['event' => $event]);
    }

    public function delete(int $id) {
        $this->requireEditor();
        Event::delete($id);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Événement supprimé avec succès.'];
        redirect('/events');
    }
}

<?php
require_once __DIR__ . '/../models/Message.php';
require_once __DIR__ . '/../models/User.php';

class MessageController {
    private function requireAuth() {
        if (!isset($_SESSION['user_id'])) { redirect('/login'); }
    }
    private function requireSender() {
        $this->requireAuth();
        $role = $_SESSION['role'] ?? '';
        if (!in_array($role, ['admin','professeur','parent'], true)) {
            http_response_code(403); echo 'Envoi de messages non autorisé'; exit;
        }
    }

    public function index() {
        $this->requireAuth();
        $inbox = Message::getInbox((int)$_SESSION['user_id']);
        $users = User::getAll();
        render('messages', ['inbox' => $inbox, 'users' => $users, 'role' => $_SESSION['role'] ?? '']);
    }

    public function create() {
        $this->requireSender();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $to_id = (int)($_POST['to_id'] ?? 0);
            $content = trim($_POST['content'] ?? '');
            if ($to_id <= 0 || $content === '') {
                $inbox = Message::getInbox((int)$_SESSION['user_id']);
                $users = User::getAll();
                return render('messages', ['inbox' => $inbox, 'users' => $users, 'errors' => ['Message ou destinataire invalide'], 'role' => $_SESSION['role'] ?? '']);
            }
            Message::send((int)$_SESSION['user_id'], $to_id, $content);
            redirect('/messages');
        }
        redirect('/messages');
    }

    public function thread(int $other_id) {
        $this->requireAuth();
        $thread = Message::getThread((int)$_SESSION['user_id'], $other_id);
        $users = User::getAll();
        render('messages', ['thread' => $thread, 'other_id' => $other_id, 'users' => $users, 'role' => $_SESSION['role'] ?? '']);
    }
}

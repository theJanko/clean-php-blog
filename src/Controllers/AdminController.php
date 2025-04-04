<?php

namespace App\Controllers;

use App\Models\Article;
use Twig\Environment;

class AdminController extends BaseController
{
    private readonly Article $article;

    public function __construct(Environment $twig)
    {
        parent::__construct($twig);
        $this->article = new Article();
    }

    public function adminPage(): string
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $this->render('admin/dashboard.twig', [
            'articles' => $this->article->getAll(),
            'csrf_token' => $_SESSION['csrf_token']
        ]);
    }

    public function createArticle(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->verifyCsrfToken();

                $data = [
                    'title' => $_POST['title'] ?? '',
                    'description' => $_POST['description'] ?? ''
                ];

                $id = $this->article->create($data);
                $this->handleResponse($id, 'Article created successfully!', 'Error creating article!');
            } catch (\Throwable $e) {
                if (
                    !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
                ) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => false,
                        'error' => $e->getMessage()
                    ]);
                    exit;
                }
                throw $e;
            }
        }
    }

    public function editArticle(int $id): string
    {
        if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
            try {
                $this->verifyCsrfToken();

                parse_str(file_get_contents('php://input'), $patchData);
                $data = [
                    'title' => $patchData['title'] ?? '',
                    'description' => $patchData['description'] ?? ''
                ];

                $success = $this->article->update($id, $data);
                $this->handleResponse($success, 'Article updated successfully!', 'Error updating article!');
            } catch (\Throwable $e) {
                if (
                    !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
                ) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => false,
                        'error' => $e->getMessage()
                    ]);
                    exit;
                }
                throw $e;
            }
        }

        try {
            $article = $this->article->getById($id);
            if (!$article) {
                $this->redirect('/admin');
            }

            return json_encode($article);
        } catch (\Throwable $e) {
            if (
                !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
            ) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'error' => $e->getMessage()
                ]);
                exit;
            }
            throw $e;
        }
    }

    public function deleteArticle(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            try {
                $this->verifyCsrfToken();

                $success = $this->article->delete($id);
                $this->handleResponse($success, 'Article deleted successfully!', 'Error deleting article!');
            } catch (\Throwable $e) {
                if (
                    !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
                ) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => false,
                        'error' => $e->getMessage()
                    ]);
                    exit;
                }
                throw $e;
            }
        }

        $this->redirect('/admin');
    }

    public function getArticle(int $id): string
    {
        try {
            $article = $this->article->getById($id);
            header('Content-Type: application/json');
            return json_encode($article ?: ['error' => 'Article not found']);
        } catch (\Throwable $e) {
            if (
                !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
            ) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'error' => $e->getMessage()
                ]);
                exit;
            }
            throw $e;
        }
    }

    private function handleResponse($result, string $successMessage, string $errorMessage): never
    {
        if ($result) {
            $_SESSION['success'] = $successMessage;
        } else {
            $_SESSION['error'] = $errorMessage;
        }

        if (
            !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
        ) {
            header('Content-Type: application/json');
            echo json_encode(['success' => (bool)$result]);
            exit;
        }

        $this->redirect('/admin');
    }

    private function verifyCsrfToken(): void
    {
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;

        if (!$token || !isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
            throw new \InvalidArgumentException('Błąd weryfikacji tokenu CSRF');
        }
    }
}

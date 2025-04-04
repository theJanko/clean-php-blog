<?php

namespace App\Controllers;

use App\Models\Article;

class AdminController extends BaseController
{
    private Article $article;

    public function __construct(\Twig\Environment $twig)
    {
        parent::__construct($twig);
        $this->article = new Article();
    }

    public function adminPage(): string
    {
        return $this->render('admin/dashboard.twig', [
            'articles' => $this->article->getAll()
        ]);
    }

    public function createArticle(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'title' => $_POST['title'] ?? '',
                'description' => $_POST['description'] ?? ''
            ];

            $id = $this->article->create($data);
            $this->handleResponse($id, 'Article created successfully!', 'Error creating article!');
        }
    }

    public function editArticle(int $id): string
    {
        if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
            parse_str(file_get_contents('php://input'), $patchData);
            $data = [
                'title' => $patchData['title'] ?? '',
                'description' => $patchData['description'] ?? ''
            ];

            $success = $this->article->update($id, $data);
            $this->handleResponse($success, 'Article updated successfully!', 'Error updating article!');
        }

        $article = $this->article->getById($id);
        if (!$article) {
            $this->redirect('/admin');
        }

        return json_encode($article);
    }

    public function deleteArticle(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            $success = $this->article->delete($id);
            $this->handleResponse($success, 'Article deleted successfully!', 'Error deleting article!');
        }

        $this->redirect('/admin');
    }

    public function getArticle(int $id): string
    {
        $article = $this->article->getById($id);
        return json_encode($article ?: ['error' => 'Article not found']);
    }

    private function handleResponse($result, string $successMessage, string $errorMessage): void
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
}

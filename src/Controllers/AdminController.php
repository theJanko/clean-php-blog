<?php

namespace App\Controllers;

use App\Models\Article;

class AdminController extends BaseController
{
    private $article;

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
            $id = $this->article->create([
                'title' => $_POST['title'] ?? '',
                'content' => $_POST['content'] ?? ''
            ]);

            if ($id) {
                $_SESSION['success'] = 'Article created successfully!';
            } else {
                $_SESSION['error'] = 'Error creating article!';
            }

            if (
                !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
            ) {
                echo json_encode(['success' => true]);
                exit;
            }

            $this->redirect('/admin');
        }
    }

    public function editArticle(int $id): string
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $success = $this->article->update($id, [
                'title' => $_POST['title'] ?? '',
                'content' => $_POST['content'] ?? ''
            ]);

            if ($success) {
                $_SESSION['success'] = 'Article updated successfully!';
            } else {
                $_SESSION['error'] = 'Error updating article!';
            }

            if (
                !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
            ) {
                echo json_encode(['success' => true]);
                exit;
            }

            $this->redirect('/admin');
        }

        $article = $this->article->getById($id);
        if (!$article) {
            $this->redirect('/admin');
        }

        return json_encode($article);
    }

    public function deleteArticle(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $success = $this->article->delete((int)$_POST['id']);

            if ($success) {
                $_SESSION['success'] = 'Article deleted successfully!';
            } else {
                $_SESSION['error'] = 'Error deleting article!';
            }
        }

        $this->redirect('/admin');
    }

    public function getArticle(int $id): string
    {
        $article = $this->article->getById($id);
        return json_encode($article ?: ['error' => 'Article not found']);
    }
}

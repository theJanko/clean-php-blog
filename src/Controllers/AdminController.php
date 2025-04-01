<?php

namespace App\Controllers;

class AdminController extends BaseController
{
    public function adminPage(): string
    {
        return $this->render('admin/admin.twig');
    }
}

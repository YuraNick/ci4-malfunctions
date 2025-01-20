<?php

namespace App\Controllers;

class Main extends BaseController
{
    public function index(): string
    {
        $html = view('html_elements/header', ['title' => 'Система диагностики']);
        $html .= view('main/main');
        $html .= view('html_elements/footer');
        return $html;
    }
}

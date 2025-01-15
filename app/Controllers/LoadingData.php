<?php

namespace App\Controllers;

class LoadingData extends BaseController
{
    public function index(): string
    {
        return 'LoadingData';
    }

    public function addUser(): string
    {
        return view('users/add', ['title' => 'Добавление пользователя']);
    } 
}

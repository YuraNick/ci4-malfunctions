<?php

namespace App\Controllers;

use App\Models\User;
use CodeIgniter\Database\Exceptions\DatabaseException;

class Users extends BaseController
{
    public function index(): string
    {
        return 'LoadingData';
    }

    public function getUsers(): string {
        $userModel = new User();
        $users = $userModel->findAll();
        $columns = [];
        foreach ($users as $user) {
            foreach ($user as $column => $val) {
                $columns[] = $column;
            }
            break;
        }

        $html = view('html_elements/header', ['title' => 'Пользователи']);
        $html .= view('users/all', ['rows' => $users, 'columns' => $columns]);
        $html .= view('html_elements/footer_data_table');
        return $html;
    }

    public function addUser(): string
    {
        $login = $this->request->getPost('login');
        $user = $this->request->getPost('name');
        $email = $this->request->getPost('email');
        $role = $this->request->getPost('role');
        $data = [
            'login' => $login,
            'name' => $user,
            'email' => $email,
            'role' => $role,
        ];

        $added = 0;
        $error = '';

        if ($login && $user && $email && $role) {
            // Inserts data and returns inserted row's primary key
            $userModel = new User();
            try {
                $added = $userModel->insert($data);
            } catch (DatabaseException $e) {
                $error = $e->getMessage();
                // echo 'PHP перехватил исключение: ',  $e->getMessage(), "\n";
            }
        }
        
        $html = view('html_elements/header', ['title' => 'Добавление пользователя']);
        $html .= view('users/add', $data);
        if ($added || $error) {
            $html .= view('html_elements/text_info', ['added' => $added, 'error' => $error]);
        }
        $html .= view('html_elements/footer');
        return $html;
    } 
}

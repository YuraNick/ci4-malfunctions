<?php

namespace App\Controllers;

use App\Models\MonObject;
use App\Models\User;
use CodeIgniter\Database\Exceptions\DatabaseException;

class IndependentTables extends BaseController
{
  public function index(): string
  {
    return 'LoadingData';
  }

  private function get($rows, $title): string {
    $columns = [];
    foreach ($rows as $row) {
      foreach ($row as $column => $val) {
        $columns[] = $column;
      }
      break;
    }

    $html = view('html_elements/header', ['title' => $title]);
    $html .= view('users/all', ['rows' => $rows, 'columns' => $columns]);
    $html .= view('html_elements/footer_data_table');
    return $html;
  }

  private function add(\CodeIgniter\Model $model, array $data, array $description, string $heading) {
    $added = 0;
    $error = '';

    if ($data['state_number']) {
      try {
        $added = $model->insert($data);
      } catch (DatabaseException $e) {
        $error = $e->getMessage();
      }
    }
    
    $html = view('html_elements/header', ['title' => $heading]);
    $html .= view('add_forms/simple_add.php', [
      'heading' => $heading,
      'data' => $data, 
      'description' => $description
    ]);
    if ($added || $error) {
      $html .= view('html_elements/text_info', ['added' => $added, 'error' => $error]);
    }
    $html .= view('html_elements/footer');
    return $html;
  }

  public function getMonObjects(): string {
    $model = new MonObject();
    $rows = $model->findAll();
    return $this->get($rows, 'Объекты мониторинга (траспортные средства)');
  }

  public function addMonObject(): string
  {
    $data = [
      'state_number' => $this->request->getPost('state_number'),
      'model' => $this->request->getPost('model'),
    ];

    $description = [
      'state_number' => ['label' => 'Гос. номер', 'required' => 'required'],
      'model' => ['label' => 'Модель', 'required' => 'required'],
    ];

    $heading = 'Добавление объекта мониторинга (автомобиля)';
    $model = new MonObject();
    return $this->add($model, $data, $description, $heading);
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

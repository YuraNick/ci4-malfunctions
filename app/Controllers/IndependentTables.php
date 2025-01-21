<?php

namespace App\Controllers;

use App\Models\MonObject;
use App\Models\User;
use App\Models\Сriticality;
use App\Models\Reasons;
use App\Models\DispatcherStatuses;
use CodeIgniter\Database\Exceptions\DatabaseException;

class IndependentTables extends BaseController
{
  public function index(): string
  {
    return 'LoadingData';
  }

  private function get($rows, $title, $label = ''): string {
    if (!$label) $label = $title;
    $columns = [];
    foreach ($rows as $row) {
      foreach ($row as $column => $val) {
        $columns[] = $column;
      }
      break;
    }

    $html = view('html_elements/header', ['title' => $title]);
    $html .= view('pages/show_table', ['rows' => $rows, 'columns' => $columns, 'label' => $label]);
    $html .= view('html_elements/footer_data_table');
    return $html;
  }

  private function add(\CodeIgniter\Model $model, array $data, string $requiredColumn, array $description, string $heading) {
    $added = 0;
    $error = '';

    if ($data[$requiredColumn]) {
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
    return $this->add($model, $data, 'state_number', $description, $heading);
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

  public function addCriticality(): string {
    // criticality
    $data = [
      'name' => $this->request->getPost('name'),
      'is_notification' => (bool)($this->request->getPost('is_notification') === 'on'),
    ];

    $description = [
      'name' => ['label' => 'Наименование', 'required' => 'required'],
      'is_notification' => ['label' => 'Отправить уведомление', 'required' => '', 'type' => 'checkbox'],
    ];

    $heading = 'Добавление критичности события в справочник';
    $model = new Сriticality();
    return $this->add($model, $data, 'name', $description, $heading);
  }

  public function getСriticality(): string {
    $model = new Сriticality();
    $rows = $model->findAll();
    return $this->get($rows, 'Справочник критичности событий');
  }

  public function addReason() {
    // criticality
    $data = [
      'name' => $this->request->getPost('name'),
      'is_mileage' => (bool)($this->request->getPost('is_mileage') === 'on'),
      'is_fuel_level' => (bool)($this->request->getPost('is_fuel_level') === 'on'),
      'is_moto' => (bool)($this->request->getPost('is_moto') === 'on'),
      'is_can' => (bool)($this->request->getPost('is_can') === 'on'),
    ];

    $description = [
      'name' => ['label' => 'Наименование', 'required' => 'required'],
      'is_mileage' => ['label' => 'Влияет на пробег', 'required' => '', 'type' => 'checkbox'],
      'is_fuel_level' => ['label' => 'Влияет на расход топлива', 'required' => '', 'type' => 'checkbox'],
      'is_moto' => ['label' => 'Влияет на время работы ДВС', 'required' => '', 'type' => 'checkbox'],
      'is_can' => ['label' => 'Влияет на can данные', 'required' => '', 'type' => 'checkbox'],
    ];

    $heading = 'Добавление причины неисправности в справочник';
    $model = new Reasons();
    return $this->add($model, $data, 'name', $description, $heading);
  }
  public function getReasons() {
    $model = new Reasons();
    $rows = $model->findAll();
    return $this->get($rows, 'Справочник причин неисправности');
  }
  public function addDispatcherStatus() {
    // criticality
    $data = [
      'status' => $this->request->getPost('status'),
    ];

    $description = [
      'status' => ['label' => 'Статус', 'required' => 'required'],
    ];

    $heading = 'Добавление статуса неисправности от диспетчера в справочник';
    $model = new DispatcherStatuses();
    return $this->add($model, $data, 'status', $description, $heading);
  }
  public function getDispatcherStatuses() {
    $model = new DispatcherStatuses();
    $rows = $model->findAll();
    return $this->get($rows, 'Справочник статусов неисправностей от диспетчера');
  }
}

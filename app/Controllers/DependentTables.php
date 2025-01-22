<?php

namespace App\Controllers;

use App\Models\MonObject;
use App\Models\User;
use App\Models\Сriticality;
use App\Models\Reasons;
use App\Models\DispatcherStatuses;
use CodeIgniter\Database\Exceptions\DatabaseException;

class DependentTables extends BaseController
{
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
  public function malfunctionsAdd() {
    $data = [
      'id_obj' => $this->request->getPost('id_obj'),
      'id_reason' => $this->request->getPost('id_reason'),
      'id_criticality' => $this->request->getPost('id_criticality'),
      'begin' => $this->request->getPost('begin'),
      'end' => $this->request->getPost('end'),
      'reliability' => $this->request->getPost('reliability'),
      'percent' => $this->request->getPost('percent'),
    ];

    $objects = $this->getObjects();
    $reasons = $this->getReasons();
    $criticality = $this->getCriticality();
    // $now = \CodeIgniter\I18n\Time::now();
    // $model->where('my_dt_field', $now->format('Y-m-d H:i:s.u'))->findAll();


    $description = [
      'id_obj' => ['label' => 'Объект мониторинга (ТС)', 'type' => 'select', 'options' => $objects, 'required' => 'required'],
      'id_reason' => ['label' => 'Причина', 'type' => 'select', 'options' => $reasons, 'required' => 'required'],
      'id_criticality' => ['label' => 'Критичность', 'type' => 'select', 'options' => $criticality, 'required' => 'required'],
      'begin' => ['label' => 'Начало', 'required' => 'required'],
      'end' => ['label' => 'Окончание', 'required' => 'required'],
      'reliability' => ['label' => 'Достоверность в %', 'required' => 'required'],
      'percent' => ['label' => 'Процент неиспраности', 'required' => 'required'],
    ];

    $heading = 'Добавление объекта мониторинга (автомобиля)';
    $model = new MonObject();
    return $this->add($model, $data, 'id_obj', $description, $heading);
  }

  private function getObjects() {
    $model = new MonObject();
    $objects = $model->select(['id','state_number as value'])->findAll();
    return $objects;
  }

  private function getReasons() {
    $model = new Reasons();
    $reasons = $model->select(['id','name as value'])->findAll();
    return $reasons;
  }

  private function getCriticality() {
    $model = new Сriticality();
    $criticality = $model->select(['id','name as value'])->findAll();
    return $criticality;
  }
}
<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\MonObject;
use App\Models\Reasons;
use App\Models\Сriticality;
use App\Models\Malfunctions;
use App\Models\Notifications;
use App\Models\DispatcherStatuses;
use CodeIgniter\Database\Exceptions\DatabaseException;

class ExampleFill extends BaseController
{
  public function fill(): string
  {
    $info = [];
    $usersCount = $this->fillUsers();
    $info[] = "Добавлено пользователей в таблицу users: $usersCount";
    $monObjectsCount = $this->fillMonObjects(100);
    $info[] = "Добавлено объектов мониторинга (автомобилей) в таблицу obj_mon: $monObjectsCount";
    $reasonsCount = $this->fillReasons();
    $info[] = "Добавлено статусаов причин неисправностей в таблицу reasons: $reasonsCount";
    $criticalityCount = $this->fillСriticality();
    $info[] = "Добавлено статусов критичностей неисправностей в таблицу criticality: $criticalityCount";
    $dispatcherStatusesCount = $this->fillDispatcherStatuses();
    $info[] = "Добавлено критичностей в таблицу cdispatcher_statuses: $dispatcherStatusesCount";
    $malfunctionsCount = $this->fillMalfunctions();
    $info[] = "Добавлено неисправностей по объектам мониторинга (автомобилям) в таблицу malfunctions: $malfunctionsCount";
    $notificationsCount = $this->fillNotifications();
    $info[] = "Добавлено уведомлений о неисправностях в таблицу notifications: $notificationsCount";
    
    return implode('<br>', $info);
  }

  private function fillReasons(): string {
    $model = new Reasons();
    $data = $this->getReasonsExample();
    return $this->insertData($model, $data);
  }

  private function fillСriticality(): string {
    $model = new Сriticality();
    $data = $this->getСriticalityExample();
    return $this->insertData($model, $data);
  }

  private function fillDispatcherStatuses(): string {
    $model = new Сriticality();
    $data = $this->getDispatcherStatusesExample();
    return $this->insertData($model, $data);
  }

  private function fillMalfunctions(): string {
    $model = new Malfunctions();
    $data = $this->getMalfunctionsExample();
    return $this->insertData($model, $data);
  }

  public function fillUsers(): string {
    $model = new User();
    $data = $this->getUserDataExample();
    return $this->insertData($model, $data);
  }

  public function fillMonObjects(int $count): string {
    $model = new MonObject();
    $data = $this->getMonObjectDataExample($count);
    return $this->insertData($model, $data);
  }

  public function fillNotifications(): string {
    $model = new Notifications();
    $data = $this->getFillNotificationsDataExample();
    return $this->insertData($model, $data);
  }

  private function insertData(\CodeIgniter\Model $model, &$data) : string {
    // $first = $model->getWhere($data[0])->getResultArray();
    // $rowsCount = $model->getWhere($data[0])->num_rows();
    $rowsCount = $model->limit(1)->get()->getResultArray();
    if (count($rowsCount)) {
      return '0 - табллица не пуста';
    }
    $model->insertBatch($data);
    return (string)count($data);
  }

  private function getMalfunctionsExample(): array {
    $malfunctionsExample = [];
    
    $monObject = new MonObject();
    $monObjectIds = $monObject->select('id')->findAll();
    $monObject = null;
    foreach($monObjectIds as $key => $val) {
      $monObjectIds[$key] = (int)$val['id'];
    }

    $reasons = new Reasons();
    $reasonIds = $reasons->select('id')->findAll();
    $reasons = null;
    foreach($reasonIds as $key => $val) {
      $reasonIds[$key] = (int)$val['id'];
    }
    
    $criticality = new Сriticality();
    $criticalityIds = $criticality->select('id')->findAll();
    $criticality = null;
    foreach($criticalityIds as $key => $val) {
      $criticalityIds[$key] = (int)$val['id'];
    }

    $ninetyDays = 7776000;
    $OneDay = 86400;

    foreach($monObjectIds as $id_obj) {
      for ($i = 0; $i < 20; $i++) {
        $random = rand(0, 100);
        //Generate a timestamp using mt_rand.
      
        $timestamp = mt_rand(time() - $ninetyDays, time());
        //Format that timestamp into a readable date string.
        $randomDateBegin = date("Y-m-d H:i:s+05", $timestamp); // https://www.php.net/manual/en/datetime.format.php
        $randomDateEnd = date("Y-m-d H:i:s+05", $timestamp + rand(60, $OneDay));
        $malfunctionsExample[] = [
          'id_obj' => $id_obj,
          'id_reason' => $this->getIndexAsRoundArray($reasonIds, $random),
          'id_criticality' => $this->getIndexAsRoundArray($criticalityIds, $random), 
          'begin' => $randomDateBegin,
          'end' => $randomDateEnd,
          'reliability' => rand(50, 100),
          'percent' => rand(1, 100)
        ];
      }
    }
    return $malfunctionsExample;
  }

  private function getFillNotificationsDataExample(): array {
    $notificationsDataExample = [];
    // получить все уведомления со статусом отправить
    $model = new Malfunctions();
    $needNotifications = $model->select(
      'malfunction.malfunctions.id as id, 
      malfunction.malfunctions.id_criticality as id_criticality,
      malfunction.malfunctions.begin as begin,
      malfunction.malfunctions.end as end,
      malfunction.malfunctions.reliability as reliability,
      malfunction.malfunctions.percent as percent,
      malfunction.obj_mon.state_number as state_number,
      malfunction.criticality.is_notification as is_notification,
      malfunction.reasons.name as reason'
    )->join(
      'malfunction.criticality', 'malfunction.malfunctions.id_criticality = malfunction.criticality.id'
    )->join(
      'malfunction.obj_mon', 'malfunction.malfunctions.id_obj = malfunction.obj_mon.id'
    )->join(
      'malfunction.reasons', 'malfunction.malfunctions.id_reason = malfunction.reasons.id'
    )->where('is_notification', TRUE)->findAll();
    foreach($needNotifications as $needNotification) {
      [
        'begin' => $begin,
        'end' => $end,
        'reliability' => $reliability,
        'percent' => $percent,
        'state_number' => $state_number,
        'reason' => $reason,
      ] = $needNotification;
      $text = "По объекту $state_number с вероятностью $reliability% обнаружена неисправность \"$reason\" с $begin по $end в течение $percent% времени";
      $notificationsDataExample[] = [
        'id_malfunction' => $needNotification['id'],
        'text' => $text,
      ];
    }
    return $notificationsDataExample;
  }

  private function getUserDataExample(): array {
    $res = [];
    $rus = [
      'user' => 'Пользователь', 
      'dispatcher' => 'Диспетчер', 
      'support' => 'Сотрудник техподдержки', 
      'developer' => 'Разработчик'
    ];
    foreach(['user', 'dispatcher', 'support', 'developer'] as $role) {
      for($i = 1; $i < 6; $i++) {
        $name = $rus[$role] ?? $role;
        $res[] = [
          'login' => "$role-$i",
          'name' => "$name-$i",
          'role' => $role,
          'email' => "$role-$i@email.ru",
        ];
      }
    }
    return $res;
  }
 
  private function getMonObjectDataExample(int $count): array {
    $res = [];
    $letters = ['А', 'В', 'Е', 'К', 'М', 'Н', 'О', 'Р', 'С', 'Т', 'Х', 'У'];
    $models = ['Урал', 'КамАЗ', 'MAN', 'Sitrak', 'Volvo'];
    for($i = 1; $i < $count + 1; $i++) {
      $state_number = $this->getIndexAsRoundArray($letters, $i) . 
        ' ' . rand(100, 999) . ' ' . 
        $this->getIndexAsRoundArray($letters, $i + 1) .
        $this->getIndexAsRoundArray($letters, $i + 2);
      $model = $this->getIndexAsRoundArray($models, $i);
      
      $res[] = [
        'state_number' => $state_number, 
        'model' => $model
      ];
    }
    return $res;
  }

  private function getIndexAsRoundArray(array &$arr, int $iteration) {
    $count = count($arr);
    if (!$count) return null;
    $i = $iteration % $count;
    return $arr[$i];
  }

  private function getСriticalityExample(): array {
    return [
      [
        'name' => 'Не критичное', 
        'is_notification' => FALSE,
      ],
      [
        'name' => 'Временная неисправность', 
        'is_notification' => FALSE,
      ],
      [
        'name' => 'Влияет на важные показатели', 
        'is_notification' => TRUE,
      ],
      [
        'name' => 'Система неисправна', 
        'is_notification' => TRUE,
      ],
    ];
  }

  private function getDispatcherStatusesExample(): array {
    return [
      [
        'status' => 'Отсутствует', 
      ],
      [
        'name' => 'Проверяется диспетчером', 
      ],
      [
        'name' => 'Диспетчер отклонил неисправность', 
      ],
      [
        'name' => 'Отправлен вопрос в техподдержку',
      ],
    ];
  }

  private function getReasonsExample(): array {
    return [
      [
        'name' => 'Отсутствие ГЛОНАСС сигнала', 
        'is_mileage' => TRUE, 
        'is_fuel_level' => FALSE,
        'is_moto' => FALSE,
        'is_can' => FALSE
      ],
      [
        'name' => 'Неисправность датчика уровня топлива в баке', 
        'is_mileage' => FALSE, 
        'is_fuel_level' => TRUE,
        'is_moto' => FALSE,
        'is_can' => FALSE
      ],
      [
        'name' => 'Движение без фиксации работы двигателя', 
        'is_mileage' => FALSE, 
        'is_fuel_level' => FALSE,
        'is_moto' => TRUE,
        'is_can' => FALSE
      ],
      [
        'name' => 'Отсутствуют CAN данные', 
        'is_mileage' => FALSE, 
        'is_fuel_level' => FALSE,
        'is_moto' => FALSE,
        'is_can' => TRUE
      ],
    ];
  }
}

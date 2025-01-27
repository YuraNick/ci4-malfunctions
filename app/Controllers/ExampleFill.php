<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\MonObject;
use App\Models\Reasons;
use App\Models\Сriticality;
use App\Models\Malfunctions;
use App\Models\Notifications;
use App\Models\NotificationsUsers;
use App\Models\DispatcherStatuses;
use App\Models\DispatcherConfirms;
use App\Models\DispatcherSupportQuestions;
use CodeIgniter\Database\Exceptions\DatabaseException;

class ExampleFill extends BaseController
{
  public function fill(): string
  {
    $info = [
      '<a href="/">На главную</a><br>'
    ];
    try {
      $info = $this->fillTables($info);
    } catch (DatabaseException $err) {
      $info[] = "Ошибка добавления данных: " . $err->getMessage(); 
    }
    
    return implode('<br>', $info);
  }
  
  private function fillTables(array $info) {
    $usersCount = $this->fillUsers();
    $info[] = "Добавлено пользователей в таблицу users: $usersCount";
    $monObjectsCount = $this->fillMonObjects(100);
    $info[] = "Добавлено объектов мониторинга (автомобилей) в таблицу obj_mon: $monObjectsCount";
    $reasonsCount = $this->fillReasons();
    $info[] = "Добавлено статусаов причин неисправностей в таблицу reasons: $reasonsCount";
    $criticalityCount = $this->fillСriticality();
    $info[] = "Добавлено статусов критичностей неисправностей в таблицу criticality: $criticalityCount";
    $dispatcherStatusesCount = $this->fillDispatcherStatuses();
    $info[] = "Добавлено в справочник статусов диспетчера в таблицу dispatcher_statuses: $dispatcherStatusesCount";
    $malfunctionsCount = $this->fillMalfunctions();
    $info[] = "Добавлено неисправностей по объектам мониторинга (автомобилям) в таблицу malfunctions: $malfunctionsCount";
    $notificationsCount = $this->fillNotifications();
    $info[] = "Добавлено подготовленных уведомлений о неисправностях в таблицу notifications: $notificationsCount";
    $notificationsUsersCount = $this->fillNotificationsUsers();
    $info[] = "Добавлено уведомлений пользователям о неисправностях в таблицу notifications_users: $notificationsUsersCount";
    $dispatcherConfirmsCount = $this->fillDispatcherConfirms();
    $info[] = "Добавлено статусов неисправностей, имитирующих работу диспетчера, в таблицу dispatcher_confirms: $dispatcherConfirmsCount";
    // $dispatcherSupportQuestionsCount = $this->fillDispatcherSupportQuestions();
    // $info[] = "Добавлено вопросов в техподдержку, имитирующих работу диспетчера, в таблицу dispatcher_support_questions: $dispatcherSupportQuestionsCount";
    return $info;
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
    $model = new DispatcherStatuses();
    $data = $this->getDispatcherStatusesExample();
    return $this->insertData($model, $data);
  }

  private function fillMalfunctions(): string {
    $model = new Malfunctions();
    $data = $this->getMalfunctionsExample();
    return $this->insertData($model, $data);
  }

  private function fillDispatcherConfirms(): string {
    $model = new DispatcherConfirms();
    $data = $this->getDispatcherConfirmsExample();
    return $this->insertData($model, $data);
  }

  private function fillDispatcherSupportQuestions(): string {
    $model = new DispatcherSupportQuestions();
    $data = $this->getDispatcherSupportQuestions();
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
    $data = $this->getNotificationsDataExample();
    return $this->insertData($model, $data);
  }

  public function fillNotificationsUsers(): string {
    $model = new NotificationsUsers();
    $data = $this->getNotificationsUsersDataExample();
    return $this->insertData($model, $data);
  }

  private function insertData(\CodeIgniter\Model $model, &$data) : string {
    $rowsCount = $model->limit(1)->get()->getResultArray();
    if (count($rowsCount)) {
      return '0 - таблица не пуста';
    }
    $model->insertBatch($data);
    return (string)count($data);
  }

  private function getMalfunctionsExample(): array {
    $malfunctionsExample = [];
    
    $monObject = new MonObject();
    $monObjectIds = $monObject->select('id')->findAll();
    $monObject = null;
    $monObjectIds = $this->leaveOnlyIds($monObjectIds);
    
    $reasons = new Reasons();
    $reasonIds = $reasons->select('id')->findAll();
    $reasons = null;
    $reasonIds = $this->leaveOnlyIds($reasonIds);
    
    $criticality = new Сriticality();
    $criticalityIds = $criticality->select('id')->findAll();
    $criticality = null;
    $criticalityIds = $this->leaveOnlyIds($criticalityIds);

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

  private function getDispatcherConfirmsExample(): array {
    $dispatcherConfirmsExample = [];

    $malfunctions = new Malfunctions();
    $malfunctionsData = $malfunctions->select('id, extract(epoch from "end") as end')->findAll();
    $malfunctions = null;
    // $malfunctionsIds = $this->leaveOnlyIds($malfunctionsIds);

    $users = new User();
    $usersIds = $users->select('id')->where('role', 'dispatcher')->findAll();
    $users = null;
    $usersIds = $this->leaveOnlyIds($usersIds);

    $dispatcherStatuses = new DispatcherStatuses();
    $dispatcherStatusesIds = $dispatcherStatuses->select('id')->findAll();
    $dispatcherStatuses = null;
    $dispatcherStatusesIds = $this->leaveOnlyIds($dispatcherStatusesIds);

    $commentsRandom = [
      'На рассмотрении',
      'Подтверждена, требуется выезд специалистов',
      'Производилось техническое обслуживание системы',
      'Намеренная порча оборудования',
      'Неисправность не подтверждена'
    ];

    foreach($malfunctionsData as $malfunction) {
      if (rand(0,100) < 50) continue;
      $end = (int)$malfunction['end'];
      $timestamp = date("Y-m-d H:i:s+05", $end + rand(60, 86400*7));
      $dispatcherConfirmsExample[] = [
        'id_malfunction' => $malfunction['id'], 
        'id_user' => $usersIds[rand(0, count($usersIds) - 1)], 
        'id_status' => $dispatcherStatusesIds[rand(0, count($dispatcherStatusesIds) - 1)], 
        'timestamp' => $timestamp, 
        'comment' => $commentsRandom[rand(0, count($commentsRandom) - 1)], 
      ];
    }
    
    return $dispatcherConfirmsExample;
  }

  private function getDispatcherSupportQuestions(): array {
    $dispatcherSupportQuestionsExample = [];

    $malfunctions = new Malfunctions();
    $malfunctionsData = $malfunctions->select(
      'malfunction.malfunctions.id, 
      extract(epoch from malfunction.malfunctions.end) as end,
      dispatcher_confirms.user_id'
    )->join(
      'malfunction.dispatcher_confirms as dispatcher_confirms', 
      'malfunction.malfunctions.id = dispatcher_confirms.id_malfunction'
    )->findAll();
    $malfunctions = null;
    // $malfunctionsIds = $this->leaveOnlyIds($malfunctionsIds);

    $users = new User();
    $usersIds = $users->select('id')->where('role', 'dispatcher')->findAll();
    $users = null;
    $usersIds = $this->leaveOnlyIds($usersIds);

    $dispatcherStatuses = new DispatcherStatuses();
    $dispatcherStatusesIds = $dispatcherStatuses->select('id')->findAll();
    $dispatcherStatuses = null;
    $dispatcherStatusesIds = $this->leaveOnlyIds($dispatcherStatusesIds);

    $commentsRandom = [
      'На рассмотрении',
      'Подтверждена, требуется выезд специалистов',
      'Производилось техническое обслуживание системы',
      'Намеренная порча оборудования',
      'Неисправность не подтверждена'
    ];

    foreach($malfunctionsData as $malfunction) {
      if (rand(0,100) < 50) continue;
      $end = (int)$malfunction['end'];
      $timestamp = date("Y-m-d H:i:s+05", $end + rand(60, 86400*7));
      $dispatcherSupportQuestionsExample[] = [
        // [
        //   'id_user', 
        //   'id_malfunction', 
        //   'timestamp', 
        //   'importance', 
        //   'text', 
        // ]
        'id_malfunction' => $malfunction['id'], 
        'id_user' => $usersIds[rand(0, count($usersIds) - 1)], 
        'id_status' => $dispatcherStatusesIds[rand(0, count($dispatcherStatusesIds) - 1)], 
        'timestamp' => $timestamp, 
        'comment' => $commentsRandom[rand(0, count($commentsRandom) - 1)], 
      ];
    }
    
    return $dispatcherSupportQuestionsExample;
  }

  private function leaveOnlyIds(array $arr) : array {
    foreach($arr as $key => $val) {
      $arr[$key] = (int)$val['id'];
    }
    return $arr;
  }

  private function getNotificationsDataExample(): array {
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

  private function getNotificationsUsersDataExample(): array {
    $notificationsUsersDataExample = [];
    
    $userModel = new User();
    $usersIds = $userModel->select('id')->findAll();
    $userModel = null;
    $usersIds = $this->leaveOnlyIds($usersIds);
    
    $notificationsModel = new Notifications();
    $notificationsIds = $notificationsModel->select('id')->findAll();
    $notificationsModel = null;
    $notificationsIds = $this->leaveOnlyIds($notificationsIds);
    $usersCount = count($usersIds);

    foreach($notificationsIds as $notifications_id) {
      $usersCount = rand(1, $usersCount < 5 ? $usersCount : 5);
      $usersIdsRemained = $usersIds;
      for ($i = 0; $i < $usersCount; $i++) {
        $userIndex = rand(0, count($usersIdsRemained) - 1);
        $id_user = $usersIdsRemained[$userIndex];
        array_splice($usersIdsRemained, $userIndex, 1);
        $notificationsUsersDataExample[] = [
          'notifications_id' => $notifications_id,
          'id_user' => $id_user,
          'is_sended' => (bool)(rand(0, 100) < 50),
        ];
      }
    }
    
    return $notificationsUsersDataExample;
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
        'status' => 'Проверяется диспетчером', 
      ],
      [
        'status' => 'Диспетчер отклонил неисправность', 
      ],
      [
        'status' => 'Отправлен вопрос в техподдержку',
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

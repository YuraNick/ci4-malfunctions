<?php

namespace App\Controllers;

use App\Models\Malfunctions;
use App\Models\MonObject;
use App\Models\User;
use App\Models\Сriticality;
use App\Models\Reasons;
use App\Models\Notifications;
use App\Models\NotificationsUsers;
use App\Models\DispatcherStatuses;
use App\Models\DispatcherConfirms;
use App\Models\DispatcherSupportQuestions;
use App\Models\DispatcherSupportAnswers;
use App\Models\SupportDeveloperQuestions;
use App\Models\SupportDeveloperAnswers;
use CodeIgniter\Database\Exceptions\DatabaseException;

class DependentTables extends BaseController
{
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
  
  private function add(
    \CodeIgniter\Model $model, 
    array $data, 
    string $requiredColumn, 
    array $description, 
    string $heading,
  ) {
    $added = 0;
    $error = '';

    $timestampColumns = [];
    foreach($description as $column => $desc) {
      if (($desc['type'] ?? '') === 'datetime') {
        $timestampColumns[] = $column;
      }
    }

    if ($data[$requiredColumn]) {
      try {
        $dataInsert = $data;
        foreach($timestampColumns as $column) {
          $dataInsert[$column] = $this->timeConvertFromPostgres($data[$column] ?? '', $data['timezone_name'] ?? '');
        }
        // $dataInsert['end'] = $this->timeConvertFromPostgres($data['end'] ?? '', $data['timezone_name'] ?? '');
        $added = $model->insert($dataInsert);
        $dataInsert = null;
      } catch (DatabaseException $e) {
        $error = $e->getMessage();
      }
    }
    
    $html = view('html_elements/header', ['title' => $heading]);
    $html .= view('add_forms/simple_add.php', [
      'heading' => $heading,
      'data' => $data, 
      'description' => $description,
    ]);
    if ($added || $error) {
      $html .= view('html_elements/text_info', [
        'added' => $added, 
        'error' => $error,
      ]);
    }
    $html .= view('html_elements/footer');
    return $html;
  }

  private function timeConvertFromPostgres(string $t, string $timezoneName): string {
    if (!$t || !$timezoneName) return $t;
    // list($day, $month, $year, $hh, $mm, $ss) = sscanf($t, "%d.%d.%dT%d:%d::%d");
    $t = str_replace('T', ' ', $t);
    return "$t $timezoneName";
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
      'timezone_name' => $this->request->getPost('timezone_name'),
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
      'begin' => ['label' => 'Начало', 'type' => 'datetime', 'required' => 'required'],
      'end' => ['label' => 'Окончание', 'type' => 'datetime', 'required' => 'required'],
      'reliability' => ['label' => 'Достоверность в %', 'required' => 'required'],
      'percent' => ['label' => 'Процент неиспраности', 'required' => 'required'],
      'timezone_name' => ['type' => 'auto']
    ];

    $heading = 'Добавление выявленной неисправности';
    $model = new Malfunctions();
    return $this->add(
      $model,
      $data,
      'id_obj',
      $description,
      $heading,
    );
  }

  public function notificationsAdd() {
    $data = [
      'id_malfunction' => $this->request->getPost('id_malfunction'),
      'text' => $this->request->getPost('text'),
    ];

    $malfunctions = $this->getMalfunctionsFullDescription();

    $description = [
      'id_malfunction' => ['label' => 'Неисправность', 'type' => 'select', 'options' => $malfunctions, 'required' => 'required'],
      'text' => ['label' => 'Описание', 'type' => 'textarea', 'required' => 'required'],
    ];

    $heading = 'Добавление уведомления о неисправности';
    $model = new Notifications();
    return $this->add(
      $model,
      $data,
      'id_malfunction',
      $description,
      $heading,
    );
  }

  public function notificationsUsersAdd(): string {
    $data = [
      'notifications_id' => $this->request->getPost('notifications_id'),
      'id_user' => $this->request->getPost('id_user'),
      'is_sended' => (bool)($this->request->getPost('is_sended') === 'on'),
    ];

    $notifications = $this->getShortNotifications();
    $users = $this->getShortUsers();

    $description = [
      'notifications_id' => ['label' => 'Уведомление', 'type' => 'select', 'options' => $notifications, 'required' => 'required'],
      'id_user' => ['label' => 'Пользователь', 'type' => 'select', 'options' => $users, 'required' => 'required'],
      'is_sended' => ['label' => 'Сообщение было отправлено (вставлено для разработки)', 'required' => '', 'type' => 'checkbox'],
    ];

    $heading = 'Добавление уведомления пользователю о неисправности';
    $model = new NotificationsUsers();
    return $this->add(
      $model,
      $data,
      'notifications_id',
      $description,
      $heading,
    );
  }

  public function dispatcherConfirmsAdd(): string {
    $data = [
      'id_malfunction' => $this->request->getPost('id_malfunction'),
      'id_user' => $this->request->getPost('id_user'),
      'id_status' => $this->request->getPost('id_status'),
      'timestamp' => $this->request->getPost('timestamp'),
      'comment' => $this->request->getPost('comment'),
      'timezone_name' => $this->request->getPost('timezone_name'),
    ];

    $malfunction = $this->getMalfunctionsFullDescription();
    $users = $this->getShortUsers();
    $dispatcherStatuses = $this->getShortDispatcherStatuses();

    $description = [
      'id_malfunction' => ['label' => 'Неисправность', 'type' => 'select', 'options' => $malfunction, 'required' => 'required'],
      'id_user' => ['label' => 'Пользователь', 'type' => 'select', 'options' => $users, 'required' => 'required'],
      'id_status' => ['label' => 'Статус неисправности', 'type' => 'select', 'options' => $dispatcherStatuses, 'required' => 'required'],
      'timestamp' => ['label' => 'Статус выставлен (для разработки)', 'type' => 'datetime', 'required' => 'required'],
      'comment' => ['label' => 'Комментарий', 'type' => 'textarea', 'required' => ''],
      'timezone_name' => ['type' => 'auto'],
    ];

    $heading = 'Добавление статуса неисправности диспетчером';
    $model = new DispatcherConfirms();
    return $this->add(
      $model,
      $data,
      'id_malfunction',
      $description,
      $heading,
    );
  }

  public function dispatcherSupportQuestionsAdd(): string {
    $data = [
      'id_user' => $this->request->getPost('id_user'),
      'id_malfunction' => $this->request->getPost('id_malfunction'),
      'timestamp' => $this->request->getPost('timestamp'),
      'importance' => $this->request->getPost('importance'),
      'text' => $this->request->getPost('text'),
      'timezone_name' => $this->request->getPost('timezone_name'),
    ];

    $users = $this->getShortUsers();
    $malfunction = $this->getMalfunctionsFullDescription();

    $importances = [];
    for($i = 1; $i < 11; $i++) {
      $importances[] = ['id' => (string)$i, 'value' => $i];
    }

    $description = [
      'id_user' => ['label' => 'Пользователь', 'type' => 'select', 'options' => $users, 'required' => 'required'],
      'id_malfunction' => ['label' => 'Неисправность', 'type' => 'select', 'options' => $malfunction, 'required' => 'required'],
      'timestamp' => ['label' => 'Вопрос задан (для разработки)', 'type' => 'datetime', 'required' => 'required'],
      'importance' => ['label' => 'Важность', 'type' => 'select', 'options' => $importances, 'required' => 'required'],
      'text' => ['label' => 'Комментарий', 'type' => 'textarea', 'required' => ''],
      'timezone_name' => ['type' => 'auto'],
    ];

    $heading = 'Добавление вопроса диспетчера в техподдержку';
    $model = new DispatcherSupportQuestions();
    return $this->add(
      $model,
      $data,
      'id_malfunction',
      $description,
      $heading,
    );
  }

  public function supportDeveloperQuestionsAdd(): string {
    $data = [
      'id_user' => $this->request->getPost('id_user'),
      'id_malfunction' => $this->request->getPost('id_malfunction'),
      'timestamp' => $this->request->getPost('timestamp'),
      'importance' => $this->request->getPost('importance'),
      'text' => $this->request->getPost('text'),
      'timezone_name' => $this->request->getPost('timezone_name'),
    ];

    $users = $this->getShortUsers();
    $malfunction = $this->getMalfunctionsFullDescription();

    $importances = [];
    for($i = 1; $i < 11; $i++) {
      $importances[] = ['id' => (string)$i, 'value' => $i];
    }

    $description = [
      'id_user' => ['label' => 'Пользователь', 'type' => 'select', 'options' => $users, 'required' => 'required'],
      'id_malfunction' => ['label' => 'Неисправность', 'type' => 'select', 'options' => $malfunction, 'required' => 'required'],
      'timestamp' => ['label' => 'Вопрос задан (для разработки)', 'type' => 'datetime', 'required' => 'required'],
      'importance' => ['label' => 'Важность', 'type' => 'select', 'options' => $importances, 'required' => 'required'],
      'text' => ['label' => 'Комментарий', 'type' => 'textarea', 'required' => ''],
      'timezone_name' => ['type' => 'auto'],
    ];

    $heading = 'Добавление вопроса техподдержки разработчикам';
    $model = new SupportDeveloperQuestions();
    return $this->add(
      $model,
      $data,
      'id_malfunction',
      $description,
      $heading,
    );
  }

  public function supportDeveloperAnswersAdd(): string {
    $data = [
      'id_user' => $this->request->getPost('id_user'),
      'id_question' => $this->request->getPost('id_question'),
      'timestamp' => $this->request->getPost('timestamp'),
      'text' => $this->request->getPost('text'),
      'timezone_name' => $this->request->getPost('timezone_name'),
    ];

    $users = $this->getShortUsers();
    $questions = $this->getShortSupportDeveloperQuestions();

    $description = [
      'id_user' => ['label' => 'Пользователь', 'type' => 'select', 'options' => $users, 'required' => 'required'],
      'id_question' => ['label' => 'Вопрос техподдержки', 'type' => 'select', 'options' => $questions, 'required' => 'required'],
      'timestamp' => ['label' => 'Вопрос задан (для разработки)', 'type' => 'datetime', 'required' => 'required'],
      'text' => ['label' => 'Ответ разработчика техподдержке', 'type' => 'textarea', 'required' => ''],
      'timezone_name' => ['type' => 'auto'],
    ];

    $heading = 'Добавление ответа разработчика на вопрос техподдержки';
    $model = new SupportDeveloperAnswers();
    return $this->add(
      $model,
      $data,
      'id_question',
      $description,
      $heading,
    );
  }
  
  public function dispatcherSupportAnswersAdd(): string {
    
    $data = [
      'id_user' => $this->request->getPost('id_user'),
      'id_question' => $this->request->getPost('id_question'),
      'timestamp' => $this->request->getPost('timestamp'),
      'text' => $this->request->getPost('text'),
      'timezone_name' => $this->request->getPost('timezone_name'),
    ];

    $users = $this->getShortUsers();
    $questions = $this->getShortDispatcherSupportQuestions();

    $description = [
      'id_user' => ['label' => 'Пользователь', 'type' => 'select', 'options' => $users, 'required' => 'required'],
      'id_question' => ['label' => 'Вопрос диспетчера', 'type' => 'select', 'options' => $questions, 'required' => 'required'],
      'timestamp' => ['label' => 'Вопрос задан (для разработки)', 'type' => 'datetime', 'required' => 'required'],
      'lifetime' => ['label' => 'Техподдержка ожидает ответа до', 'type' => 'datetime', 'required' => 'required'],
      'text' => ['label' => 'Ответ техподдержки диспетчеру', 'type' => 'textarea', 'required' => ''],
      'timezone_name' => ['type' => 'auto'],
    ];

    $heading = 'Добавление ответа техподдержки на вопрос диспетчера';
    $model = new DispatcherSupportAnswers();
    return $this->add(
      $model,
      $data,
      'id_question',
      $description,
      $heading,
    );
  }

  public function getMalfunctions() {
    $model = new Malfunctions();
    $rows = $model->findAll();
    return $this->get($rows, 'Выявленные неисправности');
  }

  public function getDispatcherSupportQuestions(): string {
    $model = new DispatcherSupportQuestions();
    $rows = $model->findAll();
    return $this->get($rows, 'Вопросы диспетчера в техподдержку');
  }

  public function getDispatcherSupportAnswers(): string {
    $model = new DispatcherSupportAnswers();
    $rows = $model->findAll();
    return $this->get($rows, 'Ответы техподдержки на вопросы диспетчеров');
  }

  public function getSupportDeveloperQuestions(): string {
    $model = new SupportDeveloperQuestions();
    $rows = $model->findAll();
    return $this->get($rows, 'Вопросы техподдержки разработчикам');
  }
  
  public function getSupportDeveloperAnswers(): string {
    $model = new SupportDeveloperAnswers();
    $rows = $model->findAll();
    return $this->get($rows, 'Ответы разработчиков техподдержке');
  }

  public function getShortDispatcherSupportQuestions(): array {
    $model = new DispatcherSupportQuestions();
    $rows = $model->select('id, text as value')->findAll();
    return $rows;
  }

  public function getShortSupportDeveloperQuestions(): array {
    $model = new SupportDeveloperQuestions();
    $rows = $model->select('id, text as value')->findAll();
    return $rows;
  }

  public function getDispatcherConfirms(): string {
    $model = new DispatcherConfirms();
    $rows = $model->findAll();
    return $this->get($rows, 'Статусы и комментарии диспетчера по обнаруженным неисправностям');
  }

  public function getShortDispatcherStatuses(): array {
    $model = new DispatcherStatuses();
    $rows = $model->select('id, status as value')->findAll();
    return $rows;
  }

  public function getNotifications() {
    $model = new Notifications();
    $rows = $model->findAll();
    return $this->get($rows, 'Уведомления о неисправностях - текст');
  }

  public function getShortNotifications() {
    $model = new Notifications();
    $rows = $model->select('id, text as value')->findAll();
    return $rows;
  }

  public function getShortUsers() {
    $model = new User();
    $rows = $model->select('id, login as value')->findAll();
    return $rows;
  }

  public function getNotificationsUsers() {
    $model = new NotificationsUsers();
    $rows = $model->findAll();
    return $this->get($rows, 'Уведомления пользователей о неисправностях');
  }

  private function getMalfunctionsFullDescription(): array {
    $model = new Malfunctions();
    $malfunctions = $model->select(
      'malfunction.malfunctions.id as id, 
      malfunction.obj_mon.state_number as state_number, 
      malfunction.reasons.name as reason,
      malfunction.malfunctions.begin as begin, 
      malfunction.malfunctions.end as end'
    )->join('malfunction.obj_mon', 'malfunction.obj_mon.id = malfunction.malfunctions.id_obj'
    )->join('malfunction.reasons', 'malfunction.reasons.id = malfunction.malfunctions.id_reason'
    )->findAll();
    foreach ($malfunctions as $index => $malfunction) {
      ['id' => $m_id, 
        'state_number' => $m_state_number,
        'begin' => $m_begin,
        'end' => $m_end,
        'reason' => $m_reason,
      ] = $malfunction;
      $malfunctions[$index] = [
        'id' => $m_id,
        'value' => "id $m_id объект $m_state_number причина $m_reason с $m_begin по $m_end"
      ];
    }
    return $malfunctions;
  }

  private function getObjects(): array {
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
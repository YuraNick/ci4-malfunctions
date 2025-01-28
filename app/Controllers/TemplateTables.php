<?php

namespace App\Controllers;

use App\Models\Malfunctions;
use App\Models\MonObject;
use App\Models\User;
use App\Models\Сriticality;
use App\Models\Reasons;
use App\Models\DispatcherStatuses;
use App\Models\DispatcherConfirms;
use App\Models\DispatcherSupportQuestions;
use App\Models\DispatcherSupportAnswers;
use App\Models\SupportDeveloperQuestions;
use App\Models\SupportDeveloperAnswers;
use CodeIgniter\Database\Exceptions\DatabaseException;

class TemplateTables extends BaseController
{
  private function get($rows, $data, $description, $title, $label = ''): string {
    if (!$label) $label = $title;
    $columns = [];
    foreach ($rows as $row) {
      foreach ($row as $column => $val) {
        $columns[] = $column;
      }
      break;
    }

    $html = view('html_elements/header', ['title' => $title]);
    // $html .= view('pages/show_table', ['rows' => $rows, 'columns' => $columns, 'label' => $label]);
    $html .= view('template/malfunctions', ['label' => $label]);
    $html .= view('add_forms/simple_add.php', [
      'heading' => '',
      'data' => $data, 
      'description' => $description,
      'butttonText' => 'Выполнить',
    ]);
    $html .= view('template/table', [
      'rows' => $rows, 
      'columns' => $columns, 
      'label' => $label,
      
    ]);
    $html .= view('html_elements/footer_data_table');
    return $html;
  }

  public function getOneMalfunction(): string {
    $id = $this->request->getGet('id') ?? 0;
    $malfunctions = new Malfunctions();
    $malfunctionData = $malfunctions->findAll();
    $options = array_map(function($malfunctionRow) {
      return $malfunctionRow['id'];
    }, $malfunctionData);

    $data = [];

    if ($id) {
      $data = $this->getMalfunctionCardData($id);
    }

    $html = view('html_elements/header', ['title' => 'Карточка неисправности']);
    $html .= view('pages/malfunction_card', ['options' => $options, 'val' => $id, 'data' => $data]);
    $html .= view('html_elements/footer_data_table');
    return $html;
  }

  private function getMalfunctionCardData(int $id) {
    $malfunctionsModel = new Malfunctions();
    $malfunction = $malfunctionsModel->select(
      '*, malfunction.malfunctions.id as id, malfunction.malfunctions.begin, malfunction.malfunctions.end,
      reliability, percent, obj_mon.state_number, obj_mon.model, reasons.name as reason,
      criticality.name'
    )->join('malfunction.obj_mon as obj_mon', 'malfunction.malfunctions.id_obj = obj_mon.id'
    )->join('malfunction.reasons as reasons', 'malfunction.malfunctions.id_reason = reasons.id'
    )->join('malfunction.criticality as criticality', 'malfunction.malfunctions.id_criticality = criticality.id'
    )->where('malfunction.malfunctions.id', $id)->findAll();

    $malfunctionsModel = null;

    $dispatcherConfirmsModel = new DispatcherConfirms();
    $dispatcherConfirms = $dispatcherConfirmsModel->select(
      'malfunction.dispatcher_confirms.id, malfunction.dispatcher_confirms.timestamp, malfunction.dispatcher_confirms.comment, 
      status, comment, login, name, role'
    ) ->join(
      'malfunction.users as users', 'users.id = malfunction.dispatcher_confirms.id_user'
    )->join(
      'malfunction.dispatcher_statuses as ds', 'ds.id = malfunction.dispatcher_confirms.id_status'
    )->where('malfunction.dispatcher_confirms.id_malfunction', $id)->findAll();
    $dispatcherConfirmsModel = null;

    $dispatcherSupportQuestionsModel = new DispatcherSupportQuestions();
    $dispatcherSupportQuestions = $dispatcherSupportQuestionsModel->select(
      'malfunction.dispatcher_support_questions.id, malfunction.dispatcher_support_questions.timestamp, malfunction.dispatcher_support_questions.text,
      importance, login, name, role'
    )->join(
      'malfunction.users as users', 'users.id = malfunction.dispatcher_support_questions.id_user'
    )->where('malfunction.dispatcher_support_questions.id_malfunction', $id)->findAll();
    $dispatcherSupportQuestionsModel = null;

    $dsqIds = array_map(function($dispatcherSupportQuestionRow) {
      return $dispatcherSupportQuestionRow['id'];
    }, $dispatcherSupportQuestions);


    $dispatcherSupportAnswersModel = new DispatcherSupportAnswers();
    $dispatcherSupportAnswers = $dispatcherSupportAnswersModel->select(
      'dispatcher_support_answers.id, timestamp, lifetime, text, login, name, role'
    )->join(
      'malfunction.users as users', 'users.id = malfunction.dispatcher_support_answers.id_user'
    )->where("malfunction.dispatcher_support_answers.id_question IN (" 
      . implode(',', $dsqIds) . ")"
    )->findAll();
    $dispatcherSupportQuestionsModel = null;

    $supportDeveloperQuestionsModel = new SupportDeveloperQuestions();
    $supportDeveloperQuestions = $supportDeveloperQuestionsModel->select(
      'malfunction.support_developer_questions.id, malfunction.support_developer_questions.timestamp, malfunction.support_developer_questions.text,
      importance, login, name, role'
    )->join(
      'malfunction.users as users', 'users.id = malfunction.support_developer_questions.id_user'
    )->where('malfunction.support_developer_questions.id_malfunction', $id)->findAll();
    $supportDeveloperQuestionsModel = null;

    $sdqIds = array_map(function($supportDeveloperQuestionRow) {
      return $supportDeveloperQuestionRow['id'];
    }, $supportDeveloperQuestions);

    $supportDeveloperAnswersModel = new SupportDeveloperAnswers();
    $supportDeveloperAnswers = $supportDeveloperAnswersModel->select(
      'support_developer_answers.id, timestamp, text, login, name, role'
    )->join(
      'malfunction.users as users', 'users.id = malfunction.support_developer_answers.id_user'
    )->where("malfunction.support_developer_answers.id_question IN (" 
      . implode(',', $sdqIds) . ")"
    )->findAll();
    $supportDeveloperAnswersModel = null;

    return [
      'malfunction' => $malfunction,
      'dispatcher_confirms' => $dispatcherConfirms,
      'dispatcher_support_questions' => $dispatcherSupportQuestions,
      'dispatcher_support_answers' => $dispatcherSupportAnswers,
      'support_developer_questions' => $supportDeveloperQuestions,
      'support_developer_answers' => $supportDeveloperAnswers,
    ];
  }

  public function getMalfunctions(): string {
    $data = [
      'from' => $this->request->getPost('from'),
      'to' => $this->request->getPost('to'),
      'group_by' => $this->request->getPost('group_by'),
      'timezone_name' => $this->request->getPost('timezone_name'),
    ];

    [
      'from' => $from,
      'to' => $to,
      'timezone_name' => $timezone_name,
      'group_by' => $group_by,
    ] = $data;

    $select = match($group_by) {
      'state_number' => '"malfunction"."obj_mon"."state_number" as "state_number", 
        count("malfunction"."malfunctions"."id") as count, 
        ROUND(avg("malfunction"."malfunctions"."reliability"), 2) as avg_reliability, 
        ROUND(avg("malfunction"."malfunctions"."percent"), 2) as avg_percent
      ',
      'reason' => 'malfunction.reasons.name as reason,
        count("malfunction"."malfunctions"."id") as count,
        ROUND(avg("malfunction"."malfunctions"."reliability"), 2) as avg_reliability,
        ROUND(avg("malfunction"."malfunctions"."percent"), 2) as avg_percent
      ',
      'criticality' => 'malfunction.criticality.name as criticality,
        count("malfunction"."malfunctions"."id") as count,
        ROUND(avg("malfunction"."malfunctions"."reliability"), 2) as avg_reliability,
        ROUND(avg("malfunction"."malfunctions"."percent"), 2) as avg_percent
      ',
      'dispatcher_status' => 'dispatcher_status,
        count("malfunction"."malfunctions"."id") as count,
        ROUND(avg("malfunction"."malfunctions"."reliability"), 2) as avg_reliability,
        ROUND(avg("malfunction"."malfunctions"."percent"), 2) as avg_percent
      ',
      default => 'malfunction.malfunctions.id, 
        malfunction.obj_mon.state_number as state_number,
        malfunction.reasons.name as reason,
        malfunction.criticality.name as criticality,
        begin, end, reliability, percent, dispatcher_status',
    };

    $where = '';
    if ($from && $to && $timezone_name) {
      helper('main_helper');
      $from = timeConvertFromPostgres_helper($from, $timezone_name);
      $to = timeConvertFromPostgres_helper($to, $timezone_name);
      $where = "(malfunction.malfunctions.begin > '$from' AND malfunction.malfunctions.begin < '$to') OR 
    (malfunction.malfunctions.end > '$from' AND malfunction.malfunctions.end < '$to')";
    }

    $model = new Malfunctions();
    $build = $model->select($select   
      )->join('malfunction.obj_mon', 'malfunction.obj_mon.id = malfunction.malfunctions.id_obj'
      )->join('malfunction.reasons', 'malfunction.reasons.id = malfunction.malfunctions.id_reason'
      )->join('malfunction.criticality', 'malfunction.criticality.id = malfunction.malfunctions.id_criticality'
      )->join('LATERAL 
        (SELECT id_malfunction, id_status, ds.status dispatcher_status FROM malfunction.dispatcher_confirms dc
          JOIN  malfunction.dispatcher_statuses ds ON dc.id_status = ds.id
          WHERE malfunction.malfunctions.id = dc.id_malfunction ORDER BY dc.id DESC LIMIT 1
        ) dc', 
      'malfunction.malfunctions.id = dc.id_malfunction' , 'left'
      );
    if ($where) {
      $build->where($where);
    }

    $groupByOptions = [
      ['id' => 'state_number', 'value' => 'объекту мониторинга'],
      ['id' => 'reason', 'value' => 'причине неисправности'],
      ['id' => 'criticality', 'value' => 'критичности'],
      ['id' => 'dispatcher_status', 'value' => 'статусу диспетчера'],
    ];

    if (array_search($group_by, array_column($groupByOptions, 'id')) !== FALSE) {
      $build->groupBy($group_by);
    }
    $rows = $build->findAll();



    $description = [
      'from' => ['label' => 'С', 'type' => 'datetime', 'required' => 'required'],
      'to' => ['label' => 'ПО', 'type' => 'datetime', 'required' => 'required'],
      'group_by' => ['label' => 'Группировка по', 'type' => 'select', 'options' => $groupByOptions, 'required' => ''],
      'timezone_name' => ['type' => 'auto'],
    ];
    
    return $this->get($rows, $data, $description, 'Выявленные неисправности');
  }
}
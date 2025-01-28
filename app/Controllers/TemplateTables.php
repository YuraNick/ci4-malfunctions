<?php

namespace App\Controllers;

use App\Models\Malfunctions;
use App\Models\MonObject;
use App\Models\User;
use App\Models\Сriticality;
use App\Models\Reasons;
use App\Models\DispatcherStatuses;
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
        begin, end, reliability, percent, dc.id_status, dispatcher_status',
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
          WHERE malfunction.malfunctions.id = dc.id_malfunction ORDER BY dc.id LIMIT 1
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
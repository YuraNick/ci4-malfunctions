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
    ] = $data;

    $where = '';
    if ($from && $to && $timezone_name) {
      helper('main_helper');
      $from = timeConvertFromPostgres_helper($from, $timezone_name);
      $to = timeConvertFromPostgres_helper($to, $timezone_name);
      $where = "(malfunction.malfunctions.begin > '$from' AND malfunction.malfunctions.begin < '$to') OR 
    (malfunction.malfunctions.end > '$from' AND malfunction.malfunctions.end < '$to')";
    }


    $model = new Malfunctions();
    $build = $model->select(
      'malfunction.malfunctions.id, 
      malfunction.obj_mon.state_number as state_number,
      malfunction.reasons.name as reason,
      malfunction.criticality.name as criticality,
      begin, end, reliability, percent'    
    )->join('malfunction.obj_mon', 'malfunction.obj_mon.id = malfunction.malfunctions.id_obj'
    )->join('malfunction.reasons', 'malfunction.reasons.id = malfunction.malfunctions.id_reason'
    )->join('malfunction.criticality', 'malfunction.criticality.id = malfunction.malfunctions.id_criticality');
    if ($where) {
      $build->where($where);
    }
    // )->where('malfunction.malfunctions.end <', $data['to']  
    $rows = $build->findAll();

    $group_by = [
      ['id' => 'obj_id', 'value' => 'объекту мониторинга'],
      ['id' => 'reason', 'value' => 'причине неисправности'],
      ['id' => 'criticality', 'value' => 'критичности'],
    ];

    $description = [
      'from' => ['label' => 'С', 'type' => 'datetime', 'required' => 'required'],
      'to' => ['label' => 'ПО', 'type' => 'datetime', 'required' => 'required'],
      'group_by' => ['label' => 'Группировка', 'type' => 'select', 'options' => $group_by, 'required' => ''],
      'timezone_name' => ['type' => 'auto'],
    ];
    
    return $this->get($rows, $data, $description, 'Выявленные неисправности');
  }
}
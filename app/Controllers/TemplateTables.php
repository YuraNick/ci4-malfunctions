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

  public function getMalfunctions() {
    $model = new Malfunctions();
    $rows = $model->select(
      'malfunction.malfunctions.id, 
      malfunction.obj_mon.state_number as state_number,
      malfunction.reasons.name as reason,
      malfunction.criticality.name as criticality,
      begin, end, reliability, percent'    
    )->join('malfunction.obj_mon', 'malfunction.obj_mon.id = malfunction.malfunctions.id_obj'
    )->join('malfunction.reasons', 'malfunction.reasons.id = malfunction.malfunctions.id_reason'
    )->join('malfunction.criticality', 'malfunction.criticality.id = malfunction.malfunctions.id_criticality'
    )->findAll();
    return $this->get($rows, 'Выявленные неисправности');
  }
}
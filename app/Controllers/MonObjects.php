<?php

// namespace App\Controllers;

// use App\Models\MonObject;
// use CodeIgniter\Database\Exceptions\DatabaseException;

// class MonObjects extends BaseController
// {
//   public function index(): string
//   {
//     return 'LoadingData';
//   }

//   public function getMonObjects(): string {
//     $model = new MonObject();
//     $rows = $model->findAll();
//     $columns = [];
//     foreach ($rows as $row) {
//       foreach ($row as $column => $val) {
//         $columns[] = $column;
//       }
//       break;
//     }

//     $html = view('html_elements/header', ['title' => 'Объекты мониторинга (траспортные средства)']);
//     $html .= view('users/all', ['rows' => $rows, 'columns' => $columns]);
//     $html .= view('html_elements/footer_data_table');
//     return $html;
//   }

//   public function addMonObject(): string
//   {
//     $data = [
//       'state_number' => $this->request->getPost('state_number'),
//       'model' => $this->request->getPost('model'),
//     ];

//     $description = [
//       'state_number' => ['label' => 'Гос. номер', 'required' => 'required'],
//       'model' => ['label' => 'Модель', 'required' => 'required'],
//     ];

//     $heading = 'Добавление объекта мониторинга (автомобиля)';

//     $added = 0;
//     $error = '';

//     if ($data['state_number']) {
//       // Inserts data and returns inserted row's primary key
//       $model = new MonObject();
//       try {
//         $added = $model->insert($data);
//       } catch (DatabaseException $e) {
//         $error = $e->getMessage();
//         // echo 'PHP перехватил исключение: ',  $e->getMessage(), "\n";
//       }
//     }
    
//     $html = view('html_elements/header', ['title' => $heading]);
//     $html .= view('add_forms/simple_add.php', [
//       'heading' => $heading,
//       'data' => $data, 
//       'description' => $description
//     ]);
//     if ($added || $error) {
//       $html .= view('html_elements/text_info', ['added' => $added, 'error' => $error]);
//     }
//     $html .= view('html_elements/footer');
//     return $html;
//   } 
// }

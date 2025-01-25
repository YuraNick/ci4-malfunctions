<?php

namespace App\Controllers;

use App\Models\User;
use CodeIgniter\Database\Exceptions\DatabaseException;

class ExampleFill extends BaseController
{
  public function fill(): string
  {
    $info = [];
    $usersCount = $this->fillUsers();
    $info[] = "Добавлено пользователей в таблицу users: $usersCount";
    
    return implode('<br>', $info);
  }

  public function fillUsers() {
    $model = new User();
    $data = $this->getUserDataExample();
    $firstUser = $model->getWhere(['login' => $data[0]['login']])->getResultArray();
    if (count($firstUser)) {
      return 0;
    }
    $model->insertBatch($data);
    return count($data);
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
}

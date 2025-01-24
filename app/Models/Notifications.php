<?php
namespace App\Models;
use CodeIgniter\Model;

class Notifications extends Model
{
  protected $table = 'malfunction.notifications';
  protected $primaryKey = 'id';
  protected $useAutoIncreament = true;

  protected $allowedFields = [
    'id_obj',
    'text',
    'id_malfunction', 
  ];
  // protected array $casts = [
  //   'id'        => 'int',
  //   // 'birthdate' => '?datetime',
  //   // 'name'   => 'text',
  //   // 'email'   => 'text',
  //   // 'role'    => 'text',
  // ];

}
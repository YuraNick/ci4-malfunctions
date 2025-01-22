<?php
namespace App\Models;
use CodeIgniter\Model;

class Malfunctions extends Model
{
  protected $table = 'malfunction.malfunctions';
  protected $primaryKey = 'id';
  protected $useAutoIncreament = true;

  protected $allowedFields = [
    'id_obj',
    'id_reason', 
    'id_criticality', 
    'begin',
    'end',
    'reliability',
    'percent'
  ];
  // protected array $casts = [
  //   'id'        => 'int',
  //   // 'birthdate' => '?datetime',
  //   // 'name'   => 'text',
  //   // 'email'   => 'text',
  //   // 'role'    => 'text',
  // ];

}
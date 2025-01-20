<?php
namespace App\Models;
use CodeIgniter\Model;

class User extends Model
{
  protected $table = 'malfunction.users';
  protected $primaryKey = 'id';
  protected $useAutoIncreament = true;

  protected $allowedFields = ['login','name', 'email', 'role'];
  // protected array $casts = [
  //   'id'        => 'int',
  //   // 'birthdate' => '?datetime',
  //   // 'name'   => 'text',
  //   // 'email'   => 'text',
  //   // 'role'    => 'text',
  // ];

  public function add(array $user) {

  }

  // vendor\codeigniter4\framework\system\Model.php
  // foreach (array_keys($row) as $key) {
  //   // Do not remove the non-auto-incrementing primary key data.
  //   if ($this->useAutoIncrement === false && $key === $this->primaryKey) {
  //       continue;
  //   }

  //   if ($this->db->DBDriver === "Postgre" && $this->useAutoIncrement && $key === $this->primaryKey) {
  //       $row[$key] = 'DEFAULT';
  //       continue;
  //   }
}
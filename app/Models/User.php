<?php
namespace App\Models;
use CodeIgniter\Model;

class User extends Model
{
  protected $table = 'malfunction.users';
  protected $primaryKey = 'id';
  protected $useAutoIncreament = true;

  protected $allowedFields = [
    'login',
    'name', 
    'email',
    'role'
  ];

}
<?php
namespace App\Models;
use CodeIgniter\Model;

class MonObject extends Model
{
  protected $table = 'malfunction.obj_mon';
  protected $primaryKey = 'id';
  protected $useAutoIncreament = true;

  protected $allowedFields = ['state_number','model'];
}
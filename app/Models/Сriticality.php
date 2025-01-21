<?php
namespace App\Models;
use CodeIgniter\Model;

class Сriticality extends Model
{
  protected $table = 'malfunction.criticality';
  protected $primaryKey = 'id';
  protected $useAutoIncreament = true;

  protected $allowedFields = ['name','is_notification'];
}
<?php
namespace App\Models;
use CodeIgniter\Model;

class Notifications extends Model
{
  protected $table = 'malfunction.notifications';
  protected $primaryKey = 'id';
  protected $useAutoIncreament = true;

  protected $allowedFields = [
    'text',
    'id_malfunction', 
  ];

}
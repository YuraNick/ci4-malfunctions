<?php
namespace App\Models;
use CodeIgniter\Model;

class DispatcherConfirms extends Model
{
  protected $table = 'malfunction.dispatcher_confirms';
  protected $primaryKey = 'id';
  protected $useAutoIncreament = true;

  protected $allowedFields = [
    'id_malfunction', 
    'id_user', 
    'id_status', 
    'timestamp',
    'comment', 
  ];
}
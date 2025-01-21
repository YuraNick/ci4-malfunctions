<?php
namespace App\Models;
use CodeIgniter\Model;

class DispatcherStatuses extends Model
{
  protected $table = 'malfunction.dispatcher_statuses';
  protected $primaryKey = 'id';
  protected $useAutoIncreament = true;

  protected $allowedFields = [
    'status', 
  ];
}
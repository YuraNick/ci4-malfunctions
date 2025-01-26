<?php
namespace App\Models;
use CodeIgniter\Model;

class DispatcherSupportQuestions extends Model
{
  protected $table = 'malfunction.dispatcher_support_questions';
  protected $primaryKey = 'id';
  protected $useAutoIncreament = true;

  protected $allowedFields = [
    'id_user', 
    'id_malfunction', 
    'timestamp', 
    'importance', 
    'text', 
  ];
}
<?php
namespace App\Models;
use CodeIgniter\Model;

class DispatcherSupportAnswers extends Model
{
  protected $table = 'malfunction.dispatcher_support_answers';
  protected $primaryKey = 'id';
  protected $useAutoIncreament = true;

  protected $allowedFields = [
    'id_user', 
    'id_question', 
    'timestamp', 
    'lifetime', 
    'text', 
  ];
}
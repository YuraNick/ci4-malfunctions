<?php
namespace App\Models;
use CodeIgniter\Model;

class SupportDeveloperAnswers extends Model
{
  protected $table = 'malfunction.support_developer_answers';
  protected $primaryKey = 'id';
  protected $useAutoIncreament = true;

  protected $allowedFields = [
    'id_user', 
    'id_question', 
    'timestamp', 
    'text', 
  ];
}
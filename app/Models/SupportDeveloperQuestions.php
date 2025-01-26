<?php
namespace App\Models;
use CodeIgniter\Model;

class SupportDeveloperQuestions extends Model
{
  protected $table = 'malfunction.support_developer_questions';
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
<?php
namespace App\Models;
use CodeIgniter\Model;

class NotificationsUsers extends Model
{
  protected $table = 'malfunction.notifications_users';
  protected $primaryKey = 'id';
  protected $useAutoIncreament = true;

  protected $allowedFields = [
    'notifications_id',
    'id_user',
    'is_sended',
  ];

}
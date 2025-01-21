<?php
namespace App\Models;
use CodeIgniter\Model;

class Reasons extends Model
{
  protected $table = 'malfunction.reasons';
  protected $primaryKey = 'id';
  protected $useAutoIncreament = true;

  protected $allowedFields = [
    'name', 
    'is_mileage', 
    'is_fuel_level',
    'is_moto',
    'is_can',
  ];
}
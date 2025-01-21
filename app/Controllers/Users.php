<?php

namespace App\Controllers;

use App\Models\User;
use CodeIgniter\Database\Exceptions\DatabaseException;

class Users extends BaseController
{
    public function index(): string
    {
        return 'LoadingData';
    }

    
}

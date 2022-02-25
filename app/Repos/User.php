<?php

namespace App\Repos;

use App\Interfaces\IUser;

class User extends Base implements IUser
{
    protected $table_name;

    public function __construct($table_name="users")
    {
        parent::__construct($table_name);
        //$this->table_name = $table_name;
    }
}
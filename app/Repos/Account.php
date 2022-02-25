<?php

namespace App\Repos;

use App\Interfaces\IAccount;

class Account extends Base implements IAccount
{
    protected $table_name;

    public function __construct($table_name="accounts")
    {
        parent::__construct($table_name);
        //$this->table_name = $table_name;
    }
}
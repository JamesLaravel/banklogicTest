<?php

namespace App\Repos;


use App\Interfaces\ITransaction;

class Transaction extends Base implements ITransaction
{
    protected $table_name;

    public function __construct($table_name="transactions")
    {
        parent::__construct($table_name);
        //$this->table_name = $table_name;
    }
}
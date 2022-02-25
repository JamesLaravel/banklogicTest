<?php

namespace App\Repos;

use App\Interfaces\IBase;
use Illuminate\Support\Facades\DB;

class Base implements IBase
{
    
    protected $table_name;

    public function __construct($table_name)
    {
        $this->table_name = $table_name;
    }

    public function selectAllPaginate(array $selects)
    {
        return DB::table($this->table_name)->select($selects)->orderByDesc('created_at')->paginate();
    }

    public function insert(array $data)
    {
        return DB::table($this->table_name)->insertGetId($data);
    }

    public function findItem(array $condition, array $selects)
    {
        return DB::table($this->table_name)->where($condition)->select($selects)->first();
    }

    public function updateItem(array $condition, array $data)
    {
        return DB::table($this->table_name)->where($condition)->update($data);
    }

    public function getAll(array $selects)
    {
        return DB::table($this->table_name)->where($selects)->orderByDesc('created_at')->get();
    }

    public function getItems(array $condition, array $selects)
    {
        return DB::table($this->table_name)->where($condition)->select($selects)->orderByDesc('created_at');
    }

    public function deleteItem(array $condition)
    {
        return DB::table($this->table_name)->where($condition)->delete();
    }

}
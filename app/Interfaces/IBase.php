<?php

namespace App\Interfaces;

interface IBase 
{
    public function selectAllPaginate(array $selects);
    public function getAll(array $selects);
    public function getItems(array $condition, array $selects);
    public function insert(array $data);
    public function findItem(array $condition, array $selects);
    public function updateItem(array $condition, array $data);
    public function deleteItem(array $condition);
}
<?php

/**
 * Created by PhpStorm.
 * User: Omer
 * Date: 05/08/2017
 * Time: 16:24
 */
interface IDBConvertable
{
    public static function &GetById(int $id);
    public static function GetByIds(array $ids);
    public function GetId();
    public function GetKeyColumn();
}
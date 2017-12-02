<?php
/**
 * Created by PhpStorm.
 * User: Ohad Shalev
 * Date: 24/11/2017
 * Time: 16:57
 */

interface ILogRead
{
    public function Read(int $rows = 0, DateTime $TimeFrom = null, DateTime $TimeTo = null);
}
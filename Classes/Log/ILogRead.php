<?php
/**
 * Created by PhpStorm.
 * User: Ohad Shalev
 * Date: 24/11/2017
 * Time: 16:57
 */

interface ILogRead
{
    /**
     * @param int $rows
     * @param DateTime|null $TimeFrom
     * @param DateTime|null $TimeTo
     * @return \Log\Message[]
     */
    public function Read(int $rows = 0, DateTime $TimeFrom = null, DateTime $TimeTo = null);
}
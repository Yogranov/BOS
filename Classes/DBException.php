<?php
/**
 * Created by PhpStorm.
 * User: Yogev
 * Date: 18-Nov-17
 * Time: 14:37
 */

namespace BugOrderSystem;

use Log\ELogLevel;

class DBException extends \Exception
{
    public function __construct($message = "", $dumpVar = null, ...$vars) {

        //IsrTG::GetDb()->startTransaction();
        $code = BugOrderSystem::GetDb()->getLastErrno();
        $data["lastQuery"] = BugOrderSystem::GetDb()->getLastQuery();
        $data["lasetErrorCode"] = $code;
        $data["lastError"] = BugOrderSystem::GetDb()->getLastError();

        $trace = debug_backtrace();
        unset($trace[0]);
        //IsrTG::GetDb()->commit();

        $res = \Services::dump($data, false);

        $dumpVarRes = "";
        if ($dumpVar)
            $dumpVarRes = \Services::dump($dumpVar, false);

        for ($i=0; $i < count($vars); $i++) {
            $message = str_replace("{".$i."}", $vars[$i], $message);
        }

        $message = "DB Exception: ".$message;
        BugOrderSystem::GetLog()->Write($message, ELogLevel::ERROR(), array($data, $trace));

        $message = $message."<BR>".$res."<BR>".$dumpVarRes;

        parent::__construct($message, $code, null);
    }
}
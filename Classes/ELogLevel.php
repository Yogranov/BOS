<?php
/**
 * Created by PhpStorm.
 * User: Yogev
 * Date: 07-Nov-17
 * Time: 22:54
 */

namespace BugOrderSystem;

include "Enum.php";

class ELogLevel extends \Enum
{
    const DEBUG = array(100, "דיבג");
    const INFO = array(200, "מידע");
    const NOTICE = array(250, "התראה");
    const WARNING = array(300, "אזהרה");
    const ERROR = array(400, "שגיאה");
    const CRITICAL = array(500, "שגיאה קריטית");
    const ALERT = array(550, "התראה חמורה");
    const EMERGENCY = array(600, "חירום");
}
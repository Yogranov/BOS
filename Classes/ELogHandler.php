<?php
/**
 * Created by PhpStorm.
 * User: Yogev
 * Date: 07-Nov-17
 * Time: 22:58
 */

namespace BugOrderSystem;

include "Enum.php";

class ELogHandler extends \Enum
{
    const File = array(1, "קובץ");
    const Email = array(2, "אימייל");
    const DB = array(3, "בסיס נתונים");
    const SMS = array(4, "הודעה סלולרית");
}
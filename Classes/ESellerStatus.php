<?php
/**
 * Created by PhpStorm.
 * User: Yogev
 * Date: 14-Sep-17
 * Time: 22:55
 */

namespace BugOrderSystem;


class ESellerStatus extends \Enum
{
    const Active = array(1, "פעיל");
    const Fired = array(2, "מפוטר");
}
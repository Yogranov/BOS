<?php
/**
 * Created by PhpStorm.
 * User: Yogev
 * Date: 16-Sep-17
 * Time: 18:26
 */

namespace BugOrderSystem;


class EOrderStatus extends \Enum
{
    const Open = array(1, "הזמנה פתוחה");
    const Ordered = array(2, "הוזמן");
    const Enroute = array(3, "בדרך לסניף");
    const Arrived = array(4, "הגיעה לסניף");
    const No_comment = array(5, "אין מענה");
    const Client_Informed = array(6, "לקוח מעודכן");
    const Delivered = array(7, "נאסף");
    const Aborted = array(8, "מבוטל");
    const Pre_order = array(9, "הזמנה מוקדמת");
}
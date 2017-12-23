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
    const Ordered = array(2, "המוצרים הוזמנו");
    const Enroute = array(3, "המוצרים בדרך");
    const Arrived = array(4, "המוצרים בסניף");
    const No_comment = array(5, "אין מענה");
    const Client_Informed = array(6, "לקוח מעודכן");
    const Delivered = array(7, "ההזמנה נאספה");
    const Aborted = array(8, "ההזמנה מבוטלת");
    const Pre_order = array(9, "הזמנה מוקדמת");
    const Unknown = array(10, "לא ידוע");
}
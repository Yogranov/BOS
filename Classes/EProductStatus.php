<?php
/**
 * Created by PhpStorm.
 * User: Yogev
 * Date: 19-Sep-17
 * Time: 10:28
 */

namespace BugOrderSystem;


class EProductStatus extends \Enum
{
    const Created = array(1, "הזמנה פתוחה");
    const Ordered = array(3, "הוזמן לסניף");
    const Enroute = array(4, "בדרך לסניף");
    const Arrived = array(5, "הגיע לסניף");
    const Client_Informed = array(6, "לקוח מעודכן");
    const Delivered = array(7, "המוצר נאסף");
    const Aborted = array(8, "בוטל");
    const Pre_order = array(9, "הזמנה מוקדמת");
    const Message_Sent = array(10, "הודעה נשלחה ללקוח");
}
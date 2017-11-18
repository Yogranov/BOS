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
    const Created = array(1, "המוצר נוסף להזמנה");
    const Ordered = array(2, "המוצר הוזמן עבור הלקוח");
    const Enroute = array(3, "המוצר בדרכו אל הסניף");
    const Arrived = array(4, "המוצר הגיע אל הסניף");
    const Delivered = array(5, "המוצר נאסף על ידי הלקוח");
}
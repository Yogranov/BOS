<?php
/**
 * Created by PhpStorm.
 * User: Yogev
 * Date: 04-Nov-17
 * Time: 16:07
 */
namespace BugOrderSystem;

require_once("Classes/BugOrderSystem.php");
header('Access-Control-Allow-Origin: *');

$pageMethod = html_entity_decode($_REQUEST['method']);
$pageData = \Services::MultiToArray(html_entity_decode($_REQUEST['data']),"|");
unset($_REQUEST);

$outputData = "";
try {
    switch ($pageMethod) {
        case 'GetChart': list($chartType, , $ClassName, $id, $cut, $sinceDate) = $pageData;
            $shopClass = &Shop::GetById($id);
            $since = new \DateTime($sinceDate);
            switch ($cut) {
                case 'CancelledOrders': $innerData = Order::GetCanceledOrders($shopClass, $since);
                    break;

                case 'ActiveOrders':
                default: $innerData = Order::GetActiveOrders($shopClass);
            }

            // do not touch below!
            $orderCount = array();
            foreach ($innerData as $orderObject) {
                $orderCount[$orderObject->GetStatus()->getValue()]++;
            }
            $outputData = array(
                array("Column", "Value")
            );
            foreach ($orderCount as $countStatus => $count) {
                $outputData[] = array(EOrderStatus::search($countStatus)->getDesc(), $count);
            }
            break;

        default: throw new Exception("invalid API '%1' method!", $pageData, $pageMethod);
    }
} catch(Exception $e) {
    echo $e;
    exit;

} catch(\Exception $e) {
    echo $e;
    exit;
}

echo json_encode($outputData);
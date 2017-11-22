<?php
/**
 * Created by PhpStorm.
 * User: Yogev
 * Date: 19-Sep-17
 * Time: 11:27
 */

namespace BugOrderSystem;

session_start();

require_once "Classes/BugOrderSystem.php";


$shopId = $_SESSION["ShopId"];
if(!isset($shopId)) {
    header("Location: login.php");
}
$shopObject = Shop::GetById($shopId);

//setting header
require_once "Header.php";
$PageTemplate = headerTemplate;
//setting page title
\Services::setPlaceHolder($PageTemplate, "PageTitle", "הזמנות ישנות");
//setting menu bar
$PageTemplate .= headerMenu;
\Services::setPlaceHolder($PageTemplate, "shopName", $shopObject->GetShopName());
\Services::setPlaceHolder($PageTemplate, "oldOrdersClass", "'current'");
///


$PageTemplate .= <<<PAGE
<main>
    <oldorderboard>
    <div class="wrapper">
        <div id="old-order-table" class="responstable" style="margin-top: 30px;">
                <table id="OldOrderBoard" class="table" cellpadding="0" cellspacing="0" style=" font-size: 14px !important;">
                  <thead>
                    <tr>
                        <th>סטאטוס</th>
                        <th>מוכרן</th>
                        <th>הערות</th>
                        <th>ברקוד</th> 
                        <th>מוצר</th>
                        <th>פלאפון</th>
                        <th>שם הלקוח</th>
                        <th>תאריך</th>
                    </tr>
                  </thead>
                  <tbody>
                    {OrderBoard_Table_Template}
                  </tbody>
                </table>
            </div>
        </div>
    </oldorderboard>
</main>
PAGE;
//setting footer
$PageTemplate .= footer;



$OrderBoard_Table_Temlplate = <<<EOF
<tr onclick="document.location = 'vieworder.php?id={orderId}';">
    <td>{orderStatus}</td>
    <td>{orderSellerName}</td>
    <td>{orderRemarks}</td>
    <td>
        <ul>{barcodeTemplate}</ul>
    </td>
    <td>
        <ul>{productTemplate}</ul>
    </td>
    <td>{clientCellPhone}</td>
    <td>{clientName}</td>
    <td>{orderDate}</td>
</tr>
EOF;
$productOrderTemplate_Quantity_More_Then_One = "<li><span style='color: indianred'> {ProductQuantity} X </span>{ProductName}</li>";
$productOrderTemplate_Quantity_One = "<li>{ProductName}</li>";
$productOrderTemplate_Quantity_One_Code = "<li>{ProductCode}</li>";

$shopOrders = Order::GetOldOrders($shopObject);

$orderBoard = (count($shopOrders) > 0) ? "" : "<tr colspan='7'><div id='no-orders-available'>אין הזמנות </div></tr>";
foreach ($shopOrders as $order) {
    $orderBoard .= $OrderBoard_Table_Temlplate;
    \Services::setPlaceHolder($orderBoard, "orderId", $order->GetId());
    \Services::setPlaceHolder($orderBoard, "orderStatus", $order->GetStatus()->getDesc());
    try {
        \Services::setPlaceHolder($orderBoard, "orderSellerName", $order->GetSeller()->GetFullName());
    } catch (\Exception $e) {
        $errorMsg = $e->getMessage();
        \Services::setPlaceHolder($orderBoard, "orderSellerName", "מוכר לא ידוע");
        //\Services::ExceptionToDB($e);
    }
    \Services::setPlaceHolder($orderBoard, "orderRemarks", $order->GetRemarks());
    \Services::setPlaceHolder($orderBoard, "clientCellPhone", $order->GetClient()->GetPhoneNumber());
    \Services::setPlaceHolder($orderBoard, "clientName", $order->GetClient()->GetFullName());
    \Services::setPlaceHolder($orderBoard, "orderDate", $order->GetTimeStamp()->format("d/m/Y"));

    $orderProductString = "";
    foreach ($order->GetOrderProducts() as $orderProduct) {
        if ($orderProduct->GetQuantity() > 1) {
            $orderProductString .= $productOrderTemplate_Quantity_More_Then_One;
            \Services::setPlaceHolder($orderProductString, "ProductQuantity", $orderProduct->GetQuantity());
        } else {
            $orderProductString .= $productOrderTemplate_Quantity_One;
        }
        \Services::setPlaceHolder($orderProductString, "ProductName", $orderProduct->getProductName());
    }
    \Services::setPlaceHolder($orderBoard, "productTemplate", $orderProductString);

    $orderProductCode = "";
    foreach ($order->GetOrderProducts() as $orderProduct) {
            $orderProductCode .= $productOrderTemplate_Quantity_One_Code;

        \Services::setPlaceHolder($orderProductCode, "ProductCode", $orderProduct->GetProductBarcode());
    }
    \Services::setPlaceHolder($orderBoard, "barcodeTemplate", $orderProductCode);

}
\Services::setPlaceHolder($PageTemplate, "OrderBoard_Table_Template", $orderBoard);

echo $PageTemplate;


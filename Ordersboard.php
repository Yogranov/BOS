<?php

/**
 * Created by PhpStorm.
 * User: Yogev
 * Date: 09-Sep-17
 * Time: 17:40
 */
namespace BugOrderSystem;

session_start();
require_once "Classes/BugOrderSystem.php";

if (Constant::SYSTEM_DEBUG) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

$shopId = $_SESSION["ShopId"];
if(!isset($shopId)) {
    header("Location: login.php");
}

$shopObject = &Shop::GetById($shopId);

//setting header
require_once "Header.php";
$PageTemplate = headerTemplate;
//setting page title
\Services::setPlaceHolder($PageTemplate, "PageTitle", "לוח הזמנות");
//setting menu bar
$PageTemplate .= headerBody;
\Services::setPlaceHolder($PageTemplate, "HeaderMenu", headerMenu);
\Services::setPlaceHolder($PageTemplate, "shopName", $shopObject->GetShopName());
\Services::setPlaceHolder($PageTemplate, "ordersBoardClass", "active");
///
$PageTemplate .= <<<PAGE
<main>
  <orderboard>
    <div class="wrapper">
        <div class="responstable">
                <table id="OrderBoard" style="direction: rtl">
                  <thead>
                    <tr>
                        <th>הזמנה</th>
                        <th>תאריך</th>
                        <th>שם הלקוח</th>
                        <th>פלאפון</th>
                        <th>מוצר</th>
                        <th>ברקוד</th>
                        <th>הערות</th>
                        <th>מוכרן</th>
                        <th>סטאטוס</th>  
                    </tr>
                  </thead>
                  <tbody>
                    {OrderBoard_Table_Template}
                  </tbody>
                </table>
        </div>
        {PreOrderBoard_Table}            
  </div>
    </orderboard>
</main>
PAGE;

//setting footer
$PageTemplate .= footer;

$OrderBoard_Table_Temlplate = <<<EOF
<tr data-action="OpenBOSDialog" data-page="vieworder.php" data-dialogTitle="הזמנה {orderId}" data-variables="id={orderId}&ShowHeaderFooter=0">
  <td>{orderId}</td>
  <td>{orderDate}</td>
  <td>{clientName}</td>
  <td>{clientCellPhone}</td>
  <td>
     <ul>{productTemplate}</ul>
  </td>
  <td>
      <ul>{barcodeTemplate}</ul>
  </td>
  <td>{orderRemarks}</td>
  <td>{orderSellerName}</td>
  <td class="{flashClass}" style="{rowClass} !important; font-weight: 600;"><span>{orderStatus}</span></td>    
</tr>
EOF;

$productOrderTemplate_Quantity_More_Then_One = "<li>{ProductName}<span style='color: indianred'> {ProductQuantity} X </span></li>";
$productOrderTemplate_Quantity_One = "<li style='{productColor}'>{ProductName}</li>";

$productOrderTemplate_Quantity_One_Code = "<li>{ProductCode}</li>";

$shopOrders = Order::GetActiveOrders($shopObject);

$orderBoard = (count($shopOrders) > 0) ? "" : "<tr colspan='7'><div id='no-orders-available'>אין הזמנות </div></tr>";

foreach ($shopOrders as $order) {
    $orderBoard .= $OrderBoard_Table_Temlplate;

    if (array_key_exists($order->GetStatus()->getValue(),Constant::ORDER_STATUS_STYLE)) {
        \Services::setPlaceHolder($orderBoard, "rowClass", Constant::ORDER_STATUS_STYLE[$order->GetStatus()->getValue()][0]);
        \Services::setPlaceHolder($orderBoard, "flashClass", Constant::ORDER_STATUS_STYLE[$order->GetStatus()->getValue()][1]);
    }
    else {
        \Services::setPlaceHolder($orderBoard, "rowClass", Constant::ORDER_STATUS_STYLE["default"][0]);
        \Services::setPlaceHolder($orderBoard, "flashClass", Constant::ORDER_STATUS_STYLE["default"][1]);
    }


    \Services::setPlaceHolder($orderBoard, "orderId", $order->GetId());
    \Services::setPlaceHolder($orderBoard, "orderStatus", $order->GetStatus()->getDesc());
    try {
        \Services::setPlaceHolder($orderBoard, "orderSellerName", $order->GetSeller()->GetFullName());
    } catch (\Exception $e) {
        $errorMsg = $e->getMessage();
        \Services::setPlaceHolder($orderBoard, "orderSellerName", "מוכר לא ידוע");

    }    \Services::setPlaceHolder($orderBoard, "orderRemarks", $order->GetRemarks());
    \Services::setPlaceHolder($orderBoard, "clientCellPhone", substr_replace(substr_replace($order->GetClient()->GetPhoneNumber(), '-' , 3,0),'-',7,0));
    \Services::setPlaceHolder($orderBoard, "clientName", $order->GetClient()->GetFullName());
    \Services::setPlaceHolder($orderBoard, "orderDate", $order->GetTimeStamp()->format("d/m"));

    $orderProductString = "";
    foreach ($order->GetOrderProducts() as $orderProduct) {
        if ($orderProduct->GetQuantity() > 1) {
            $orderProductString .= $productOrderTemplate_Quantity_More_Then_One;
            \Services::setPlaceHolder($orderProductString, "ProductQuantity", $orderProduct->GetQuantity());

            //Add color to product name if arrived
            if(count($order->GetOrderProducts()) > 1)
                \Services::setPlaceHolder($orderProductString, "productColor", Constant::PRODUCTS_STATUS_STYLE[$orderProduct->GetStatus()->getValue()[0]]);


        } else {
            $orderProductString .= $productOrderTemplate_Quantity_One;

            //Add color to product name if arrived
            if(count($order->GetOrderProducts()) > 1)
                    \Services::setPlaceHolder($orderProductString, "productColor", Constant::PRODUCTS_STATUS_STYLE[1][0]);
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

///////////////////////////////////////////////////////////////////////////////////////////////////////

$preOrderTemplate = <<<PreOrder
<div class="pre-order-title">הזמנות מוקדמות</div>
    <div class="responstable">
        <table id="PreOrderBoard" cellpadding="0" cellspacing="0" style="direction: rtl">
          <thead>
            <tr>
                <th>הזמנה</th>
                <th>תאריך</th>
                <th>שם הלקוח</th>
                <th>פלאפון</th>
                <th>מוצר</th>
                <th>ברקוד</th>
                <th>הערות</th>
                <th>מוכרן</th>
                <th>סטאטוס</th>    
            </tr>
          </thead>
          <tbody>
            {PreOrderBoard_Table_Template}
          </tbody>
        </table>
    </div>
PreOrder;


$PreOrderBoard_Table_Temlplate = <<<EOF
<tr data-action="OpenBOSDialog" data-page="vieworder.php" data-dialogTitle="הזמנה {orderId}" data-variables="id={orderId}&ShowHeaderFooter=0">
    <td>{orderId}</td>
    <td>{orderDate}</td>
    <td>{clientName}</td>
    <td>{clientCellPhone}</td>
    <td>
        <ul>{productTemplate}</ul>
    </td>
    <td>
        <ul>{barcodeTemplate}</ul>
    </td>
    <td>{orderRemarks}</td>
    <td>{orderSellerName}</td>
    <td>{orderStatus}</td>
</tr>
EOF;
$productPreOrderTemplate_Quantity_More_Then_One = "<li><span style='color: indianred'> {ProductQuantity} X </span>{ProductName}</li>";
$productPreOrderTemplate_Quantity_One = "<li>{ProductName}</li>";
$productPreOrderTemplate_Quantity_One_Code = "<li>{ProductCode}</li>";

$shopPreOrders = Order::GetPreOrders($shopObject);

$PreOrderBoard = (count($shopPreOrders) > 0) ? "" : "<tr colspan='7'><div id='no-orders-available'>אין הזמנות </div></tr>";

foreach ($shopPreOrders as $order) {
    $PreOrderBoard .= $PreOrderBoard_Table_Temlplate;

    \Services::setPlaceHolder($PreOrderBoard, "orderId", $order->GetId());
    \Services::setPlaceHolder($PreOrderBoard, "orderStatus", $order->GetStatus()->getDesc());
    try {
        \Services::setPlaceHolder($PreOrderBoard, "orderSellerName", $order->GetSeller()->GetFullName());
    } catch (\Exception $e) {
        \Services::setPlaceHolder($PreOrderBoard, "orderSellerName", "מוכר לא ידוע");

    }
    \Services::setPlaceHolder($PreOrderBoard, "orderRemarks", $order->GetRemarks());
    \Services::setPlaceHolder($PreOrderBoard, "clientCellPhone", $order->GetClient()->GetPhoneNumber());
    \Services::setPlaceHolder($PreOrderBoard, "clientName", $order->GetClient()->GetFullName());
    \Services::setPlaceHolder($PreOrderBoard, "orderDate", $order->GetTimeStamp()->format("d/m"));

    $preOrderProductString = "";
    foreach ($order->GetOrderProducts() as $orderProduct) {
        if ($orderProduct->GetQuantity() > 1) {
            $preOrderProductString .= $productPreOrderTemplate_Quantity_More_Then_One;
            \Services::setPlaceHolder($preOrderProductString, "ProductQuantity", $orderProduct->GetQuantity());
        }
        else {
            $preOrderProductString .= $productPreOrderTemplate_Quantity_One;
        }
        \Services::setPlaceHolder($preOrderProductString, "ProductName", $orderProduct->getProductName());
    }
    \Services::setPlaceHolder($PreOrderBoard, "productTemplate", $preOrderProductString);

    $preOrderProductCode = "";
    foreach ($order->GetOrderProducts() as $orderProduct) {
        $preOrderProductCode .= $productPreOrderTemplate_Quantity_One_Code;
        \Services::setPlaceHolder($preOrderProductCode, "ProductCode", $orderProduct->GetProductBarcode());
    }

    \Services::setPlaceHolder($PreOrderBoard, "barcodeTemplate", $preOrderProductCode);
}
\Services::setPlaceHolder($preOrderTemplate, "PreOrderBoard_Table_Template", $PreOrderBoard);

if(count($shopPreOrders)) {
    \Services::setPlaceHolder($PageTemplate, "PreOrderBoard_Table", $preOrderTemplate);
} else {
    \Services::setPlaceHolder($PageTemplate, "PreOrderBoard_Table", "");

}


echo $PageTemplate;


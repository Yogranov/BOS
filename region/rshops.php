<?php
/**
 * Created by PhpStorm.
 * User: Yogev
 * Date: 02-Nov-17
 * Time: 10:08
 */
namespace BugOrderSystem;
require_once "../Classes/BugOrderSystem.php";

session_start();

$regionId = $_SESSION["RegionId"];
if(!isset($regionId)) {
    header("Location: ../login.php");
}

$regionObj = Region::GetById($regionId);

//setting header
require_once "Rheader.php";
$PageTemplate = headerTemplate;
//setting page title
\Services::setPlaceHolder($PageTemplate, "PageTitle", "ראשי");
//setting menu bar
$PageTemplate .= headerMenu;
\Services::setPlaceHolder($PageTemplate, "regionName", $regionObj->GetManager()->GetFirstName());
\Services::setPlaceHolder($PageTemplate, "shopsPageClass", "active");
///

$PageTemplate .= <<<INDEX
<main>
    <div class="wrapper" style="direction: rtl">
    <h1 style="direction: rtl">סניפים</h1>
        <div id="region-shops-list">
        {shopBlock}
        </div>
    </div>  
</main>
INDEX;


$shopBlock = "";
foreach ($regionObj->GetShops() as $shop) {

    $numSellers = count($shop->GetSellers());
    $arrivedOrders = count(Order::GetArrivedOrders($shop,new \DateTime("-1 month")));
    $canceledOrders = count(Order::GetCanceledOrders($shop,new \DateTime("-1 month")));

    $chartsArray = array();

    //charts//
    $chartsArray[] = array(
        array("Pie", "", "Shop", $shop->GetId(), "ActiveOrders", ""),
        json_encode(array("width" => 250,"is3D" => "true", "pieSliceText" => "label", "pieHole" => "0.4"))
    );

    $since = new \DateTime("-1 month");
    $chartsArray[] = array(
        array("Clock", "", "Shop", $shop->GetId(), "CancelledOrders", $since->format("Y-m-d")),
        json_encode(array("width" => 250,"max" => 20, "greenFrom" => 0, "greenTo" => 5, "yellowFrom" => 12, "yellowTo" => 17, "redFrom" => 17, "redTo" => 20, "minorTicks" => 5, "majorTicks" => 10))
    );

    $shopBlock .= <<<SHOP
    <div class="region-shop" style="direction: rtl">
        <div class="region-shop-details">
            <h2 style="margin: 0 0">{$shop->GetShopName()}</h2><br>
            <span>מנהל: </span>{$shop->GetManager()->GetFullName()}<br>
            <span>טלפון: </span>{$shop->GetPhoneNumber()} <br>
            <span>מספר עובדים: </span>{$numSellers} <br>
            <span>נאספו החודש:</span> {$arrivedOrders}<br>
            <span>בוטלו החודש: </span>{$canceledOrders}<br><br>
            <div class="order-button" style="width: 80px" onclick="document.location = 'reditshop.php?id={$shop->GetId()}';"> ערוך חנות </div>
            <div class="order-button" style="width: 80px; margin-right: 5px; background-color: #eb5756;" onclick="document.location = 'rorderboard.php?shopid={$shop->GetId()}';"> לוח הזמנות </div>

        </div>
    {ChartsPlaceHolder}
    </div>
SHOP;

    $chartText = "";
    foreach ($chartsArray as $chartData) {
        list($innerData, $jsonOptions) = $chartData;
        $url = Constant::API_URL."?method=GetChart&data=".\Services::ArrayToMulti($innerData,"|");
        $chartText .= "<div Id='chart_{$innerData[3]}_{$innerData[0]}' class='Chart' data-chart-type='{$innerData[0]}' data-url='$url' data-title='{$innerData[1]}' data-options='{$jsonOptions}'></div>";
    }
    \Services::setPlaceHolder($shopBlock, "ChartsPlaceHolder", $chartText);

}


\Services::setPlaceHolder($PageTemplate,"shopBlock",$shopBlock);


//setting footer
$PageTemplate .= footer;



echo $PageTemplate;

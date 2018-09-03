<?php
/**
 * Created by PhpStorm.
 * User: Yogev
 * Date: 26-Sep-17
 * Time: 09:26
 */

namespace BugOrderSystem;

require_once "Classes/BugOrderSystem.php";


$allShops = BugOrderSystem::GetDB()->get("shops",null, "Id");


foreach ($allShops  as $oneShop) {

    $shopObject = &Shop::GetById($oneShop["Id"]);
    $shopOrders = Order::GetActiveOrders($shopObject);

    foreach ($shopOrders as $order) {
        if($order->GetStatus()->getValue() == 1){

                $Subject = "נא לעדכן הזמנה {$order->GetId()} בהקדם! ";
                $SellerMessage = Constant::EMAIL_SELLER_NEED_TO_ORDER;
                \Services::setPlaceHolder($SellerMessage,"Name",$order->GetShop()->GetShopName());
                \Services::setPlaceHolder($SellerMessage,"OrderID", $order->GetId());
                \Services::setPlaceHolder($SellerMessage,"ClientName", $order->GetClient()->GetFullName());
                $ShopMessage = Constant::EMAIL_SHOP_NEED_TO_ORDER;
                \Services::setPlaceHolder($ShopMessage,"Name",$order->GetShop()->GetShopName());
                \Services::setPlaceHolder($ShopMessage,"OrderId", $order->GetId());
                \Services::setPlaceHolder($ShopMessage,"ClientName", $order->GetClient()->GetFullName());
                \Services::setPlaceHolder($ShopMessage,"SellerName", $order->GetSeller()->GetFullName());

                //sending emails


                $order->GetSeller()->SendEmail($SellerMessage, $Subject);
                $order->GetShop()->SendEmail($ShopMessage, $Subject);
        }

    }


}


?>
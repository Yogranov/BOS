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

$shopId = $_SESSION["ShopId"];
if(!isset($shopId)) {
    header("Location: login.php");
}

$shopObj = &Shop::GetById($shopId);


//setting header
require_once "Header.php";
$PageTemplate = headerTemplate;
//setting page title
\Services::setPlaceHolder($PageTemplate, "PageTitle", "הזמנה חדשה");
//setting menu bar
$PageTemplate .= headerBody;
$data = "";
if ((is_bool($_GET["ShowHeaderFooter"]) && $_GET["ShowHeaderFooter"] == 1) || !isset($_GET["ShowHeaderFooter"])) {
    //setting menu bar
    $data = headerMenu;
    \Services::setPlaceHolder($data, "shopName", $shopObj->GetShopName());
    \Services::setPlaceHolder($data, "newOrdersClass", "active");
}
\Services::setPlaceHolder($PageTemplate, "HeaderMenu", $data);
///

$PageTemplate .= <<<PAGE
<main>
    <div class="container">
        <div class="row centered-form" id="new-order">
            <div class="col-12">
                <span><h3> הזמנה חדשה </h3></span>
            </div>
            <div class="col-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <form method="post" id="CreateOrderForm" role="form" style="font-size: 18px">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                    <label for="form-PhoneNumber">מספר טלפון</label>
                                    <input type="text" class="form-control" id="form-PhoneNumber" name="phonenumber" placeholder="מספר טלפון"  pattern=".{10,}" maxlength="10" title="10 ספרות" onkeyup="this.value=this.value.replace(/[^\d]/,'');" required>
                                    </div>                                
                                </div>
                                
                                <div class="col-xs-6 col-sm-6 col-md-6">
                                    <div class="form-group">
                                        <label for="form-LastName">שם משפחה</label>
                                        <span><input type="text" class="form-control" id="form-LastName" name="lastname" placeholder="שם משפחה" required></span>
                                    </div>
                                </div>
                                
                                
                                <div class="col-xs-6 col-sm-6 col-md-6">
                                    <div class="form-group">
                                      <label for="form-FirstName">שם פרטי</label>
                                      <span><input type="text" class="form-control" id="form-FirstName" name="firstname" placeholder="שם פרטי" required></span>
                                    </div>                                
                                </div>
                                
                                <div class="col-sm-12">
                                    <div class="form-check">
                                        <input type="checkbox" name="wantsemail" id="form-checkwantsemails" style="cursor: pointer" onclick="emailsClick()" value="1">
                                        <label for="form-checkwantsemails">עדכונים באימייל</label>
                                    </div>
                                </div>
                                
                                <div class="col-sm-12">
                                   <div id="clientwantsemails" class="form-group">
                                        <label for="form-Email">אימייל</label>
                                        <span><input type="text" class="form-control" id="form-Email" name="email" placeholder="דואר אלקטרוני"></span>
                                    </div>
                                </div>
                                
                               <div class="col-sm-12">
                                  <hr>
                               </div>
                                 <!-- End of client info -->
                                
                               <div class="col-sm-8">
                                    <div class="form-group">
                                        <label for="form-remarks">הערות להזמנה</label>
                                        <input type="text" class="form-control" id="form-remarks" name="remarks" placeholder="הערות עבור ההזמנה">
                                    </div>
                               </div>
                                
                               <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="form-seller">מוכרן</label>
                                        <select class="form-control" name="seller" id="form-seller" required><br>
                                             {sellerSelect}
                                        </select>
                                    </div>
                               </div>
                                
                               <div class="col-sm-12">
                                  <hr>
                               </div>
                                <!--End of order info-->
                                
                                
                                
                                <div class="row">
                                    <div id="OrderProducts" class="col-sm-12" style="display:none">
                                        <table class="table table-striped" style="font-size: 14px">
                                            <thead>
                                                <th>שם מוצר</th>
                                                <th>ברקוד</th>
                                                <th>כמות</th>
                                                <th>הערות</th>
                                                <th></th>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                
                                <div class="row" style="width: 98%; margin-right: 5px">
                                    <div id="AddNewProduct" class="col-sm-12">
                                        <div class="row" id="showHideNewProductForm" style="display:none">
                                            <div class="col-sm-12">
                                                <button type="button" class="btn btn-basic-improved2">
                                                    <span class="glyphicon glyphicon-plus"></span>
                                                    <span>הוסף מוצר חדש</span>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="row" id="NewProductData">
                                            <div class="col-sm-2">
                                                <button type="button" id="AddProductButton" class="btn shadow">
                                                    <span class="glyphicon glyphicon-plus"></span>
                                                    <span>&nbsp;הוסף מוצר</span>
                                                </button>
                                            </div>
                                            <div class="col-sm-10">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="row">
                                                            <div class="col-sm-2">
                                                                    <div class="form-group">
                                                                          <label for="form-product-quantity">כמות</label>
                                                                          <input type="text" class="form-control" id="form-product-quantity" name="quantity" value="1" onkeyup="this.value=this.value.replace(/[^\d]/,'')">
                                                                    </div>
                                                            </div>   
                                                                
                                                            <div class="col-sm-5">
                                                                    <div class="form-group" id="productBarcode">
                                                                          <label for="form-product-barcode">ברקוד</label>
                                                                          <input type="text" class="form-control" id="form-product-barcode" name="productbarcode" placeholder="ברקוד" onkeyup="this.value=this.value.replace(/[^\d]/,'')">
                                                                    </div>
                                                            </div>    
                                                                
                                                            <div class="col-sm-5">
                                                                    <div class="form-group" id="productName">
                                                                         <label for="form-product-name">שם המוצר</label>
                                                                         <input type="text" class="form-control" id="form-product-name" name="productname" placeholder="שם המוצר">
                                                                    </div>
                                                            </div>
                                                        </div>   
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="form-group">
                                                             <label for="form-product-remarks">הערות למוצר</label>
                                                             <input type="text" class="form-control" id="form-product-remarks" name="productremarks" placeholder="הערות עבור המוצר">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div> 
                                </div>
                            </div>
                            <input type="submit" id="CreateOrderButton" style="margin-top: 10px" value="צור הזמנה" name="neworder" class="btn btn-info btn-block" disabled>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
PAGE;

//setting footer
if ((is_bool($_GET["ShowHeaderFooter"]) && $_GET["ShowHeaderFooter"] == 0) || !isset($_GET["ShowHeaderFooter"]))
    $PageTemplate .= footer;

//setting sellers list
$orderSellersString = "";
foreach ($shopObj->GetActiveSellers() as $sellerId => $sellerObj) {
    $orderSellersString .= "<option value='".$sellerId."' ";
    $orderSellersString .= ">".$sellerObj->GetFullName()."</option>";
}
\Services::setPlaceHolder($PageTemplate, "sellerSelect", $orderSellersString);
/////

//Take form filed and make them variable.
if(isset($_POST['neworder']))  {
    try {
        $client_first_name = $_REQUEST['firstname'];
        $client_last_name = $_REQUEST['lastname'];
        $client_phone_number = $_REQUEST['phonenumber'];

        $client_wants_emails = 0;
        if (isset($_REQUEST['wantsemail']))
            $client_wants_emails = 1;

        $client_email = $_REQUEST['email'];
        $order_seller = $_REQUEST['seller'];
        $order_remarks = $_REQUEST['remarks'];

        //products
        preg_match_all("/product_[0-9a-z]+_[A-Z]+/i", \Services::dump(\Services::ArrayToMulti(array_keys($_REQUEST), "\N")), $matches);
        $NewProductArray = array();
        foreach ($matches[0] as $productRequestName) {
            list(, $barcode, $property) = \Services::MultiToArray($productRequestName, "_");
            $NewProductArray[$barcode][$property] = $_REQUEST[$productRequestName];
        }

        if (!empty($client_first_name) && !empty($client_last_name) && !empty($client_phone_number) && !empty($order_seller) && count($NewProductArray) > 0) {
            //starting create order//
            //create client//
            $clientId = Client::isPhoneExist($client_phone_number);
            if ($clientId == False)
                $NewClientObj = &Client::Add($client_first_name, $client_last_name, $client_phone_number, $client_email, $client_wants_emails);
            else
                $NewClientObj = &Client::GetById($clientId);

            //create order//
            $orderObject = &Order::Add($NewClientObj, Shop::GetById($shopId), Seller::GetById($order_seller), $order_remarks);

            //Add products to order.
            foreach ($NewProductArray as $productBarcode => $productArray) {
                $productObject = &Products::Add($productBarcode, $NewProductArray[$productBarcode]["Name"]);
                $orderObject->AddOrderProduct($productObject, $NewProductArray[$productBarcode]["Quantity"], $NewProductArray[$productBarcode]["Remark"]);
            }
            $orderProductsArray = $orderObject->GetOrderProducts();

            if (count($orderProductsArray) > 0) {
                //Send order summery to client
                if ($NewClientObj->IsWantEmail() && $NewClientObj->GetEmail() !== "") {
                    $orderSummery = Constant::EMAIL_CLIENT_SUMMERY_ORDER;
                    $encode = base64_encode($orderObject->GetShop()->GetId() . "_" . $orderObject->GetId() . "_" . $orderObject->GetTimeStamp()->format("U"));

                    \Services::setPlaceHolder($orderSummery, "OrderId", $orderObject->GetId());
                    \Services::setPlaceHolder($orderSummery, "ClientName", $orderObject->GetClient()->GetFirstName());
                    \Services::setPlaceHolder($orderSummery, "StatusCheckURL", $encode);
                    \Services::setPlaceHolder($orderSummery, "OrderDate", $orderObject->GetTimeStamp()->format("d/m/y H:m"));
                    \Services::setPlaceHolder($orderSummery, "ShopName", $orderObject->GetShop()->GetShopName());
                    \Services::setPlaceHolder($orderSummery, "Address", $orderObject->GetShop()->GetLocation());
                    \Services::setPlaceHolder($orderSummery, "Seller", $orderObject->GetSeller()->GetFirstName());
                    \Services::setPlaceHolder($orderSummery, "PhoneNumber", $orderObject->GetShop()->GetPhoneNumber());
                    \Services::setPlaceHolder($orderSummery, "ShopName", $orderObject->GetShop()->GetShopName());

                    $NewClientObj->SendEmail($orderSummery, "סיכום הזמנה");
                }

                header("Location: Ordersboard.php");
            }
            else {
                //Todo: need to remove the order
            }

        } else {
            echo "אנא מלא את כל השדות הנדרשים";
        }
    } catch (\Throwable $e) {
        echo $e->getMessage();
    }
}

echo $PageTemplate;

?>
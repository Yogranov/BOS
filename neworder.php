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
$PageTemplate .= headerMenu;
\Services::setPlaceHolder($PageTemplate, "shopName", $shopObj->GetShopName());
\Services::setPlaceHolder($PageTemplate, "newOrdersClass", "active");
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
                        <form method="post" role="form" style="font-size: 18px">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                    <label for="form-PhoneNumber">מספר טלפון</label>
                                    <input type="text" class="form-control" id="form-PhoneNumber" name="phonenumber" placeholder="מספר טלפון"  pattern=".{10,}" maxlength="10" title="10 ספרות" onkeyup="this.value=this.value.replace(/[^\d]/,''); autofill();" required>
                                    </div>                                
                                </div>
                                
                                <div class="col-xs-6 col-sm-6 col-md-6">
                                    <div class="form-group">
                                        <label for="form-LastName">שם משפחה</label>
                                        <input type="text" class="form-control" id="form-LastName" name="lastname" placeholder="שם משפחה" required>
                                    </div>
                                </div>
                                
                                
                                <div class="col-xs-6 col-sm-6 col-md-6">
                                    <div class="form-group">
                                      <label for="form-FirstName">שם פרטי</label>
                                      <input type="text" class="form-control" id="form-FirstName" name="firstname" placeholder="שם פרטי" required>
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
                                        <input type="text" class="form-control" id="form-Email" name="email" placeholder="דואר אלקטרוני">
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
                                
                                <div class="col-sm-2">
                                        <div class="form-group">
                                              <label for="form-product-quantity">כמות</label>
                                              <input type="text" class="form-control" id="form-product-quantity" name="quantity" value="1" onkeyup="this.value=this.value.replace(/[^\d]/,'')" required>
                                        </div>
                                </div>   
                                    
                                <div class="col-sm-5">
                                        <div class="form-group">
                                              <label for="form-product-barcode">ברקוד</label>
                                              <input type="text" class="form-control" id="form-product-barcode" name="productbarcode" placeholder="ברקוד" onkeyup="this.value=this.value.replace(/[^\d]/,'')" required>
                                        </div>
                                </div>    
                                    
                                <div class="col-sm-5">
                                        <div class="form-group">
                                             <label for="form-product-name">שם המוצר</label>
                                             <input type="text" class="form-control" id="form-product-name" name="productname" placeholder="שם המוצר" required>
                                        </div>
                                </div>   
                                
                                <div class="col-sm-12">
                                    <div class="form-group">
                                         <label for="form-product-remarks">הערות למוצר</label>
                                         <input type="text" class="form-control" id="form-product-remarks" name="productremarks" placeholder="הערות עבור המוצר">
                                    </div>
                                </div>
                                   
                            </div>
                            <input type="submit" value="צור הזמנה" name="neworder" class="btn btn-info btn-block">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<script type="text/javascript">
    //auto complete client
  function autofill() {
      var email = 0;
      var phoneNumber = $("#form-PhoneNumber").val();
      $.ajax({
          url: 'auto-fill.php',
          data: 'phoneNumber='+phoneNumber,
          success: function(data){
              var json = data,
                  obj = JSON.parse(json);
              $("#form-FirstName").val(obj.FirstName);
              $("#form-LastName").val(obj.LastName);
              $("#form-Email").val(obj.Email);
              if(obj.Email) {
                   $('input[name=wantsemail]').attr('checked', true);
                   document.getElementById("clientwantsemails").className = "open";
              } else {
                     $('input[name=wantsemail]').attr('checked', false);
                    document.getElementById("clientwantsemails").className = "form-group";
              }

          }
      })
  }
</script>
<script src="js/main.js"></script>
PAGE;

//setting footer
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
        $client_first_name = $_POST['firstname'];
        $client_last_name = $_POST['lastname'];
        $client_phone_number = $_POST['phonenumber'];

        $client_wants_emails = 0;
        if (isset($_POST['wantsemail']))
            $client_wants_emails = 1;

        $client_email = $_POST['email'];
        $product_name = $_POST['productname'];
        $product_barcode = $_POST['productbarcode'];
        $product_quantity = $_POST['quantity'];
        $order_seller = $_POST['seller'];
        $client_remarks = $_POST['remarks'];
        $client_product_remarks = $_POST['productremarks'];

        if (!empty($client_first_name) && !empty($client_last_name) && !empty($client_phone_number)
            && !empty($product_name) && !empty($product_barcode) && !empty($order_seller)) {
            //starting create order//
            //create client//
            $clientId = Client::isPhoneExist($client_phone_number);
            if (empty($clientId)) {
                $newClient = Client::Add($client_first_name, $client_last_name, $client_phone_number, $client_email, $client_wants_emails);

                $clientId = $newClient->GetId();

            }

            //create order//
            if (!is_integer($clientId))
                throw new Exception("לא ניתן לבצע הזמנה - בעיית שיוך לקוח {0} להזמנה!", null, $clientId);


            $orderObject = Order::Add(
                array("ClientId" => $clientId,
                    "ShopId" => $shopId,
                    "SellerId" => $order_seller,
                    "Remarks" => $client_remarks)
            );

            //Add products to order.
            $orderProductsObject = new OrderProducts($orderObject->GetId(), $product_name, $product_barcode, $client_product_remarks, $product_quantity);
            if ($orderProductsObject) {

                //Send order summery to client
                if (!empty($client_email)) {
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

                    //set client object
                    $clientObj = Client::GetById($clientId);

                    $clientObj->SendEmail($orderSummery, "סיכום הזמנה");

                }
                $locationToorder = $orderObject->GetId();
                header("Location: vieworder.php?id=$locationToorder");

            }

        } else {
            echo "אנא מלא את כל השדות הנדרשים";
        }
    } catch (\Throwable $e) {
        $errorMsg = $e->getMessage();
        echo $errorMsg;
    }
}

echo $PageTemplate;

?>
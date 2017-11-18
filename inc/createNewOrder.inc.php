<?php

/**
 * Created by PhpStorm.
 * User: Yogev
 * Date: 09-Sep-17
 * Time: 17:40
 */
namespace BugOrderSystem;

require('core.inc.php');
?>


<form class="new-order" method="post">
            <center>הזמנה חדשה</center>
    <br>
    פלאפון<br>
    <input type="text" name="phonenumber" id="PhoneNumber" minlength="10" maxlength="10" onkeyup="this.value=this.value.replace(/[^\d]/,''); autofill();" required><br>

    שם פרטי<br>
                    <input type="text" name="firstname" id="FirstName" required><br>
    שם משפחה<br>
    <input type="text" name="lastname" id="LastName" required><br>
    <label for="checkwantsemails"> מעוניין לקבל עדכונים במייל</label>
                        <input type="checkbox" id="checkwantsemails" name="wantsemail" style="cursor: pointer" onclick="emailsClick()" ><br><br>

                        <div id="clientwantsemails">
                           אימייל<br>
                    <input type="text" name="email" placeholder="להסביר שזה פרטי ושזאת לא מערכת של באג" id="Email">
                        </div>


                        :הערות להזמנה<br>
                        <input type="text" name="remarks"><br>
    :מוכרן<br>
    <select  name="seller" required><br>

        <?php
        $shopObj = &Shop::GetById($shopId);
        foreach ($shopObj->GetActiveSellers() as $sellerId => $sellerObj) {
            print "<option value='{$sellerId}'>{$sellerObj->GetFullName()}</option>";
        }

        ?>

    </select>
    <br>
    <hr>
    <br>
                        שם המוצר<br>
                    <input type="text" name="productname" required><br>
                        ברקוד<br>
                    <input type="text" name="productbarcode" onkeyup="this.value=this.value.replace(/[^\d]/,'')" required><br>
                        כמות<br>
                    <input type="text" name="quantity" value="1" onkeyup="this.value=this.value.replace(/[^\d]/,'')" required><br>

    <br>
    :הערות למוצר
                    <input type="text" name="productremarks"><br>
                    <br>
                    <button type="submit" name="neworder">צור הזמנה</button>

    <br>
</form>





<?php

//Take form filed and make them variable.

if(isset($_POST['neworder']))  {

   $client_first_name = $_POST['firstname'];
   $client_last_name = $_POST['lastname'];
   $client_phone_number = $_POST['phonenumber'];
    
    if(!isset($_POST['wantsemail'])) {
        $client_wants_emails = "0";
    } else {
        $client_wants_emails = "1";
    }
    
   $client_email = $_POST['email'];
   $product_name = $_POST['productname'];
   $product_barcode = $_POST['productbarcode'];
   $product_quantity = $_POST['quantity'];
   $order_seller = $_POST['seller'];
   $client_remarks = $_POST['remarks'];
   $client_product_remarks = $_POST['productremarks'];

    if(!empty($client_first_name) && !empty($client_last_name) && !empty($client_phone_number)
    && !empty($product_name) && !empty($product_barcode) && !empty($order_seller)) {

        //starting create order//
        //create client//
        $clientId = Client::isPhoneExist($client_phone_number);
        if (empty($clientId)) {
            $newClient = Client::Add(
                array(
                    "FirstName" => $client_first_name,
                    "LastName" => $client_last_name,
                    "PhoneNumber" => $client_phone_number,
                    "Email" => $client_email,
                    "ClientWantsMails" => $client_wants_emails
                )
            );

            $clientId = $newClient->GetId();

        }

        //create order//
        if (!is_integer($clientId))
            throw new Exception("לא ניתן לבצע הזמנה - בעיית שיוך לקוח {0} להזמנה!", null, $clientId);

        try {
            $orderObject = Order::Add(
                array("ClientId" => $clientId,
                    "ShopId" => $shopId,
                    "SellerId" => $order_seller,
                    "Remarks" => $client_remarks)
            );

            //Add products to order.
            $orderProductsObject = new OrderProducts($orderObject->GetId(), $product_name, $product_barcode, $client_product_remarks, $product_quantity);
            if ($orderProductsObject) {
                $locationToorder = $orderObject->GetId();
                header("Location:vieworder.php?id=$locationToorder");
            }

        } catch (Exception $e) {
            echo $e->getMessage() . "<br>";
        }

    } else {
        echo "אנא מלא את כל השדות הנדרשים";
    }
}










?>
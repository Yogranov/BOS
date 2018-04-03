/*
This file contain jquery code that deal with the View Order Page vieworder.php
 */

var ClickEvent = "click";

var viewOrder = "";
var viewOrderProducts = "";

$(document).ready(function(){
    viewOrder = $(document).find("#ViewOrder");
    viewOrderProducts = viewOrder.find("#viewOrderProducts");
    ClientOrderEmail = $("#order-email");

    viewOrderProducts.on(ClickEvent, ".editable", function() {
        ConvertChildrensInput($(this),"text", "editing");
        $(".editing").find("input[type=text]").focus();
    });

    viewOrderProducts.on("blur", ".editing", function() {
        var attrData = $(this).find("input")[0].attributes;
        var OldValue = attrData["data-oldvalue"].nodeValue;
        var NewValue = $(this).find("input").val();

        var retdata = 0;
        if (NewValue !== OldValue) {
            //Sent DBUpdate In Ajax
            var OrderId = $(this).parent("tr")[0].attributes["data-orderId"].nodeValue;
            var productBarcode = $(this).parent("tr")[0].attributes["data-ProductBarcode"].nodeValue;
            var postData = OrderId + "|" + productBarcode + "|" + attrData["data-function"].nodeValue + "|" + NewValue;
            retdata = DoAPIAjax("UpdateOrderProductData", postData);
        }

        if (retdata === 0)
            $(this).find("input").val(OldValue);
        else
            $(this).find("input").attr("data-OldValue", NewValue);

        ConvertChildrensInput($(this),"hidden", "editable");
    });


    $(".productstatus").change(function(){
        var productBarcode = $(this).attr("name");
        var submitLoc = "changeProductStatus_" + productBarcode;
        document.getElementById(submitLoc).submit();
    });

    ClientOrderEmail.on(ClickEvent, ".editable", function() {
        ConvertChildrensInput($(this),"text", "editing");
        $(".editing").find("input[type=text]").focus();
    });

    ClientOrderEmail.on("blur", ".editing", function() {
        var attrData = $(this).find("input")[0].attributes;
        var OldValue = attrData["data-oldvalue"].nodeValue;
        var NewValue = $(this).find("input").val();

        var retdata = 0;
        if (NewValue !== OldValue) {
            //Sent DBUpdate In Ajax
            var OrderId = attrData["data-orderId"].nodeValue;
            var postData = OrderId + "|" + attrData["data-function"].nodeValue + "|" + NewValue;
            retdata = DoAPIAjax("UpdateOrderData", postData);
        }

        if (retdata === 0)
            $(this).find("input").val(OldValue);
        else
            $(this).find("input").attr("data-OldValue", NewValue);

        ConvertChildrensInput($(this),"hidden", "editable");

        if (NewValue === "") {
            $(this).find("input").next("span").text("לא הוזן");
        }
    });
});
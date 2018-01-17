/*
This file contain jquery code that deal with the View Order Page vieworder.php
 */

var ClickEvent = "dblclick";

var viewOrder = "";
var viewOrderProducts = "";

$(document).ready(function(){
    viewOrder = $(document).find("#ViewOrder");
    viewOrderProducts = viewOrder.find("#viewOrderProducts");

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

});
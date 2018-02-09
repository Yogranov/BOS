/*
This file contain jquery code that deal with the Edit Add Order Page addeditproduct.php
 */

var newEditProduct = "";
var newEditProductName = "";
var newEditProductBarcode = "";

$(document).ready(function() {

    newEditProduct = $(document).find("#new-edit-product");
    newEditProductName = newEditProduct.find("#product-name");
    newEditProductBarcode = newEditProduct.find("#product-barcode");

    //**************************** {BugOrderSystem Ajax Barcode autocomplete  } *****************************//
    newEditProductBarcode.find("input[type=text]").autocomplete({
        source: function (request, response) {
            $.ajax({
                url: "API_CALLS.php",
                dataType: "json",
                data: {
                    method: "SearchProduct",
                    data: request.term + "|Barcode"
                },
                success: function (data) {
                    response(data);
                }
            });
        },
        minLength: 2,
        select: function (event, ui) {
            newEditProductName.find("input").val(ui.item.Name);
        },
        change: function (event, ui) {
            if (ui.item === null)
                barcode = newEditProductBarcode.find("input").val();
            else
                var barcode = ui.item.Barcode;

            var retData = DoAPIAjax("GetProductData", barcode + "|javascript");
            //console.log(retData);

            if (retData !== 0 && retData !== false) {
                //console.log("null retdata");
                newEditProductName.find("input").val(retData.Name);
            }
            else {
                newEditProductName.find("input").val("");
            }
        }
    });
});



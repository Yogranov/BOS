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


    //**************************** {BugOrderSystem Ajax Product Name autocomplete  } *****************************//
    /*newEditProductName.find("input[type=text]").autocomplete({
        source: function (request, response) {
            $.ajax({
                url: "API_CALLS.php",
                dataType: "json",
                data: {
                    method: "SearchProduct",
                    data: request.term + "|Name"
                },
                success: function (data) {
                    response(data);
                }
            });
        },
        minLength: 4,
        select: function (event, ui) {
            GetBarcodeData(ui.item.Barcode);
            newEditProductBarcode.find("input").val(ui.item.Barcode);
            ConvertChildrensInput(newEditProductBarcode, "hidden", "");

        },
        change: function (event, ui) {
            //console.log("Changed!");
            //console.log(ui.item);
            if (ui.item === null) {
                var productName = newEditProductName.find("input").val();
                var retData = DoAPIAjax("SearchProduct", productName + "|Name");

                //console.log(productName);
                if(retData !== 0 && retData !== false && productName !== "") {
                    barcode = retData[0].Barcode;
                    newEditProductName.find("input").val(retData[0].Name);
                }
                else
                    barcode = -1;
            }
            else
                var barcode = ui.item.Barcode;

            //console.log(barcode);
            if (barcode !== -1) {
                newEditProductBarcode.find("input").val(barcode);
                ConvertChildrensInput(newEditProductBarcode, "hidden", "");
            } else {
                newEditProductBarcode.find("input").val("");
                ConvertChildrensInput(newEditProductBarcode, "text", "");
            }
        }
    });
*/
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
           // ConvertChildrensInput(newEditProductName, "hidden", "");
        },
        change: function (event, ui) {
            /*
            if (ui.item === null)
                barcode = newEditProductBarcode.find("input").val();
            else
                var barcode = ui.item.Barcode;

            var retData = DoAPIAjax("GetProductData", barcode + "|javascript");
            console.log(retData);

            if (retData !== 0 && retData !== false) {
                console.log("null retdata");
                newEditProductName.find("input").val(retData.Name);
                ConvertChildrensInput(newEditProductName, "hidden", "");
            }
            else {
                newEditProductName.find("input").val("");
                ConvertChildrensInput(newEditProductName, "text", "");
            }
        */
        }
    });
});



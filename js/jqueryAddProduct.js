
var ProductNameDiv = "";
var ProductBarcodeDiv = "";

$(document).ready(function() {

    ProductNameDiv = $('#productName');
    ProductBarcodeDiv = $("#productBarcode");

    //**************************** {BugOrderSystem Ajax Product Name autocomplete  } *****************************//
    var Barcode = "";
    $("#form-product-name").autocomplete({
        source: function (request, response) {
            $.ajax({
                url: "API_CALLS.php?method=SearchProduct",
                dataType: "json",
                data: {
                    data: request.term + "|Name"
                },
                success: function (data) {
                    response(data);
                }
            });
        },
        minLength: 2,
        select: function (event, ui) {
            GetBarcodeData(ui.item.Barcode);
        },
        change: function (event, ui) {
            if (ui.item == null)
            {
                var productName = $("#form-product-name").val();
                var retData = DoAPIAjax("SearchProduct", productName + "|Name");
                barcode = retData[0].Barcode;
            }
            else
                var barcode = ui.item.Barcode;
            GetBarcodeData(barcode);
        }
    });
});

function ChangeBarcodeToSpan(value) {
    ProductBarcodeDiv.find("#form-product-barcode[type!='hidden']").replaceWith("<div id='barcode_Text'><input type='hidden' id='form-product-barcode' name='ProductBarcode' value='" + value + "'><span>" + value + "</span></div>");
}

function ChangeBarcodeToInput(requiredBool = false) {
    var AdditionalRequire = "";
    if (requiredBool)
        AdditionalRequire = " required";
    ProductBarcodeDiv.find('div').replaceWith("<input type='text' class='form-control' id='form-product-barcode' placeholder='ברקוד' name='ProductBarcode' "+AdditionalRequire+">");
}
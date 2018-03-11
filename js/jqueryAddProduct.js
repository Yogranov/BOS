
var ProductNameDiv = "";
var ProductBarcodeDiv = "";

$(document).ready(function() {

    ProductNameDiv = $('#productName');
    ProductBarcodeDiv = $("#productBarcode");

    //**************************** {BugOrderSystem Ajax Product Name autocomplete  } *****************************//
    var Barcode = "";
    $("#form-product-barcode").autocomplete({
        source: function (request, response) {
            $.ajax({
                url: "API_CALLS.php?method=SearchProduct",
                dataType: "json",
                data: {
                    data: request.term + "|Barcode"
                },
                success: function (data) {
                    response(data);
                }
            });
        },
        minLength: 2,
        select: function (event, ui) {
            GetNameData(ui.item.Barcode);
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
            GetNameData(barcode);
        }
    });
});

function productBlankBarcode() {
    var val = $("#form-product-barcode").val();
    if(val == "") {
        ChangeNameToInput();
    }
}

function productBlankName() {
    var val = $("#form-product-name").val();
    if(val == "") {
        ChangeBarcodeToInput();
    }
}

function ChangeBarcodeToSpan(value) {
    ProductBarcodeDiv.find("#form-product-barcode[type!='hidden']").replaceWith("<div id='barcode_Text'><input type='hidden' id='form-product-barcode' name='ProductBarcode' value='" + value + "'><span>" + value + "</span></div>");
}

function ChangeBarcodeToInput(requiredBool = false) {
    var AdditionalRequire = "";
    if (requiredBool)
        AdditionalRequire = " required";
    ProductBarcodeDiv.find('div').replaceWith("<input type='text' class='form-control' id='form-product-barcode' placeholder='ברקוד' name='ProductBarcode' "+AdditionalRequire+">");
}

function ChangeNameToSpan(value) {
    ProductNameDiv.find("#form-product-name[type!='hidden']").replaceWith("<div id='barcode_Text'><input type='hidden' id='form-product-name' name='productname' value='" + value + "'><span>" + value + "</span></div>");
}

function ChangeNameToInput(requiredBool = false) {
    var AdditionalRequire = "";
    if (requiredBool)
        AdditionalRequire = " required";
    ProductNameDiv.find('div').replaceWith("<input type='text' class='form-control' id='form-product-name' placeholder='שם המוצר' name='productname' "+AdditionalRequire+">");
}
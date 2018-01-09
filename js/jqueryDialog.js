/*
This file contain jquery code that deal with popup's and dialogs
 */

var WindowDialogWidthSize = 0.6;
var WindowDialogHeightSize = 0.9;
var overlayColor = "white";
var overlayOpacity = 0.8;
var dialogShowEffect = "fold";
var dialogShowSpeed = speed;
var dialogHideEffect = "fold";
var dialogHideSpeed = speed;
var dialogSizeChangeSpeed = 200;
var DialogClasses = "dialogWithDropShadow dialogBackground";

function outputUpdate(range, where) {
    document.querySelector(where).value = range;
}

$(document).ready(function(){

    var Dialog = $("#BOS_Dialog");
    var DialogIframe = Dialog.children("iframe");
    var Body = $('body');
    var IframeUrl = "";

    //**************************** {BugOrderSystem addproduct.php Page -  } *****************************//
    $( "#form-product-barcode" ).autocomplete({
        source: function( request, response ) {
            $.ajax( {
                url: "API_CALLS.php?method=SearchProductByBarcode",
                dataType: "json",
                data: {
                    data: request.term
                },
                success: function( data ) {
                    response( data );
                }
            } );
        },
        minLength: 3,
        change: function( event, ui ) {
            var value = $("#form-product-barcode").val();
            GetBarcodeData(value);
        },
        select: function( event, ui ) {
            GetBarcodeData(ui.item.value, DialogIframe);
        }
    });

    //**************************** {BugOrderSystem ProductsDelivered button - to change products that need to be deliver to delivered status } *****************************//
    $("#ProductsDelivered").on( "click", function() {
        window.location.href = $(this).attr("data-SubmitPage");
    });

    //**************************** {BugOrderSystem ProductsOrdered button - to inform client for arriving products } *****************************//
    $("#ProductsOrdered").on( "click", function() {
        window.location.href = $(this).attr("data-SubmitPage");
    });

    //**************************** {BugOrderSystem ClientInformed button - to inform client for arriving products } *****************************//
    $("#ClientInformed").on( "click", function() {
        window.location.href = $(this).attr("data-SubmitPage");
    });

    //**************************** {BugOrderSystem vieworder confirmation dialogs} *****************************//
    var InformClientObject = $("#InformClient");
    if ( InformClientObject.length ) {
        //InformClient span exist!
        var DialogConfType = InformClientObject.attr("data-confirmationType");
        var OrderId = InformClientObject.attr("data-orderId");
        var buttonData = "";
        var ApiUrl = InformClientObject.attr("data-ApiUrl");

        switch (DialogConfType) {
            case "dialog-EmailConfirm":
                buttonData = [
                    {
                        text: "שלח",
                        class: "btn btn-danger",
                        click: function(e) {
                            e.preventDefault();

                            var close = false;
                            $.ajax({
                                url: ApiUrl,
                                method: "POST",
                                crossDomain: true,
                                dataType: 'json',
                                async: false,
                                error: function() {
                                    console.log("Failed!");

                                    var ErrorText = "אירעה שגיאה בשליחת האימייל ללקוח!\nעליך לעדכן את הלקוח ידנית!";
                                    $( "#"+DialogConfType ).text(ErrorText);
                                },
                                success: function(data) {
                                    close = true;
                                }
                            });

                            if (close)
                                $( this ).dialog( "close" );
                        }
                    },
                    {
                        text: "סגור ללא שליחה",
                        class: "btn btn-primary",
                        click: function(e) {
                            e.preventDefault();
                            $( this ).dialog( "close" );
                        }
                    }
                ];
                break;

            case "dialog-ManualConfirm":
            default: buttonData = [
                {
                    text: "אישור",
                    class: "btn btn-primary",
                    click: function(e) {
                        e.preventDefault();
                        $( this ).dialog( "close" );
                    }
                }
            ]
        }

        $( "#"+DialogConfType ).dialog({
            resizable: false,
            height: "auto",
            width: 400,
            modal: false,
            buttons: buttonData,
            open: function() {
                var buttonJquery = $(".ui-dialog-buttonset");
                SetButtonIcon(buttonJquery,"שלח", "glyphicon glyphicon-envelope");
                SetButtonIcon(buttonJquery,"אישור", "glyphicon glyphicon-ok");
                SetButtonIcon(buttonJquery,"סגור", "glyphicon glyphicon-remove");
            },
            close: function() {
                //closing function
                var source = "vieworder.php?id="+OrderId+"&ShowHeaderFooter=0";
                window.location.href = source;
            }
        });
    }


    //**************************** {BugOrderSystem Big Dialog} *****************************//
    Body.on('click', '.ui-widget-overlay', function(e){
        //listening to overlay click for closing the dialog
        Dialog.dialog('close');
    });

    Dialog.dialog({
        autoOpen: false,
        resizable: false,
        closeOnEscape: true,
        modal: true,
        classes: {
            "ui-dialog": DialogClasses
        },
        show: {
            effect: dialogShowEffect,
            duration: dialogShowSpeed
        },
        hide: {
            effect: dialogHideEffect,
            duration: dialogHideSpeed
        },
        height: 'auto',
        width: 'auto',
        maxWidth: windowWidth - 100,
        maxHeight: windowHeight - 100,
        buttons: [
            {
                text: "הקודם",
                class: "btn btn-primary",
                click: function(e) {
                    e.preventDefault();
                    Dialog.children("iframe").attr("src", IframeUrl);
                }
            },
            {
                text: "סגור",
                class: "btn btn-danger",
                click: function(e) {
                    e.preventDefault();
                    $( this ).dialog( "close" );
                }
            }
        ],
        open: function(event, ui) {
            $(".ui-dialog-titlebar-close", ui.dialog | ui).hide(); //hiding top close button
            $(".ui-widget-overlay").css({background: overlayColor, opacity: overlayOpacity});

            var buttonJquery = $(".ui-dialog-buttonset");
            SetButtonIcon(buttonJquery, "הקודם", "glyphicon glyphicon-repeat");
            SetButtonIcon(buttonJquery, "שמור", "glyphicon glyphicon-floppy-disk");
        },
        close: function(event, ui) {
            $(this).children('iframe').css("display", "none");
            $(".ui-widget-overlay").css({background: '', opacity: ''});
            window.location.reload();
        }
    });

    $("[data-action='OpenBOSDialog']").on( "click", function() {
        IframeUrl = $(this).attr("data-page") + "?" + $(this).attr("data-variables");
        Dialog.children("iframe").css("display", "block");

        SetIframeUrl(Dialog.children("iframe"), IframeUrl);
        SetIframeSize(Dialog.children("iframe"), windowWidth * WindowDialogWidthSize, windowHeight * WindowDialogHeightSize);

        Dialog.dialog({
            title: $(this).attr("data-dialogTitle")
        });

        Dialog.dialog( "open" ); //open dialog when everything are ready
    });

    //auto adjust iframe size on load/reload
    DialogIframe.on("load", function () {
        if (LegalIframe($(this))) {
            SetIframeSize($(this));
            $(this).css("display", "block");
        }
        else {
            Dialog.dialog( "close" );
        }
    });
    //**************************** {END BugOrderSystem Big Dialog} *****************************//
});

function LegalIframe(iFrame) {
    var illigal = iFrame.contents().find("nav").length;
    if (illigal === 0)
        return true;
    else
        return false;
}

function SetIframeUrl(iFrame, newUrl) {
    iFrame.attr("src", newUrl);
    IframeUrl = newUrl;
}

function SetIframeSize(iFrame, newWidth = 0, newHeight = 0) {
    var iframeBody = iFrame.contents().find("body");

    if (newWidth == 0)
        var newWidth = iframeBody.width();

    if (newHeight == 0)
        var newHeight =  iframeBody.height() + 20;

    iFrame.attr({
        height: newHeight,
        width: newWidth
    });

    iFrame.animate({
        height: newHeight,
        width: newWidth
    }, dialogSizeChangeSpeed, function(){
        //console.log("iframe resized to "+newWidth+"x"+newHeight);
    });
}

function GetBarcodeData(productBarcode, DialogIframe) {
    $.ajax( {
        url: "API_CALLS.php?method=GetProductData",
        dataType: "json",
        data: {
            data: productBarcode + "|javascript"
        },
        success: function( data ) {
            if (data !== 0) {
                //exist in product db
                $("#productName").find('#form-product-name').replaceWith("<div id='productName_Text'><input type='hidden' name='ProductName' value='"+data.Name+"'><span>" + data.Name + "</span></div>");
            }
            else {
                //not exist in products db
                $("#productName").find('div').replaceWith("<input type='text' class='form-control' id='form-product-name' placeholder='שם המוצר' name='ProductName' required>");
            }
        }
    } );
}
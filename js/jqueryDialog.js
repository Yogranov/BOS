/*
This file contain jquery code that deal with popup's and dialogs
 */

var WindowDialogWidthSize = 0.7;
var WindowDialogHeightSize = 0.85;
var overlayClass = "modal-backdrop";
var dialogShowEffect = "fold";
var dialogShowSpeed = speed;
var dialogHideEffect = "fold";
var dialogHideSpeed = speed;
var dialogSizeChangeSpeed = 200;
var DialogClasses = "dialogWithDropShadow dialogBackground";

function outputUpdate(range, where) {
    document.querySelector(where).value = range;
}

var Dialog = "";
var DialogIframe = "";

$(document).ready(function(){
    Dialog = $("#BOS_Dialog");
    TestDialog = $("#myModal");
    DialogIframe = Dialog.children("iframe");
    var Body = $('body');
    var IframeUrl = "";

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

    //**************************** {BugOrderSystem Big Dialog Bootstrap 3} *****************************//
    /*
    TestDialog.modal({
        keyboard: false,
        show: false,
        backdrop: true
    });

    $("[data-action='OpenBOSDialog']").on( "click", function() {
        //data things//
        IframeUrl = $(this).attr("data-page") + "?" + $(this).attr("data-variables");
        SetIframeUrl(TestDialog.find("iframe"), IframeUrl);
        TestDialog.find(".modal-title").text($(this).attr("data-dialogTitle"));

        //css things
        SetIframeSize(TestDialog.find("iframe"), windowWidth * WindowDialogWidthSize, windowHeight * WindowDialogHeightSize);
        TestDialog.find("iframe").css("display", "block");
        TestDialog.find(".modal-backdrop").addClass(overlayClass);
        TestDialog.find(".modal-body").addClass("dialogBackground");
        TestDialog.find(".modal-footer").addClass("dialogFooterBackground");
        TestDialog.find(".modal-header").addClass("dialogHeaderBackground");
        TestDialog.find('.modal-dialog').css({
            width: (windowWidth * WindowDialogWidthSize) + 30, //probably not needed
            height: windowHeight * WindowDialogHeightSize, //probably not needed
            'max-height': windowHeight - 100,
            'max-width': windowWidth - 100
        });

        sleep(50);
        TestDialog.modal('show'); //open dialog when everything are ready
    });

    //auto adjust iframe size on load/reload
    TestDialog.find("iframe").on("load", function () {
        if (LegalIframe($(this))) {
            SetIframeSize($(this), 0, 0);
            TestDialog.data('bs.modal').handleUpdate();
            $(this).css("display", "block");
        }
        else {
            TestDialog.modal('hide');
        }
    });

    TestDialog.on('show.bs.modal', function () {

    });
    */

    //**************************** {BugOrderSystem Big Dialog Jquery UI} *****************************//

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
            $(".ui-widget-overlay").addClass(overlayClass);

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
            SetIframeSize($(this), 0, 0);
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

function SetIframeSize(iFrame, newWidth, newHeight) {
    var iframeBody = iFrame.contents().find("body");

    if (newWidth == 0)
        var newWidth = parseInt(iframeBody.innerWidth());

    if (newHeight == 0)
        var newHeight =  parseInt(iframeBody.innerHeight()) + 20;

    console.log(newWidth+"x"+newHeight);

    iFrame.attr({
        height: newHeight,
        width: newWidth
    });

    iFrame.animate({
        height: newHeight,
        width: newWidth
    }, dialogSizeChangeSpeed, function(){
        console.log("iframe resized to "+newWidth+"x"+newHeight);
    });
}
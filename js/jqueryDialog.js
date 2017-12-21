/*
This file contain jquery code that deal with popup's and dialogs
 */

$(document).ready(function(){

    var Dialog = $("#BOS_Dialog");
    var Body = $('body');
    var backgroundimage = "";

    Body.on('click', '.ui-widget-overlay', function(e){
        Dialog.dialog('close');
    });

    Dialog.dialog({
        autoOpen: false,
        resizable: false,
        closeOnEscape: true,
        modal: true,
        classes: {
            "ui-dialog": 'dialogWithDropShadow dialogBackground'
        },
        show: {
            effect: "fold",
            duration: speed
        },
        hide: {
            effect: "fold",
            duration: speed
        },
        height: 'auto',
        width: 'auto',
        maxWidth: windowWidth * WindowDialogSize,
        maxHeight: (windowHeight * WindowDialogSize) + 200,
        buttons: [
            {
                text: "חזור",
                class: "btn btn-primary",
                click: function(e) {
                    e.preventDefault();
                    history.back(1);
                    return false;
                }
            },
            {
                text: "סגור",
                class: "btn btn-danger",
                click: function(e) {
                    $( this ).dialog( "close" );
                }
            }
        ],
        open: function(event, ui) {
            $('.ui-dialog-titlebar-close').removeClass("ui-dialog-titlebar-close");
            $(".ui-widget-overlay").css({background: "white", opacity: 0.8});
        },
        close: function(event, ui) {
            $(this).children('iframe').css("display", "none");
            $(".ui-widget-overlay").css({background: '', opacity: ''});
            location.reload();
        }
    }).prev(".dialogBackground").css("background-image", backgroundimage);

    $("[data-action='OpenBOSDialog']").on( "click", function() {
        var url = $(this).attr("data-page") + "?" + $(this).attr("data-variables");
        Dialog.children("iframe").css("display", "block");
        Dialog.children("iframe").attr({
            src: url,
            width: windowWidth * WindowDialogSize,
            height: (windowHeight * WindowDialogSize) + 200
        });

        //auto adjust iframe size
        Dialog.children("iframe").on("load", function () {
            //console.log("BOSDialog iframe Reloaded!");

            var iframeBody = $(this).contents().find("body");
            backgroundimage = iframeBody.css("background-image");

            //console.log(backgroundimage);

            var DialogHeight = iframeBody.height();
            var DialogWidth = iframeBody.width();
            $(this).height( DialogHeight + 20);
            $(this).width( DialogWidth );

            //console.log("resizeing iframe to "+DialogWidth+"x"+DialogHeight);

            $(this).css("display", "inline");
        });

        Dialog.dialog({
            title: $(this).attr("data-dialogTitle")
        });

        Dialog.dialog( "open" );
        //Dialog.draggable( "option", "containment", [0, 0, Body.width(), Body.height()] );
    });
});
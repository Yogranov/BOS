/*
This file contain jquery code that deal with popup's and dialogs
 */

$(document).ready(function(){

    var Dialog = $("#BOS_Dialog");

    Dialog.dialog({
        autoOpen: false,
        resizable: false,
        closeOnEscape: true,
        modal: true,
        classes: {
            "ui-dialog": 'dialogWithDropShadow'
        },
        show: {
            effect: "fade",
            duration: speed
        },
        hide: {
            effect: "puff",
            duration: speed
        },
        height: 'auto',
        width: 'auto',
        maxWidth: windowWidth * WindowDialogSize,
        buttons: [
            {
                text: "Back",
                icon: "glyphicon glyphicon-repeat",
                click: function(e) {
                    e.preventDefault();
                    history.back(1);
                    return false;
                },
                showText: false
            },
            {
                text: "Close",
                icon: "glyphicon glyphicon-remove",
                click: function(e) {
                    $( this ).dialog( "close" );
                },
                showText: false
            }
        ],
        open: function(event, ui) {
            $('.ui-dialog-titlebar-close')
                .removeClass("ui-dialog-titlebar-close");
        }
    });

    $("[data-action='OpenBOSDialog']").on( "click", function() {
        var url = $(this).attr("data-page") + "?" + $(this).attr("data-variables");
        Dialog.children("iframe").attr({
            src: url,
            width: windowWidth * WindowDialogSize,
            height: windowHeight * WindowDialogSize
        });

        var DialogWidth = 0;
        var DialogHeight = 0;

        //auto adjust iframe size
        Dialog.children("iframe").on("load", function () {
            console.log("BOSDialog iframe Reloaded!");

            var iframeBody = $(this).contents().find("body");
            DialogHeight = iframeBody.height();
            DialogWidth = iframeBody.width();
            $(this).height( DialogHeight );
            $(this).width( DialogWidth );

            console.log("resizeing iframe to "+DialogWidth+"x"+DialogHeight);
        });

        Dialog.dialog({
            title: $(this).attr("data-dialogTitle")
        });

        Dialog.dialog( "open" );

        /*
        //centering
        var left = (windowWidth - DialogWidth) / 2;
        var top = (windowHeight - DialogHeight) / 2;
        Dialog.css({
            position: "relative",
            top: top,
            left: left
        });
        console.log("centering dialog to Top:"+top+"px Left:"+left+"px");
        */
    });
});
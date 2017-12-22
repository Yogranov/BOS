/*
This file contain jquery code that deal with popup's and dialogs
 */

var WindowDialogWidthSize = 0.6;
var WindowDialogHeightSize = 0.9;

$(document).ready(function(){

    var Dialog = $("#BOS_Dialog");
    var Body = $('body');
    var backgroundimage = "";
    var IframeUrl = "";

    Body.on('click', '.ui-widget-overlay', function(e){
        Dialog.dialog('close');
    });

    Dialog.on('click', '.btn', function(e){
        //var IframeUrl =  $(this).parent().children("iframe").attr("src");
        console.log("outer blabla");
        //history.back(1);
        //return false;
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
        maxWidth: windowWidth - 100,
        maxHeight: windowHeight - 100,
        buttons: [
            {
                text: "הקודם",
                class: "btn btn-primary",
                click: function(e) {
                    e.preventDefault();
                    Dialog.children("iframe").attr("src", IframeUrl);
                    //history.back(1);
                }
            },
            {
                text: "שמור וסגור",
                class: "btn btn-danger",
                click: function(e) {
                    e.preventDefault();
                    $( this ).dialog( "close" );
                }
            }
        ],
        open: function(event, ui) {
            //$('.ui-dialog-titlebar-close').removeClass("ui-dialog-titlebar-close");
            $(".ui-dialog-titlebar-close", ui.dialog | ui).hide();
            $(".ui-widget-overlay").css({background: "white", opacity: 0.8});
            $(".ui-dialog-buttonset > .btn-primary")
                .removeClass('ui-button-text-only')
                .addClass('ui-button-text-icon-primary')
                .append("&nbsp;&nbsp;<span class='glyphicon glyphicon-repeat'></span>");

            $(".ui-dialog-buttonset > .btn-danger")
                .removeClass('ui-button-text-only')
                .addClass('ui-button-text-icon-primary')
                .append("&nbsp;&nbsp;<span class='glyphicon glyphicon-floppy-disk'></span>");
        },
        close: function(event, ui) {
            $(this).children('iframe').css("display", "none");
            $(".ui-widget-overlay").css({background: '', opacity: ''});
            location.reload();
        }
    }).prev(".dialogBackground").css("background-image", backgroundimage);

    $("[data-action='OpenBOSDialog']").on( "click", function() {
        IframeUrl = $(this).attr("data-page") + "?" + $(this).attr("data-variables");
        Dialog.children("iframe").css("display", "block");
        Dialog.children("iframe").attr({
            src: IframeUrl,
            width: windowWidth * WindowDialogWidthSize,
            height: windowHeight * WindowDialogHeightSize
        });

        Dialog.dialog({
            title: $(this).attr("data-dialogTitle")
        });

        Dialog.dialog( "open" );
        //Dialog.draggable( "option", "containment", [0, 0, Body.width(), Body.height()] );
    });

    //auto adjust iframe size
    Dialog.children("iframe").on("load", function () {
        //console.log("BOSDialog iframe Reloaded!");

        var iframeBody = $(this).contents().find("body");
        backgroundimage = iframeBody.css("background-image");

        //console.log(backgroundimage);

        var DialogHeight = iframeBody.height();
        var DialogWidth = iframeBody.width();
        $(this).animate({
            height: DialogHeight + 20,
            width: DialogWidth
        }, 200, function(){
            //animation complete
        });

        //console.log("resizeing iframe to "+DialogWidth+"x"+DialogHeight);

        $(this).css("display", "inline");
    });
});
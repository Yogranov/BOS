$(document).ready(function() {
    //**************************** { Client Data AutoFill } *****************************//
    var editClientId = $("#edit-client-phone-number");
    editClientId.autocomplete({
        source: function (request, response) {
            $.ajax({
                url: "API_CALLS.php?method=SearchClient",
                dataType: "json",
                data: {
                    data: request.term + "|PhoneNumber"
                },
                success: function (data) {
                    response(data);
                }
            });
        },
        minLength: 3,
        select: function (event, ui) {
            $("#edit-client-first-name").val(ui.item.FirstName);
            $("#edit-client-last-name").val(ui.item.LastName);
            $("#edit-client-email").val(ui.item.Email);
            $("#edit-client-id").val(ui.item.Id);
        },
        change: function (event, ui) {
        }
    });

    editClientId.focusout(function(){
        if (editClientId.val() === "") {
            $("#edit-client-first-name").val("");
            $("#edit-client-last-name").val("");
            $("#edit-client-email").val("");
            $("#edit-client-id").val("");
        }
    });
});

function emailsClick() {
    var checkBox = document.getElementById("checkwantsemails");
    var emailBox = document.getElementById("clientwantsemails");

    if(emailBox.className == "open") {
        emailBox.className = "";
    } else {
        emailBox.className = "open";
    }
}

function arrayKeys(input) {
    var output = new Array();
    var counter = 0;
    for (i in input) {
        output[counter++] = i;
    }
    return output;
}

function object2array(obj) {
    var array= new Array();

    for (var property in obj) {
        if (obj.hasOwnProperty(property)) {
            if (typeof obj[property] == "object") {
                array[array.length] = object2array(obj[property]);
            }
            else {
                array[array.length] = [property, obj[property]];
            }
        }
    }

    return array;
}

function sleep(milliseconds) {
    var start = new Date().getTime();
    for (var i = 0; i < 1e7; i++) {
        if ((new Date().getTime() - start) > milliseconds){
            break;
        }
    }
}

function DoAPIAjax(method, postData) {
    var retData = false;
    $.ajax({
        url: "API_CALLS.php?method="+method,
        dataType: "json",
        async: false,
        data: {
            data: postData
        },
        success: function (returnedData) {
            retData = returnedData;
        },
        error: function (ajaxobject, statusText, errorThrowen) {
            //console.log(statusText);
            console.log(ajaxobject);
            retData = false;
        }
    });

    return retData;
}
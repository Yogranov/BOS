
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


function ConvertChildrensInput(inputDom, ConvertedTo, classTo) {
    var outString = "";
    switch (ConvertedTo) {
        case 'hidden':
            inputDom.find("input[type='text']").each(function() {
                var InputAttr = GetInputsAttributes(this);
                var arr = [];
                var innerString = "<input type='" + ConvertedTo + "'";
                InputAttr.forEach(function(element) {
                    var index = element[0];
                    var value = element[1];
                    arr[index] = value;

                    if (index !== "type" && index !== "value")
                        innerString = innerString.concat(" " + index + "='"+ value +"'");
                });

                var valueData = inputDom.find("input[name='" + arr['name'] + "']").val(); //dynamic get value
                innerString = innerString.concat(" value='"+ valueData +"'");
                innerString = innerString.concat("><span>" + valueData + "</span>");
                outString = outString.concat(innerString);
            });
            inputDom.attr('class', classTo);
            break;

        default:
            inputDom.find("input[type='hidden']").each(function() {
                var InputAttr = GetInputsAttributes(this);
                var innerString = "<input type='" + ConvertedTo + "'";
                InputAttr.forEach(function(element) {
                    var index = element[0];
                    var value = element[1];

                    if (index !== "type")
                        innerString = innerString.concat(" " + index + "='"+ value +"'");
                });
                innerString = innerString.concat(">");
                outString = outString.concat(innerString);
            });
            inputDom.attr('class', classTo);
    }
    inputDom.html(outString);
}

function GetInputsAttributes(inputElement) {
    var arr = [];
    $.each(inputElement.attributes,function(i,a){
        arr[i] = [a.name, a.value];
    });
    //console.log(arr);
    return arr;
}
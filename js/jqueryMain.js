//Accordian Action
var action = 'click';
var speed = "500";

var windowWidth = window.innerWidth;
var windowHeight = window.innerHeight;

$(document).ready(function(){
    $("[class='goBack']").click(function(event) {
        event.preventDefault();
        history.back(1);
    });

    $(".productstatus").change(function(){
        var productId = $(this).attr("data-ProductId");
        var submitLoc = "changeProductStatus_" + productId;
        document.getElementById(submitLoc).submit();
    });

    //Question handler
    $('li.q').on(action, function(){
        //gets next element
        //opens .a of selected question
        $(this).next().slideToggle(speed)
        //selects all other answers and slides up any open answer
            .siblings('li.a').slideUp();

        //Grab img from clicked question
        var img = $(this).children('img');
        //Remove Rotate class from all images except the active
        $('img').not(img).removeClass('rotate');
        //toggle rotate class
        img.toggleClass('rotate');

    });//End on click

    $("#burger-nav").on("click", function (){
        $("header nav ul").toggleClass("open");

    });

    // Load the Visualization API and the corechart package.
    google.charts.load('current', {'packages':['corechart']});
    google.charts.load('current', {'packages':['gauge']});

    // Set a callback to run when the Google Visualization API is loaded.
    google.charts.setOnLoadCallback(GetCharts);

});

jQuery.expr[':'].regex = function(elem, index, match) {
    var matchParams = match[3].split(','),
        validLabels = /^(data|css):/,
        attr = {
            method: matchParams[0].match(validLabels) ?
                matchParams[0].split(':')[0] : 'attr',
            property: matchParams.shift().replace(validLabels,'')
        },
        regexFlags = 'ig',
        regex = new RegExp(matchParams.join('').replace(/^\s+|\s+$/g,''), regexFlags);
    return regex.test(jQuery(elem)[attr.method](attr.property));
};


// instantiates the pie chart, passes in the data and
// draws it.

// function the loop over all elements that have 'chart' class and
function GetCharts() {
    $("div[class=Chart]").each(function() {
        var domElemetObject = $(this);
        $.ajax({
            url: $(this).attr("data-url"),
            type: 'GET',
            crossDomain: true,
            dataType: 'json',
            //async: false,
            error: function() {
                console.log("Failed!");
            },
            success: function(data) {
                //console.log(data);
                drawChart(domElemetObject, data);
            }
        });
    });
}

function drawChart(chartObject, ajaxData) {
    var data = google.visualization.arrayToDataTable(ajaxData);

    // Set default chart options
    var options = {
        'title': chartObject.attr('data-title'),
        backgroundColor: { fill:'transparent' },
        pieSliceBorderColor: 'white',
        legend: 'none',
        chartArea:{left:0,top:0,width:'100%',height:'100%'}
    };

    //add user defined options to the code
    if(chartObject.attr('data-options')) {
        var optionsData = object2array(JSON.parse(chartObject.attr('data-options')));
        optionsData.forEach(function(option) {
            options[option[0]] = option[1];
        });
    }
    //console.log(options);

    // Instantiate and draw our chart, passing in some options.
    var chartType = chartObject.attr("data-chart-type");
    var chart = "";
    switch(chartType) {
        case 'Combo': chart = new google.visualization.ComboChart(chartObject[0]);
            break;
        case 'Clock': chart = new google.visualization.Gauge(chartObject[0]);
            break;
        case 'Line': chart = new google.visualization.LineChart(chartObject[0]);
            break;
        case 'Column': chart = new google.visualization.ColumnChart(chartObject[0]);
            break;
        case 'Donut': if (typeof options.pieHole === 'undefined') {
                options.pieHole = 0.6;
                }
        default: chart = new google.visualization.PieChart(chartObject[0]);
    }

    if (chart !== "") {
        chart.draw(data, options);
    }
}

function SetButtonIcon(buttonJquery, buttonInnerText, newIconClass) {
    var IconText = "<span class='buttonUserIcon'>&nbsp;&nbsp;<span class='"+newIconClass+"'></span></span>";
    var ButtonDOM = buttonJquery.children("button:contains('"+buttonInnerText+"')");

    ButtonDOM.children(".buttonUserIcon").remove(); //remove old userIcon if exist
    ButtonDOM.removeClass('ui-button-text-only')
        .addClass('ui-button-text-icon-primary')
        .append(IconText);

    //console.log("Icon of the button that contain the text '"+buttonInnerText+"' as assign the class '"+newIconClass+"'!");
}
"use strict";
$(document).ready(function() {

    alert(datos_chart);
    google.charts.load('current', {'packages':['bar']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = google.visualization.arrayToDataTable(datos_chart);

        var options = {
            chart: {
                //title: 'Company Performance',
                //subtitle: 'Sales, Expenses, and Profit: 2014-2017',
            },
            hAxis: {
                title: 'Estado Tramite',
            },
            vAxis: {
                title: 'N° Tramites'
            },
        };

        /*var chart_div = document.getElementById('chart_div');
        var chart = new google.charts.Bar(chart_div);

        // Wait for the chart to finish drawing before calling the getImageURI() method.
        google.visualization.events.addListener(chart, 'ready', function () {
            chart_div.innerHTML = '<img src="' + chart.getImageURI() + '">';
            console.log(chart_div.innerHTML);
        });

        chart.draw(data, options);*/


        var chart = new google.charts.Bar(document.getElementById('avance'));

        google.visualization.events.addListener(chart, 'ready', function () {
            /* html2canvas*/
            //console.log(chart.getImageURI());
            /*avance.innerHTML = '<img id="chart" src=' + chart.getImageURI() + '>';
            document.getElementById("download_link").setAttribute("href", chart.getImageURI())
            document.getElementById("download_link").click();*/

        });

        chart.draw(data, google.charts.Bar.convertOptions(options));
    }

});

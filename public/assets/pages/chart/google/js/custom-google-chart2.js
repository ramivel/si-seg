"use strict";
$(document).ready(function() {

    google.charts.load('current', {'packages':['bar']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = google.visualization.arrayToDataTable([
            ['ESTADO', 'ESTATAL', 'COOPERATIVA', 'EMPRESA'],
            ['1', 1000, 400, 200],
            ['2', 1170, 460, 250],
            ['3', 660, 1120, 300],
            ['4', 1030, 540, 350],
            ['5', 1030, 540, 350],
            ['6', 1030, 540, 350],
            ['7', 1030, 540, 350],
            ['8', 1030, 540, 350],
            ['9', 1030, 540, 350],
            ['10', 1030, 540, 350],
            ['11', 1030, 540, 350],
            ['12', 1030, 540, 350],
            ['13', 1030, 540, 350],
            ['14', 1030, 540, 350],
            ['15', 1030, 540, 350],
        ]);

        var options = {
            chart: {
                title: 'DIRECCIÓN DEPARTAMENTAL CHUQUISACA',
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


        var chart = new google.charts.Bar(document.getElementById('avance_oficina'));

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

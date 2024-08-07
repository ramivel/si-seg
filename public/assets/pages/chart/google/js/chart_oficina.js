"use strict";
$(document).ready(function() {

    google.charts.load('current', {'packages':['bar'], 'language': 'es'});
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
                title: 'N° Tramites',
            },
        };

        var chart = new google.charts.Bar(document.getElementById('avance_oficina'));
        chart.draw(data, google.charts.Bar.convertOptions(options));
    }

    setUpDownloadPageAsImage();

    function setUpDownloadPageAsImage() {
        document.getElementById("imprimir-chart-oficina").addEventListener("click", function() {
            html2canvas($('#avance_oficina').get(0)).then(function(canvas) {
                simulateDownloadImageClick(canvas.toDataURL(), getRandomFileName());
            });
        });
    }

    function simulateDownloadImageClick(uri, filename) {
        var link = document.createElement('a');
        if (typeof link.download !== 'string') {
            window.open(uri);
        } else {
            link.href = uri;
            link.download = filename;
            accountForFirefox(clickLink, link);
        }
    }

    function clickLink(link) {
        link.click();
    }

    function accountForFirefox(click) { // wrapper function
        let link = arguments[1];
        document.body.appendChild(link);
        click(link);
        document.body.removeChild(link);
    }

    function getRandomFileName() {
        var timestamp = new Date().toISOString().replace(/[-:.]/g,"");
        var random = ("" + Math.random()).substring(2, 8);
        var random_number = timestamp+random+".png";
        return random_number;
    }

});

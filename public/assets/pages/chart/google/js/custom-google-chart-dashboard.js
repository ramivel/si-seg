"use strict";
$(document).ready(function() {


    google.charts.load("current", {packages:["corechart"]});
    google.charts.setOnLoadCallback(drawChart);
    function drawChart() {
      var data = google.visualization.arrayToDataTable([
        ['Estados', 'N Tramites'],
        ['1. FORMALIZADO', 11],
        ['2. OBSERVADO', 11],
        ['3. CON AUTO DE ADMISIÓN', 11],
        ['4. CON RESOLUCIÓN ADMINISTRATIVA DE PROSECUCIÓN', 11],
        ['5. PLAN DE TRABAJO', 11],
        ['6. CONSULTA PREVIA', 11],
        ['7. CON RESOLUCIÓN ADMINISTRATIVA DE APROBACIÓN DE CONSULTA PREVIA', 11],
        ['8. CON INFORME TÉCNICO CONCLUSIVO', 11],
        ['9. CON RESOLUCIÓN ADMINISTRATIVA DE APROBACIÓN DE SUSCRIPCIÓN DE CONTRATO ADMINISTRATIVO MINERO', 11],
        ['10. CON MINUTA DE CONTRATO ADMINISTRATIVO MINERO', 11],
        ['11. CON REGISTRO MINERO', 11],
        ['12. SOBREPUESTAS', 11],
        ['13. RECHAZADAS', 11],
        ['14. CON PERENCIÓN', 11],
        ['15. CON DESISTIMIENTO', 11],
      ]);

      var options = {

      };

      var options = {
        pieHole: 0.4,
        /*title: 'Estados de los Tramites',
        titleTextStyle: {
            color: '#353C4E',
            fontSize: 20,
            bold: false,
        },*/
        //pieStartAngle: 100,
        //pieHole: 1,
      };

      var chart = new google.visualization.PieChart(document.getElementById('avancecam'));
      chart.draw(data, options);
    }

});

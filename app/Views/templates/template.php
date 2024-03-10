<!DOCTYPE html>
<html lang="en">

<head>
    <title><?= TITLE_PAGE;?></title>
    <!-- HTML5 Shim and Respond.js IE10 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 10]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!-- Meta -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="#">
    <meta name="keywords"
        content="flat ui, admin Admin , Responsive, Landing, Bootstrap, App, Template, Mobile, iOS, Android, apple, creative app">
    <meta name="author" content="#">
    <!-- Favicon icon -->
    <link rel="icon" href="<?= base_url('assets/images/ajam.ico');?>" type="image/x-icon">
    <!-- Google font-->
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600" rel="stylesheet">

    <!-- Required Fremwork -->
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/bower_components/bootstrap/css/bootstrap.min.css');?>">
    <!-- sweet alert framework -->
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/bower_components/sweetalert/css/sweetalert.css');?>">
    <!-- themify-icons line icon -->
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/icon/themify-icons/themify-icons.css');?>">
    <!-- ico font -->
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/icon/icofont/css/icofont.css');?>">
    <!-- Font Awesome -->
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/icon/font-awesome/css/font-awesome.min.css');?>">
    <!-- feather Awesome -->
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/icon/feather/css/feather.css');?>">
    <!-- Select 2 css -->
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/bower_components/select2/css/select2.min.css');?>">
    <!-- Data Table Css -->
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/bower_components/datatables.net-bs4/css/dataTables.bootstrap4.min.css');?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/pages/data-table/css/buttons.dataTables.min.css');?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/bower_components/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css');?>">
    <!-- notify js Fremwork -->
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/bower_components/pnotify/css/pnotify.css');?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/bower_components/pnotify/css/pnotify.brighttheme.css');?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/bower_components/pnotify/css/pnotify.buttons.css');?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/bower_components/pnotify/css/pnotify.history.css');?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/bower_components/pnotify/css/pnotify.mobile.css');?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/pages/pnotify/notify.css');?>">
    <!-- leaflet -->
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/pages/leaflet/leaflet.css');?>">    

    <!-- Style.css -->
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/style.css');?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/jquery.mCustomScrollbar.css');?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/scss/partials/menu/_pcmenu.scss');?>">
</head>

<body>

    <?= view('templates/loader')?>

    <div id="pcoded" class="pcoded" nav-type='offcanvas'>
        <div class="pcoded-overlay-box"></div>
        <div class="pcoded-container navbar-wrapper">

            <?= view('templates/header')?>

            <div class="pcoded-main-container">
                <div class="pcoded-wrapper">

                    <?= view('templates/menu')?>

                    <div class="pcoded-content">
                        <div class="pcoded-inner-content">
                            <!-- Main-body start -->
                            <div class="main-body">
                                <?= $content; ?>
                            </div>
                        </div>
                        <?= view('templates/footer')?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Required Jquery -->
    <script type="text/javascript" src="<?= base_url('assets/bower_components/jquery/js/jquery.min.js');?>"></script>
    <script type="text/javascript" src="<?= base_url('assets/bower_components/jquery-ui/js/jquery-ui.min.js');?>"></script>
    <script type="text/javascript" src="<?= base_url('assets/bower_components/popper.js/js/popper.min.js');?>"></script>
    <script type="text/javascript" src="<?= base_url('assets/bower_components/bootstrap/js/bootstrap.min.js');?>"></script>
    <!-- jquery slimscroll js -->
    <script type="text/javascript" src="<?= base_url('assets/bower_components/jquery-slimscroll/js/jquery.slimscroll.js');?>"></script>
    <!-- modernizr js -->
    <script type="text/javascript" src="<?= base_url('assets/bower_components/modernizr/js/modernizr.js');?>"></script>
    <script type="text/javascript" src="<?= base_url('assets/bower_components/modernizr/js/css-scrollbars.js');?>"></script>

    <!-- google chart -->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

        <!--script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></!--script-->
    <!-- Chart js
    <script type="text/javascript" src="<?= base_url('assets/bower_components/chart.js/js/Chart.js');?>"></script>
    -->

    <!-- gauge js
    <script type="text/javascript" src="<?= base_url('assets/pages/widget/amchart/amcharts.js');?>"></script>
    <script type="text/javascript" src="<?= base_url('assets/pages/widget/amchart/serial.js');?>"></script>
    <script type="text/javascript" src="<?= base_url('assets/pages/widget/amchart/light.js');?>"></script>
    -->

    <!-- ck editor -->
    <script type="text/javascript" src="<?= base_url('assets/pages/ckeditor/ckeditor.js');?>"></script>
    <script type="text/javascript" src="<?= base_url('assets/pages/ckeditor/translations/es.js');?>"></script>

    <!-- sweet alert js -->
    <script type="text/javascript" src="<?= base_url('assets/bower_components/sweetalert/js/sweetalert.min.js');?>"></script>
    <script type="text/javascript" src="<?= base_url('assets/js/modal.js');?>"></script>

    <!-- data-table js -->
    <script type="text/javascript" src="<?= base_url('assets/bower_components/datatables.net/js/jquery.dataTables.min.js');?>"></script>
    <script type="text/javascript" src="<?= base_url('assets/bower_components/datatables.net-buttons/js/dataTables.buttons.min.js');?>"></script>
    <script type="text/javascript" src="<?= base_url('assets/pages/data-table/js/jszip.min.js');?>"></script>
    <script type="text/javascript" src="<?= base_url('assets/pages/data-table/js/pdfmake.min.js');?>"></script>
    <script type="text/javascript" src="<?= base_url('assets/pages/data-table/js/vfs_fonts.js');?>"></script>
    <script type="text/javascript" src="<?= base_url('assets/bower_components/datatables.net-buttons/js/buttons.print.min.js');?>"></script>
    <script type="text/javascript" src="<?= base_url('assets/bower_components/datatables.net-buttons/js/buttons.html5.min.js');?>"></script>
    <script type="text/javascript" src="<?= base_url('assets/bower_components/datatables.net-bs4/js/dataTables.bootstrap4.min.js');?>"></script>
    <script type="text/javascript" src="<?= base_url('assets/bower_components/datatables.net-responsive/js/dataTables.responsive.min.js');?>"></script>
    <script type="text/javascript" src="<?= base_url('assets/bower_components/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js');?>"></script>

    <!-- Validation js -->
    <!--script src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.min.js"></script-->
    <script type="text/javascript" src="<?= base_url('assets/pages/form-validation/underscore-min.js');?>"></script>
    <script type="text/javascript" src="<?= base_url('assets/pages/form-validation/moment.min.js');?>"></script>
    <script type="text/javascript" src="<?= base_url('assets/pages/form-validation/validate.js');?>"></script>

    <!-- pnotify js -->
    <script type="text/javascript" src="<?= base_url('assets/bower_components/pnotify/js/pnotify.js');?>"></script>
    <script type="text/javascript" src="<?= base_url('assets/bower_components/pnotify/js/pnotify.desktop.js');?>"></script>
    <script type="text/javascript" src="<?= base_url('assets/bower_components/pnotify/js/pnotify.buttons.js');?>"></script>
    <script type="text/javascript" src="<?= base_url('assets/bower_components/pnotify/js/pnotify.confirm.js');?>"></script>
    <script type="text/javascript" src="<?= base_url('assets/bower_components/pnotify/js/pnotify.callbacks.js');?>"></script>
    <script type="text/javascript" src="<?= base_url('assets/bower_components/pnotify/js/pnotify.animate.js');?>"></script>
    <script type="text/javascript" src="<?= base_url('assets/bower_components/pnotify/js/pnotify.history.js');?>"></script>
    <script type="text/javascript" src="<?= base_url('assets/bower_components/pnotify/js/pnotify.mobile.js');?>"></script>
    <script type="text/javascript" src="<?= base_url('assets/bower_components/pnotify/js/pnotify.nonblock.js');?>"></script>
    <?php if(isset($alertas) && count($alertas)>0){?>
        <?php foreach($alertas as $alerta){?>
            <script type="text/javascript">
                $(function() {
                    // Danger notification
                    new PNotify({
                        title: '<?= $alerta['title'];?>',
                        text: '<?= $alerta['text'];?>',
                        icon: 'icofont icofont-info-circle',
                        type: 'error',
                        hide: false,
                    });
                });
            </script>
        <?php }?>
    <?php }?>


    <!-- i18next.min.js -->
    <script type="text/javascript" src="<?= base_url('assets/bower_components/i18next/js/i18next.min.js');?>"></script>
    <script type="text/javascript" src="<?= base_url('assets/bower_components/i18next-xhr-backend/js/i18nextXHRBackend.min.js');?>"></script>
    <script type="text/javascript" src="<?= base_url('assets/bower_components/i18next-browser-languagedetector/js/i18nextBrowserLanguageDetector.min.js');?>"></script>
    <script type="text/javascript" src="<?= base_url('assets/bower_components/jquery-i18next/js/jquery-i18next.min.js');?>"></script>

    <!-- Select 2 js -->
    <script type="text/javascript" src="<?= base_url('assets/bower_components/select2/js/select2.full.min.js');?>"></script>
    <script type="text/javascript" src="<?= base_url('assets/bower_components/select2/js/es.js');?>"></script>

    <!-- data-table-custom -->
    <script src="<?= base_url('assets/pages/data-table/js/data-table-custom.js');?>"></script>

    <!-- Custom Select 2 -->
    <script type="text/javascript" src="<?= base_url('assets/pages/advance-elements/select2-custom.js');?>"></script>

    <!-- Custom From Validation -->
    <?php if(isset($validacion_js)){ ?>
    <script src="<?= base_url('assets/pages/form-validation/'.$validacion_js);?>"></script>
    <?php }?>

    <!-- Custom Graficas -->
    <?php if(isset($graficar)){ ?>
        <script type="text/javascript">
            $(document).ready(function() {
                google.charts.load("current", {packages:["corechart"]});
                google.charts.setOnLoadCallback(drawChart);
                function drawChart() {
                    var data = google.visualization.arrayToDataTable([
                        ['Estados', 'N Tramites'],
                        <?php
                        foreach($resumen as $row){
                            echo "['".$row['estado_tramite']."', ".$row['n']."],";
                        }
                        ?>
                    ]);

                    var options = {
                        pieHole: 0.4,
                    };

                    var chart = new google.visualization.PieChart(document.getElementById('dashcam'));
                    chart.draw(data, options);
                }

            });
        </script>

    <!--script type="text/javascript" src="<?= base_url('assets/pages/widget/custom-widget1.js');?>"></!--script-->
    <?php }?>

    <?php if(isset($charts_js)){ ?>
        <script type="text/javascript">
            <?= 'const datos_chart = '.$data_chart.';'?>
        </script>
        <script type="text/javascript" src="<?= base_url('assets/pages/chart/google/js/'.$charts_js);?>"></script>
    <?php }?>

    <!-- Custom ck editor -->
    <?php if(isset($editor_ck)){ ?>
    <script type="text/javascript" src="<?= base_url('assets/pages/ckeditor/ckeditor-custom.js');?>"></script>
    <?php }?>

    <!-- leaflet -->
    <?php if(isset($mapas) && $mapas){ ?>
    <script type="text/javascript" src="<?= base_url('assets/pages/leaflet/leaflet.js');?>"></script>    
    <script type="text/javascript" src="<?= base_url('assets/js/maps.js');?>"></script>
    <?php if(isset($puntos) && count($puntos)>0){?>
        <script type="text/javascript">
            <?php
            foreach($puntos as $punto)
                echo "L.marker([".$punto['latitud'].", ".$punto['longitud']."]).addTo(myMap);";
            ?>            
        </script>
    <?php }?>
    <?php }?>

    <script type="text/javascript" src="<?= base_url('assets/js/pcoded.min.js');?>"></script>
    <script type="text/javascript" src="<?= base_url('assets/js/vartical-layout.js');?>"></script>
    <script type="text/javascript" src="<?= base_url('assets/js/jquery.mCustomScrollbar.concat.min.js');?>"></script>
    <!-- Custom js -->
    <script type="text/javascript" src="<?= base_url('assets/js/script.js');?>"></script>

</body>

</html>
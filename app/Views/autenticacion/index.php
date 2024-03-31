<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if !IE]><!-->
<html lang="es">
<!--<![endif]-->

<head>
    <meta charset="utf-8" />
    <title><?= TITLE_PAGE;?></title>
    <link rel="icon" href="<?= base_url('assets/images/ajam.ico');?>" type="image/x-icon">
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
    <meta content="" name="description" />
    <meta content="" name="author" />

    <!-- ================== BEGIN BASE CSS STYLE ================== -->
	<link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
	<link href="<?= base_url("assets/login/plugins/jquery-ui/themes/base/minified/jquery-ui.min.css"); ?>" rel="stylesheet" />
	<link href="<?= base_url("assets/login/plugins/bootstrap/css/bootstrap.min.css"); ?>" rel="stylesheet" />
	<link href="<?= base_url("assets/login/plugins/font-awesome/css/font-awesome.min.css"); ?>" rel="stylesheet" />
	<link href="<?= base_url("assets/login/css/animate.min.css"); ?>" rel="stylesheet" />
	<link href="<?= base_url("assets/login/css/style.css"); ?>" rel="stylesheet" />
	<link href="<?= base_url("assets/login/css/style-responsive.min.css"); ?>" rel="stylesheet" />
	<link href="<?= base_url("assets/login/css/theme/default.css"); ?>" rel="stylesheet" id="theme" />
	<!-- ================== END BASE CSS STYLE ================== -->

    <!-- ================== BEGIN BASE JS ================== -->
	<script src="<?= base_url("assets/login/plugins/pace/pace.min.js"); ?>"></script>
	<!-- ================== END BASE JS ================== -->

</head>

<body class="pace-top bg-white">
    <?php $prefijo = ''; ?>
    <!-- begin #page-loader -->
    <div id="page-loader" class="fade in"><span class="spinner"></span></div>
    <!-- end #page-loader -->

    <!-- begin #page-container -->
    <div id="page-container" class="fade">
        <!-- begin login -->
        <div class="login login-with-news-feed">
            <!-- begin news-feed -->
            <div class="news-feed">
                <div class="news-image">
                    <img src="<?= base_url("assets/login/img/bg-login.jpg"); ?>" data-id="login-cover-image" alt="" />
                </div>
                <!--div class="news-caption">
                    <h4 class="caption-title"><b>Sistema de Inscripción al<br>Registro Minero</b></h4>
                </!--div-->
            </div>
            <!-- end news-feed -->
            <!-- begin right-content -->
            <div class="right-content">
                <!-- begin login-header -->
                <div class="login-header">
                    <div class="brand">
                        <img src="<?= base_url("assets/login/img/logo.png"); ?>" data-id="login-cover-image" alt="" width="280px" />
                    </div>
                </div>
                <!-- end login-header -->
                <!-- begin login-content -->
                <div class="login-content">
                    <?= form_open('autenticacion/login', ['class'=>'md-float-material form-material']);?>
                        <?= csrf_field();?>
                        <?php if(!empty(session()->getFlashdata('fail'))){?>
                            <div class="alert alert-danger background-danger"><?= session()->getFlashdata('fail');?></div>
                        <?php }?>
                        <div class="form-group m-b-15">
                            <div class="input-group">
                                <?php
                                $campo = 'usuario';
                                echo form_input(array(
                                    'name' => $campo,
                                    'id' => $campo,
                                    'class' => 'form-control input-lg',
                                    'placeholder' => 'Ingrese su Usuario',
                                    'value' => set_value($campo)
                                ));
                                ?>
                                <div class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></div>
                            </div>
                            <?php if(isset($validation) && $validation->hasError($campo)){?>
                                <span class="help-block text-danger"><?= $validation->getError($campo);?></span>
                            <?php }?>
                        </div>
                        <div class="form-group m-b-15">
                            <div class="input-group">
                                <?php
                                    $campo = 'pass';
                                    echo form_password(array(
                                        'name' => $campo,
                                        'id' => $campo,
                                        'class' => 'form-control  input-lg',
                                        'placeholder' => 'Introducir Contraseña'
                                    ));
                                ?>
                                <div class="input-group-addon"><i class="fa fa-lock" aria-hidden="true"></i></div>
                            </div>
                            <?php if(isset($validation) && $validation->hasError($campo)){?>
                                <span class="help-block text-danger"><?= $validation->getError($campo);?></span>
                            <?php }?>
                        </div>

                        <div class="login-buttons">
                            <button type="submit" class="btn btn-login btn-block btn-lg">INGRESAR AL SISTEMA</button>
                        </div>
                        <hr />
                        <p class="text-center">Copyright &copy; 2024 | AJAM<br>Reservados todos los derechos.</p>
                        <!--img src="<?= base_url("assets/login/img/logo_pie.png"); ?>" data-id="login-cover-image" alt="" width="100%" /-->
                    <?= form_close();?>
                    <div class="row">
                        <div class="col-sm-6 text-center">
                            <img src="<?= base_url("assets/login/img/logo-ajam.png"); ?>" data-id="login-cover-image" alt="" width="170px" />
                        </div>
                        <div class="col-sm-6 text-center">
                            <img src="<?= base_url("assets/login/img/estado_plurinaciona.png"); ?>" data-id="login-cover-image" alt="" width="170px" />
                        </div>
                    </div>
                </div>
                <!-- end login-content -->
            </div>
            <!-- end right-container -->
        </div>
        <!-- end login -->

    </div>
    <!-- end page container -->

    <!-- ================== BEGIN BASE JS ================== -->
	<script src="<?= base_url("assets/login/plugins/jquery/jquery-1.9.1.min.js"); ?>"></script>
	<script src="<?= base_url("assets/login/plugins/jquery/jquery-migrate-1.1.0.min.js"); ?>"></script>
	<script src="<?= base_url("assets/login/plugins/jquery-ui/ui/minified/jquery-ui.min.js"); ?>"></script>
	<script src="<?= base_url("assets/login/plugins/bootstrap/js/bootstrap.min.js"); ?>"></script>
	<!--[if lt IE 9]>
		<script src="<?= base_url("assets/login/crossbrowserjs/html5shiv.js"); ?>"></script>
		<script src="<?= base_url("assets/login/crossbrowserjs/respond.min.js"); ?>"></script>
		<script src="<?= base_url("assets/login/crossbrowserjs/excanvas.min.js"); ?>"></script>
	<![endif]-->
	<script src="<?= base_url("assets/login/plugins/slimscroll/jquery.slimscroll.min.js"); ?>"></script>
	<script src="<?= base_url("assets/login/plugins/jquery-cookie/jquery.cookie.js"); ?>"></script>
	<!-- ================== END BASE JS ================== -->

    <!-- ================== BEGIN PAGE LEVEL JS ================== -->
	<script src="<?= base_url("assets/login/js/apps.min.js"); ?>"></script>
	<!-- ================== END PAGE LEVEL JS ================== -->

	<script>
		$(document).ready(function() {
			App.init();
		});
	</script>

</body>

</html>
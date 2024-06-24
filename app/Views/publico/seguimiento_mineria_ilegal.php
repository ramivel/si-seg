<!doctype html>
<html lang="es" data-bs-theme="auto">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="AJAM">
    <title>Seguimiento Minería Ilegal</title>
    <link rel="icon" href="<?= base_url('assets/images/ajam.ico');?>" type="image/x-icon">

    <link href="<?= base_url('assets/seguimiento/css/bootstrap.min.css');?>" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Custom styles for this template -->
    <link href="<?= base_url('assets/seguimiento/css/checkout.css');?>" rel="stylesheet">

</head>

<body class="bg-body-tertiary">
    <div class="container">
        <main>
            <div class="text-center mt-2">
                <img src="<?= base_url('assets/seguimiento/img/banner.png');?>" class="img-fluid" alt="SEGUIMIENTO DE TRAMITES">
            </div>

            <div class="row">

                <?= $enlaces;?>                

                <?php if(isset($response)){?>
                    <div class="col-md-12">
                        <div class="alert <?= $style?>" role="alert">
                            <h4 class="alert-heading"><?= $titulo;?></h4>
                            <?php if($titulo == 'SE ENCONTRO EL TRÁMITE'){?>
                                <p>Se tiene el siguiente detalle:</p>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th class="table-info text-center">Estado</th>
                                                <th class="table-info text-center">Fecha Actualización</th>                                                                                                
                                                <th class="table-info text-center">Funcionario Actual</th>
                                                <th class="table-info text-center">Horarios de Atención</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="text-center"><?= $estado?></td>
                                                <td class="text-center"><?= $fecha?></td>                                                                                                
                                                <td class="text-center"><?= $usuario_actual;?></td>
                                                <td class="text-center"><?= $atencion;?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <p><strong>Nota.</strong> Este reporte no tiene validez legal.</p>
                            <?php }else{?>
                                <p><?= $contenido;?></p>
                            <?php }?>
                        </div>
                    </div>
                <?php }?>

                <div class="col-md-12">
                    <div class="card text-bg-light mb-3">
                        <div class="card-header text-center">
                            <h3>Minería Ilegal</h3>
                            <small>Verifica el estado de tu Trámite en la AJAM</small>
                        </div>
                        <div class="card-body">
                            <?= form_open($accion, ['class'=>'needs-validation', 'novalidate'=>'']);?>
                                <div class="row g-3">                                    
                                    <div class="col-sm-12">
                                        <div class="form-floating">
                                            <?php
                                                $campo = 'correlativo';
                                                echo form_input(array(
                                                    'name' => $campo,
                                                    'id' => $campo,
                                                    'class' => 'form-control mayusculas',
                                                    'placeholder' => 'N° de Trámite',
                                                    'required' => 'true',
                                                    'autocomplete' => "off"
                                                ));
                                            ?>
                                            <label for="<?= $campo?>">N° de Formulario de Denuncia:</label>
                                            <div class="invalid-feedback">Debe ingresar correctamente el N° de Trámite (Solo se permite letras, números y "/")</div>
                                        </div>
                                    </div>                                    
                                    <div class="col-sm-12">
                                        <div class="form-floating">
                                            <?php
                                                $campo = 'codigo_seguimiento';
                                                echo form_input(array(
                                                    'name' => $campo,
                                                    'id' => $campo,
                                                    'class' => 'form-control',
                                                    'placeholder' => 'Código de Seguimiento',
                                                    'required' => 'true',
                                                    'pattern' => "[0-9]+",
                                                    'autocomplete' => "off"
                                                ));
                                            ?>
                                            <label for="<?= $campo?>">Código de Seguimiento:</label>
                                            <div class="invalid-feedback">Debe ingresar correctamente el Código de Seguimiento (Solo se permite números).</div>
                                        </div>
                                    </div>

                                    <div class="col-sm-3"> </div>
                                    <div class="col-sm-4 text-center"><canvas id="codigo_seguridad_imagen" class="codigo_seguridad_imagen" style="border: 1px solid #CBCCCD;"></canvas></div>
                                    <div class="col-sm-3 text-center"><button type="button" id="codigo_seguridad_refresh" class="btn btn-outline-primary"><i class="fa-solid fa-arrow-rotate-right"></i></button></div>
                                    <div class="col-sm-2"> </div>

                                    <div class="col-sm-12">
                                        <div class="form-floating">
                                            <?php
                                                $campo = 'codigo_seguridad';
                                                echo form_input(array(
                                                    'name' => $campo,
                                                    'id' => $campo,
                                                    'class' => 'form-control',
                                                    'placeholder' => 'Código de Seguridad',
                                                    'required' => 'true',
                                                    'pattern' => "[A-Za-z0-9]+",
                                                    'autocomplete' => "off"
                                                ));
                                            ?>
                                            <label for="<?= $campo?>">Código de Seguridad:</label>
                                            <div class="invalid-feedback">Debe ingresar correctamente el Código de Seguridad (El codigo es sensible de minúsculas y mayusculas).</div>
                                        </div>
                                    </div>
                                </div>
                                <hr class="my-4">
                                <button class="w-100 btn btn-success btn-lg" type="submit">CONSULTAR</button>
                            <?= form_close();?>
                        </div>
                    </div>
                </div>                

            </div>
        </main>

        <footer class="my-5 pt-5 text-body-secondary text-center text-small">
            <p class="mb-1">Copyright &copy; 2024 | AJAM, Reservados todos los derechos.</p>
        </footer>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="<?= base_url('assets/seguimiento/js/color-modes.js');?>"></script>
    <script src="<?= base_url('assets/seguimiento/js/bootstrap.bundle.min.js');?>"></script>
    <script src="<?= base_url('assets/seguimiento/js/jquery-captcha.min.js');?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?= base_url('assets/seguimiento/js/checkout.js');?>"></script>
</body>

</html>
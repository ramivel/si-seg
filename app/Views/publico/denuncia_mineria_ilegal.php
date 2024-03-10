<!doctype html>
<html lang="es" data-bs-theme="auto">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="AJAM">
    <title>Minería Ilegal</title>
    <link rel="icon" href="<?= base_url('assets/images/ajam.ico');?>" type="image/x-icon">

    <link href="<?= base_url('assets/seguimiento/css/bootstrap.min.css');?>" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- sweet alert framework -->
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/bower_components/sweetalert/css/sweetalert.css');?>">

    <!-- leaflet -->
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/pages/leaflet/leaflet.css');?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/pages/leaflet/leaflet-control-geocoder/Control.Geocoder.css');?>">

    <!-- Custom styles for this template -->
    <link href="<?= base_url('assets/seguimiento/css/checkout.css');?>" rel="stylesheet">

    <style>
        .set-map{
            width: 100% !important;
            height: 550px;
        }
        .msj-error{
            width: 100%;
            margin-top: 0.25rem;
            font-size: .875em;
            color: #dc3545;
        }
    </style>

</head>

<body class="bg-body-tertiary">
    <div class="container">
        <main>
            <div class="py-2 text-center">
                <img src="<?= base_url('assets/images/mineria_ilegal/banner.png');?>" class="img-fluid" alt="DENUNCIA MINERIA ILEGAL">
            </div>

            <div class="row">

                <?= $enlaces;?>

                <?php if(!empty(session()->getFlashdata('success'))){?>
                    <div class="col-md-12">
                        <div class="alert alert-success" role="alert"><?= session()->getFlashdata('success');?></div>
                    </div>
                <?php }?>

                <?php if(isset($response)){?>
                    <div class="col-md-12">
                        <div class="alert <?= $style?>" role="alert">
                            <h4 class="alert-heading"><?= $titulo;?></h4>
                            <?php if($titulo = 'SE ENCONTRO EL TRÁMITE'){?>
                                <p>Se tiene el siguiente detalle:</p>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th class="table-info text-center">Fecha Actualización</th>
                                                <th class="table-info text-center">Estado Actual</th>
                                                <th class="table-info text-center">Observaciones</th>
                                                <th class="table-info text-center">Documento Generado</th>
                                                <th class="table-info text-center">Funcionario Actual</th>
                                                <th class="table-info text-center">Horarios de Atención</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="text-center"><?= isset($tramite['fecha'])?$tramite['fecha']:''?></td>
                                                <td class="text-center"><?= isset($tramite['estado'])?$tramite['estado']:''?></td>
                                                <td class="text-center"><?= isset($derivacion['observaciones'])?$derivacion['observaciones']:''?></td>
                                                <td class="text-center"><?= isset($documentos_generados)?$documentos_generados:''?></td>
                                                <td class="text-center"><?= isset($tramite['usuario_actual'])?$tramite['usuario_actual']:''?></td>
                                                <td class="text-center">Lunes - Martes - Viernes</td>
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

                <?= form_open_multipart('denuncia_mineria_ilegal', ['class'=>'needs-validation', 'novalidate'=>'']);?>
                <div class="col-md-12">
                    <div class="card text-bg-light mb-3">
                        <div class="card-header text-center">
                            <strong>DATOS DEL DENUNCIANTE</strong><br>
                            <small>Datos obligatorios (*)</small>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-sm-12 col-md-6">
                                    <div class="form-floating">
                                        <?php
                                            $campo = 'nombres';
                                            echo form_input(array(
                                                'name' => $campo,
                                                'id' => $campo,
                                                'class' => 'form-control mayusculas',
                                                'placeholder' => 'Nombre(s)',
                                                'required' => 'true',
                                                'value' => set_value($campo,'',false)
                                            ));
                                        ?>
                                        <label for="<?= $campo?>">Nombre(s) * :</label>
                                        <div class="invalid-feedback">Debe ingresar este campo.</div>
                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                            <div class="msj-error"><?= $validation->getError($campo);?></div>
                                        <?php }?>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-6">
                                    <div class="form-floating">
                                        <?php
                                            $campo = 'apellidos';
                                            echo form_input(array(
                                                'name' => $campo,
                                                'id' => $campo,
                                                'class' => 'form-control mayusculas',
                                                'placeholder' => 'Apellidos',
                                                'required' => 'true',
                                                'value' => set_value($campo,'',false)
                                            ));
                                        ?>
                                        <label for="<?= $campo?>">Apellidos * :</label>
                                        <div class="invalid-feedback">Debe ingresar este campo.</div>
                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                            <div class="msj-error"><?= $validation->getError($campo);?></div>
                                        <?php }?>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-6">
                                    <div class="form-floating">
                                        <?php
                                            $campo = 'documento_identidad';
                                            echo form_input(array(
                                                'name' => $campo,
                                                'id' => $campo,
                                                'class' => 'form-control mayusculas',
                                                'placeholder' => 'Documento de Identidad',
                                                'required' => 'true',
                                                'type' => 'number',
                                                'value' => set_value($campo,'',false)
                                            ));
                                        ?>
                                        <label for="<?= $campo?>">Documento de Identidad (Solo Números)* :</label>
                                        <div class="invalid-feedback">Debe ingresar correctamente este campo.</div>
                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                            <div class="msj-error"><?= $validation->getError($campo);?></div>
                                        <?php }?>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-6">
                                    <div class="form-floating">
                                        <?php
                                            $campo = 'expedido';
                                            echo form_dropdown($campo, $expedidos, set_value($campo), array('id'=>$campo,'class' => 'form-control', 'required' => 'true',));
                                        ?>
                                        <label for="<?= $campo?>">Expedido * :</label>
                                        <div class="invalid-feedback">Debe seleccionar una opción.</div>
                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                            <div class="msj-error"><?= $validation->getError($campo);?></div>
                                        <?php }?>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-6">
                                    <div class="form-floating">
                                        <?php
                                            $campo = 'telefonos';
                                            echo form_input(array(
                                                'name' => $campo,
                                                'id' => $campo,
                                                'class' => 'form-control mayusculas',
                                                'placeholder' => 'Celular',
                                                'required' => 'true',
                                                'value' => set_value($campo,'',false)
                                            ));
                                        ?>
                                        <label for="<?= $campo?>">Celular * :</label>
                                        <div class="invalid-feedback">Debe ingresar este campo.</div>
                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                            <div class="msj-error"><?= $validation->getError($campo);?></div>
                                        <?php }?>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-6">
                                    <div class="form-floating">
                                        <?php
                                            $campo = 'email';
                                            echo form_input(array(
                                                'name' => $campo,
                                                'id' => $campo,
                                                'class' => 'form-control',
                                                'placeholder' => 'E-Mail',
                                                'type' => 'email',
                                                'value' => set_value($campo,'',false)
                                            ));
                                        ?>
                                        <label for="<?= $campo?>">E-Mail :</label>
                                        <div class="invalid-feedback">Debe ingresar correctamente este campo.</div>
                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                            <div class="msj-error"><?= $validation->getError($campo);?></div>
                                        <?php }?>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-12">
                                    <div class="form-floating">
                                        <?php
                                            $campo = 'direccion';
                                            echo form_textarea(array(
                                                'name' => $campo,
                                                'id' => $campo,
                                                'class' => 'form-control mayusculas',
                                                'placeholder' => 'Dirección',
                                                'style' => "height: 80px",
                                                'required' => 'true',
                                                'value' => set_value($campo,'',false)
                                            ));
                                        ?>
                                        <label for="<?= $campo?>">Dirección * :</label>
                                        <div class="invalid-feedback">Debe ingresar este campo.</div>
                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                            <div class="msj-error"><?= $validation->getError($campo);?></div>
                                        <?php }?>
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="row">
                                        <?php $campo = 'documento_identidad_digital'; ?>
                                        <label for="<?= $campo?>" class="col-sm-6 col-form-label">Documento de Identidad Digital (.pdf) (Max. 5 MB) * :</label>
                                        <div class="col-sm-6">
                                            <?php
                                                echo form_upload(array(
                                                    'name' => $campo,
                                                    'id' => $campo,
                                                    'class' => 'form-control',
                                                    'accept' => '.pdf',
                                                    'required' => 'true',
                                                ));
                                            ?>
                                            <div class="invalid-feedback">Debe ingresar este campo.</div>
                                            <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                <div class="msj-error"><?= $validation->getError($campo);?></div>
                                            <?php }?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card text-bg-light mb-3">
                        <div class="card-header text-center">
                            <strong>DESCRIPCIÓN DE LA ACTIVIDAD MINERA</strong><br>
                            <small>Datos obligatorios (*)</small><br>
                            <small>Recuerde realizar su denuncia de la forma más clara y detallada posible. Para iniciar una investigación, se requiere contar con los detalles de los hechos, los nombres de los intervinientes y toda la información que sea de utilidad.</small>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-sm-12 col-md-4">
                                    <div class="form-floating">
                                        <?php
                                            $campo = 'departamento';
                                            echo form_dropdown($campo, $departamentos, set_value($campo, ''), array('id'=>$campo, 'class'=>'form-select', 'aria-label'=>"Departamento", 'required'=>'true'));
                                        ?>
                                        <label for="<?= $campo?>">Departamento * :</label>
                                        <div class="invalid-feedback">Debe seleccionar una opción.</div>
                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                            <div class="msj-error"><?= $validation->getError($campo);?></div>
                                        <?php }?>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-4">
                                    <div class="form-floating">
                                        <?php
                                            $campo = 'provincia';
                                            echo form_dropdown($campo, $provincias, set_value($campo, ''), array('id'=>$campo, 'class'=>'form-select', 'aria-label'=>"Provincia", 'required'=>'true'));
                                        ?>
                                        <label for="<?= $campo?>">Provincia * :</label>
                                        <div class="invalid-feedback">Debe seleccionar una opción.</div>
                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                            <div class="msj-error"><?= $validation->getError($campo);?></div>
                                        <?php }?>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-4">
                                    <div class="form-floating">
                                        <?php
                                            $campo = 'fk_municipio';
                                            echo form_dropdown($campo, $municipios, set_value($campo, ''), array('id'=>$campo, 'class'=>'form-select', 'aria-label'=>"Municipio", 'required'=>'true'));
                                        ?>
                                        <label for="<?= $campo?>">Municipio * :</label>
                                        <div class="invalid-feedback">Debe seleccionar una opción.</div>
                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                            <div class="msj-error"><?= $validation->getError($campo);?></div>
                                        <?php }?>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-floating">
                                        <?php
                                            $campo = 'comunidad_localidad';
                                            echo form_input(array(
                                                'name' => $campo,
                                                'id' => $campo,
                                                'class' => 'form-control mayusculas',
                                                'placeholder' => 'Comunidad/Localidad',
                                                'required' => 'true',
                                                'value' => set_value($campo,'',false)
                                            ));
                                        ?>
                                        <label for="<?= $campo?>">Comunidad/Localidad * :</label>
                                        <div class="invalid-feedback">Debe ingresar este campo.</div>
                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                            <div class="msj-error"><?= $validation->getError($campo);?></div>
                                        <?php }?>
                                    </div>
                                </div>                                
                                <div class="col-sm-11">
                                    <div class="form-floating">
                                        <?php
                                            $campo = 'descripcion_lugar';
                                            echo form_textarea(array(
                                                'name' => $campo,
                                                'id' => $campo,
                                                'class' => 'form-control mayusculas',
                                                'placeholder' => 'Descripción del lugar o punto de referencia',
                                                'style' => "height: 100px",
                                                'required' => 'true',
                                                'value' => set_value($campo,'',false)
                                            ));
                                        ?>
                                        <label for="<?= $campo?>">Descripción del lugar o punto de referencia * :</label>
                                        <div class="invalid-feedback">Debe ingresar este campo.</div>
                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                            <div class="msj-error"><?= $validation->getError($campo);?></div>
                                        <?php }?>
                                    </div>
                                </div>
                                <div class="col-sm-1 text-start">
                                    <span data-bs-toggle="tooltip" data-bs-placement="top" title="Brindar información que permita ubicar el área denunciada (Área Protegida, Rio u otros)."><i class="fa fa-question-circle"></i></span>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-floating">
                                        <?php
                                            $campo = 'autores';
                                            echo form_textarea(array(
                                                'name' => $campo,
                                                'id' => $campo,
                                                'class' => 'form-control mayusculas',
                                                'placeholder' => 'Nombre(s) del posible(s) autor(es)',
                                                'style' => "height: 100px",
                                                'value' => set_value($campo,'',false)
                                            ));
                                        ?>
                                        <label for="<?= $campo?>">Nombre(s) del posible(s) autor(es) :</label>
                                        <div class="invalid-feedback">Debe ingresar este campo.</div>
                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                            <div class="msj-error"><?= $validation->getError($campo);?></div>
                                        <?php }?>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-floating">
                                        <?php
                                            $campo = 'persona_juridica';
                                            echo form_textarea(array(
                                                'name' => $campo,
                                                'id' => $campo,
                                                'class' => 'form-control mayusculas',
                                                'placeholder' => 'Nombre de la persona jurídica (empresa, cooperativa u otro) que este vinculado a la actividad',
                                                'style' => "height: 100px",
                                                'value' => set_value($campo,'',false)
                                            ));
                                        ?>
                                        <label for="<?= $campo?>">Nombre de la persona jurídica (empresa, cooperativa u otro) que este vinculado a la actividad :</label>
                                        <div class="invalid-feedback">Debe ingresar este campo.</div>
                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                            <div class="msj-error"><?= $validation->getError($campo);?></div>
                                        <?php }?>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-floating">
                                        <?php
                                            $campo = 'descripcion_materiales';
                                            echo form_textarea(array(
                                                'name' => $campo,
                                                'id' => $campo,
                                                'class' => 'form-control mayusculas',
                                                'placeholder' => 'Descripción de la maquinaria(s) u objeto(s) utilizado(s) en la actividad',
                                                'style' => "height: 100px",
                                                'value' => set_value($campo,'',false)
                                            ));
                                        ?>
                                        <label for="<?= $campo?>">Descripción de la maquinaria u objeto(s) utilizado(s) en la actividad :</label>
                                        <div class="invalid-feedback">Debe ingresar este campo.</div>
                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                            <div class="msj-error"><?= $validation->getError($campo);?></div>
                                        <?php }?>
                                    </div>
                                </div>                                
                                <div class="col-sm-12">
                                    <div class="form-floating">
                                        <?php
                                            $campo = 'areas_denunciadas';
                                            echo form_textarea(array(
                                                'name' => $campo,
                                                'id' => $campo,
                                                'class' => 'form-control mayusculas',
                                                'placeholder' => 'Nombre del área o correlativo de la solicitud en caso de estar en trámite en la AJAM',
                                                'style' => "height: 100px",
                                                'value' => set_value($campo,'',false)
                                            ));
                                        ?>
                                        <label for="<?= $campo?>">Nombre del área o correlativo de la solicitud en caso de estar en trámite en la AJAM :</label>
                                        <div class="invalid-feedback">Debe ingresar este campo.</div>
                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                            <div class="msj-error"><?= $validation->getError($campo);?></div>
                                        <?php }?>
                                    </div>
                                </div>                                
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card text-bg-light mb-3">
                        <div class="card-header text-center">
                            <strong>COORDENADA(S) GEOGRÁFICA(S)</strong><br>
                            <small>Haga doble click para seleccionar uno o mas puntos (Seleccione la ubicación más precisa)</small>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-sm-12">
                                    <div id="mi-map" class="set-map"></div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-floating">
                                        <?php
                                            $campo = 'coordenadas';
                                            echo form_textarea(array(
                                                'name' => $campo,
                                                'id' => $campo,
                                                'class' => 'form-control mayusculas',
                                                'placeholder' => 'Coordenada(s)',
                                                'style' => "height: 100px",
                                                'required' => 'true',
                                                'readonly' => 'true',
                                                'value' => set_value($campo,'',false)
                                            ));
                                        ?>
                                        <label for="<?= $campo?>">Coordenada(s) * :</label>
                                        <div class="invalid-feedback">Debe ingresar este campo.</div>
                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                            <div class="msj-error"><?= $validation->getError($campo);?></div>
                                        <?php }?>
                                    </div>
                                </div>
                                <div class="col-sm-12 text-center">
                                    <button type="button" class="btn btn-danger" onclick="limpiarCoordenadas();" title="Borrar Coordenadas"><i class="fa-regular fa-trash-can"></i> Borrar Coordenada(s) </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card text-bg-light mb-3">
                        <div class="card-header text-center">
                            <strong>ADJUNTO(S)</strong><br>
                            <small>Puede agregar imágenes que permitan una mayor claridad sobre los hechos denunciados. Tenga en cuenta que de no presentar pruebas torna más difícil la investigación.</small><br>
                            <small>Extensiones permitidas: .png, .jpg, .jpeg con un tamaño máximo de 5 MB.</small>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-sm-12">
                                    <div class="row">
                                        <?php $campo = 'adjunto_uno'; ?>
                                        <label for="<?= $campo?>" class="col-sm-2 col-form-label">Fotografía 1 * :</label>
                                        <div class="col-sm-10">
                                            <?php
                                                echo form_upload(array(
                                                    'name' => $campo,
                                                    'id' => $campo,
                                                    'class' => 'form-control',
                                                    'accept' => 'image/*',
                                                    'required' => 'true',
                                                ));
                                            ?>
                                            <div class="invalid-feedback">Debe ingresar este campo.</div>
                                            <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                <div class="msj-error"><?= $validation->getError($campo);?></div>
                                            <?php }?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="row">
                                        <?php $campo = 'adjunto_dos'; ?>
                                        <label for="<?= $campo?>" class="col-sm-2 col-form-label">Fotografía 2 :</label>
                                        <div class="col-sm-10">
                                            <?php
                                                echo form_upload(array(
                                                    'name' => $campo,
                                                    'id' => $campo,
                                                    'class' => 'form-control',
                                                    'accept' => 'image/*',
                                                ));
                                            ?>
                                            <div class="invalid-feedback">Debe ingresar este campo.</div>
                                            <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                <div class="msj-error"><?= $validation->getError($campo);?></div>
                                            <?php }?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="row">
                                        <?php $campo = 'adjunto_tres'; ?>
                                        <label for="<?= $campo?>" class="col-sm-2 col-form-label">Fotografía 3 :</label>
                                        <div class="col-sm-10">
                                            <?php
                                                echo form_upload(array(
                                                    'name' => $campo,
                                                    'id' => $campo,
                                                    'class' => 'form-control',
                                                    'accept' => 'image/*',
                                                ));
                                            ?>
                                            <div class="invalid-feedback">Debe ingresar este campo.</div>
                                            <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                <div class="msj-error"><?= $validation->getError($campo);?></div>
                                            <?php }?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card text-bg-light mb-3">
                        <div class="card-header text-center">
                            <strong>CODIGO DE SEGURIDAD</strong><br>
                            <small>El codigo es sensible de minúsculas y mayusculas</small>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-sm-3"> </div>
                                <div class="col-sm-4 text-center"><canvas id="codigo_seguridad_imagen" class="codigo_seguridad_imagen" style="border: 1px solid #CBCCCD;"></canvas></div>
                                <div class="col-sm-1 text-center"><button type="button" id="codigo_seguridad_refresh" class="btn btn-outline-primary"><i class="fa-solid fa-arrow-rotate-right"></i></button></div>
                                <div class="col-sm-3"> </div>

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
                                            ));
                                        ?>
                                        <label for="<?= $campo?>">Código de Seguridad * :</label>
                                        <div class="invalid-feedback">Debe ingresar correctamente el Código de Seguridad (El codigo es sensible de minúsculas y mayusculas).</div>
                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                            <div class="msj-error"><?= $validation->getError($campo);?></div>
                                        <?php }?>
                                    </div>
                                </div>
                            </div>
                            <hr class="my-4">
                            <button class="w-100 btn btn-success btn-lg" type="submit">ENVIAR DENUNCIA</button>
                        </div>
                    </div>
                </div>
                <?= form_close();?>
            </div>
        </main>

        <footer class="my-5 pt-5 text-body-secondary text-center text-small">
            <p class="mb-1">Copyright &copy; 2023 | AJAM, Reservados todos los derechos.</p>
        </footer>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="<?= base_url('assets/seguimiento/js/color-modes.js');?>"></script>
    <script src="<?= base_url('assets/seguimiento/js/bootstrap.bundle.min.js');?>"></script>
    <script src="<?= base_url('assets/seguimiento/js/jquery-captcha.min.js');?>"></script>
    <!-- sweet alert js -->
    <script type="text/javascript" src="<?= base_url('assets/bower_components/sweetalert/js/sweetalert.min.js');?>"></script>
    <script type="text/javascript" src="<?= base_url('assets/pages/leaflet/leaflet.js');?>"></script>
    <script type="text/javascript" src="<?= base_url('assets/pages/leaflet/leaflet-control-geocoder/Control.Geocoder.js');?>"></script>
    <script type="text/javascript" src="<?= base_url('assets/pages/leaflet/custom.js');?>"></script>
    <script src="<?= base_url('assets/js/mineria-ilegal.js');?>"></script>
</body>

</html>
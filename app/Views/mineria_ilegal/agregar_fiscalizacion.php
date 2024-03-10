<div class="page-wrapper">
    <?= $title?>
    <div class="page-body">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Formulario</h5>
                        <span>Los campos con <code>*</code> son obligatorios.</span>
                    </div>
                    <div class="card-block">
                        <?= form_open_multipart($accion, ['id'=>'formulario']);?>

                        <div class="form-group row d-none">
                            <div class="col-sm-10">
                                <?= form_input(array('type'=>'hidden','name'=>'tmp_adjuntos','id'=>'tmp_adjuntos','value'=>set_value('tmp_adjuntos', 0)));?>
                                <span class="messages"></span>
                            </div>
                        </div>

                        <!-- Row start -->
                        <div class="row">
                            <div class="col-lg-12 col-xl-12">
                                <!-- Nav tabs -->
                                <ul class="nav nav-tabs  tabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-toggle="tab" href="#datos_personales" role="tab"><strong>Datos Personales</strong></a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link " data-toggle="tab" href="#descripcion_explotacion" role="tab"><strong>Descripción de la Explotación Ilegal</strong></a>
                                    </li>
                                    <li class="nav-item">
                                    <a class="nav-link " data-toggle="tab" href="#coordenadas_geograficas" onclick="redimensionar()" role="tab"><strong>Coordenada(s) Geográfica(s)</strong></a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link " data-toggle="tab" href="#adjuntos" role="tab"><strong>Adjunto(s)</strong></a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#hoja_ruta" role="tab"><strong>Hoja de Ruta</strong></a>
                                    </li>
                                </ul>
                                <!-- Tab panes -->
                                <div class="tab-content tabs card-block">
                                    <div class="tab-pane active" id="datos_personales" role="tabpanel">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group row">
                                                    <label class="col-sm-2 col-form-label">Buscar Persona:</label>
                                                    <div class="col-sm-7">
                                                        <?php $campo = 'id_denunciante'; ?>
                                                        <select id="<?= $campo; ?>" name="<?= $campo; ?>" class="denunciante-ajax col-sm-12">
                                                            <option value="">Escriba el Documento de Identidad o Nombre de la Persona</option>
                                                        </select>
                                                        <span class="messages"></span>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <button type="button" class="btn btn-info denunciante-limpiar"><i class="fa fa-minus-square"></i> Deseleccionar Persona</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-sm-12">
                                                <div class="form-group row">
                                                    <label class="col-sm-2 col-form-label">Nombre(s) * :</label>
                                                    <div class="col-sm-10">
                                                        <?php
                                                            $campo = 'nombres';
                                                            echo form_input(array(
                                                                'name' => $campo,
                                                                'id' => $campo,
                                                                'class' => 'form-control form-control-uppercase',
                                                                'value' => set_value($campo,(isset($denuncia[$campo]) ? $denuncia[$campo] : ''),false)
                                                            ));
                                                        ?>
                                                        <span class="messages"></span>
                                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                            <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                        <?php }?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-sm-12">
                                                <div class="form-group row">
                                                    <label class="col-sm-2 col-form-label">Apellido(s) * :</label>
                                                    <div class="col-sm-10">
                                                        <?php
                                                            $campo = 'apellidos';
                                                            echo form_input(array(
                                                                'name' => $campo,
                                                                'id' => $campo,
                                                                'class' => 'form-control form-control-uppercase',
                                                                'value' => set_value($campo,(isset($denuncia[$campo]) ? $denuncia[$campo] : ''),false)
                                                            ));
                                                        ?>
                                                        <span class="messages"></span>
                                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                            <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                        <?php }?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-12">
                                                <div class="form-group row">
                                                    <label class="col-sm-5 col-form-label">Documento de Identidad * : </label>
                                                    <div class="col-sm-7">
                                                        <?php
                                                            $campo = 'documento_identidad';
                                                            echo form_input(array(
                                                                'name' => $campo,
                                                                'id' => $campo,
                                                                'type' => 'number',
                                                                'class' => 'form-control form-control-uppercase',
                                                                'value' => set_value($campo,(isset($denuncia[$campo]) ? $denuncia[$campo] : ''),false)
                                                            ));
                                                        ?>
                                                        <span class="messages"></span>
                                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                            <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                        <?php }?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-12">
                                                <div class="form-group row">
                                                    <label class="col-sm-3 col-form-label">Expedido * :</label>
                                                    <div class="col-sm-9">
                                                        <?php
                                                            $campo = 'expedido';
                                                            echo form_dropdown($campo, $expedidos, set_value($campo, set_value($campo,(isset($denuncia[$campo]) ? $denuncia[$campo] : ''))), array('id' => $campo, 'class' => 'form-control'));
                                                        ?>
                                                        <span class="messages"></span>
                                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                            <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                        <?php }?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-12">
                                                <div id="documento_identidad_digital_ventanilla" class="form-group row" style="display: none;">
                                                    <label class="col-sm-6 col-form-label">Documento de Identidad Digital : </label>
                                                    <div class="col-sm-6">
                                                        <a id="documento_identidad_digital_actual" href="" class="btn btn-inverse" target="_blank"><i class="icofont icofont-download-alt"></i>Descargar</a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-12">
                                                <div class="form-group row">
                                                    <label class="col-sm-3 col-form-label">Dirección * :</label>
                                                    <div class="col-sm-9">
                                                        <?php
                                                            $campo = 'direccion';
                                                            echo form_textarea(array(
                                                                'name' => $campo,
                                                                'id' => $campo,
                                                                'rows' => '4',
                                                                'class' => 'form-control form-control-uppercase',
                                                                'value' => set_value($campo,(isset($denuncia[$campo]) ? $denuncia[$campo] : ''),false)
                                                            ));
                                                        ?>
                                                        <span class="messages"></span>
                                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                            <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                        <?php }?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-12">
                                                <div class="form-group row">
                                                    <label class="col-sm-3 col-form-label">Celular * : </label>
                                                    <div class="col-sm-9">
                                                        <?php
                                                            $campo = 'telefonos';
                                                            echo form_input(array(
                                                                'name' => $campo,
                                                                'id' => $campo,
                                                                'class' => 'form-control form-control-uppercase',
                                                                'value' => set_value($campo,(isset($denuncia[$campo]) ? $denuncia[$campo] : ''),false)
                                                            ));
                                                        ?>
                                                        <span class="messages"></span>
                                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                            <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                        <?php }?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-12">
                                                <div class="form-group row">
                                                    <label class="col-sm-2 col-form-label">E-Mail :</label>
                                                    <div class="col-sm-10">
                                                        <?php
                                                            $campo = 'email';
                                                            echo form_input(array(
                                                                'name' => $campo,
                                                                'id' => $campo,
                                                                'class' => 'form-control',
                                                                'value' => set_value($campo,(isset($denuncia[$campo]) ? $denuncia[$campo] : ''),false)
                                                            ));
                                                        ?>
                                                        <span class="messages"></span>
                                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                            <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                        <?php }?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-8">
                                                <div class="form-group row">
                                                    <label class="col-sm-6 col-form-label">Documento de Identidad Digital (.pdf) (Max. 20MB) * :</label>
                                                    <div class="col-sm-6">
                                                        <?php
                                                        $campo = 'documento_identidad_digital';
                                                        echo form_upload(array(
                                                            'name' => $campo,
                                                            'id' => $campo,
                                                            'class' => 'form-control',
                                                            'accept' => '.pdf',
                                                        ));
                                                        ?>
                                                        <span class="messages"></span>
                                                        <?php if (isset($validation) && $validation->hasError($campo)) { ?>
                                                            <span class="form-bar text-danger"><?= $validation->getError($campo); ?></span>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane " id="descripcion_explotacion" role="tabpanel">
                                        <div class="row">
                                            <div class="col-md-4 col-sm-12">
                                                <div class="form-group row">
                                                    <label class="col-sm-4 col-form-label">Departamento * :</label>
                                                    <div class="col-sm-8">
                                                        <?php
                                                            $campo = 'departamento';
                                                            echo form_dropdown($campo, $departamentos, set_value($campo, set_value($campo,(isset($municipio[$campo]) ? $municipio[$campo] : ''))), array('id' => $campo, 'class' => 'form-control'));
                                                        ?>
                                                        <span class="messages"></span>
                                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                            <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                        <?php }?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-12">
                                                <div class="form-group row">
                                                    <label class="col-sm-4 col-form-label">Provincia * :</label>
                                                    <div class="col-sm-8">
                                                        <?php
                                                            $campo = 'provincia';
                                                            echo form_dropdown($campo, $provincias, set_value($campo, set_value($campo,(isset($municipio[$campo]) ? $municipio[$campo] : ''))), array('id' => $campo, 'class' => 'form-control'));
                                                        ?>
                                                        <span class="messages"></span>
                                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                            <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                        <?php }?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-12">
                                                <div class="form-group row">
                                                    <label class="col-sm-4 col-form-label">Municipio * :</label>
                                                    <div class="col-sm-8">
                                                        <?php
                                                            $campo = 'fk_municipio';
                                                            echo form_dropdown($campo, $municipios, set_value($campo, set_value($campo,(isset($denuncia[$campo]) ? $denuncia[$campo] : ''))), array('id' => $campo, 'class' => 'form-control'));
                                                        ?>
                                                        <span class="messages"></span>
                                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                            <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                        <?php }?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-sm-12">
                                                <div class="form-group row">
                                                    <label class="col-sm-2 col-form-label">Comunidad/Localidad * : </label>
                                                    <div class="col-sm-10">
                                                        <?php
                                                            $campo = 'comunidad_localidad';
                                                            echo form_input(array(
                                                                'name' => $campo,
                                                                'id' => $campo,
                                                                'class' => 'form-control form-control-uppercase',
                                                                'value' => set_value($campo,(isset($denuncia[$campo]) ? $denuncia[$campo] : ''),false)
                                                            ));
                                                        ?>
                                                        <span class="messages"></span>
                                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                            <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                        <?php }?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-sm-12">
                                                <div class="form-group row">
                                                    <label class="col-sm-2 col-form-label">Descripción del Lugar o Punto de Referencia <span class="mytooltip tooltip-effect-5">
                                                        <span class="tooltip-item"><i class="fa fa-question-circle"></i></span>
                                                        <span class="tooltip-content clearfix">
                                                            <span class="tooltip-text">Se debe describir el lugar donde se realiza la actividad minera ilegal y el nombre del mineral que se explota.</span>
                                                        </span>
                                                    </span> * : </label>
                                                    <div class="col-sm-10">
                                                        <?php
                                                            $campo = 'descripcion_lugar';
                                                            echo form_textarea(array(
                                                                'name' => $campo,
                                                                'id' => $campo,
                                                                'rows' => '3',
                                                                'class' => 'form-control form-control-uppercase',
                                                                'value' => set_value($campo,(isset($denuncia[$campo]) ? $denuncia[$campo] : ''),false)
                                                            ));
                                                        ?>
                                                        <span class="messages"></span>
                                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                            <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                        <?php }?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-sm-12">
                                                <div class="form-group row">
                                                    <label class="col-sm-2 col-form-label">Nombre de los posibles autores : </label>
                                                    <div class="col-sm-10">
                                                        <?php
                                                            $campo = 'autores';
                                                            echo form_textarea(array(
                                                                'name' => $campo,
                                                                'id' => $campo,
                                                                'rows' => '3',
                                                                'class' => 'form-control form-control-uppercase',
                                                                'value' => set_value($campo,(isset($denuncia[$campo]) ? $denuncia[$campo] : ''),false)
                                                            ));
                                                        ?>
                                                        <span class="messages"></span>
                                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                            <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                        <?php }?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-sm-12">
                                                <div class="form-group row">
                                                    <label class="col-sm-2 col-form-label">Nombre de la persona(s) jurídica(s) (empresa, cooperativa u otro) que están vinculada(s) a la actividad : </label>
                                                    <div class="col-sm-10">
                                                        <?php
                                                            $campo = 'persona_juridica';
                                                            echo form_textarea(array(
                                                                'name' => $campo,
                                                                'id' => $campo,
                                                                'rows' => '3',                                                                
                                                                'class' => 'form-control form-control-uppercase',
                                                                'value' => set_value($campo,(isset($denuncia[$campo]) ? $denuncia[$campo] : ''),false)
                                                            ));
                                                        ?>
                                                        <span class="messages"></span>
                                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                            <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                        <?php }?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-sm-12">
                                                <div class="form-group row">
                                                    <label class="col-sm-2 col-form-label">Descripción de la maquinaria(s) o instrumento(s) utilizado(s) <span class="mytooltip tooltip-effect-5">
                                                        <span class="tooltip-item"><i class="fa fa-question-circle"></i></span>
                                                        <span class="tooltip-content clearfix">
                                                            <span class="tooltip-text">Ejemplo: Maquinarias, vehículos, gasolina, etc.</span>
                                                        </span>
                                                    </span> : </label>
                                                    <div class="col-sm-10">
                                                        <?php
                                                            $campo = 'descripcion_materiales';
                                                            echo form_textarea(array(
                                                                'name' => $campo,
                                                                'id' => $campo,
                                                                'rows' => '3',
                                                                'class' => 'form-control form-control-uppercase',
                                                                'value' => set_value($campo,(isset($denuncia[$campo]) ? $denuncia[$campo] : ''),false)
                                                            ));
                                                        ?>
                                                        <span class="messages"></span>
                                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                            <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                        <?php }?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane " id="coordenadas_geograficas" role="tabpanel">
                                        <div class="row">
                                            <div class="col-md-12 col-sm-12">
                                                <div id="mi-map" class="set-map"></div>
                                            </div>
                                            <div class="col-md-12 col-sm-12 mt-4">
                                                <div class="form-group row">
                                                    <label class="col-sm-2 col-form-label">Coordenada(s) : </label>
                                                    <div class="col-sm-8">
                                                        <?php
                                                            $campo = 'coordenadas';
                                                            echo form_textarea(array(
                                                                'name' => $campo,
                                                                'id' => $campo,
                                                                'rows' => '3',
                                                                'class' => 'form-control form-control-uppercase',
                                                                'value' => set_value($campo,(isset($coordenadas) ? $coordenadas : ''),false)
                                                            ));
                                                        ?>
                                                        <span class="messages"></span>
                                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                            <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                        <?php }?>
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <button type="button" class="btn btn-warning" onclick="limpiarCoordenadas();"><i class="fa fa-trash-o"></i> Borrar Coordenada(s) </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane " id="adjuntos" role="tabpanel">
                                        <p>Puede agregar imágenes, documentos, vídeos y audios que permitan una mayor claridad sobre los hechos denunciados.</p>
                                        <p>Extensiones permitidas de Imágen: jpg, gif, bmp, png; de Documento: txt, doc, docx, pdf; de Vídeo: avi, mp4, mpeg, mwv; de Audio: mp3, wav, wma con un tamaño máximo de 20 MB.</p>
                                        <div class="row">
                                            <div class="col-sm-12 text-center mb-3">
                                                <button type="button" class="btn btn-inverse agregar_adjunto"><i class="fa fa-plus-square"></i> Agregar Nuevo Adjunto</button>
                                            </div>
                                            <div class="col-md-12 col-sm-12">
                                            <table id="tabla_adjuntos" class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">Tipo</th>
                                                            <th class="text-center">Nombre</th>
                                                            <th class="text-center">Adjunto</th>
                                                            <th class="text-center"> </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        
                                                    </tbody>
                                                </table>
                                                
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane " id="hoja_ruta" role="tabpanel">
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Dirección Departamental/Regional * :</label>
                                            <div class="col-sm-10">
                                                <?php
                                                    $campo = 'fk_oficina';
                                                    echo form_dropdown($campo, $oficinas, set_value($campo, set_value($campo,(isset($denuncia[$campo]) ? $denuncia[$campo] : ''))), array('id' => $campo, 'class' => 'form-control'));
                                                ?>
                                                <span class="messages"></span>
                                                <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                    <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                <?php }?>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Destinatario*:</label>
                                            <div class="col-sm-10">
                                                <?php $campo = 'fk_usuario_destinatario';?>
                                                <select id="<?= $campo;?>" name="<?= $campo;?>" class="analista-destinatario-ajax col-sm-12">
                                                    <?php if(isset($usu_destinatario)){ ?>
                                                        <option value="<?= $usu_destinatario['id'];?>"><?= $usu_destinatario['nombre'];?></option>
                                                    <?php }else{ ?>
                                                        <option value="">Escriba el Nombre o Cargo...</option>
                                                    <?php } ?>
                                                </select>
                                                <span class="messages"></span>
                                                <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                    <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                <?php }?>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Instrucción*:</label>
                                            <div class="col-sm-10">
                                                <?php
                                                    $campo = 'instruccion';
                                                    echo form_textarea(array(
                                                        'name' => $campo,
                                                        'id' => $campo,
                                                        'rows' => '3',
                                                        'class' => 'form-control form-control-uppercase',
                                                        'value' => set_value($campo,'',false)
                                                    ));
                                                ?>
                                                <span class="messages"></span>
                                                <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                    <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                <?php }?>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Anexar H.R.: <span class="mytooltip tooltip-effect-5">
                                                <span class="tooltip-item"><i class="fa fa-question-circle"></i></span>
                                                <span class="tooltip-content clearfix">
                                                    <span class="tooltip-text">Debe escribir el(los) correlativo(s) de la Hoja de Ruta Interna o Externa que desea anexar al Tramite.</span>
                                                </span>
                                            </span> : </label>
                                            <div class="col-sm-10">
                                                <?php $campo = 'anexar_hr';?>
                                                <select id="<?= $campo;?>" name="<?= $campo;?>[]" class="hr-in-ex-ajax col-sm-12" multiple="multiple">
                                                    <option value="">Escriba el correlativo de la Hoja de Ruta Interna o Externa...</option>
                                                </select>
                                                <span class="messages"></span>
                                                <span class="form-bar"><b>Nota.</b> La(s) H.R. no deben estar archivadas o anexadas en el SINCOBOL caso contrario no aparecera para su selección.</span>
                                                <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                    <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                <?php }?>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Motivo de Anexar:</label>
                                            <div class="col-sm-10">
                                                <?php
                                                    $campo = 'motivo_anexo';
                                                    echo form_textarea(array(
                                                        'name' => $campo,
                                                        'id' => $campo,
                                                        'rows' => '3',
                                                        'class' => 'form-control form-control-uppercase',
                                                        'value' => set_value($campo,'',false)
                                                    ));
                                                ?>
                                                <span class="messages"></span>
                                                <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                    <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                <?php }?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Row end -->
                        <div class="form-group row">
                            <label class="col-sm-8"></label>
                            <div class="col-sm-4 text-right">
                                <?php echo form_submit('enviar', 'GUARDAR','class="btn btn-primary m-b-0"');?>
                                <a href="<?= base_url($controlador.'mis_tramites');?>" class="btn btn-success m-b-0">CANCELAR</a>
                            </div>
                        </div>
                        <?= form_close();?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

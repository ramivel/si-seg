<!-- Page-header start -->
<div class="page-header">
        <div class="row align-items-end">
            <div class="col-lg-8">
                <div class="page-header-title">
                    <div class="d-inline">
                        <?php if(isset($titulo)){?>
                            <h4><?= $titulo?></h4>
                        <?php }?>
                        <?php if(isset($subtitulo)){?>
                            <span><?= $subtitulo?></span>
                        <?php }?>                        
                    </div>
                </div>
            </div>
            <?php if(isset($navegador) && $navegador){?>
            <!--div class="col-lg-4">
                <div class="page-header-breadcrumb">
                    <ul class="breadcrumb-title">
                        <li class="breadcrumb-item">
                            <a href="<?= base_url('dashboard');?>"> <i class="feather icon-home"></i></a>
                        </li>
                        <li class="breadcrumb-item"><a href="<?= current_url();?>"><?= $titulo?></a></li>
                    </ul>
                </div>
            </div-->
            <?php }?>            
        </div>
    </div>
    <!-- Page-header end -->
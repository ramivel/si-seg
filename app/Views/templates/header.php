<nav class="navbar header-navbar pcoded-header">
    <div class="navbar-wrapper">
        <div class="navbar-logo">
            <a class="mobile-menu" id="mobile-collapse" href="#!">
                <i class="feather icon-menu"></i>
            </a>
            <a href="<?=base_url('dashboard');?>">
                <img class="img-fluid" src="<?=base_url('assets/images/logo.png');?>" alt="Theme-Logo" width="180px">
            </a>
            <a class="mobile-options">
                <i class="feather icon-more-horizontal"></i>
            </a>
        </div>
        <div class="navbar-container container-fluid">
            <ul class="nav-left">
                <li>
                    <h3 class="titulo-sistema"><?=TITLE_PAGE;?></h3>
                </li>
            </ul>
            <ul class="nav-right">
                <li class="user-profile header-notification">
                    <div class="dropdown-primary dropdown">
                        <div class="dropdown-toggle" data-toggle="dropdown">
                            <img src="<?=base_url('assets/images/usuario.jpg');?>" class="img-radius" alt="User-Profile-Image">
                            <span><?=session()->get('registroUserName');?></span>
                            <i class="feather icon-chevron-down"></i>
                        </div>
                        <ul class="show-notification profile-notification dropdown-menu" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                            <li><?php echo anchor('usuarios/cambiar_contraseña_usuario', '<i class="feather icon-lock"></i> Cambiar Contraseña'); ?></li>
                            <li><?php echo anchor('autenticacion/logout', '<i class="feather icon-log-out"></i> Salir del Sistema'); ?></li>
                        </ul>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>
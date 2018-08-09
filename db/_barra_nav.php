
<?php if (($_SESSION['MM_UserGroup'] ==  'Adm') or ($_SESSION['MM_UserGroup'] ==  'Vendedor')):?> 
    <nav class="navbar navbar-fixed-top navbar-inverse">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Navegar</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="inicio.php">Inicio</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="dropdown">
              <a aria-expanded="false" aria-haspopup="true" role="button" data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="fa fa-user fa-fw" aria-hidden="true"></i> Ctes<span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li><a href="clientes.php"><i class="fa fa-list fa-fw" aria-hidden="true"></i> Listar</a></li>
                <li class="divider" role="separator"></li>
                <li><a href="clientes_insertar.php"><i class="fa fa-plus fa-fw" aria-hidden="true"></i> Agregar</a></li>
                <li><a href="clientes_buscador.php"><i class="fa fa-search fa-fw" aria-hidden="true"></i> Buscar</a></li>
                </ul>
            </li>
            
            <li class="dropdown">
              <a aria-expanded="false" aria-haspopup="true" role="button" data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="fa fa-link fa-fw" aria-hidden="true"></i> Ctas<span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li><a href="cuentas.php"><i class="fa fa-list fa-fw" aria-hidden="true"></i> Listar</a></li>
                <li><a href="cuentas_con_saldo.php"><i class="fa fa-list fa-fw" aria-hidden="true"></i> Saldo libre</a></li>
				<li><a href="cuentas_para_ps3.php"><i class="fa fa-list fa-fw" aria-hidden="true"></i> Para Juego PS3</a></li>
				<li><a href="cuentas_para_ps4.php"><i class="fa fa-list fa-fw" aria-hidden="true"></i> Para Juego PS4</a></li>
                <li class="divider" role="separator"></li>
                <li><a href="cuentas_insertar.php"><i class="fa fa-plus fa-fw" aria-hidden="true"></i> Agregar</a></li>
                <li><a href="cuentas_buscador.php"><i class="fa fa-search fa-fw" aria-hidden="true"></i> Buscar</a></li>
              </ul>
            </li>
            
            <li class="dropdown">
              <a aria-expanded="false" aria-haspopup="true" role="button" data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="fa fa-gamepad fa-fw" aria-hidden="true"></i> Stk<span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li><a href="stock.php"><i class="fa fa-list fa-fw" aria-hidden="true"></i> Listar</a></li>
                <li><a href="catalogo_link_ps_store.php"><i class="fa fa-list fa-fw" aria-hidden="true"></i> Link PS Store</a></li>
				<li><a href="productos_catalogo.php"><i class="fa fa-list fa-fw" aria-hidden="true"></i> Catalogo Completo</a></li>
                <li class="divider" role="separator"></li>
                <li><a href="stock_insertar.php"><i class="fa fa-plus fa-fw" aria-hidden="true"></i> Agregar</a></li>
                <li><a href="stock_buscador.php"><i class="fa fa-search fa-fw" aria-hidden="true"></i> Buscar</a></li>
                <?php if (($_SESSION['MM_UserGroup'] ==  'Adm') or ($_SESSION['MM_UserName'] == "Francisco")):?> 
                <li class="divider" role="separator"></li>
                <li><a href="stock_insertar_codigos.php"><i class="fa fa-gift fa-fw" aria-hidden="true"></i> P1</a></li>
				<?php if ($_SESSION['MM_UserGroup'] ==  'Adm'):?> 
				 <li><a href="stock_insertar_codigos_g.php"><i class="fa fa-gift fa-fw" aria-hidden="true"></i> P2</a></li>
                <?php endif; ?>
				<?php endif; ?>
              </ul>
            </li>

            <li class="dropdown">
              <a aria-expanded="false" aria-haspopup="true" role="button" data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="fa fa-shopping-bag fa-fw" aria-hidden="true"></i> Vtas<span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li><a href="ventas.php"><i class="fa fa-list fa-fw" aria-hidden="true"></i> Listar</a></li>
				  <li><a href="ventas_web_sin_oii.php"><i class="fa fa-list fa-fw" aria-hidden="true"></i> Sin order_item_id</a></li>
                <li class="divider" role="separator"></li>
                <?php if ($_SESSION['MM_UserGroup'] ==  'Adm'):?> 
                <li><a href="ventas_insertar.php"><i class="fa fa-plus fa-fw" aria-hidden="true"></i> Agregar</a></li>
                <?php endif; ?>
                <li><a href="ventas_buscador.php"><i class="fa fa-search fa-fw" aria-hidden="true"></i> Buscar</a></li>
              </ul>
            </li>
            <li><a href="ventas_web.php"><i class="text-success fa fa-check-circle fa-fw" aria-hidden="true"></i> Ped Cobrados</a></li>
                
            <?php if ($_SESSION['MM_UserGroup'] ==  'Adm'):?> 
            <li class="dropdown">
              <a aria-expanded="false" aria-haspopup="true" role="button" data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="fa fa-bank fa-fw" aria-hidden="true"></i> Gtos<span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li><a href="gastos.php"><i class="fa fa-list fa-fw" aria-hidden="true"></i> Listar</a></li>
                <li class="divider" role="separator"></li>
                <li><a href="gastos_insertar.php"><i class="fa fa-plus fa-fw" aria-hidden="true"></i> Agregar</a></li>
                <li><a href="gastos_buscador.php"><i class="fa fa-search fa-fw" aria-hidden="true"></i> Buscar</a></li>
              </ul>
            </li>
            <?php endif;?>
            </ul>
           
          <ul class="nav navbar-nav navbar-right">
          <li><a target="_blank" href="https://dixgamer.com/base-de-conocimiento/"><i class="fa fa-info fa-fw" aria-hidden="true"></i> Info</a></li>
          <?php if ($_SESSION['MM_UserGroup'] !=  'Adm'):?>
          <li><a href="horario.php"><i class="fa fa-clock-o fa-fw" aria-hidden="true"></i> Horas</a></li>
          <?php endif;?>
          <?php if ($_SESSION['MM_UserGroup'] ==  'Adm'):?>   
           
           <li class="dropdown">
              <a aria-expanded="false" aria-haspopup="true" role="button" data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="fa fa-cog fa-fw" aria-hidden="true"></i> Config<span class="caret"></span></a>
              <ul class="dropdown-menu">
              	<li><a href="_control/balance.php"><i class="fa fa-line-chart fa-fw" aria-hidden="true"></i> Balance</a></li>
                <li><a href="_control/balance_productos.php"><i class="fa fa-bar-chart fa-fw" aria-hidden="true"></i> Por productos</a></li>
				<li><a href="_control/balance_productos_dias.php"><i class="fa fa-bar-chart fa-fw" aria-hidden="true"></i> Por Dias</a></li>
                <li class="divider" role="separator"></li>
                <li><a href="publicaciones_generar_descripcion.php"><i class="fa fa-wpforms fa-fw" aria-hidden="true"></i> Generar Descrip</a></li>
                <li><a href="publicaciones_detalles.php"><i class="fa fa-wpforms fa-fw" aria-hidden="true"></i> Publicaciones</a></li>
                <li><a href="publicaciones_detalles_secundario.php"><i class="fa fa-wpforms fa-fw" aria-hidden="true"></i> Secundarias</a></li>
                <li><a href="publicaciones_insertar.php"><i class="fa fa-plus fa-fw" aria-hidden="true"></i> Agregar</a></li>
                <li class="divider" role="separator"></li>
                <li><a href="adwords_detalles.php"><i class="fa fa-google fa-fw" aria-hidden="true"></i> Adwords</a></li>
                <li><a href="titulos.php"><i class="fa fa-gamepad fa-fw" aria-hidden="true"></i> Titulos</a></li>
              </ul>
            </li>
            
			<li class="dropdown">
              <a aria-expanded="false" aria-haspopup="true" role="button" data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="fa fa-database fa-fw" aria-hidden="true"></i> Control<span class="caret"></span></a>
              <ul class="dropdown-menu">
              	<li><a href="horarios.php"><i class="fa fa-clock-o fa-fw" aria-hidden="true"></i> Horas</a></li>
				  <li class="divider" role="separator"></li>
				  <li><a href="_control/control_carga_gc.php"><i class="fa fa-barcode fa-fw" aria-hidden="true"></i> Carga GC</a></li>
				  <li><a href="control_precios_web.php"><i class="fa fa-money fa-fw" aria-hidden="true"></i> Precios</a></li>
				  <li class="divider" role="separator"></li>
                <li><a href="_control/control_mp.php"><i class="fa fa-credit-card-alt fa-fw" aria-hidden="true"></i> MP</a></li>
              	<li><a href="modificaciones_control.php"><i class="fa fa-check fa-fw" aria-hidden="true"></i> Modif</a></li>
                <li><a href="_control/control_ventas.php"><i class="fa fa-shopping-bag fa-fw" aria-hidden="true"></i> Ventas</a></li>
             </ul>
            </li>
			<?php endif;?>
            <li><a href="<?php echo $logoutAction ?>"><i class="fa fa-sign-out fa-fw" aria-hidden="true"></i> Salir</a></li>
          </ul>
        </div><!-- /.nav-collapse -->
      </div><!-- /.container -->
    </nav><!-- /.navbar -->
    <?php endif;?>
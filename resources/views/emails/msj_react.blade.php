<p>
    Hola {{ $cliente->nombre }}, por favor ingresa a nuestra cuenta/usuario con el nombre <b>{{ $account->name . " " . $account->surname }}</b> una vez más para RE ACTIVAR el slot primario, una vez dentro de nuestro usuario:<br /><br />

    E-mail: {{$account->mail_fake}} <br>
    Contraseña: <?php echo $account->pass;?><br /><br />

    1) Ir a Configuración > PSN/Administración de cuentas > Activar como tu PS4 principal > Activar<br />
    2) Ir a Configuración > PSN/Administración de cuentas > Restaurar Licencias > Restaurar<br />
    3) Reiniciar tu consola y acceder con tu usuario personal, recordá no volver a abrir nuestro usuario.<br /><br />

    <!-- Aprovecho para contarte que nuestros paVos de Fortnite bajaron de precio, <a href="https://dixgamer.com/categoria-producto/tarjetas/fortnite/">ver paVos baratos</a><br /><br /> -->
    {!! html_entity_decode($oferta_fortnite) !!} <br>
    Saludos, <?php echo session()->get('usuario')->nombre_visible;?> de DixGamer.<br/>
</p>
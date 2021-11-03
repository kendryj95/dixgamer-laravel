<p>
    Por favor ingrese a nuestro usuario/cuenta una vez más para RE ACTIVAR el slot primario: <br /><br />

    Email: {{$account->mail_fake}} <br>
    Contraseña: {{ $account->pass }}<br /><br />

    Una vez dentro de nuestro usuario:<br /><br />

    1) Ir a Ajustes > Usuarios y cuentas > Otro > Compartir consola y jugar offline > Activar.<br />
    2) Ir a Ajustes > Usuarios y cuentas > Otro > Restaurar Licencias > Restaurar.<br />
    3) Cerrar sesión y luego acceder con su usuario personal, recuerde no volver a abrir nuestro usuario.<br /><br />

    Saludos, <?php echo session()->get('usuario')->nombre_visible;?> de DixGamer.<br/>

    <!-- Aprovecho para contarte que nuestros paVos de Fortnite bajaron de precio, <a href="https://dixgamer.com/categoria-producto/tarjetas/fortnite/">ver paVos baratos</a><br /><br /> -->
    {!! html_entity_decode($oferta_fortnite) !!} <br>
</p>
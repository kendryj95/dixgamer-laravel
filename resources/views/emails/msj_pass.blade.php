<p>
    Hola {{ $cliente->nombre }}, por mantenimiento de los servidores actualizamos los datos de la cuenta.<br /><br />

    Nuevo e-mail: {{ $account->mail_fake }} <br>
    Contraseña: {{ $account->pass }} <br><br>

    {!! html_entity_decode($oferta_fortnite) !!} <br>
    Saludos, <?php echo session()->get('usuario')->nombre_visible;?> de DixGamer.<br/>
</p>
<p>
    Hola {{ $cliente->nombre }}, necesitamos que nos confirme si está usando su juego {{ strtoupper(str_replace("-"," ",$stock->titulo)) }} y si puede acceder normalmente a la cuenta para jugar. Tuvimos un error de sistema y si no puede acceder queremos ayudarle a solucionar.<br /><br />

    {!! html_entity_decode($oferta_fortnite) !!} <br>
    
    Saludos, <?php echo session()->get('usuario')->nombre_visible;?> de DixGamer.<br/>
</p>
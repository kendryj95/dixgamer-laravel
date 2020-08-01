<p>
    {{ $cliente->nombre }}, necesitamos que nos confirme si está usando su juego {{ strtoupper(str_replace("-"," ",$stock->titulo)) }} y si puede usarlo con normalidad. Tuvimos un error de sistema y si no puede jugar queremos ayudarle a solucionar.<br /><br />

    {!! html_entity_decode($oferta_fortnite) !!} <br>
    
    Saludos, <?php echo session()->get('usuario')->nombre_visible;?> de DixGamer.<br/>
</p>
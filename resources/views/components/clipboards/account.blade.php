<div style="position:absolute; top:-1000px; left:-3000px;">
    <div style="position: absolute; height: 100px; width: 100px;right: -50px; top: 50px;">
					<span id="newpass-copy{{$clientes_id}}" style="font-size:15px; background: white; font-weight: normal; color:#111;"><p>Hola {{ $nombre_cliente }}, por mantenimiento de los servidores actualizamos los datos de la cuenta.<br /><br />

						Mail: {{ $mail_fake }}<br />
						Contraseña: {{ $pass }}<br /><br />

						Saludos, <?php echo session()->get('usuario')->Nombre;?> de DixGamer.<br/></p>
					</span>
    </div>

    <div style="position: absolute; height: 100px; width: 100px;right: -50px; top: 50px;">
					<span id="avisosecu-copy{{$clientes_id}}" style="font-size:15px; background: white; font-weight: normal; color:#111;"><p>Hola {{ $nombre_cliente }}, necesitamos que nos confirme si está usando su juego {{ strtoupper(str_replace("-"," ",$titulo)) }} y si puede acceder normalmente a la cuenta para jugar. Tuvimos un error de sistema y si no puede acceder queremos ayudarle a solucionar.<br /><br />

						{!! html_entity_decode($oferta_fortnite) !!} <br>

						Saludos, <?php echo session()->get('usuario')->nombre_visible;?> de DixGamer.<br/></p>
					</span>

        <span id="avisopri-copy{{$clientes_id}}" style="font-size:15px; background: white; font-weight: normal; color:#111;"><p>{{ $nombre_cliente }}, necesitamos que nos confirme si está usando su juego {{ strtoupper(str_replace("-"," ",$titulo)) }} y si puede usarlo con normalidad. Tuvimos un error de sistema y si no puede jugar queremos ayudarle a solucionar.<br /><br />

						{!! html_entity_decode($oferta_fortnite) !!} <br>

						Saludos, <?php echo session()->get('usuario')->nombre_visible;?> de DixGamer.<br/></p>
					</span>
    </div>

    <div style="position: absolute; height: 100px; width: 100px;right: -50px; top: 50px;">
					<span id="avisonewemail-copy{{$clientes_id}}" style="font-size:15px; background: white; font-weight: normal; color:#111;">
						Gracias por su respuesta! <br>

						<p>Por favor, inicie sesión con estos datos:<br /><br />

						Juego: {{strtoupper(str_replace("-"," ",$titulo))}} <br>
						Usuario: {{$account_name}} {{$account_surname}} <br>
						Mail: {{ $mail_fake }} <br>
						Contraseña: {{ $pass }} <br><br>

						Confirme si logró ingresar. Muchas gracias! <br>
						Muchas gracias! <br>
					</span>
    </div>

    <div style="position: absolute; height: 100px; width: 100px;right: -50px; top: 50px;">
					<span id="reactivar-copy{{$clientes_id}}" style="font-size:15px; background: white; font-weight: normal; color:#111;">
						Gracias por su respuesta! <br>

						<p>Por favor, ingrese al usuario <b>{{ $account_name . " " . $account_surname }}</b> para reactivar el slot primario. Inicie la sesión en la cuenta con estos datos: <br><br>

						Email: {{$mail_fake}} <br>
						Contraseña: <?php echo $pass;?><br /><br />

						Una vez dentro de nuestro usuario:<br /><br />


						1) Ir a Configuración > PSN/Administración de cuentas > Activar como tu PS4 principal > Activar<br />
						2) Ir a Configuración > PSN/Administración de cuentas > Restaurar Licencias > Restaurar<br />
						3) Reiniciar la consola y acceder con su usuario personal, recuerde no volver a abrir nuestro usuario.<br /><br />

						Confirme si logró ingresar al juego desde su usuario. <br>
						Muchas gracias! <br>
						</p>
					</span>

		<span id="msjreact-copy{{$clientes_id}}" style="font-size:15px; background: white; font-weight: normal; color:#111;"><p>Hola {{ $nombre_cliente }}, por favor ingrese a nuestra cuenta/usuario con el nombre <b>{{ $account_name . " " . $account_surname }}</b> una vez más para RE ACTIVAR el slot primario: <br><br>

						Email: {{$mail_fake}} <br>
						Contraseña: <?php echo $pass;?><br /><br />

						Una vez dentro de nuestro usuario:<br /><br />


						1) Ir a Configuración > PSN/Administración de cuentas > Activar como tu PS4 principal > Activar<br />
						2) Ir a Configuración > PSN/Administración de cuentas > Restaurar Licencias > Restaurar<br />
						3) Reiniciar la consola y acceder con su usuario personal, recuerde no volver a abrir nuestro usuario.<br /><br />

						Saludos, <?php echo session()->get('usuario')->nombre_visible;?> de DixGamer.<br/><br>

				<!-- Aprovecho para contarte que nuestros paVos de Fortnite bajaron de precio, <a href="https://dixgamer.com/categoria-producto/tarjetas/fortnite/">ver paVos baratos</a><br /><br /> -->
						{!! html_entity_decode($oferta_fortnite) !!} <br>
						</p>
					</span>

		<span id="reactivar-copy{{$clientes_id}}-ps5" style="font-size:15px; background: white; font-weight: normal; color:#111;">
			Gracias por su respuesta! <br>

			<p>Por favor ingrese a nuestro usuario/cuenta una vez más para RE ACTIVAR el slot primario: <br><br>

						Email: {{$mail_fake}} <br>
						Contraseña: <?php echo $pass;?><br /><br />

						Una vez dentro de nuestro usuario:<br /><br />


						1) Ir a Ajustes > Usuarios y cuentas > Otro > Compartir consola y jugar offline > Activar.<br />
						2) Ir a Ajustes > Usuarios y cuentas > Otro > Restaurar Licencias > Restaurar.<br />
						3) Cerrar sesión y luego acceder con su usuario personal, recuerde no volver a abrir nuestro usuario.<br />
						</p>
					</span>
    </div>
</div>
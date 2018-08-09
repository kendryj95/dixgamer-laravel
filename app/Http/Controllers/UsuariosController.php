<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Validator;

class UsuariosController extends Controller
{
    public function create()
    {
    	return view('usuarios.create');
    }

    public function store(Request $request)
    {
    	// Mensajes de alerta
    	$msgs = [
    	  'nombre.required' => 'Nombre requerido',
    	  'nombre.alpha' => 'Nombre solo debe ser alfabético',
    	  'password.required' => 'Contraseña requerida',
    	];
    	// Validamos
    	$v = Validator::make($request->all(), [
    	    'nombre' => 'required|alpha',
    	    'password' => 'required'
    	], $msgs);

    	// Si hay errores retornamos a la pantalla anterior con los mensajes
    	if ($v->fails())
    	{
    	    return redirect()->back()->withErrors($v->errors());
    	}

    	DB::beginTransaction();

    	try {
    		DB::insert("INSERT INTO usuarios VALUES (null,?,?,?,null)", [$request->nombre, \Hash::make($request->password), $request->level]);
    		DB::commit();

    		// Mensaje de notificacion
    		\Helper::messageFlash('Usuarios','Usuario creado');

    		return redirect('usuario');
    	} catch (Exception $e) {
    		DB::rollback();
    		return redirect()->back()->withErrors(['Ha ocurrido un error inesperado. Intentalo de nuevo.']);
    	}
    }
}

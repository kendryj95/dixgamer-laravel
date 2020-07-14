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
    		DB::insert("INSERT INTO usuarios VALUES (null,?,?,?,?,null,?)", [$request->nombre, \Hash::make($request->password), $request->level, $request->nombre_visible, $request->color]);
    		DB::commit();

    		// Mensaje de notificacion
    		\Helper::messageFlash('Usuarios','Usuario creado');

    		return redirect('usuario');
    	} catch (Exception $e) {
    		DB::rollback();
    		return redirect()->back()->withErrors(['Ha ocurrido un error inesperado. Intentalo de nuevo.']);
    	}
    }

    public function listar()
    {
    	$usuarios = DB::select("SELECT * FROM usuarios");

    	return view('usuarios.listar', [
    		"usuarios" => $usuarios
    	]);
    }

    public function edit($id)
    {
    	$usuarios = DB::select("SELECT * FROM usuarios WHERE ID=?", [$id]);

    	if ($usuarios) {
    		return view('usuarios.edit', ['usuarios' => $usuarios[0]]);
    	}

    	return redirect()->back();
    }

    public function storeEdit(Request $request)
    {
    	// Mensajes de alerta
    	$msgs = [
    	  'password.required' => 'Contraseña requerida',
    	];
    	// Validamos
    	$v = Validator::make($request->all(), [
    	    'password' => 'required'
    	], $msgs);

    	// Si hay errores retornamos a la pantalla anterior con los mensajes
    	if ($v->fails())
    	{
    	    return redirect()->back()->withErrors($v->errors());
    	}

    	$id = $request->id_usuario;
    	$password = \Hash::make($request->password);
        $level = $request->level;
    	$color = $request->color;

    	DB::beginTransaction();

        if ($request->password == $request->old_pass) {
            try {
                DB::update("UPDATE usuarios SET Level=?, color=?, nombre_visible=? WHERE ID=?", [$level, $color, $request->nombre_visible, $id]);
                DB::commit();

                // Mensaje de notificacion
                \Helper::messageFlash('Usuarios','Usuario Editado');

                return redirect('usuario/list');
            } catch (Exception $e) {
                DB::rollback();
                return redirect()->back()->withErrors(['Ha ocurrido un error inesperado. Intentalo de nuevo.']);
            }
        } else {
            try {
                DB::update("UPDATE usuarios SET Contra=?, Level=?, color=?, nombre_visible=? WHERE ID=?", [$password, $level, $color, $request->nombre_visible, $id]);
                DB::commit();

                // Mensaje de notificacion
                \Helper::messageFlash('Usuarios','Usuario Editado');

                return redirect('usuario/list');

            } catch (Exception $e) {
                DB::rollback();
                return redirect()->back()->withErrors(['Ha ocurrido un error inesperado. Intentalo de nuevo.']);
            }
        }
    }
}

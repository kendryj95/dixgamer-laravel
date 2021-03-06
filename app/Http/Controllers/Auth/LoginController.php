<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;
use Validator;
use App\User;

class LoginController extends Controller
{

  public function __construct()
  {
      $this->middleware('guest');
  }

  public function index(){
    return view('auth.login');
  }

  public function auth(Request $request){
    // Mensajes de alerta
    $msgs = [
      'name.max' => 'Solo puedes ingresar un nombre con un maximo de 13 caracteres',
      'password.required' => 'La contraseña es requerida',
      'name.required' => 'El nombre es requerida'
    ];
    // Validamos
    $v = Validator::make($request->all(), [
        'name' => 'required|max:13',
        'password' => 'required',
    ], $msgs);

    // Si hay errores retornamos a la pantalla anterior con los mensajes
    if ($v->fails())
    {
        return redirect()->back()->withErrors($v->errors());
    }

    // Buscamos al usuario en la base de datos
    $password = $request->password;
    $name = $request->name;
    $user = User::where('Nombre',$name)->first();
    // $user = DB::select("SELECT * FROM usuarios WHERE Nombre=?", [$name])[0];
    // dd($user);
    // Si encontramos un usuario validamos sus datos
    if ($user) {
      // Si las password coinciden pasaremos
      if (\Hash::check($password, $user->Contra))
      {
        
        $request->session()->put('usuario', $user);
        return redirect('home');

      }
      return redirect()->back()->withErrors(['Credenciales incorrectas']);
    }

    return redirect()->back()->withErrors(['Usuario no encontrado']);

  }

  public function logout(Request $request)
  {
    $request->session()->flush();
    return redirect('login');
  }



}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Validator;
use Auth;
use Schema;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      // Mensajes de alerta
      $msgs = [
        'id.required' => 'Intentelo nuevamente',
        'link.required' => 'Enlace necesario'
      ];
      // Validamos
      $v = Validator::make($request->all(), [
          'id' => 'required',
          'link' => 'required'
      ], $msgs);


      // Si hay errores retornamos a la pantalla anterior con los mensajes
      if ($v->fails())
      {
          return redirect()->back()->withErrors($v->errors());
      }


      try {
        $meta = [];
        $meta['post_id'] = $request->id;
        $meta['meta_key'] = 'link_ps';
        $meta['meta_value'] = $request->link;

        DB::table('cbgw_postmeta')
            ->insert($meta);

        return redirect()->back();
      } catch (\Exception $e) {
        return redirect()->back()->withErrors(['Intentelo nuevamente']);
      }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function show(Account $account)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function edit(Account $account)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Account $account)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function destroy(Account $account)
    {
        //
    }

}

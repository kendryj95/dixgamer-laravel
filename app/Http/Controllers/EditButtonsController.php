<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class EditButtonsController extends Controller
{
    //
    public function getDataName(Request $request){

        $cliente = DB::table('clientes')
            ->select('nombre','apellido','ID','email','ml_user','provincia','ciudad','carac','tel','cel')
            ->where('ID',$request->id)->first();

        return Response()->json($cliente);
    }

    public function saveDataName(Request $request){
        $prevName = DB::table('clientes')
            ->where('ID',$request->id)->first();

        DB::table('clientes')
            ->where('ID',$request->id)->update(['nombre' => $request->nombre, 'apellido' => $request->apellido]);

        DB::table('clientes_notas')->insert(['notas' => 'Nombre Anterior: ' . $prevName->nombre . ' '. $prevName->apellido,
            'clientes_id' => $prevName->ID ,
            'usuario' => 'Victor', 'Day' => (string)\Carbon\Carbon::now()]);

        return Response()->json('Variables Actualizadas');
    }

    public function saveDataEmail(Request $request){
        $prevName = DB::table('clientes')
            ->where('ID',$request->id)->first();

        DB::table('clientes')
            ->where('ID',$request->id)->update(['email' => $request->email]);

        DB::table('clientes_notas')->insert(['notas' => 'Email Anterior: ' . $prevName->email,
            'clientes_id' => $prevName->ID ,
            'usuario' => 'Victor', 'Day' => (string)\Carbon\Carbon::now()]);

        return Response()->json('Variable de Correo Actualizada');
    }

    public function saveDataML(Request $request){
        $prevName = DB::table('clientes')
            ->where('ID',$request->id)->first();

        DB::table('clientes')
            ->where('ID',$request->id)->update(['ml_user' => $request->ml]);

        DB::table('clientes_notas')->insert(['notas' => 'ML Anterior: ' . $prevName->ml_user,
            'clientes_id' => $prevName->ID ,
            'usuario' => 'Victor', 'Day' => (string)\Carbon\Carbon::now()]);

        return Response()->json('Variable de ML Actualizada');
    }

    public function saveFB(Request $request){

        DB::table('clientes')
            ->where('ID',$request->id)->update(['face' => $request->face]);

        DB::table('clientes_notas')->insert(['notas' => 'Cuenta de Face agreagada: ' . $request->face,
            'clientes_id' => $request->id ,
            'usuario' => 'Victor', 'Day' => (string)\Carbon\Carbon::now()]);

        return Response()->json('Variable de ML Actualizada');
    }

    public function saveDataOther(Request $request){
        DB::table('clientes')
            ->where('ID',$request->id)->update(['provincia' => $request->provincia,'ciudad' => $request->ciudad,'carac' => $request->carac,'tel' => $request->tel,'cel' => $request->cel]);

        return Response()->json('Variables  Actualizadas');
    }

    public function saveNotes(Request $request){

        DB::table('clientes_notas')->insert([
            'clientes_id' => $request->id,
            'Notas' => $request->notes,
            'Day' => \Carbon\Carbon::now('America/New_York')
        ]);

        return Response()->json('Variables  Insertadas con exito');
    }

    public function locateFB(Request $request){

        $cliente = DB::table('clientes')
            ->where('ID',$request->id)->first();

        return Response()->json($cliente);
    }


}

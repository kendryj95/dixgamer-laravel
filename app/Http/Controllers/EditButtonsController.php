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

        $status = '';
        $id_cliente = '';

        $prevName = DB::table('clientes')
            ->where('ID',$request->id)->first();

        $check = DB::table('clientes_email')->where('email', $request->email)->first();

        if ($prevName->email != $request->email) {

            if ($check) {
                if (($check->email != $request->email) || ($check->email == $request->email && $check->clientes_id == $request->id)) {
                    DB::table('clientes')
                    ->where('ID',$request->id)->update(['email' => strtolower($request->email)]);
                    $status = 200;
                } else {
                    $status = 500;
                    $id_cliente = $check->clientes_id;
                }
            } else {
                DB::table('clientes')
                ->where('ID',$request->id)->update(['email' => strtolower($request->email)]);
                $status = 200;
            }
            
            DB::table('clientes_notas')->insert(['notas' => 'Email Anterior: ' . $prevName->email,
            'clientes_id' => $prevName->ID ,
            'usuario' => session()->get('usuario')->Nombre , 'Day' => (string)\Carbon\Carbon::now()]);
        } else {
            $status = 505;
        }

        $exists = DB::table('clientes_email')->where('email',$request->email)->first();

        if (!$exists) {
            $data = [];
            $data['clientes_id'] = $request->id;
            $data['email'] = strtolower($request->email);
            
            DB::table('clientes_email')->insert($data);
        }

        echo json_encode(["status" => $status,"id_cliente" => $id_cliente]);
    }

    public function saveDataML(Request $request){
        $prevName = DB::table('clientes')
            ->where('ID',$request->id)->first();

        if ($prevName->ml_user != $request->ml) {
            DB::table('clientes')
            ->where('ID',$request->id)->update(['ml_user' => $request->ml]);

            DB::table('clientes_notas')->insert(['notas' => 'ML Anterior: ' . $prevName->ml_user,
            'clientes_id' => $prevName->ID ,
            'usuario' => 'Victor', 'Day' => (string)\Carbon\Carbon::now()]);
        }

        $exists = DB::table('clientes_ml_user')->where('ml_user',$request->ml)->first();

        if (!$exists) {
            $data = [];
            $data['clientes_id'] = $request->id;
            $data['ml_user'] = $request->ml_user;
            
            DB::table('clientes_ml_user')->insert($data);
        }

        

        return Response()->json('Variable de ML Actualizada');
    }

    public function saveFB(Request $request){

        $fb = DB::table('clientes')
            ->where('ID',$request->id)->value('face');

        DB::table('clientes')
            ->where('ID',$request->id)->update(['face' => $request->face]);

        if ($fb != null) {
            DB::table('clientes_notas')->insert(['notas' => 'Facebook anterior: '. $fb,
                'clientes_id' => $request->id ,
                'usuario' => 'Victor', 'Day' => (string)\Carbon\Carbon::now()]);
        }

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

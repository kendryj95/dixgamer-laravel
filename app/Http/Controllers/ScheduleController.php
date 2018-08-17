<?php

namespace App\Http\Controllers;

use App\Schedule;
use Illuminate\Http\Request;
use Auth;
use Validator;
use DB;

class ScheduleController extends Controller
{
    private $sch;

    function __construct(){
      $this->sch = new Schedule;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $toDay = Schedule::scheduleByUserName(session()->get('usuario')->Nombre)->first();
        $shedulesDay = Schedule::shedulesDay(session()->get('usuario')->Nombre)->get();
        $shedulesMonth = Schedule::shedulesMonth(session()->get('usuario')->Nombre)->get();
        // dd($shedulesMonth);
        return view('schedule.index', compact(
          'toDay',
          'shedulesMonth',
          'shedulesDay'
        ));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      // Creamos arreglo de datos a insertar
      $scheduleArr = [];
      $scheduleArr['Day'] = date('Y-m-d', time());
      $scheduleArr['inicio'] = date('Y-m-d H:i:s', time());
      $scheduleArr['usuario'] = session()->get('usuario')->Nombre;

      // Validamos excepciones
      try {
        // Pasamos datos a la funcion del modelo
        $this->sch->storeSchedule($scheduleArr);

        \Helper::messageFlash('Horario','Tu día inicio, bienvenido');
        return redirect('home');
      } catch (\Exception $e) {
        // Si sucede algo retornamos con un error
        return redirect()->back()->withErrors(['Intentelo nuevamente']);
      }


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function show(Schedule $schedule)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function edit(Schedule $schedule)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {

      // Creamos arreglo de datos a actualizar
      $scheduleArr = [];
      $scheduleArr['fin'] = date('Y-m-d H:i:s', time());
      $scheduleArr['id'] = $id;
      $scheduleArr['usuario'] = session()->get('usuario')->Nombre;

      // Validamos excepciones
      try {
        // Pasamos datos a la funcion del modelo
        $this->sch->updateSchedule($scheduleArr);

        \Helper::messageFlash('Horario','Tu día finalizo, descanza');
        // Redireccionamos a la pagina donde estabamos
        return redirect()->back();
      } catch (\Exception $e) {
        // Si sucede algo retornamos con un error
        return redirect()->back()->withErrors(['Intentelo nuevamente']);
      }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function destroy(Schedule $schedule)
    {
        //
    }

    ######### ACCIONES DE ADMINISTRADOR ##############

    public function indexAdmin()
    {
        $shedulesDayAdmin = Schedule::shedulesDayAdmin()->get();
        $shedulesMonthAdmin = Schedule::shedulesMonthAdmin()->get();
        // dd($shedulesMonth);
        return view('schedule.admin.index', compact(
          'shedulesMonthAdmin',
          'shedulesDayAdmin'
        ));
    }

    public function verificarHorario($id)
    {
        DB::beginTransaction();

        try {
            DB::update("UPDATE horario SET verificado='si' WHERE id=?", [$id]);
            DB::commit();

            // Mensaje de notificacion
            \Helper::messageFlash('Horarios','Horario verificado');

            return redirect('horarios');
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['Ha ocurrido un error inesperado. Intentalo de nuevo.']);
        }
    }

    public function editarHorario($id)
    {
        $horario_ope = Schedule::find($id);

        if ($horario_ope) {
            return view('schedule.admin.edit_horario', ["horario_ope" => $horario_ope]);
        } else {
            return redirect()->back()->withErrors(['Ha ocurrido un error inesperado. Intentalo de nuevo.']);
        }
    }

    public function editHorario(Request $request)
    {

        // Mensajes de alerta
        $msgs = [
          'h_inicio.required' => 'Hora inicio es obligatorio',
          'f_fin.required' => 'Fecha fin es obligatorio',
          'h_fin.required' => 'Hora fin es obligatorio',
        ];
        // Validamos
        $v = Validator::make($request->all(), [
            'h_inicio' => 'required',
            'f_fin' => 'required',
            'h_fin' => 'required'
        ], $msgs);

        // Si hay errores retornamos a la pantalla anterior con los mensajes
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }

        $inicio = $request->day_h . " " . $request->h_inicio;
        $fin = $request->f_fin . " " . $request->h_fin;
        $id = $request->id_hor;

        $timestamp_inicio = strtotime($inicio);
        $timestamp_fin = strtotime($fin);

        if ($timestamp_fin <= $timestamp_inicio) {
            return redirect()->back()->withErrors(['La fecha de fin no puede ser menor ni igual a la fecha de inicio.']);
        } else {
            DB::beginTransaction();

            try {
                DB::update("UPDATE horario SET inicio=?, fin=? WHERE id=?", [$inicio, $fin, $id]);
                DB::commit();

                // Mensaje de notificacion
                \Helper::messageFlash('Horarios','Horario editado');

                return redirect('horarios');
            } catch (Exception $e) {
                DB::rollback();
                return redirect()->back()->withErrors(['Ha ocurrido un error inesperado. Intentalo de nuevo.']);
            }
        }
    }
}

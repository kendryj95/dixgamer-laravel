<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class Schedule extends Model
{
  protected $table = 'horario';

  public function ScopeScheduleByUserName($query,$userName){
    return $query->where('usuario',$userName)->orderBy('ID','DESC');
  }


  public function storeSchedule($schedule){
    return DB::table('horario')->insert($schedule);
  }

  public function updateSchedule($schedule){
    return DB::table('horario')
              ->where('usuario',$schedule['usuario'])
              ->where('ID',$schedule['id'])
              ->update([
                'fin' => $schedule['fin']
              ]);
  }

  public function ScopeShedulesDay($query,$user){
    return $query->select(DB::raw("*, (time_to_sec(timediff(fin, inicio)) / 3600) as Q_horas"))
                ->whereRaw("date_format(Day, '%%Y-%%m')=date_format(NOW(), '%%Y-%%m')")
                ->where('usuario',$user)
                ->whereRaw('fin IS NOT NULL')
                ->orderBy('Day','Desc');

  }

  public function ScopeShedulesDayAdmin($query){
    return $query->select(DB::raw("*, (time_to_sec(timediff(fin, inicio)) / 3600) as Q_horas"))
                ->whereRaw("(date_format(Day, '%%Y-%%m')=date_format(NOW(), '%%Y-%%m') or (date_format(Day, '%%Y-%%m') = date_format((DATE_SUB(curdate(), INTERVAL 1 MONTH)), '%%Y-%%m'))) AND fin IS NOT NULL")
                ->orderByRaw('Day DESC, inicio DESC');

  }

  public function ScopeShedulesMonth($query,$user){
    return $query->select(DB::raw("
                  date_format(Day, '%Y-%m') as mes,
                  COUNT(*) as Q_dias,
                  SUM(time_to_sec(timediff(fin, inicio)) / 3600) as Q_horas ,
                  usuario"
                  ))
                ->where('usuario',$user)
                ->groupBy('mes','usuario')
                ->orderBy('mes','Desc');

  }

  public function ScopeShedulesMonthAdmin($query){
    return $query->select(DB::raw("date_format(Day, '%Y-%m') as mes, COUNT(*) as Q_dias, SUM(time_to_sec(timediff(fin, inicio)) / 3600) as Q_horas , usuario"))
                ->groupBy(DB::raw('date_format(Day, "%%Y-%%m"), usuario DESC'))
                ->orderBy('mes','Desc');

  }


}

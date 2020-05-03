<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use DB;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            
            DB::beginTransaction();

            try {
                DB::update("UPDATE stock SET costo_usd_modif=5 where consola='ps4' and costo_usd<=5");

                echo "ACTUALIZACION EXITOSA DE JUEGOS PS4 QUE VALEN 5 USD O MENOS.\n";
    
                DB::update("UPDATE stock s LEFT JOIN 
                            (SELECT ID, IF(sugerido<(costo_usd * 0.25),FORMAT((costo_usd * 0.25),2),sugerido) as costo_usd_a_modificar FROM (SELECT ID, costo_usd, FORMAT(costo_usd * (0.02 + POWER(0.98,FLOOR((datediff(CURDATE(), Day))/7))),2) as sugerido FROM stock where consola='ps4' and costo_usd>5 and (titulo LIKE '%fifa%' or titulo LIKE '%pes$') ORDER BY ID ASC) AS primer_calculo) as rdo
                            ON s.ID = rdo.ID
                            SET s.costo_usd_modif = rdo.costo_usd_a_modificar
                            WHERE rdo.ID is not null");
    
                echo "ACTUALIZACION EXITOSA DE JUEGOS PS4 FIFA O PES QUE VALEN MAS DE 5 USD.\n";
    
    
                DB::update("UPDATE stock s LEFT JOIN 
                            (SELECT ID, IF(sugerido<(costo_usd * 0.25),FORMAT((costo_usd * 0.25),2),sugerido) as costo_usd_a_modificar FROM (SELECT ID, costo_usd, FORMAT(costo_usd * (0.015 + POWER(0.988,FLOOR((datediff(CURDATE(), Day))/7))),2) as sugerido FROM stock where consola='ps4' and costo_usd>5 and titulo NOT LIKE '%fifa%' and titulo NOT LIKE '%pes$' ORDER BY ID ASC) AS primer_calculo) as rdo
                            ON s.ID = rdo.ID
                            SET s.costo_usd_modif = rdo.costo_usd_a_modificar
                            WHERE rdo.ID is not null");
    
                echo "ACTUALIZACION EXITOSA DE JUEGOS PS4 QUE NO SON FIFA O PES QUE VALEN MAS DE 5 USD.\n";

                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                echo "Error al ejecutar el crontab programado para domingos 5AM: " . $e->getMessage() . "\n";
            }

        })->cron('0 5 * * 0');
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}

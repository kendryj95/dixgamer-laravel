<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Stock extends Model
{
    protected $table = 'stock';

    protected $fillable = [
        'titulo',
        'consola',
        'cuentas_id',
        'medio_pago',
        'costo_usd',
        'costo',
        'code',
        'code_prov',
        'n_order',
        'Day',
        'Notas',
        'usuario'
    ];


    // Scope que retorna el STOCK dependiento de un objeto que le pasemos
    public function ScopeShowStock($query, $obj){
        /*return $query->Select(
            'stock.ID AS id_stk',
            'stock.consola',
            'stock.titulo',
            'stock.cuentas_id AS stk_ctas_id',
            'vendido.id_vta',
            'vendido.q_vta',
            'vendido.dayvta',
            DB::raw('round(AVG(costo),0) as costo'),
            DB::raw('COUNT(*) AS q_stock')
        )
            ->leftjoin(DB::raw('
          (SELECT ventas.ID as id_vta, stock_id, slot, COUNT(*) AS q_vta, Day AS dayvta
          FROM ventas
          GROUP BY stock_id
          ORDER BY ventas.ID DESC) AS vendido'
            ), function($join)
            {
                $join->on('stock.id', '=', 'vendido.stock_id');
            })
            ->where('stock.consola', '!=', $obj->console_1)
            ->where('stock.consola', '!=', $obj->console_2)
            ->where('q_vta', NULL)
            ->where('stock.titulo','!=', $obj->title)
            ->groupBy('consola','titulo')->orderBy('consola','desc');*/

            return $query->Select(
            'stock.ID AS id_stk',
            'stock.consola',
            'stock.titulo',
            'stock.cuentas_id AS stk_ctas_id',
            'ID_vtas',
            'Q_vta',
            'dayvta',
            DB::raw('round(AVG(costo),0) as costo'),
            DB::raw('COUNT(*) AS Q_Stock')
        )
            ->leftjoin(DB::raw('
          (SELECT ventas.ID as ID_vtas, stock_id, slot, COUNT(*) AS Q_vta, Day AS dayvta
            FROM ventas
            GROUP BY stock_id
            ORDER BY ID DESC) AS vendido'
            ), function($join)
            {
                $join->on('stock.ID', '=', 'vendido.stock_id');
            })
            ->where('stock.consola', '!=', $obj->console_1)
            ->where('stock.consola', '!=', $obj->console_2)
            ->where('stock.consola', '!=', 'xps')
            ->where('q_vta', NULL)
            ->where('stock.titulo','!=', $obj->title)
            ->groupBy('consola','titulo')->orderBy(DB::raw("consola, SUBSTRING(titulo, 1, 5), costo"));
    }

    // Scope que retorna primary or secudary PS4
    public function ScopePrimaryOrSecundaryConsole($query,$obj,$title=null){
        $type = ($obj->type == 'primary') ? 'q_vta_pri' : 'q_vta_sec';
        return $query->Select(
            'stock.ID AS id_stk',
            'stock.consola',
            'stock.titulo',
            'stock.cuentas_id AS stk_ctas_id',
            'vendido.id_vta',
            'vendido.q_vta',
            'vendido.dayvta',
            'vendido.q_vta_pri',
            'vendido.q_vta_sec',
            DB::raw('round(AVG(costo),0) as costo'),
            DB::raw('COUNT(*) AS q_stock')
        )
            ->leftjoin(DB::raw(
                "(SELECT ventas.ID as ID_vta, stock_id, SUM(case when slot = 'Primario' then 1 else null end) AS Q_vta_pri, SUM(case when slot = 'Secundario' then 1 else null end) AS Q_vta_sec, COUNT(*) AS Q_vta, Day AS dayvta
          FROM ventas
          GROUP BY stock_id) AS vendido"
            ), function($join)
            {
                $join->on('stock.id', '=', 'vendido.stock_id');
            })
            ->whereRaw("(consola = '".$obj->console."' OR titulo = '".$obj->title."')")
            ->where('vendido.'.$type, Null)
            ->where(function ($query) use ($title) {
                if (!empty($title)) {
                    $query->where('titulo',$title);
                }
            })
            ->groupBy('consola','titulo')
            ->orderBy('consola','desc');
    }


    /*** NUEVA EDICION DE STOCK SOLUCIONADO EL PROBLEMA (antes si la cuenta tenia dos juegos y ninguna venta solo mostraba 1 juego) */
    public function ScopeStockDetailSold($query, $id){
        if (empty($id))
            $id = "-1";

        return $query->select(DB::raw("ID AS ID_stock,
                      titulo,
                      consola,
                      stock.usuario,
                      cuentas_id AS stock_cuentas_id,
                      medio_pago, costo_usd,
                      costo,
                      stock.Notas AS stock_Notas,
                      Day AS daystock,
                      reset.ID_reseteo AS ID_reset,
                      reset.r_cuentas_id AS reset_cuentas_id,
                      reset.dayreseteo AS dayreset,
                      reset.Q_reseteado AS Q_reset, vtas.*"))
            ->leftjoin(DB::raw("
                      (SELECT ID AS ID_reseteo, cuentas_id AS r_cuentas_id, COUNT(*) AS Q_reseteado, MAX(Day) AS dayreseteo
                      FROM reseteo
                      GROUP BY cuentas_id
                      ORDER BY ID DESC) AS reset"
            ), function($join)
            {
                $join->on('ID', '=', 'r_cuentas_id');
            })
            ->leftjoin(DB::raw("
                      (SELECT ventas.ID as ID_ventas,
                        stock_id AS v_stock_id,
                        slot,
                        medio_cobro, SUM(precio) AS total_ing,
                        SUM(comision) AS total_com,
                        COUNT(ventas.ID) AS Q_venta,
                        ventas.Day AS dayventa
                        FROM ventas
                        LEFT JOIN (select ventas_id, medio_cobro, ref_cobro, sum(precio) as precio,
                        sum(comision) as comision
                        FROM ventas_cobro GROUP BY ventas_id) as ventas_cobro ON ventas.ID = ventas_cobro.ventas_id GROUP BY stock_id)
                        AS vtas"

            ), function($join)
            {
                $join->on('stock.ID', '=', 'vtas.v_stock_id');
            })
            ->where('cuentas_id',$id)
            ->orderBy('stock.ID','DESC');

    }


    public function ScopeQuantityAccountId($query,$id){
        return $query->select(DB::raw('COUNT(*) as Q'),'cuentas_id')
            ->where('cuentas_id',$id)
            ->groupBy('cuentas_id');
    }


    public function ScopeStockExpensesByAccountId($query,$id){
        return $query->select(
            "cuentas_id",
            DB::raw("SUM(costo_usd) as costo_usd"),
            DB::raw("SUM(costo) as costo")
        )
            ->where('cuentas_id',$id)
            ->groupBy('cuentas_id')
            ->orderBy('ID','Desc');
    }

    public function storeStockAccount($data){
        return DB::table('stock')->insert($data);
    }

    public function soldFronConcept($id){
        return DB::select(DB::raw("
              SELECT *
              FROM
              (SELECT 'venta' as concepto, client.Day, ID AS ID_stock, titulo, consola, cuentas_id, clientes_id, slot, ventas_Notas, apellido, nombre, email,  NULL as new_pass, NULL as usuario
              FROM stock
              RIGHT JOIN
              (SELECT ventas.ID AS ID_ventas, clientes_id, stock_id, slot, medio_cobro, precio, comision, estado, ventas.Notas AS ventas_Notas, ventas.Day, clientes.ID AS ID_clientes, apellido, nombre, email
              FROM ventas
              LEFT JOIN (select ventas_id, medio_cobro, ref_cobro, sum(precio) as precio, sum(comision) as comision FROM ventas_cobro GROUP BY ventas_id) as ventas_cobro ON ventas.ID = ventas_cobro.ventas_id
              LEFT JOIN
              clientes
              ON ventas.clientes_id = clientes.ID) AS client
              ON stock.ID = client.stock_id
              WHERE cuentas_id = $id
              UNION ALL
              SELECT 'contra' as concepto, Day, NULL as ID_stock, NULL as titulo, NULL as consola, cuentas_id, NULL as clientes_id, NULL as slot, NULL as ventas_Notas, NULL as apellido, NULL as nombre, NULL as email, new_pass, usuario FROM cta_pass WHERE cuentas_id =$id
              UNION ALL
              SELECT 'reset' as concepto, Day, NULL as ID_stock, NULL as titulo, NULL as consola, cuentas_id, NULL as clientes_id, NULL as slot, NULL as ventas_Notas, NULL as apellido, NULL as nombre, NULL as email, NULL as new_pass, usuario FROM reseteo WHERE cuentas_id = $id
              UNION ALL
              SELECT 'resetear' as concepto, Day, NULL as ID_stock, NULL as titulo, NULL as consola, cuentas_id, NULL as clientes_id, NULL as slot, NULL as ventas_Notas, NULL as apellido, NULL as nombre, NULL as email, NULL as new_pass, usuario FROM resetear WHERE cuentas_id = $id
              UNION ALL
              SELECT 'notas' as concepto, Day, NULL as ID_stock, NULL as titulo, NULL as consola, cuentas_id, NULL as clientes_id, NULL as slot, NULL as ventas_Notas, NULL as apellido, NULL as nombre, NULL as email, Notas as new_pass, usuario FROM cuentas_notas WHERE cuentas_id = $id
              ) AS listado
              ORDER BY Day DESC
        "));
    }


    /*** SELECCIONO LA ULTIMA CUENTA CREADA POR EL USUARIO QUE TENGA UN JUEGO, Y LUEGO SELECCIONO TODOS LOS JUEGOS DE ESA CUENTA. */

    public function lastAccountUserGames($seller){

        return DB::select(DB::raw("
            SELECT cta.ID, stock.titulo, stock.consola, stock.costo_usd, stock.cuentas_id FROM
            (SELECT cuentas.ID, stock.titulo FROM cuentas LEFT JOIN stock ON cuentas.ID = stock.cuentas_id WHERE titulo IS NOT NULL and cuentas.usuario='".$seller."' ORDER BY ID DESC LIMIT 1) as cta
            LEFT JOIN
            stock
            ON cta.ID = stock.cuentas_id
            WHERE stock.titulo IS NOT NULL
            ORDER BY ID DESC
        "));
    }

    public function lastAccountByIdAndUser($seller,$last_account){
        return DB::select(DB::raw("
          SELECT ID, titulo, consola, costo_usd FROM stock WHERE usuario = '$seller' AND cuentas_id='$last_account' ORDER BY ID ASC LIMIT 2
        "));
    }

    public function ScopeStockList($query,$obj){
        if (!empty($obj->column) && !empty($obj->word)) {
            $query->where($obj->column,$obj->word);
        }
        // Validamos que sea administrador o analitico
        if (!\Helper::validateAdminAnalyst(session()->get('usuario')->Level)) {
            return $query->whereRaw("titulo NOT LIKE '%gift-card%' AND (titulo NOT LIKE '%plus%' AND titulo NOT LIKE '%slot' ) ")->orderBy('ID','DESC');
        }else{
            return $query->orderBy('ID','DESC');
        }

    }



    public function ScopePs3($query){
        return DB::select(DB::raw("
        SELECT ID_stk AS id_stk, titulo, consola, stk_ctas_id, dayreset, q_reset, days_from_reset, q_vta, round(AVG(costo),0) as costo, SUM(Q_Stock) AS q_stock FROM (SELECT ID AS ID_stk, titulo, consola, round(AVG(costo),0) as costo, cuentas_id AS stk_ctas_id, ID_reseteo AS ID_reset, r_cuentas_id AS reset_ctas_id, dayreseteo AS dayreset, reset.Q_reseteado AS Q_reset, DATEDIFF(NOW(), dayreseteo) AS days_from_reset, ID_vta, Q_vta, dayvta, ((2 + (IFNULL(Q_reseteado, 0) * 2)) - IFNULL(Q_vta, 0)) AS q_stock
        FROM stock
        LEFT JOIN
        (SELECT ID AS ID_reseteo, cuentas_id AS r_cuentas_id, COUNT(*) AS Q_reseteado, MAX(Day) AS dayreseteo
        FROM reseteo
        GROUP BY cuentas_id
        ORDER BY ID DESC) AS reset
        ON cuentas_id = r_cuentas_id
        LEFT JOIN
        (SELECT ventas.ID as ID_vta, stock_id, COUNT(*) AS Q_vta, Day AS dayvta
        FROM ventas
        GROUP BY stock_id) AS vendido
        ON ID = stock_id
        WHERE consola = 'ps3' AND (((Q_vta IS NULL) OR (Q_vta < '2')) OR (((Q_vta >= '2') AND (Q_reseteado = FLOOR(Q_vta/2)))))
        GROUP BY ID
        ORDER BY Q_reset, consola, titulo, ID DESC) AS consulta
        GROUP BY consola, titulo
        ORDER BY consola, titulo, ID_stk"));

    }


    public function ScopePs3ByTitle($query,$title){
        return DB::select(DB::raw("
        SELECT ID_stk AS id_stk, titulo, consola, stk_ctas_id, dayreset, q_reset, days_from_reset, q_vta, round(AVG(costo),0) as costo, SUM(Q_Stock) AS q_stock FROM (SELECT ID AS ID_stk, titulo, consola, round(AVG(costo),0) as costo, cuentas_id AS stk_ctas_id, ID_reseteo AS ID_reset, r_cuentas_id AS reset_ctas_id, dayreseteo AS dayreset, reset.Q_reseteado AS Q_reset, DATEDIFF(NOW(), dayreseteo) AS days_from_reset, ID_vta, Q_vta, dayvta, ((2 + (IFNULL(Q_reseteado, 0) * 2)) - IFNULL(Q_vta, 0)) AS q_stock
        FROM stock
        LEFT JOIN
        (SELECT ID AS ID_reseteo, cuentas_id AS r_cuentas_id, COUNT(*) AS Q_reseteado, MAX(Day) AS dayreseteo
        FROM reseteo
        GROUP BY cuentas_id
        ORDER BY ID DESC) AS reset
        ON cuentas_id = r_cuentas_id
        LEFT JOIN
        (SELECT ventas.ID as ID_vta, stock_id, COUNT(*) AS Q_vta, Day AS dayvta
        FROM ventas
        GROUP BY stock_id) AS vendido
        ON ID = stock_id
        WHERE consola = 'ps3' AND (((Q_vta IS NULL) OR (Q_vta < '2')) OR (((Q_vta >= '2') AND (Q_reseteado = FLOOR(Q_vta/2)))))
        GROUP BY ID
        ORDER BY Q_reset, consola, titulo, ID DESC) AS consulta
        WHERE titulo=?
        GROUP BY consola, titulo
        ORDER BY consola, titulo, ID_stk",[$title]));

    }

    public function ScopeGift($query,$title){
        return DB::select(DB::raw("
          SELECT ID AS ID_stk, titulo, consola, cuentas_id AS stk_ctas_id, round(AVG(costo),0) as costo, ID_vtas, Q_vta, dayvta, COUNT(*) AS Q_Stock
      		FROM stock
      		LEFT JOIN
      		(SELECT ventas.ID as ID_vtas, stock_id, slot, COUNT(*) AS Q_vta, Day AS dayvta
      		FROM ventas
      		GROUP BY stock_id
      		ORDER BY ID DESC) AS vendido
      		ON ID = stock_id
      		WHERE (consola != 'ps4') AND (consola != 'ps3') AND (Q_vta IS NULL) AND (titulo != 'plus-12-meses-slot') AND titulo=?
      		GROUP BY consola, titulo
      		ORDER BY consola, titulo DESC
      "),[$title]);

    }


    public function ScopeStockDetail($query,$account){
        return $query->select('*')
            ->where('ID',$account);
    }


    public function ScopePs3Resetear($query){
        return DB::select(DB::raw("
        SELECT ID AS id_stk, titulo, consola, cuentas_id AS stk_ctas_id, costo, ID_reseteo AS id_reset, r_cuentas_id AS reset_ctas_id, dayreseteo AS dayreset, reset.Q_reseteado AS q_reset, DATEDIFF(NOW(), dayreseteo) AS days_from_reset, id_vta, q_vta, dayvta, (DATEDIFF(NOW(), dayvta) - 1) AS days_from_vta
        FROM stock
        LEFT JOIN
        (SELECT ID AS ID_reseteo, cuentas_id AS r_cuentas_id, COUNT(*) AS Q_reseteado, MAX(Day) AS dayreseteo
        FROM reseteo
        GROUP BY cuentas_id
        ORDER BY ID DESC) AS reset
        ON cuentas_id = r_cuentas_id
        LEFT JOIN
        (SELECT ventas.ID as ID_vta, stock_id, COUNT(*) AS Q_vta, MAX(Day) AS dayvta
        FROM ventas
        GROUP BY stock_id) AS vendido
        ON ID = stock_id
        WHERE (consola = 'ps3') AND ((Q_vta >= '2' AND ID_reseteo IS NULL) OR (((Q_vta >= '2') AND ((Q_reseteado + 1) = FLOOR(Q_vta/2))) AND DATEDIFF(NOW(), dayreseteo) > '180')) AND cuentas_id NOT IN (6, 361)
        ORDER BY consola, titulo, ID DESC"));

    }


    public function storeCodes($data){
        return DB::table('stock')->insert($data);
    }


    public function lastStockUser($user){
        return DB::select(DB::raw("
        SELECT * FROM
        (SELECT * FROM (SELECT COUNT(*) as q, titulo, consola, costo_usd FROM stock where usuario='$user' and Day >= DATE(NOW() - INTERVAL 2 DAY) GROUP BY consola, titulo ORDER BY q DESC LIMIT 4) AS t1
        UNION ALL
        SELECT * FROM (SELECT COUNT(*) as q, titulo, consola, costo_usd FROM stock where usuario='$user' and Day >= DATE(NOW()) GROUP BY consola, titulo ORDER BY q DESC LIMIT 4) as t2
        ORDER BY q DESC) as resultado
        GROUP BY consola, titulo
        "));
    }


    public function updateStockById($id,$data){
        return DB::table('stock')->where('ID',$id)->update($data);
    }

    public function scopeStockDisponible($query, $consola, $titulo, $slot)
    {

        if (($consola && ($consola == "ps4")) or ($titulo && ($titulo == "plus-12-meses-slot"))){
            if ($slot && (ucwords($slot) == "Primario")) {
                $row_rsSTK = DB::select(DB::raw("SELECT ID AS ID_stk, titulo, consola, cuentas_id AS stk_ctas_id, round(AVG(costo),0) as costo, ID_vta, Q_vta, dayvta, Q_vta_pri, Q_vta_sec, COUNT(*) AS Q_Stock
                        FROM stock
                        LEFT JOIN
                        (SELECT ventas.ID as ID_vta, stock_id, SUM(case when slot = 'Primario' then 1 else null end) AS Q_vta_pri, SUM(case when slot = 'Secundario' then 1 else null end) AS Q_vta_sec, COUNT(*) AS Q_vta, Day AS dayvta
                        FROM ventas
                        GROUP BY stock_id) AS vendido
                        ON ID = stock_id
                        WHERE (consola = 'ps4' OR titulo = 'plus-12-meses-slot') AND (Q_vta_pri IS NULL) AND titulo='".$titulo."'
                        GROUP BY  consola, titulo
                        ORDER BY consola, titulo, ID DESC"), [$titulo]);

                return $row_rsSTK;

            } elseif ($slot && (ucwords($slot) == "Secundario")) {

                $row_rsSTK = DB::select(DB::raw("SELECT ID AS ID_stk, titulo, consola, cuentas_id AS stk_ctas_id, round(AVG(costo),0) as costo, ID_vta, Q_vta, dayvta, Q_vta_pri, Q_vta_sec, COUNT(*) AS Q_Stock
                    FROM stock
                    LEFT JOIN
                    (SELECT ventas.ID as ID_vta, stock_id, SUM(case when slot = 'Primario' then 1 else null end) AS Q_vta_pri, SUM(case when slot = 'Secundario' then 1 else null end) AS Q_vta_sec, COUNT(*) AS Q_vta, Day AS dayvta
                    FROM ventas
                    GROUP BY stock_id) AS vendido
                    ON ID = stock_id
                    WHERE (consola = 'ps4' OR titulo = 'plus-12-meses-slot') AND (Q_vta_sec IS NULL) AND titulo='".$titulo."'
                    GROUP BY  consola, titulo
                    ORDER BY consola, titulo, ID DESC"), [$titulo]);


                return $row_rsSTK;

            }

        } elseif  ($consola && ($consola == "ps3")) {

            $row_rsSTK = DB::select(DB::raw("SELECT ID_stk, titulo, consola, stk_ctas_id, dayreset, Q_reset, days_from_reset, Q_vta, round(AVG(costo),0) as costo, SUM(Q_Stock) AS Q_Stock FROM (SELECT ID AS ID_stk, titulo, consola, round(AVG(costo),0) as costo, cuentas_id AS stk_ctas_id, ID_reseteo AS ID_reset, r_cuentas_id AS reset_ctas_id, dayreseteo AS dayreset, reset.Q_reseteado AS Q_reset, DATEDIFF(NOW(), dayreseteo) AS days_from_reset, ID_vta, Q_vta, dayvta, ((2 + (IFNULL(Q_reseteado, 0) * 2)) - IFNULL(Q_vta, 0)) AS Q_Stock
                FROM stock 
                LEFT JOIN
                (SELECT ID AS ID_reseteo, cuentas_id AS r_cuentas_id, COUNT(*) AS Q_reseteado, MAX(Day) AS dayreseteo
                FROM reseteo
                GROUP BY cuentas_id
                ORDER BY ID DESC) AS reset
                ON cuentas_id = r_cuentas_id
                LEFT JOIN
                (SELECT ventas.ID as ID_vta, stock_id, COUNT(*) AS Q_vta, Day AS dayvta
                FROM ventas
                GROUP BY stock_id) AS vendido
                ON ID = stock_id
                WHERE consola = 'ps3' AND (((Q_vta IS NULL) OR (Q_vta < '2')) OR (((Q_vta >= '2') AND (Q_reseteado = FLOOR(Q_vta/2)))))
                GROUP BY ID
                ORDER BY Q_reset, consola, titulo, ID DESC) AS consulta
                WHERE titulo='".$titulo."'
                GROUP BY consola, titulo
                ORDER BY consola, titulo, ID_stk"), [$titulo]);

            return $row_rsSTK;

        } else {

            $row_rsSTK = DB::select(DB::raw("SELECT ID AS ID_stk, titulo, consola, cuentas_id AS stk_ctas_id, round(AVG(costo),0) as costo, ID_vtas, Q_vta, dayvta, COUNT(*) AS Q_Stock
                FROM stock
                LEFT JOIN
                (SELECT ventas.ID as ID_vtas, stock_id, slot, COUNT(*) AS Q_vta, Day AS dayvta
                FROM ventas
                GROUP BY stock_id
                ORDER BY ID DESC) AS vendido
                ON ID = stock_id
                WHERE (consola != 'ps4') AND (consola != 'ps3') AND (Q_vta IS NULL) AND (titulo != 'plus-12-meses-slot') AND titulo='".$titulo."'
                GROUP BY consola, titulo
                ORDER BY consola, titulo DESC"), [$titulo]);

            return $row_rsSTK;

        }
    }


}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class WpPost extends Model
{
    protected $table = 'cbgw_posts';

    public function ScopeLinkStore($query){
      return $query->select(
                  DB::raw("
                    cbgw_posts.ID,
                    REPLACE(REPLACE(REPLACE(REPLACE(TRIM(LCASE(cbgw_posts.post_title)), ' ', '-'), '''', ''), '’', ''), '.', '') AS titulo,
                    max( CASE WHEN pm.meta_key = 'consola' and  cbgw_posts.ID = pm.post_id THEN pm.meta_value END ) as consola,
                    GROUP_CONCAT( CASE WHEN pm.meta_key = 'link_ps' and cbgw_posts.ID = pm.post_id THEN pm.meta_value END ) as link_ps,
                    post_status
                  ")
                )->leftjoin('cbgw_postmeta as pm','cbgw_posts.ID','pm.post_id')
                ->where('post_type','product')
                ->where('post_status','publish')
                ->groupBy('cbgw_posts.ID')
                ->orderBy('consola','DESC')
                ->orderBy('titulo','ASC');
    }

    public function linkCatelogueProduct(){
      return DB::select(DB::raw("
      SELECT ID, titulo, consola, max(slot) as slot, idioma, peso, max(precio) as precio, ml_url FROM (select
          p.ID,
          REPLACE(REPLACE(REPLACE(REPLACE(TRIM(LCASE(p.post_title)), ' ', '-'), '''', ''), '’', ''), '.', '') AS titulo,
          max( CASE WHEN pm.meta_key = 'consola' and p.ID = pm.post_id THEN pm.meta_value END ) as consola,
          '' as slot,
        max( CASE WHEN pm.meta_key = 'idioma' and p.ID = pm.post_id THEN pm.meta_value END ) as idioma,
          max( CASE WHEN pm.meta_key = 'peso' and p.ID = pm.post_id THEN pm.meta_value END ) as peso,
        max( CASE WHEN pm.meta_key = '_price' and p.ID = pm.post_id THEN pm.meta_value END ) as precio,
        max( CASE WHEN pm.meta_key = 'ml_url' and p.ID = pm.post_id THEN pm.meta_value END ) as ml_url
      from
          cbgw_posts as p
      LEFT JOIN
          cbgw_postmeta as pm
      ON
         p.ID = pm.post_id
      where
          post_type = 'product' and
          post_status = 'publish'
      group BY
        p.ID
      UNION ALL
      SELECT post_parent as ID, titulo, consola, GROUP_CONCAT(slot) as slot, idioma, peso, precio, ml_url FROM (select
          p.ID,
          p.post_parent,
          SUBSTRING_INDEX(SUBSTRING_INDEX(REPLACE(REPLACE(REPLACE(REPLACE(TRIM(LCASE(p.post_title)), ' ', '-'), '''', ''), '’', ''), '.', ''),'-&ndash;',1),'---',1) as titulo,
          max( CASE WHEN pm2.meta_key = 'consola' and  p.post_parent = pm2.post_id THEN pm2.meta_value END ) as consola,
          max( CASE WHEN pm.meta_key = 'attribute_pa_slot' and p.ID = pm.post_id THEN pm.meta_value END ) as slot,
        max( CASE WHEN pm2.meta_key = 'idioma' and p.post_parent = pm2.post_id THEN pm2.meta_value END ) as idioma,
          max( CASE WHEN pm2.meta_key = 'peso' and p.post_parent = pm2.post_id THEN pm2.meta_value END ) as peso,
        max( CASE WHEN pm.meta_key = '_price' and p.ID = pm.post_id THEN pm.meta_value END ) as precio,
        max( CASE WHEN pm2.meta_key = 'ml_url' and p.post_parent = pm2.post_id THEN pm2.meta_value END ) as ml_url
      from
          cbgw_posts as p
      LEFT JOIN
          cbgw_postmeta as pm
      ON
         p.ID = pm.post_id
      LEFT JOIN
        cbgw_postmeta as pm2
      ON
        p.post_parent = pm2.post_id
      where
          post_type = 'product_variation' and
          post_status = 'publish'
      GROUP BY
        p.ID
      ORDER BY consola ASC, titulo ASC, slot ASC) as conslot
      GROUP BY post_parent) as listado
      GROUP BY ID, precio
      ORDER BY consola ASC, titulo ASC, slot ASC
      "));

    }



    public function stockGiftCard(){
      return DB::select(DB::raw("
              SELECT CONCAT('< ',titulo,' > (',consola,') [',costo,']') as nombre FROM (SELECT * FROM (select
                  p.ID,
                  p.post_title as titulo,
                  max( CASE WHEN pm.meta_key = 'consola' and  p.ID = pm.post_id THEN pm.meta_value END ) as consola,
                max( CASE WHEN pm.meta_key = 'costo' and  p.ID = pm.post_id THEN pm.meta_value END ) as costo,
                  post_status
              from
                  cbgw_posts as p
              LEFT JOIN
                  cbgw_postmeta as pm
              ON
                 p.ID = pm.post_id
              where
                  post_type = 'product' and
                  post_status = 'publish'
              group by
                  p.ID
              ORDER BY `consola` DESC, `titulo` ASC) as resultado WHERE consola IN ('amazon','facebook','google-play','ps','xbox','nintendo','fifa-points','steam') and titulo != 'plus-12-meses-slot') AS rdo

      "));
    }


    /*public function lastGameStockTitles(){
      return DB::select(DB::raw("
        SELECT web.*, stk.*
        FROM
        (SELECT REPLACE(REPLACE(REPLACE(REPLACE(TRIM(LCASE(post_title)), ' ', '-'), '''', ''), '’', ''), '.', '') AS producto FROM cbgw_posts WHERE post_type = 'product' and post_status = 'publish' group by producto) as web
        LEFT JOIN
        (SELECT titulo, COUNT(*) AS Q_Stk, Day FROM stock WHERE Day >= (DATE_ADD(CURDATE(), INTERVAL -45 DAY)) and (titulo LIKE '%slot%' or consola != 'ps') GROUP BY titulo) AS stk
        ON producto = titulo
        ORDER BY Q_Stk DESC
      "));
    }*/

    public function lastGameStockTitles()
    {
      return DB::select("SELECT * FROM (SELECT CONCAT(titulo,' (',consola,')') as nombre_web FROM (select p.ID, REPLACE(REPLACE(REPLACE(REPLACE(TRIM(LCASE(p.post_title)), ' ', '-'), '''', ''), '’', ''), '.', '') as titulo, max( CASE WHEN pm.meta_key = 'consola' and p.ID = pm.post_id THEN pm.meta_value END ) as consola, post_status from cbgw_posts as p LEFT JOIN cbgw_postmeta as pm ON p.ID = pm.post_id where post_type = 'product' and post_status = 'publish' group by p.ID ORDER BY `consola` DESC, `titulo` ASC) as resultado WHERE (consola IN ('ps4', 'ps3') OR titulo LIKE '%slot%') GROUP BY nombre_web) AS rdo LEFT JOIN (SELECT CONCAT(titulo,' (',consola,')') as nombre_db, COUNT(*) AS Q_Stk, Day FROM stock WHERE Day >= (DATE_ADD(CURDATE(), INTERVAL -45 DAY)) and (consola IN ('ps4', 'ps3') OR titulo LIKE '%slot%') GROUP BY nombre_db) AS stk ON nombre_web = nombre_db ORDER BY `stk`.`Q_Stk` DESC");
    }


}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Validator;
use Schema;
use DB;
use App\Stock;
use App\WpPost;
use App\Reset;
use Illuminate\Support\Facades\Input;

class StockController extends Controller
{
    private $wp_p;
    private $st;

    function __construct(){
      $this->wp_p = new WpPost();
      $this->st = new Stock();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $obj = new \stdClass;
        $obj->column = $request->column;
        $obj->word = $request->word;

        $columns = [];
        if (\Helper::validateAdministrator(session()->get('usuario')->Level)) {
          $columns = Schema::getColumnListing('stock');
        }else{
          $columns = ['ID','titulo','consola','cuentas_id','Day'];
        }
        $stocks = Stock::stockList($obj)->paginate(50);
        return view('stock.index',compact(
          'stocks',
          'columns'
        ));
    }
    public function indexLinkPsStore()
    {
        $stocks = WpPost::linkStore()->get();
        return view('stock.index_ps_store',compact(
          'stocks'
        ));
    }
    public function indexCatalogueProduct(Request $request)
    {
        $stocks = $this->wp_p->linkCatelogueProduct();
        $stocks = \Helper::arrayPaginator($stocks, $request);
        return view('stock.index_catalogue_product',compact(
          'stocks'
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


    public function createCode(){
      $cotiz = Stock::calcularCotizCode()->value('cotiz');

      $giftCards = $this->wp_p->stockGiftCard();

      $rowsAA = array();

      foreach ($giftCards as $gifts) {
        $rowsAA[]=$gifts->nombre;
      }

      $giftCards=$rowsAA;
      $giftCards = json_encode($giftCards);
      return view('stock.store_code',compact(
        'cotiz',
        'giftCards'
      ));
    }

    public function createCodeControl(){

      $giftCards = $this->wp_p->stockGiftCard();

      $rowsAA = array();

      foreach ($giftCards as $gifts) {
        $rowsAA[]=$gifts->nombre;
      }

      $giftCards=$rowsAA;
      $giftCards = json_encode($giftCards);
      return view('stock.store_code_control',compact(
        'giftCards'
      ));
    }
    public function createCodeG(){
      $cotiz = Stock::calcularCotizCode()->value('cotiz');

      $giftCards = $this->wp_p->stockGiftCard();
      $rowsAA = array();

      foreach ($giftCards as $gifts) {
        $rowsAA[]=$gifts->nombre;
      }

      $giftCards=$rowsAA;
      $giftCards = json_encode($giftCards);
      return view('stock.store_code_g',compact(
        'cotiz',
        'giftCards'
      ));
    }
    
    public function createCodeGVCC(){
      $cotiz = Stock::calcularCotizCode()->value('cotiz');

      return view('stock.store_code_g_vcc',compact(
        'cotiz'
      ));
    }

    public function createCodep3(){
        $cotiz = Stock::calcularCotizCode()->value('cotiz');

        $giftCards = $this->wp_p->stockGiftCard();
        $rowsAA = array();

        foreach ($giftCards as $gifts) {
            $rowsAA[]=$gifts->nombre;
        }

        $giftCards=$rowsAA;
        $giftCards = json_encode($giftCards);
        return view('stock.store_code_p3',compact(
            'cotiz',
            'giftCards'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function validaCodigo(Request $request){

        $gc = $request->codigo;
        $u = Stock::where('code',$gc)->first();

            if($u){
                return response()->json('El código '. $gc . ' está duplicado o existente en la Base de datos verfique e intente de nuevo');
            }

        /*$codigos = [];
        $codigos = $request->codes;

        foreach ($codigos as $v){
            $u = Stock::where('code',$v->codes)->first();
                if (!empty($u)){
                    return response()->json('El código '. $u->code . ' está duplicado o existente en la Base de datos verfique e intente de nuevo');
                }
        }*/
    }

    public function storeCode(Request $request)
    {


      // Mensajes de alerta
      $msgs = [
        'clientes_id1.required' => 'Intentelo nuevamente 1',
        'clientes_id2.required' => 'Intentelo nuevamente 2 ',
        'medio_pago.required' => 'Medio de pago requerido',
        'costo_usd.required' => 'Costo en USD requerido',
        'costo.required' => 'Costo requerido',
        'codes.required' => 'Codigos requeridos',
        'codes.array' => 'Ingrese codigos validos'
      ];
      // Validamos
      $v = Validator::make($request->all(), [
          'clientes_id1' => 'required',
          'clientes_id2' => 'required',
          'medio_pago' => 'required',
          'costo_usd' => 'required',
          'costo' => 'required',
          'codes' => 'required|array'
      ], $msgs);

      // Si hay errores retornamos a la pantalla anterior con los mensajes
      if ($v->fails())
      {
          return redirect()->back()->withInput()->withErrors($v->errors());
      }

      foreach ($request->codes as $codes){

          $locate = DB::table('stock')->where('code',$codes)->first();


          if(!empty($locate)){
              return redirect()->back()->withInput()->withErrors('El código '. $codes . ' Esta duplicado o ya se encuentra en la Base de datos, por favor verifique e intente de nuevo.');
          } else {

          }
      }



      try {

        $stocks = [];


        $stockArr['titulo'] = $request->clientes_id1;
        $stockArr['consola'] = $request->clientes_id2;
        $stockArr['medio_pago'] = $request->medio_pago;
        $stockArr['costo_usd'] = $request->costo_usd;
        $stockArr['costo'] = $request->costo;
        $stockArr['usuario'] = session()->get('usuario')->Nombre."-GC";
        $stockArr['code_prov'] = 'P1';

        foreach ($request->codes as $code) {
          $stockArr['code'] = $code;
          array_push($stocks,$stockArr);
        }
        $saving = $this->st->storeCodes($stocks);

        // Mensaje de notificacion
        \Helper::messageFlash('Stock','Código guardado');
        return redirect('stock');

      } catch (\Exception $e) {

        return redirect()->back()->withInput()->withErrors(['Intentelo nuevamente']);
      }

    }

    public function storeCodeControl(Request $request)
    {


      // Mensajes de alerta
      $msgs = [
        'clientes_id1.required' => 'Intentelo nuevamente 1',
        'clientes_id2.required' => 'Intentelo nuevamente 2 ',
        'medio_pago.required' => 'Medio de pago requerido',
        'costo_usd.required' => 'Costo en USD requerido',
        'codes.required' => 'Codigos requeridos',
        'codes.array' => 'Ingrese codigos validos'
      ];
      // Validamos
      $v = Validator::make($request->all(), [
          'clientes_id1' => 'required',
          'clientes_id2' => 'required',
          'medio_pago' => 'required',
          'costo_usd' => 'required',
          'codes' => 'required|array'
      ], $msgs);

      // Si hay errores retornamos a la pantalla anterior con los mensajes
      if ($v->fails())
      {
          return redirect()->back()->withInput()->withErrors($v->errors());
      }

      foreach ($request->codes as $codes){

          $locate = DB::table('stock_gc_codes_control')->where('code',$codes)->first();


          if(!empty($locate)){
              return redirect()->back()->withInput()->withErrors('El código '. $codes . ' Esta duplicado o ya se encuentra en la Base de datos, por favor verifique e intente de nuevo.');
          } else {

          }
      }



      try {

        $stocks = [];


        $stockArr['titulo'] = $request->clientes_id1;
        $stockArr['consola'] = $request->clientes_id2;
        $stockArr['medio_pago'] = $request->medio_pago;
        $stockArr['costo_usd'] = $request->costo_usd;
        $stockArr['usuario'] = session()->get('usuario')->Nombre."-GC";
        $stockArr['code_prov'] = 'P1';

        foreach ($request->codes as $code) {
          $stockArr['code'] = $code;
          array_push($stocks,$stockArr);
        }
        $saving = $this->st->storeCodesControl($stocks);

        // Mensaje de notificacion
        \Helper::messageFlash('Stock','Código guardado');
        return redirect('stock');

      } catch (\Exception $e) {

        return redirect()->back()->withInput()->withErrors(['Intentelo nuevamente']);
      }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeCodeG(Request $request)
    {
      // Mensajes de alerta

      $msgs = [
        'clientes_id1.required' => 'Intentelo nuevamente 1 ',
        'clientes_id2.required' => 'Intentelo nuevamente 2 ',
        'medio_pago.required' => 'Medio de pago requerido',
        'costo_usd.required' => 'Costo en USD requerido',
        'costo.required' => 'Costo requerido',
        'codes.required' => 'Codigos requeridos',
        'codes.array' => 'Ingrese codigos validos'
      ];
      // Validamos
      $v = Validator::make($request->all(), [
          'clientes_id1' => 'required',
          // 'clientes_id2' => 'required',
          'medio_pago' => 'required',
          'costo_usd' => 'required',
          'costo' => 'required',
          'codes' => 'required|array'
      ], $msgs);

      // Si hay errores retornamos a la pantalla anterior con los mensajes
      if ($v->fails())
      {
          return redirect()->back()->withInput()->withErrors($v->errors());
      }

        foreach ($request->codes as $codes){

            $locate = DB::table('stock')->where('code',$codes)->first();


            if(!empty($locate)){
                return redirect()->back()->withInput()->withErrors('El código '. $codes . ' Esta duplicado o ya se encuentra en la Base de datos, por favor verifique e intente de nuevo.');
            } else {

            }
        }

      try {

        $stocks = [];


        $stockArr['titulo'] = $request->clientes_id1;
        $stockArr['consola'] = $request->clientes_id2;
        $stockArr['medio_pago'] = $request->medio_pago;
        $stockArr['costo_usd'] = $request->costo_usd;
        $stockArr['costo'] = $request->costo;
        $stockArr['n_order'] = $request->n_order;
        $stockArr['usuario'] = session()->get('usuario')->Nombre."-GC";
        $stockArr['code_prov'] = 'P2';

        foreach ($request->codes as $code) {
          $stockArr['code'] = $code;
          array_push($stocks,$stockArr);
        }

        $saving = $this->st->storeCodes($stocks);

        // Mensaje de notificacion
        \Helper::messageFlash('Stock','Código G guardado');
        return redirect('stock');

      } catch (\Exception $e) {
          return $e;
        //return redirect()->back()->withInput()->withErrors(['Intentelo nuevamente final']);
      }



    }

    public function storeCodeGVCC(Request $request)
    {

        foreach ($request->codes as $codes){

            $locate = DB::table('stock')->where('code',$codes)->first();


            if(!empty($locate)){
                return redirect()->back()->withInput()->withErrors('El código '. $codes . ' Esta duplicado o ya se encuentra en la Base de datos, por favor verifique e intente de nuevo.');
            } else {

            }
        }

      try {

        $stocks = [];
        $costos = $request->costo;
        $costos_usd = $request->costo_usd;
        
        $stockArr['medio_pago'] = $request->medio_pago;
        $stockArr['n_order'] = $request->n_order;
        $stockArr['usuario'] = session()->get('usuario')->Nombre."-GC";
        $stockArr['code_prov'] = 'P2';

        foreach ($request->codes as $i => $code) {
          $stockArr['titulo'] = "gift-card-$costos_usd[$i]-usd";
          $stockArr['consola'] = "ps";
          $stockArr['costo_usd'] = $costos_usd[$i];
          $stockArr['costo'] = $costos[$i];
          $stockArr['code'] = $code;
          array_push($stocks,$stockArr);
        }

        $saving = $this->st->storeCodes($stocks);

        // Mensaje de notificacion
        \Helper::messageFlash('Stock','Código G guardado');
        return redirect('stock');

      } catch (\Exception $e) {
          return $e;
        //return redirect()->back()->withInput()->withErrors(['Intentelo nuevamente final']);
      }



    }

    public function storeCodep3(Request $request)
    {
        // Mensajes de alerta

        $msgs = [
            'clientes_id1.required' => 'Intentelo nuevamente 1 ',
            'clientes_id2.required' => 'Intentelo nuevamente 2 ',
            'medio_pago.required' => 'Medio de pago requerido',
            'costo_usd.required' => 'Costo en USD requerido',
            'costo.required' => 'Costo requerido',
            'codes.required' => 'Codigos requeridos',
            'codes.array' => 'Ingrese codigos validos'
        ];
        // Validamos
        $v = Validator::make($request->all(), [
            'clientes_id1' => 'required',
            // 'clientes_id2' => 'required',
            'medio_pago' => 'required',
            'costo_usd' => 'required',
            'costo' => 'required',
            'codes' => 'required|array'
        ], $msgs);

        // Si hay errores retornamos a la pantalla anterior con los mensajes
        if ($v->fails())
        {
            return redirect()->back()->withInput()->withErrors($v->errors());
        }

        foreach ($request->codes as $codes){

            $locate = DB::table('stock')->where('code',$codes)->first();


            if(!empty($locate)){
                return redirect()->back()->withInput(Input::all())->withErrors('El código '. $codes . ' Esta duplicado o ya se encuentra en la Base de datos, por favor verifique e intente de nuevo.');
            } else {

            }
        }

        try {

            $stocks = [];


            $stockArr['titulo'] = $request->clientes_id1;
            $stockArr['consola'] = $request->clientes_id2;
            $stockArr['medio_pago'] = $request->medio_pago;
            $stockArr['costo_usd'] = $request->costo_usd;
            $stockArr['costo'] = $request->costo;
            $stockArr['n_order'] = $request->n_order;
            $stockArr['usuario'] = session()->get('usuario')->Nombre."-GC";
            $stockArr['code_prov'] = 'P3';

            foreach ($request->codes as $code) {
                $stockArr['code'] = $code;
                array_push($stocks,$stockArr);
            }

            $saving = $this->st->storeCodes($stocks);

            // Mensaje de notificacion
            \Helper::messageFlash('Stock','Código P3 guardado');
            return redirect('stock');

        } catch (\Exception $e) {
            //return $e;
            return redirect()->back()->withInput()->withErrors(['Intentelo nuevamente']);
        }



    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function publicacionesSecundariasML()
    {
      $publicaciones = $this->getDatosPublicacionesSecundariasML();

      return view('stock.publicaciones_secundarias', compact('publicaciones'));
    }

    private function getDatosPublicacionesSecundariasML()
    {

      $query = "SELECT * FROM
(SELECT COUNT(ID_stk) AS q_stk, REPLACE(REPLACE(REPLACE(REPLACE(TRIM(LCASE(titulo)), ' ', '-'), '''', ''), '’', ''), '.', '') AS producto, consola, SUM(Q_vta) as q_vta, SUM(Q_vta_pri) as vta_pri, SUM(Q_vta_sec) as vta_sec, (SUM(Q_vta_pri) - SUM(Q_vta_sec)) as libre FROM 
(SELECT ID AS ID_stk, titulo, consola, ID_vta, IFNULL(Q_vta,0) AS Q_vta, IFNULL(Q_vta_pri,0) AS Q_vta_pri, IFNULL(Q_vta_sec,0) AS Q_vta_sec
FROM stock
LEFT JOIN
(SELECT ventas.ID as ID_vta, stock_id, SUM(case when slot = 'Primario' then 1 else null end) AS Q_vta_pri, SUM(case when slot = 'Secundario' then 1 else null end) AS Q_vta_sec, MAX(estado) AS vta_estado, COUNT(*) AS Q_vta, Day AS dayvta
FROM ventas
GROUP BY stock_id) AS vendido
ON ID = stock_id
WHERE (consola = 'ps4')
ORDER BY consola, titulo, ID DESC) AS resultado
GROUP BY titulo)
AS final
LEFT JOIN
(select
    p.ID,
    REPLACE(REPLACE(REPLACE(REPLACE(TRIM(LCASE(p.post_title)), ' ', '-'), '''', ''), '’', ''), '.', '') AS titulo,
    max( CASE WHEN pm.meta_key = 'consola' and  p.ID = pm.post_id THEN pm.meta_value END ) as cons,
  max( CASE WHEN pm.meta_key = '_price' and p.ID = pm.post_id THEN pm.meta_value END ) as price,
  round(max( CASE WHEN pm.meta_key = '_price' and p.ID = pm.post_id THEN pm.meta_value END )) as max_price,
  round(min( CASE WHEN pm.meta_key = '_price' and p.ID = pm.post_id THEN pm.meta_value END )) as min_price,
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
ORDER BY `cons` ASC, `titulo` ASC) as web
ON final.producto = web.titulo
WHERE libre > 0 AND cons = 'ps4' #ahora también agrupo por consola y titulo para evitar los duplicados de productos de la web (los que genero para WS)
GROUP BY consola, titulo
ORDER BY libre DESC";

      $publicaciones = DB::select($query);

      return $publicaciones;
    }

    public function indexCargados(Request $request)
    {
      $fecha_fin = isset($request->fecha_fin) ? $request->fecha_fin : date('Y-m-d');
      $fecha_ini = isset($request->fecha_ini) ? $request->fecha_ini : $this->defaultFechaIni();
      $usuario = isset($request->usuario) ? $request->usuario : '';

      $cargados = Stock::getDatosCargados($fecha_ini, $fecha_fin, $usuario)->get();
      $usuarios = Stock::usersStock()->get();

      return view('stock.index_cargados', compact('cargados','fecha_fin','fecha_ini','usuarios'));
    }

    private function defaultFechaIni()
    {
      $hoy = strtotime(date('Y-m-d'));
      $fecha_ini = strtotime('-7 days', $hoy);
      $fecha_ini = date('Y-m-d', $fecha_ini);

      return $fecha_ini;
    }

    public function indexFaltaCargar(Request $request)
    {
      $dia = isset($request->dia) ? $request->dia : 30;
      $titulos_params = isset($request->titulos) && count($request->titulos) > 0 ? $request->titulos : [];
      $params = [];

      $titles = [];

      if (count($titulos_params) > 0) {
        foreach ($titulos_params as $titulo) {
          $titles[] = '"'.$titulo.'"';
        }
      }

      $titles = implode(",", $titles);

      // dd($titles);

      array_push($params, $dia,$dia,$titles);

      $datos = Stock::getDatosFaltaCargar($params);
      $titulos = [];

      foreach ($datos as $value) {
        if (!in_array($value->titulo, $titulos)) {
          $titulos[] = $value->titulo;
        }
      }

      return view('stock.index_falta_cargar', compact('datos','dia','titulos','titulos_params'));
    }

    public function asignarStockStore(Request $request)
    {
      // Mensajes de alerta
      $msgs = [
        'cantidad.numeric' => 'La cantidad debe ser un valor numerico',
        'cantidad.required' => 'La cantidad es un campo obligatorio ',
        'titulo.required' => 'El titulo es un campo obligatorio',
        'consola.required' => 'No has seleccionado un titulo correcto.',
        'usuarios.required' => 'Debes seleccionar al menos un usuario.',
      ];
      // Validamos
      $v = Validator::make($request->all(), [
          'cantidad' => 'required|numeric',
          'titulo' => 'required',
          'consola' => 'required',
          'usuarios' => 'required',
      ], $msgs);

      // Si hay errores retornamos a la pantalla anterior con los mensajes
      if ($v->fails())
      {
          return redirect()->back()->withInput()->withErrors($v->errors());
      }

      DB::beginTransaction();

      try {

        if (isset($request->ids) && $request->ids != '') {
          $ids = explode(',', $request->ids);

          DB::table('stock_cargar')->whereIn('ID', $ids)->delete();
        }

        foreach ($request->usuarios as $usuario) {
          $data = [];
          $data['cantidad'] = $request->cantidad;
          $data['titulo'] = $request->titulo;
          $data['consola'] = $request->consola;
          $data['usuario'] = $usuario;
          $data['Notas'] = $request->Notas;

          DB::table('stock_cargar')->insert($data);
        }

        DB::commit();

        \Helper::messageFlash('Stock','Pedido cargado satisfactoriamente');
        return redirect('pedidos_carga/admin');
      } catch (Exception $e) {
        DB::rollback();
        return redirect()->back()->withErrors(['Ha ocurrido un error en el proceso de insercion. Por favor vuelve a intentarlo']);
      }
    }

    public function pedCargaAdmin()
    {
      $titles = $this->wp_p->lastGameStockTitles();
      $users = DB::table('usuarios')->get();
      $pedidos = $this->st->listPedidosCargados()->get();
      $pedidos_finalizados = $this->st->listPedidosFinalizados()->get();

      return view('stock.pedidos_carga_admin', compact('pedidos','pedidos_finalizados','titles','users'));
    }

    public function confirmPedCarga($id)
    {
      $ids = explode(",", $id);
      DB::table('stock_cargar')->whereIn('ID',$ids)->update(["estado" => "listo"]);

      \Helper::messageFlash('Stock','Pedido confirmado satisfactoriamente.');
      return redirect()->back();
    }

    public function pedidosCargar($user = null)
    {
      $pedidos = $this->st->listPedidosPorCargar($user)->get();
      $users = $this->st->usersPedidoPorCargar()->get();
      $user = $user;

      foreach ($pedidos as $i => $value) {
        $stock = WpPost::linkStoreByCondition($value->titulo,$value->consola)->first();
        $cantidad_cargada = Stock::getCantidadStockPorCargar(date('Y-m-d',strtotime($value->Day)), $value->titulo, $value->consola, $user)->value('Q_stk') == '' ? 0 : Stock::getCantidadStockPorCargar(date('Y-m-d',strtotime($value->Day)), $value->titulo, $value->consola, $user)->value('Q_stk'); 
        $cantidad_por_cargar = $value->cantidad - $cantidad_cargada;

        $pedidos[$i]->cantidad_cargar = $cantidad_por_cargar;
        $pedidos[$i]->link_ps = isset($stock->link_ps) ? $stock->link_ps : '';
      }

      return view('stock.pedidos_cargar_operador', compact('pedidos','users','user'));
    }

    public function getPedidosEdit($id)
    {
      $ids = explode(",", $id);
      $pedidos = $this->st->listPedidosCargados($ids)->get();

      echo json_encode($pedidos);
    }

    public function updateTitleProductX($id_stock,$accion) 
    {
      $stock = DB::table('stock')->where('ID',$id_stock)->first();
      $nota = "";
      if ($accion == 'agregar') {
        $titulo_new = "xx-".$stock->titulo;
        $nota = "Agregada doble x - juego {$stock->titulo} no cargado";
      } else {
        $titulo_new = str_replace("xx-","",$stock->titulo);
        $nota = "Quitada doble x - juego {$stock->titulo} cargado";
      }

      DB::beginTransaction();

      try {
        DB::table('stock')->where('ID',$id_stock)->update(["titulo" => $titulo_new]);

        $data = [];
        $data['cuentas_id'] = $stock->cuentas_id;
        $data['Notas'] = $nota;
        $data['Day'] = date('Y-m-d H:i:s');
        $data['usuario'] = session()->get('usuario')->Nombre;

        DB::table('cuentas_notas')->insert($data);

        DB::commit();

        \Helper::messageFlash('Stock','Proceso doble x satisfactorio.');
        return redirect()->back();
        
      } catch (Exception $e) {
        DB::rollback();
        return redirect()->back()->withErrors(['Ha ocurrido un error en el proceso de insercion. Por favor vuelve a intentarlo']);
      }
      
    }

    public function deleteProduct($id_stock) {
      $stock = DB::table("stock")->where("ID",$id_stock)->first();
      $nota = "Producto ".str_replace("-"," ",$stock->titulo)." eliminado.";

      DB::beginTransaction();

      try {
        DB::table("stock")->where("ID",$id_stock)->delete();

        $data = [];
        $data['cuentas_id'] = $stock->cuentas_id;
        $data['Notas'] = $nota;
        $data['Day'] = date('Y-m-d H:i:s');
        $data['usuario'] = session()->get('usuario')->Nombre;

        DB::table('cuentas_notas')->insert($data);

        DB::commit();

        \Helper::messageFlash('Stock',$nota);
        return redirect()->back();
      } catch (Exception $e) {
        DB::rollback();
        return redirect()->back()->withErrors(['Ha ocurrido un error en el proceso de insercion. Por favor vuelve a intentarlo']);
      }
    }
}

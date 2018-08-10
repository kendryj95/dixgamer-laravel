<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Validator;
use Schema;
use DB;
use App\Stock;
use App\WpPost;
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
        $stocks = WpPost::linkStore()->paginate(50);
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
      $cotiz = 25;

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

    public function createCodeG(){
      $cotiz = 25;

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

    public function createCodep3(){
        $cotiz = 25;

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
}

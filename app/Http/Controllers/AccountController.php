<?php

namespace App\Http\Controllers;

use App\Account;
use App\Stock;
use App\AccountNote;
use App\Expenses;
use App\Balance;
use App\WpPost;
use App\Reset;
use Illuminate\Http\Request;
use DB;
use Validator;
use Auth;
use Schema;
use Mail;

class AccountController extends Controller
{

    private $wp_pst;
    private $tks;
    private $blc;
    private $ac;
    private $acc;
    private $rst;
    private $dte;
    function __construct(){

      $this->tks = new Stock();
      $this->blc = new Balance();
      $this->ac = new AccountNote();
      $this->acc = new Account();
      $this->rst = new Reset();
      $this->wp_pst = new WpPost();
      $this->dte =date('Y-m-d H:i:s', time());
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

      // Columnas de la base de datos
      $columns = Schema::getColumnListing('cuentas');
      // Traer la lista de cuentas

      // cuentas con filtro
      $obj = new \stdClass;
      $obj->column = $request->column;
      $obj->word = $request->word;

      $dominios = Account::dominiosByUser()->get();

      $accounts = Account::accountGames($obj)->paginate(50);


      return view('account.index',
                  compact(
                    'accounts',
                    'columns',
                    'dominios'
                  ));
    }

    public function accountAmount(Request $request)
    {
      // cuentas con filtro
      $range = new \stdClass;
      $range->saldoMin = $request->saldoMin;
      $range->saldoMax = $request->saldoMax;

      $orderBy = isset($request->order) ? $request->order : null;
      // Traer la lista de cuentas
      $accounts = Account::accountAmounts($request->console, $orderBy, $range)->paginate(50);

      $dominios_excluidos = DB::table('configuraciones')->where('ID',1)->value('dominios_excluidos');
      $dominios_excluidos = explode(",",$dominios_excluidos);
      $dominios_excluidos = array_map(function($val) {
        return str_replace('"','',$val);
      }, $dominios_excluidos);

      return view('account.index_amount',
                  compact(
                    'accounts',
                    'range',
                    'dominios_excluidos'
                  ));
    }

    public function details_account(Request $request, $id){

        return view('account.details_account');
    }


    // Retorna vista para modal de solicitud de reseteo
    public function requestReset($id){
      $account = Account::where('ID',$id)->first();

      if (!$account)
        return redirect('/cuentas')->withErrors('Intentelo nuevamente');


      return view('ajax.account.request_reset',compact('account'));

    }

    // Retorna vista para modal de solicitud de reseteo
    public function storeRequestReset($id,Request $request){
      $account = Account::where('ID',$id)->first();

      if (!$account)
        return redirect('/cuentas')->withErrors('Intentelo nuevamente');


      try {
        $data = [];
        $data['cuentas_id'] = $id;
        $data['Day'] = $this->dte;
        $data['Notas'] = '';
        $data['usuario'] = session()->get('usuario')->Nombre;

        $this->rst->storeRequestResetAccount($data);

        // Mensaje de notificacion
        \Helper::messageFlash('Cuentas','Solicitud de reseteo creada','alert_cuenta');

        return redirect('cuentas/'.$id);
      } catch (\Exception $e) {
        return redirect()->back()->withErrors(['Intentelo nuevamente']);
      }



    }


    // juegos de cuentas ps3
    public function accountGamePs3(Request $request)
    {
      // Traer la lista de cuentas
      $accounts = Account::accountGamesPs3()->paginate(20);

      //dd($accounts);

      return view('account.index_account_ps3',
                  compact(
                    'accounts'
                  ));
    }

    // juegos de cuentas ps4
    public function accountGamePs4(Request $request)
    {
        // Traer la lista de cuentas
        $accounts = Account::accountGamesPs4()->paginate(20);

        //dd($accounts);

        return view('account.index_account_ps4',
            compact(
                'accounts'
            ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      $dominios = Account::dominiosByUser()->get();
      $usuario = session()->get('usuario')->Nombre;

      if (count($dominios) > 0) {
        $nom_dominios = [];

        foreach ($dominios as $value) {
          if ($value->indicador_habilitado == 1) {
            $nom_dominios[] = $value->dominio;
          }
        }
        
        if (count($nom_dominios) == 0)
          return redirect()->back()->withErrors([$usuario . ' no tienes ningún dominio habilitado asociado a ti. Debes habilitar al menos uno (1) para poder crear una cuenta.']);
        
        $chars = "123456789";
        $random = substr( str_shuffle( $chars ), 0, 1 );
        $comienzo = ['qer','adf','zcv','wrt','sfg','xvb','ety','dgh','cbn'];
        $comienzo = $comienzo[$random-1];
        $vendedor = strtolower(session()->get('usuario')->Nombre);
        $vendedor = substr($vendedor, 0, 2);
        $nro = (DB::table('cuentas')->max('ID')) - 39000;
        // $chars = "1234";
        // $random = substr( str_shuffle( $chars ), 0, 1 );
        $count_dom = count($nom_dominios);
        $random = rand(1, $count_dom);
        // $dominio = ['dix111.com','dixabc.com','123dix.site','dix111.com'];
        $dominio = $nom_dominios[$random-1];
        $idcuenta = $comienzo . $nro . $vendedor;
        $emailcuenta = $comienzo . "." . $nro . $vendedor . "@" . $dominio;///// 17-02-2020 arranco con nuevo sistema mucho mas aleatorio 
            // $emailcuenta = "cuenta." . $emailcuenta1 . "." . $emailcuenta2 . "@abcdix.com"; ///// 15-02-2020 camio a abcdix de nuevo // 14-02-2020 cambio a dix111.com // cambio de abcdix.com a game24hs.com 2019-02-25


        return view('account.create', compact('idcuenta', 'emailcuenta'));
      } else {
        return redirect()->back()->withErrors(["No hay dominios disponibles para crear una cuenta"]);
      }
      
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
        'mail.required' => 'Email requerido',
        'mail_fake.required' => 'Email falso requerido',
        'mail_fake.email' => 'Ingrese Email falso valido',
        'mail.unique' => 'Emal ya existe',
        'mail_fake.unique' => 'Email falso ya existe',
        'surname.required' => 'Apellido requerido',
        'name.required' => 'Nombre requerido',
        'pass.required' => 'Password requerido'
      ];
      // Validamos
      $v = Validator::make($request->all(), [
          'mail' => 'required|unique:cuentas,mail',
          'surname' => 'required',
          'name' => 'required',
          'mail_fake' => 'required|email|unique:cuentas,mail_fake',
          'pass' => 'required'
      ], $msgs);


      // Si hay errores retornamos a la pantalla anterior con los mensajes
      if ($v->fails())
      {
          return redirect()->back()->withErrors($v->errors());
      }

      $ac = new Account();

      try {
        $account = [];
        $account['mail_fake'] = $request->mail_fake;
        $account['mail'] = $request->mail;
        $account['name'] = $request->name;
        $account['surname'] = $request->surname;
        $account['country'] = $request->country;
        $account['state'] = $request->state;
        $account['city'] = $request->city;
        $account['days'] = $request->days;
        $account['months'] = $request->months;
        $account['pc'] = $request->pc;
        $account['years'] = $request->years;
        $account['nacimiento'] = $request->nacimiento;
        $account['pass'] = $request->pass;
        $account['usuario'] = session()->get('usuario')->Nombre;
        $account['address'] = $request->address;
        $account['reg_date'] = Date('Y-m-d H:i:s');
        $id_account = $ac->createAccount($account);



        // Mensaje de notificacion
        \Helper::messageFlash('Cuentas','Cuenta guardada','alert_cuenta');


        return redirect('/cuentas/'.$id_account);
      } catch (\Exception $e) {
        return redirect()->back()->withErrors(['Intentelo nuevamente']);
      }
    }


    public function repeatLastAccount(Request $request, $account_id){
      // Mensajes de alerta
      $msgs = [
        'last_account.required' => 'Intentelo nuevamente',
        'saldo_usd.required' => 'Intentelo nuevamente',
        'saldo_ars.required' => 'Intentelo nuevamente'
      ];
      // Validamos
      $v = Validator::make($request->all(), [
          'last_account' => 'required',
          'saldo_usd' => 'required',
          'saldo_ars' => 'required'
      ], $msgs);

      $guardar = true;


      // Si hay errores retornamos a la pantalla anterior con los mensajes
      if ($v->fails())
      {
          return redirect()->back()->withErrors($v->errors());
      }

      $lastGames = $this->tks->lastAccountByIdAndUser(session()->get('usuario')->Nombre,$request->last_account);
      if (!$lastGames)
        return redirect()->back()->withErrors(['Intentelo nuevamente']);

      $data = [];
      foreach ($lastGames as $key => $game) {
        $costo = ($game->costo_usd / $request->saldo_usd) * $request->saldo_ars;
        // Arreglo que se guarda en $data para guardar multiples juegos de una sola ves
        $data[$key] = [
          'cuentas_id' => $account_id,
          'consola' => $game->consola,
          'titulo' => $game->titulo,
          'medio_pago' => 'Saldo',
          'costo_usd' => $game->costo_usd,
          'costo_usd_modif' => $game->costo_usd,
          'costo' => $costo,
          'Day' => $this->dte,
          'usuario' => session()->get('usuario')->Nombre,
        ];

        if ($costo == 0) {
          $guardar = false;
          break;
        }
      }

      try {

        if ($guardar) {
          // mandamos a guardar el arreglo de juegos
          $this->tks->storeCodes($data);

          // Mensaje de notificacion
          \Helper::messageFlash('Cuentas','Cuenta copiada','alert_cuenta');


          return redirect('cuentas/'.$account_id);
        } else {
          return redirect()->back()->withErrors(['Oops! Se ha detectado que hubo un calculo del stock con costo cero (0). Por favor revisar.']);
        }
      } catch (\Exception $e) {
        dd($e->getMessage());
        return redirect()->back()->withErrors(['Intentelo nuevamente']);

      }


    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Account  $account
     * @return \Illuminate\Http\Response
     */

    public function previo(){
        return Account::previo();
    }

    public function show(Account $account,$id)
    {

      $account = Account::resetAccountDetail($id)->first();
      //if (count($account) < 1)
        //return redirect('/cuentas')->withErrors('Cuenta no encontrada');

      $stocks = Stock::stockDetailSold($id)->get();
      $quantityStock = Stock::quantityAccountId($id)->first();
      $soldConcept = $this->tks->soldFronConcept($id);
      $expensesIncome = Expenses::expensesIncome();
      if (!empty($expensesIncome)) {
        $expensesIncome = $expensesIncome[0];
      }
      $maxDayReset = Reset::maxDayAccountIdReset($id);
      if (!empty($maxDayReset)) {
        $maxDayReset = $maxDayReset[0];
      }
      $accountBalances = Balance::accountBalance($id);
      $hasBalance = Balance::AccountHasBalance($id)->first();
      // dd($accountBalances);
      $lastAccountGames = $this->tks->lastAccountUserGames(session()->get('usuario')->Nombre);

        $next = Account::Siguiente($id)->first();
        $back = Account::Previo($id)->first();
      //dd($soldConcept);
      $oferta_fortnite = DB::table('configuraciones')->where('ID',1)->value('oferta_fortnite');

      $operador_pass = $this->showBtnSigueJugando($id);
      $operador_reset = $this->showBtnSigueJugandoPri($id);
      
      $vendedor = session()->get('usuario')->Nombre;

      $cuenta_robada = $this->isAccountStolen($id);

      $ventaPs4Pri = $this->ventaPs4Pri($id);
      $ventaPs4Secu = $this->ventaPs4Secu($id);

      $lastGame = false;
      
      if ($lastAccountGames) {
        $lastGame = $lastAccountGames[0];
      }

      $dominios_excluidos = DB::table('configuraciones')->where('ID',1)->value('dominios_excluidos');
      $dominios_excluidos = explode(",",$dominios_excluidos);
      $domains_exclu = [];
      foreach ($dominios_excluidos as $value) {
        $domains_exclu[] = str_replace('"', '', $value);
      }
      $dominio = explode("@",$account->mail_fake)[1];
      $dom_excluido = in_array($dominio,$domains_exclu);

      $product_20_off = DB::table('saldo')->where('titulo','20-off-playstation')->where('cuentas_id',$id)->first(); // Consulta para verificar si se cargó este producto en saldo con este id de cuenta
      $existeStock_product_20_off = DB::table('stock')->where('titulo','20-off-playstation')->where('consola','ps')->count(); // Consulta para verificar si existe stock de este producto.

      $fornite = false;
      $accountNotes = DB::table('cuentas_notas')->where('cuentas_id', $id)->get();

      $balance_sony = DB::table('cuentas_balance_sony')->select(DB::raw("DATE_FORMAT(Day,'%d/%m/%Y') as Day"),'balance','usuario')->where('cuentas_id',$id)->orderBy('Day','DESC')->first();

      foreach ($accountNotes as $value) {
        if ($value->Notas == "Fortnite comprado") {
          $fornite = true;
          break;
        }
      }

      $ventasPs3 = 0;
      $ventasPs3Arr = [];
      foreach ($soldConcept as $item) {
          if ($item->consola == 'ps3') {
              $ventasPs3++;
              $ventasPs3Arr[] = $item;
          }
      }
      $lastVentaPs3 = null;
      if (count($ventasPs3Arr) > 0)
          $lastVentaPs3 = $ventasPs3Arr[count($ventasPs3Arr)-1];


      return view('account.show',compact(
                'account',
                'stocks',
                'quantityStock',
                'soldConcept',
                'expensesIncome',
                'expensesIncome',
                'maxDayReset',
                'maxDayReset',
                'accountBalances',
                'hasBalance',
                'lastAccountGames',
                'lastGame',
                'next',
                'back',
                'oferta_fortnite',
                'operador_pass',
                'operador_reset',
                'product_20_off',
                'existeStock_product_20_off',
                'cuenta_robada',
                'ventaPs4Pri',
                'ventaPs4Secu',
                'dom_excluido',
                'fornite',
                'balance_sony',
                'ventasPs3',
                'lastVentaPs3'
      ));

    }

    private function isAccountStolen($account_id) {
      $account = DB::table('cuentas_robadas')->where('cuentas_id',$account_id)->value('cuentas_id');

      if ($account) {
        return true;
      }

      return false;
    }

    private function showBtnSigueJugando($account_id)
    {
      $operadores_especiales = \Helper::getOperatorsEspecials('Secu');
      $show = false;

      ## CONSULTANDO SI ESTA CUENTA TIENE UNA VENTA SECUNDARIA

      $stocks = DB::table('stock')->select(DB::raw('GROUP_CONCAT(ID) AS stocks_ids'))->where('cuentas_id',$account_id)->groupBy('cuentas_id')->value('stocks_ids');
      $stocks = explode(",", $stocks);

      $venta = DB::table('ventas')->whereIn('stock_id',$stocks)->where('slot','Secundario')->first();

      ## CONSULTANDO SI HUBO UN CAMBIO DE CONTRASEÑA PARA ESTA CUENTA CON ALGUNOS DE LOS OPERADORES ESPECIALES.
      $cuenta_pass = DB::table('cta_pass')->where('cuentas_id',$account_id)->whereIn('usuario',$operadores_especiales)->orderBy('Day','DESC')->first();

      if ($cuenta_pass && $venta) {

        ## VALIDANDO QUE LA VENTA SE HAYA HECHO ANTES DEL CAMBIO DE CONTRASEÑA

        if ($venta->Day_modif < $cuenta_pass->Day) {
          return $venta;
        }
        
      }

      return $show;
      
    }

    private function showBtnSigueJugandoPri($account_id)
    {
      $operadores_especiales = \Helper::getOperatorsEspecials('Pri');
      $show = false;

      ## CONSULTANDO SI ESTA CUENTA TIENE UNA VENTA PRIMARIA

      $stocks = DB::table('stock')->select(DB::raw('GROUP_CONCAT(ID) AS stocks_ids'))->where('cuentas_id',$account_id)->groupBy('cuentas_id')->value('stocks_ids');
      $stocks = explode(",", $stocks);

      $venta = DB::table('ventas')->whereIn('stock_id',$stocks)->where('slot','Primario')->where('cons','ps4')->first();

      ## CONSULTANDO SI HUBO UN RESETEO PARA ESTA CUENTA CON ALGUNOS DE LOS OPERADORES ESPECIALES.
      $cuenta_reset = DB::table('reseteo')->where('cuentas_id',$account_id)->whereIn('usuario',$operadores_especiales)->orderBy('Day','DESC')->first();

      if ($cuenta_reset && $venta) {

        ## VALIDANDO QUE LA VENTA SE HAYA HECHO ANTES DEL RESETEO

        if ($venta->Day_modif < $cuenta_reset->Day) {
          return $venta;
        }
        
      }

      return $show;
      
    }

    public function getDataPaginaAnt(Request $request){

    }

    public function createStockAccount($id){

      $consolas = [];
      $stocks = Stock::stockDetailSold($id)->get();

      foreach ($stocks as $stock) {
        $consolas[] = $stock->consola;
      }

      $account = Account::accountStockId($id)->first();
      $expense = Stock::stockExpensesByAccountId($id)->first();
      $titles = $this->wp_pst->lastGameStockTitles();
      $accountBalance = Balance::totalBalanceAccount($id)->first();
      return view('ajax.account.insert_stock',compact(
        'account',
        'expense',
        'titles',
        'accountBalance',
        'consolas'
      ));
    }

    public function editStockAccount($stock_id,$account_id,$opt){
      $obj = new \stdClass;
      $obj->column = 'ID';
      $obj->word = $account_id;

      $opt = $opt;

      // traemos la cuenta por el ID
      $account = Account::AccountByColumnWord($obj)->first();
      $expense = Stock::stockExpensesByAccountId($account_id)->first();
      $accountBalance = Balance::totalBalanceAccount($account_id)->first();

      // Traemos el stock
      $stock = Stock::where('ID',$stock_id)->first();
      $total_stocks = Stock::stockDetailSold($account_id)->count();
      $titles = $this->wp_pst->lastGameStockTitles();

      return view('ajax.account.update_stock',compact(
        'account',
        'stock',
        'titles',
        'expense',
        'total_stocks',
        'accountBalance',
        'opt'
      ));
    }

    public function updateStockAccount(Request $request,$account_id){
      // dd($request->all());

      $obj = new \stdClass;
      $obj->column = 'ID';
      $obj->word = $account_id;

      $opt = $request->opt;

      // traemos la cuenta por el ID
      $account = Account::AccountByColumnWord($obj)->first();

      // Traemos el stock
      $stock = Stock::where('ID',$request->stock_id)->first();
      // $expense = Stock::stockExpensesByAccountId($account_id)->first();
      

      if ($account && $stock) {
        try {

          $operador = session()->get('usuario')->Nombre;
          $notes = [];
          $data = [];

          switch ($opt) {
            case 1:

            // Mensajes de alerta
            $msgs = [
              'titulo.required' => 'Titulo requerido',
              'stock_id.required' => 'Intentelo nuevamente',
              'consola.required' => 'Consola requerida'
            ];
            // Validamos
            $v = Validator::make($request->all(), [
                'titulo' => 'required',
                'consola' => 'required',
                'stock_id' => 'required'
            ], $msgs);


            // Si hay errores retornamos a la pantalla anterior con los mensajes
            if ($v->fails())
            {
                return redirect()->back()->withErrors($v->errors());
            }

              $notes['Notas'] = "Modificacion de juego #$stock->ID, antes $stock->titulo ($stock->consola)";

              $data['titulo'] = $request->titulo;
              $data['consola'] = $request->consola;

              break;
            
            case 2:

            $accountBalance = Balance::totalBalanceAccount($account_id)->first();

            /// SI EL COSTO EN USD ES 9.99, 19.99, etc... LE SUMO UN CENTAVO
            $costo_usd = round($request->costo_usd, 1);

            $saldo_acumulado = $request->costo_act + $request->saldo_act;

            //// CALCULO EL SALDO LIBRE DE LA CUENTA EN USD Y EN ARS
            $saldo_libre_usd = ($accountBalance->costo_usd);
            $saldo_libre_ars = ($accountBalance->costo);

            /// SI EL SALDO A QUEDAR LUEGO DE INSERTAR UN PRODUCTO ES MAYOR O IGUAL A 9.99, CARGO COSTO ARS PROPORCIONAL
            if (($saldo_libre_usd - $costo_usd) > 9.99) {
              $costo_ars = ($saldo_libre_ars * ($costo_usd/$saldo_libre_usd));
            } else {
            /// SI EL SALDO A QUEDAR ES MENOR A 9.99 LE ASIGNO EL TOTAL EN PESOS LIBRES > ABSORBO TODO EL COSTO EN PESOS
              $costo_ars = $saldo_libre_ars;
            }

            if ($request->saldo_act != 0) {
              if ($costo_usd > $saldo_acumulado) {
                if ($stock->titulo == $request->titulo) { // Que valide solo cuando está tratando de actualizar el costo con el mismo stock
                  return redirect()->back()->withErrors('El costo utilizado para actualizar el producto no puede ser mayor al saldo que tienes disponible.');
                }
              }
            }

              $notes['Notas'] = "Modificacion de juego #$stock->ID, antes costo $request->costo_act a $costo_usd";

              $data['costo_usd'] = $costo_usd;
              $data['costo'] = $costo_ars;
              break;
          }

          $notes['cuentas_id'] = $account_id;
          $notes['usuario'] = session()->get('usuario')->Nombre;
          $notes['Day'] = $this->dte;
          DB::table('cuentas_notas')->insert([$notes]);

          $this->tks->updateStockById($request->stock_id,$data);

          // Mensaje de notificacion
          \Helper::messageFlash('Cuentas','Stock actualizado','alert_cuenta');
          return redirect('cuentas/'.$account_id);
          redirect('cuentas/'.$account_id);
        } catch (\Exception $e) {
          return redirect('/cuentas')->withErrors('Intentelo nuevamente');
        }

      }

      return redirect('/cuentas')->withErrors('Intentelo nuevamente');
    }

    public function storeStockAccount(Request $request,$id){
      // Mensajes de alerta
      $msgs = [
        'cuentas_id.required' => 'Intentelo nuevamente',
        'titulo.required' => 'Titulo requerido',
        'consola.required' => 'Consola requerida',
        'costo_usd.required' => 'Costo requerido'
      ];
      // Validamos
      $v = Validator::make($request->all(), [
          'cuentas_id' => 'required',
          'titulo' => 'required',
          'consola' => 'required',
          'costo_usd' => 'required'
      ], $msgs);


      // Si hay errores retornamos a la pantalla anterior con los mensajes
      if ($v->fails())
      {
          return redirect()->back()->withErrors($v->errors());
      }

      if ($request->costo_usd > $request->saldo_act) {
        return redirect()->back()->withErrors('El saldo a usar para cargar el juego no puede ser mayor al que tienes disponible.');
      }


      $account = Account::accountStockId($id)->first();
      $expense = Stock::stockExpensesByAccountId($id)->first();
      $accountBalance = Balance::totalBalanceAccount($id)->first();

      if (empty($expense)) {
        $expense = new \stdClass;
        $expense->costo_usd = 0;
        $expense->costo = 0;
      }
      /// SI EL COSTO EN USD ES 9.99, 19.99, etc... LE SUMO UN CENTAVO
      $costo_usd = round($request->costo_usd, 1);

      //// CALCULO EL SALDO LIBRE DE LA CUENTA EN USD Y EN ARS
      $saldo_libre_usd = ($accountBalance->costo_usd - $expense->costo_usd);
      $saldo_libre_ars = ($accountBalance->costo - $expense->costo);

      /// SI EL SALDO A QUEDAR LUEGO DE INSERTAR UN PRODUCTO ES MAYOR O IGUAL A 9.99, CARGO COSTO ARS PROPORCIONAL
      if (($saldo_libre_usd - $costo_usd) >= 9.99) {
        $costo_ars = ($saldo_libre_ars * ($costo_usd/$saldo_libre_usd));
      } else {
      /// SI EL SALDO A QUEDAR ES MENOR A 9.99 LE ASIGNO EL TOTAL EN PESOS LIBRES > ABSORBO TODO EL COSTO EN PESOS
        $costo_ars = $saldo_libre_ars;
      }



      try {

        if ($this->validarStock($request->cuentas_id, $request->titulo, $request->consola)) {
          return redirect('cuentas/'.$request->cuentas_id)->withErrors("Ya existe el juego $request->titulo ($request->consola) en esta cuenta.");
        } 

        $data = [];
        $data['titulo'] = $request->titulo;
        $data['consola'] = $request->consola;
        $data['cuentas_id'] = $request->cuentas_id;
        $data['costo_usd'] = $request->costo_usd;
        $data['costo_usd_modif'] = $request->costo_usd;
        $data['medio_pago'] = 'Saldo';
        $data['costo'] = $costo_ars;
        $data['Day'] = $this->dte;
        $data['usuario'] = session()->get('usuario')->Nombre;

        $id_stock = $this->tks->storeStockAccount($data);
        
        \Helper::messageFlash('Cuentas','Stock agregado','alert_cuenta');
        return redirect('cuentas/'.$request->cuentas_id);
      } catch (\Exception $e) {
        return redirect('/cuentas')->withErrors('Intentelo nuevamente');
      }

    }

    private function validarStock($id_cuenta, $titulo, $consola) {

      $resultado = DB::table('stock')->where('cuentas_id', $id_cuenta)->where('titulo', $titulo)->where('consola', $consola)->first();

      return $resultado;

    }

    public function rechargeBalance($id){
      $gifts = $this->blc->reChargeGifCards();
      $account_id = $id ;
      // dd($gifts);
      return view('ajax.account.recharge_balance',compact(
        'gifts',
        'account_id'
      ));
    }

    public function rechargeBalanceMinim($id){
      $gifts = $this->blc->reChargeGifCards(true);
      $account_id = $id ;
      // dd($gifts);
      return view('ajax.account.recharge_balance_minim',compact(
        'gifts',
        'account_id'
      ));
    }

    public function storeBalanceAccount($account,$title,$console){
      if (!empty($account) && !empty($title) && !empty($console)) {
        // cargo el stock disponible en este mismo segundo y busco el producto que quiero asignar
        $band = false;
        if ($title == 'gift-card-60-usd-org') {

            $titles = ['gift-card-10-usd', 'gift-card-50-usd'];
            $valido = true;

            foreach ($titles as $title) {
                $stock_valido = \Helper::availableStock($account,$title,$console);
                if (!is_array($stock_valido)) {
                    $valido = false;
                    break;
                }
            }

            if (!$valido) {

                $t3 = 'gift-card-20-usd';
                $t3_val = \Helper::availableStock($account,$t3,$console);
                $valido = true;
                if (is_array($t3_val)) { // Combinacion 3x20
                    $titles = [];
                    for ($i=0;$i<3;$i++) {
                        $titles[$i] = $t3;
                    }
                    if ($t3_val[0]->Q_Stock < 3)
                        $valido = false;
                }

                if (!$valido) {// Combinacion 6x10
                    $title = 'gift-card-10-usd';
                    $titles = [];
                    for ($i=0;$i<6;$i++) {
                        $titles[$i] = $title;
                    }
                    $stock_valido = \Helper::availableStock($account,$title,$console);
                    $valido = true;
                    if (is_array($stock_valido)) {
                        if ($stock_valido[0]->Q_Stock < 6) {
                            $valido = false;
                        }
                    } else {
                        $valido = false;
                    }
                }
            }

          if ($valido) {
              foreach ($titles as $title) {
                  $stock_valido = \Helper::availableStock($account,$title,$console);

                  if (is_array($stock_valido)) {
                      $stock_valido_id = $stock_valido[0]->ID_stk;
                      $stock = Stock::stockDetail($stock_valido_id)->first();

                      $date = date('Y-m-d H:i:s');
                      $data = [
                          'cuentas_id'=>$account,
                          'ex_stock_id'=>$stock->ID,
                          'titulo'=>$title,
                          'consola'=>$console,
                          'medio_pago'=>$stock->medio_pago,
                          'costo_usd'=>$stock->costo_usd,
                          'costo'=>$stock->costo,
                          'code'=>$stock->code,
                          'code_prov'=>$stock->code_prov,
                          'n_order'=>$stock->n_order,
                          'Day'=>$date,
                          'ex_Day_stock'=>$stock->Day,
                          'usuario'=>session()->get('usuario')->Nombre,
                          'ex_usuario'=>$stock->usuario
                      ];

                      try {
                          $this->blc->storeBalanceAccount($data);
                          $nota = 'Agregado a cta <a href="'.url('cuentas',$account).'" class="alert-link">#'.$account.'</a> '.$stock->code;

                          $data = [];
                          $data['stock_id'] = $stock->ID;
                          $data['Notas'] = $nota;
                          $data['Day'] = date('Y-m-d H:i:s');
                          $data['usuario'] = session()->get('usuario')->Nombre;
                          DB::table('stock_notas')->insert($data);

                          // Eliminando stock
                          $stock = Stock::where('ID',$stock->ID)->delete();
                          // Mensaje de notificacion
                          // \Helper::messageFlash('Cuentas','Saldo agregado','alert_cuenta');
                          // return redirect('cuentas/'.$account);
                          $band = true;
                      } catch (\Exception $e) {
                          // return redirect('/cuentas')->withErrors('Intentelo nuevamente');
                          $band = false;
                      }

                  } else {
                      return redirect()->back()->withErrors(['No hay stock de la gift']);
                  }
              }
          } else {
              return redirect()->back()->withErrors(['No hay stocks disponibles para combinar']);
          }
          
        } elseif ($title == 'gift-card-30-usd-org') {

          $titles = ['gift-card-10-usd', 'gift-card-20-usd'];
          $valido = true;

          foreach ($titles as $title) {
              $stock_valido = \Helper::availableStock($account,$title,$console);
              if (!is_array($stock_valido)) {
                  $valido = false;
                  break;
              }
          }

            if (!$valido) {

                $title = 'gift-card-10-usd';
                $titles = [];
                for ($i=0;$i<3;$i++) {
                    $titles[$i] = $title;
                }
                $stock_valido = \Helper::availableStock($account,$title,$console);
                $valido = true;
                if (is_array($stock_valido)) {
                    if ($stock_valido[0]->Q_Stock < 3) {
                        $valido = false;
                    }
                } else {
                    $valido = false;
                }
            }

          if ($valido) {
              foreach ($titles as $title) {
                  $stock_valido = \Helper::availableStock($account,$title,$console);

                  if (is_array($stock_valido)) {
                      $stock_valido_id = $stock_valido[0]->ID_stk;
                      $stock = Stock::stockDetail($stock_valido_id)->first();

                      $date = date('Y-m-d H:i:s');
                      $data = [
                          'cuentas_id'=>$account,
                          'ex_stock_id'=>$stock->ID,
                          'titulo'=>$title,
                          'consola'=>$console,
                          'medio_pago'=>$stock->medio_pago,
                          'costo_usd'=>$stock->costo_usd,
                          'costo'=>$stock->costo,
                          'code'=>$stock->code,
                          'code_prov'=>$stock->code_prov,
                          'n_order'=>$stock->n_order,
                          'Day'=>$date,
                          'ex_Day_stock'=>$stock->Day,
                          'usuario'=>session()->get('usuario')->Nombre,
                          'ex_usuario'=>$stock->usuario
                      ];

                      try {
                          $this->blc->storeBalanceAccount($data);
                          $nota = 'Agregado a cta <a href="'.url('cuentas',$account).'" class="alert-link">#'.$account.'</a> '.$stock->code;

                          $data = [];
                          $data['stock_id'] = $stock->ID;
                          $data['Notas'] = $nota;
                          $data['Day'] = date('Y-m-d H:i:s');
                          $data['usuario'] = session()->get('usuario')->Nombre;
                          DB::table('stock_notas')->insert($data);

                          // Eliminando stock
                          $stock = Stock::where('ID',$stock->ID)->delete();
                          // Mensaje de notificacion
                          // \Helper::messageFlash('Cuentas','Saldo agregado','alert_cuenta');
                          // return redirect('cuentas/'.$account);
                          $band = true;
                      } catch (\Exception $e) {
                          // return redirect('/cuentas')->withErrors('Intentelo nuevamente');
                          $band = false;
                      }

                  } else {
                      return redirect()->back()->withErrors(['No hay stock de la gift']);
                  }
              }
          } else {
              return redirect()->back()->withErrors(['No hay stock disponibles para combinar']);
          }

        } elseif($title == 'gift-card-40-usd-org'){
          $titles = ['gift-card-20-usd', 'gift-card-20-usd'];
            $valido = true;

            foreach ($titles as $title) {
                $stock_valido = \Helper::availableStock($account,$title,$console);
                if (!is_array($stock_valido)) {
                    $valido = false;
                    break;
                }
            }

            if (!$valido) {

                $title = 'gift-card-10-usd';
                $titles = [];
                for ($i=0;$i<4;$i++) {
                    $titles[$i] = $title;
                }
                $stock_valido = \Helper::availableStock($account,$title,$console);
                $valido = true;
                if (is_array($stock_valido)) {
                    if ($stock_valido[0]->Q_Stock < 4) {
                        $valido = false;
                    }
                } else {
                    $valido = false;
                }
            }

            if ($valido) {
                foreach ($titles as $title) {
                    $stock_valido = \Helper::availableStock($account,$title,$console);

                    if (is_array($stock_valido)) {
                        $stock_valido_id = $stock_valido[0]->ID_stk;
                        $stock = Stock::stockDetail($stock_valido_id)->first();

                        $date = date('Y-m-d H:i:s');
                        $data = [
                            'cuentas_id'=>$account,
                            'ex_stock_id'=>$stock->ID,
                            'titulo'=>$title,
                            'consola'=>$console,
                            'medio_pago'=>$stock->medio_pago,
                            'costo_usd'=>$stock->costo_usd,
                            'costo'=>$stock->costo,
                            'code'=>$stock->code,
                            'code_prov'=>$stock->code_prov,
                            'n_order'=>$stock->n_order,
                            'Day'=>$date,
                            'ex_Day_stock'=>$stock->Day,
                            'usuario'=>session()->get('usuario')->Nombre,
                            'ex_usuario'=>$stock->usuario
                        ];

                        try {
                            $this->blc->storeBalanceAccount($data);
                            $nota = 'Agregado a cta <a href="'.url('cuentas',$account).'" class="alert-link">#'.$account.'</a> '.$stock->code;

                            $data = [];
                            $data['stock_id'] = $stock->ID;
                            $data['Notas'] = $nota;
                            $data['Day'] = date('Y-m-d H:i:s');
                            $data['usuario'] = session()->get('usuario')->Nombre;
                            DB::table('stock_notas')->insert($data);

                            // Eliminando stock
                            $stock = Stock::where('ID',$stock->ID)->delete();
                            // Mensaje de notificacion
                            // \Helper::messageFlash('Cuentas','Saldo agregado','alert_cuenta');
                            // return redirect('cuentas/'.$account);
                            $band = true;
                        } catch (\Exception $e) {
                            // return redirect('/cuentas')->withErrors('Intentelo nuevamente');
                            $band = false;
                        }

                    } else {
                        return redirect()->back()->withErrors(['No hay stock de la gift']);
                    }
                }
            } else {
                return redirect()->back()->withErrors(['No hay stock para combinar']);
            }

        } elseif ($title == 'gift-card-55-usd-org') {

          $titles = ['gift-card-10-usd', 'gift-card-20-usd', 'gift-card-25-usd'];

          foreach ($titles as $title) {
            $stock_valido = \Helper::availableStock($account,$title,$console);

            if (is_array($stock_valido)) { 
              $stock_valido_id = $stock_valido[0]->ID_stk;
              $stock = Stock::stockDetail($stock_valido_id)->first();

              $date = date('Y-m-d H:i:s');
              $data = [
                'cuentas_id'=>$account,
                'ex_stock_id'=>$stock->ID,
                'titulo'=>$title,
                'consola'=>$console,
                'medio_pago'=>$stock->medio_pago,
                'costo_usd'=>$stock->costo_usd,
                'costo'=>$stock->costo,
                'code'=>$stock->code,
                'code_prov'=>$stock->code_prov,
                'n_order'=>$stock->n_order,
                'Day'=>$date,
                'ex_Day_stock'=>$stock->Day,
                'usuario'=>session()->get('usuario')->Nombre,
                'ex_usuario'=>$stock->usuario
              ];

              try {
                $this->blc->storeBalanceAccount($data);
                $nota = 'Agregado a cta <a href="'.url('cuentas',$account).'" class="alert-link">#'.$account.'</a> '.$stock->code;

                $data = [];
                $data['stock_id'] = $stock->ID;
                $data['Notas'] = $nota;
                $data['Day'] = date('Y-m-d H:i:s');
                $data['usuario'] = session()->get('usuario')->Nombre;
                DB::table('stock_notas')->insert($data);

                // Eliminando stock
                $stock = Stock::where('ID',$stock->ID)->delete();
                // Mensaje de notificacion
                // \Helper::messageFlash('Cuentas','Saldo agregado','alert_cuenta');
                // return redirect('cuentas/'.$account);
                $band = true;
              } catch (\Exception $e) {
                // return redirect('/cuentas')->withErrors('Intentelo nuevamente');
                $band = false;
              }
            
            } else {
              return redirect()->back()->withErrors(['No hay stock de la gift']);
            }
          }

        } elseif ($title == 'gift-card-45-usd-org') {

          $titles = ['gift-card-20-usd', 'gift-card-25-usd'];

          foreach ($titles as $title) {
            $stock_valido = \Helper::availableStock($account,$title,$console);

            if (is_array($stock_valido)) { 
              $stock_valido_id = $stock_valido[0]->ID_stk;
              $stock = Stock::stockDetail($stock_valido_id)->first();

              $date = date('Y-m-d H:i:s');
              $data = [
                'cuentas_id'=>$account,
                'ex_stock_id'=>$stock->ID,
                'titulo'=>$title,
                'consola'=>$console,
                'medio_pago'=>$stock->medio_pago,
                'costo_usd'=>$stock->costo_usd,
                'costo'=>$stock->costo,
                'code'=>$stock->code,
                'code_prov'=>$stock->code_prov,
                'n_order'=>$stock->n_order,
                'Day'=>$date,
                'ex_Day_stock'=>$stock->Day,
                'usuario'=>session()->get('usuario')->Nombre,
                'ex_usuario'=>$stock->usuario
              ];

              try {
                $this->blc->storeBalanceAccount($data);
                $nota = 'Agregado a cta <a href="'.url('cuentas',$account).'" class="alert-link">#'.$account.'</a> '.$stock->code;

                $data = [];
                $data['stock_id'] = $stock->ID;
                $data['Notas'] = $nota;
                $data['Day'] = date('Y-m-d H:i:s');
                $data['usuario'] = session()->get('usuario')->Nombre;
                DB::table('stock_notas')->insert($data);

                // Eliminando stock
                $stock = Stock::where('ID',$stock->ID)->delete();
                // Mensaje de notificacion
                // \Helper::messageFlash('Cuentas','Saldo agregado','alert_cuenta');
                // return redirect('cuentas/'.$account);
                $band = true;
              } catch (\Exception $e) {
                // return redirect('/cuentas')->withErrors('Intentelo nuevamente');
                $band = false;
              }
            
            } else {
              return redirect()->back()->withErrors(['No hay stock de la gift']);
            }
          }

        } elseif ($title == 'gift-card-35-usd-org') {

          $titles = ['gift-card-10-usd', 'gift-card-25-usd'];

          foreach ($titles as $title) {
            $stock_valido = \Helper::availableStock($account,$title,$console);

            if (is_array($stock_valido)) { 
              $stock_valido_id = $stock_valido[0]->ID_stk;
              $stock = Stock::stockDetail($stock_valido_id)->first();

              $date = date('Y-m-d H:i:s');
              $data = [
                'cuentas_id'=>$account,
                'ex_stock_id'=>$stock->ID,
                'titulo'=>$title,
                'consola'=>$console,
                'medio_pago'=>$stock->medio_pago,
                'costo_usd'=>$stock->costo_usd,
                'costo'=>$stock->costo,
                'code'=>$stock->code,
                'code_prov'=>$stock->code_prov,
                'n_order'=>$stock->n_order,
                'Day'=>$date,
                'ex_Day_stock'=>$stock->Day,
                'usuario'=>session()->get('usuario')->Nombre,
                'ex_usuario'=>$stock->usuario
              ];

              try {
                $this->blc->storeBalanceAccount($data);
                $nota = 'Agregado a cta <a href="'.url('cuentas',$account).'" class="alert-link">#'.$account.'</a> '.$stock->code;

                $data = [];
                $data['stock_id'] = $stock->ID;
                $data['Notas'] = $nota;
                $data['Day'] = date('Y-m-d H:i:s');
                $data['usuario'] = session()->get('usuario')->Nombre;
                DB::table('stock_notas')->insert($data);

                // Eliminando stock
                $stock = Stock::where('ID',$stock->ID)->delete();
                // Mensaje de notificacion
                // \Helper::messageFlash('Cuentas','Saldo agregado','alert_cuenta');
                // return redirect('cuentas/'.$account);
                $band = true;
              } catch (\Exception $e) {
                // return redirect('/cuentas')->withErrors('Intentelo nuevamente');
                $band = false;
              }
            
            } else {
              return redirect()->back()->withErrors(['No hay stock de la gift']);
            }
          }

        } else {
          $stock_valido = \Helper::availableStock($account,$title,$console);

          if (is_array($stock_valido)) { 
            $stock_valido_id = $stock_valido[0]->ID_stk;
            $stock = Stock::stockDetail($stock_valido_id)->first();

            $date = date('Y-m-d H:i:s');
            $data = [
              'cuentas_id'=>$account,
              'ex_stock_id'=>$stock->ID,
              'titulo'=>$title,
              'consola'=>$console,
              'medio_pago'=>$stock->medio_pago,
              'costo_usd'=>$stock->costo_usd,
              'costo'=>$stock->costo,
              'code'=>$stock->code,
              'code_prov'=>$stock->code_prov,
              'n_order'=>$stock->n_order,
              'Day'=>$date,
              'ex_Day_stock'=>$stock->Day,
              'usuario'=>session()->get('usuario')->Nombre,
              'ex_usuario'=>$stock->usuario
            ];

            try {
              $this->blc->storeBalanceAccount($data);
              $nota = 'Agregado a cta <a href="'.url('cuentas',$account).'" class="alert-link">#'.$account.'</a> '.$stock->code;

              $data = [];
              $data['stock_id'] = $stock->ID;
              $data['Notas'] = $nota;
              $data['Day'] = date('Y-m-d H:i:s');
              $data['usuario'] = session()->get('usuario')->Nombre;
              DB::table('stock_notas')->insert($data);

              // Eliminando stock
              $stock = Stock::where('ID',$stock->ID)->delete();
              // Mensaje de notificacion
              // \Helper::messageFlash('Cuentas','Saldo agregado','alert_cuenta');
              // return redirect('cuentas/'.$account);
              $band = true;
            } catch (\Exception $e) {
              // return redirect('/cuentas')->withErrors('Intentelo nuevamente');
              $band = false;
            }
          
          } else {
            return redirect()->back()->withErrors(['No hay stock de la gift']);
          }
        }

        if ($band) {

          if ($title == 'plus-12-meses') {
            $this->insertStockPlus12Meses($account);
            \Helper::messageFlash('Cuentas','Saldo y Stock Plus 12 Meses agregados.','alert_cuenta');
          } else {
            \Helper::messageFlash('Cuentas','Saldo agregado','alert_cuenta');
          }

          return redirect('cuentas/'.$account);

        } else {
          return redirect('/cuentas')->withErrors('Intentelo nuevamente');
        }

      }
    }

    public function storeBalanceMinimAccount($account_id, $stock_id) {
      if ($this->validarGift($stock_id)) {
        $stock = Stock::stockDetail($stock_id)->first();

        $date = date('Y-m-d H:i:s');
        $data = [
          'cuentas_id'=>$account_id,
          'ex_stock_id'=>$stock->ID,
          'titulo'=>$stock->titulo,
          'consola'=>$stock->consola,
          'medio_pago'=>$stock->medio_pago,
          'costo_usd'=>$stock->costo_usd,
          'costo'=>$stock->costo,
          'code'=>$stock->code,
          'code_prov'=>$stock->code_prov,
          'n_order'=>$stock->n_order,
          'Day'=>$date,
          'ex_Day_stock'=>$stock->Day,
          'usuario'=>session()->get('usuario')->Nombre,
          'ex_usuario'=>$stock->usuario
        ];

        DB::beginTransaction();

        try {
          $this->blc->storeBalanceAccount($data);
          Stock::where('ID',$stock->ID)->delete();

          $nota = 'Agregado a cta <a href="'.url('cuentas',$account_id).'" class="alert-link">#'.$account_id.'</a> '.$stock->code;

          $data = [];
          $data['stock_id'] = $stock->ID;
          $data['Notas'] = $nota;
          $data['Day'] = date('Y-m-d H:i:s');
          $data['usuario'] = session()->get('usuario')->Nombre;
          DB::table('stock_notas')->insert($data);

          DB::commit();

          \Helper::messageFlash('Cuentas','Saldo Minimo agregado correctamente.','alert_cuenta');

          return redirect()->back();
          
        } catch (Exception $e) {
          DB::rollback();
          return redirect()->back()->withErrors(['Ha ocurrido un error inesperado, por favor intentelo de nuevo.']);
        }
      } else {
        return redirect()->back()->withErrors(['No hay stock de la gift o ya el ID stock ha sido usado.']);
      }
    }


    public function createLastStock($id){
      $account = Account::where('ID',$id)->first();

      if (!$account)
        return redirect('/cuentas')->withErrors('Intentelo nuevamente');

      // Ultimos stocks
      $accountStocks = $this->tks->lastStockUser(session()->get('usuario')->Nombre);

      // Saldo real de la cuenta
      $balance = \Helper::getBalanceAccount($id);

      return view('ajax.account.last_stock',compact(
                  'account',
                  'accountStocks',
                  'balance'
              ));
    }

    public function storeLastStock(Request $request,$id){
      // Mensajes de alerta
      $msgs = [
        'titulo.required' => 'Titulo requerido',
        'consola.required' => 'Consola requerida',
        'costo_usd.required' => 'Costo requerido'
      ];
      // Validamos
      $v = Validator::make($request->all(), [
          'titulo' => 'required',
          'consola' => 'required',
          'costo_usd' => 'required'
      ], $msgs);


      // Si hay errores retornamos a la pantalla anterior con los mensajes
      if ($v->fails())
      {
          return redirect()->back()->withErrors($v->errors());
      }

      $balance = $this->blc->balanceAccount($id);
      $expenses = Stock::stockExpensesByAccountId($id)->first();
      // si la cuenta no tiene gastos validamos los datos a cero

      if(count($balance)>0){
        $balance = $balance[0];
      }else{
        return redirect('/cuentas')->withErrors('Intentelo nuevamente');
      }

      /*** Si hay un stock -> costo usd y costo ars = lo que queda en la cuenta.
      Else: Si no hay stock costo usd = el ultimo costo usd de ese mismo juego, y costo ars = a proporcional de usd */
      $costo_usd = $request->costo_usd;
      if (!empty($expenses)){
        $costo_usd = $balance->costo_usd;
        $costo = $balance->costo;
      }else{
        $costo = ($costo_usd / $balance->costo_usd) * $balance->costo;
      }


      try {
        $data = [];
        $data['titulo'] = $request->titulo;
        $data['consola'] = $request->consola;
        $data['cuentas_id'] = $id;
        $data['costo_usd'] = $costo_usd;
        $data['costo_usd_modif'] = $costo_usd;
        $data['medio_pago'] = 'Saldo';
        $data['costo'] = $costo;
        $data['Day'] = $this->dte;
        $data['usuario'] = session()->get('usuario')->Nombre;

        $this->tks->storeStockAccount($data);
        \Helper::messageFlash('Cuentas','Stock masivo agregado','alert_cuenta');
        return redirect('cuentas/'.$id);

      } catch (\Exception $e) {
        return redirect('/cuentas')->withErrors('Intentelo nuevamente');
      }

    }


    public function resetAccount($id, $recup = null){
      $account = Account::where('ID',$id)->first();

      if (!$account)
        return redirect('/cuentas')->withErrors('Intentelo nuevamente');

      $date = date('Y-m-d H:i:s', time());
      try {
        if($recup != 'secu' && $recup != 'secu_domexclu') {
          $data = [];
          $data['cuentas_id']=$id;
          $data['Day']=$date;
          if ($recup == "secu_reset") {
            $data['usuario']= "rs". session()->get('usuario')->Nombre;
          } elseif (($recup == "reset_pass" || $recup == null) && \Helper::operatorsRecoverPri(session()->get('usuario')->Nombre)) {
            $data['usuario']= "rn". session()->get('usuario')->Nombre;
          } else {
            $data['usuario']= session()->get('usuario')->Nombre;
          }
          $data['usuario_real'] = session()->get('usuario')->nombre_visible;
          $this->rst->storeResetAccount($data);

          $mensaje = 'Cuenta reseteada';
        }

        if ($recup == 'pri') {
          $mensaje = 'Intento recuperar pri satisfactoriamente.';
          $this->intentoRecuperarPri($id);
          $this->updatePassRecu($id, "pri");
        } elseif ($recup == 'secu' || $recup == 'secu_reset' || $recup == 'secu_domexclu') {
          $mensaje = 'Intento recuperar secu satisfactoriamente.';
          $this->intentoRecuperarSecu($id);
          if ($recup == 'secu' || $recup == 'secu_reset') {
            $this->updatePassRecu($id, "secu");
          } else {
            $this->changeEmailFakeDomain($id);
          }
          
        } elseif ($recup == 'conj') {
          $mensaje = 'Intento recuperar pri y secu satisfactoriamente.';
          $this->intentoRecuperarPri($id);
          $this->intentoRecuperarSecu($id);
          $this->updatePassRecu($id);
        } elseif ($recup == "reset_pass") {
          $this->updatePassRecu($id, "reset_pass");
        }

        \Helper::messageFlash('Cuentas',$mensaje,'alert_cuenta');
        return redirect('cuentas/'.$id);

      } catch (\Exception $e) {
        return redirect('cuentas/'.$id)->withErrors('Intentelo nuevamente');
      }


    }

    private function changeEmailFakeDomain($account_id)
    {
      $email_fake = DB::table('cuentas')->where('ID',$account_id)->value('mail_fake');
      $dominio_hab = DB::table('dominios')->where('indicador_habilitado',1)->value('dominio');
      list($user_mail, $dominio) = explode("@",$email_fake);
      $new_email_fake = $user_mail . "@" . $dominio_hab;

      DB::beginTransaction();

      try {
        DB::table('cuentas')->where('ID',$account_id)->update(['mail_fake' => $new_email_fake]);

        $nota = "Email Fake ha cambiado de $email_fake a $new_email_fake";

        $data['cuentas_id'] = $account_id;
        $data['Notas'] = $nota;
        $data['Day'] = date('Y-m-d H:i:s');
        $data['usuario'] = session()->get('usuario')->Nombre;

        DB::table('cuentas_notas')->insert($data);

        DB::commit();
      } catch (\Exception $th) {
        DB::rollback();
      }
    }

    public function rollbackBalance(Request $request){
      // Mensajes de alerta
      $msgs = [
        'id.required' => 'Intentelo nuevamente',
        'c_id.required' => 'Faltan datos'
      ];
      // Validamos
      $v = Validator::make($request->all(), [
          'id' => 'required',
          'c_id' => 'required'
      ], $msgs);


      // Si hay errores retornamos a la pantalla anterior con los mensajes
      if ($v->fails())
      {
          return redirect()->back()->withErrors($v->errors());
      }
      $date = date('Y-m-d H:i:s');
      DB::beginTransaction();
      try {

        // datos del saldo
        $balance = Balance::balanceByExEstockId($request->id)->first();

        // datos a guardar en stock
        $data = [];
        $data['ID'] = $balance->ex_stock_id;
        $data['titulo'] = $balance->titulo;
        $data['consola'] = $balance->consola;
        $data['cuentas_id'] = NULL;
        $data['medio_pago'] = $balance->medio_pago;;
        $data['costo_usd'] = $balance->costo_usd;
        $data['costo_usd_modif'] = $balance->costo_usd;
        $data['costo'] = $balance->costo;
        $data['code'] = $balance->code;
        $data['code_prov'] = $balance->code_prov;
        $data['n_order'] = $balance->n_order;
        $data['Day'] = $balance->ex_Day_stock;
        $data['usuario'] = $balance->ex_usuario;
        $saving = $this->tks->storeCodes($data);
        $nota = "Devuelto de cta <a class=\"alert-link\" href=\"".url('cuentas',$request->c_id)."\">#$request->c_id</a> {$balance->code}";
        $this->tks->storeNotesStock($request->id,$nota);

        // Eliminando saldo actual
        DB::table('saldo')->where('ex_stock_id',$request->id)->delete();
        DB::commit();
        // Mensaje de notificacion
        \Helper::messageFlash('Cuentas','Saldo retornado','alert_cuenta');
        return redirect('cuentas/'.$request->c_id);
      } catch (\Exception $e) {
        DB::rollback();
        return redirect('cuentas/'.$request->c_id)->withErrors('Ocurrio un error al intentar generar la devolucion de saldo.');
      }

    }

    public function edit(Request $request, $id){
      $obj = new \stdClass;
      $obj->column = 'ID';
      $obj->word = $id;

      $account = Account::accountByColumnWord($obj)->first();

      return view('account.edit',compact('account'));
    }


    public function update(Request $request,$id){
      // dd($request->all());
      // Mensajes de alerta
      $msgs = [
        'mail.required' => 'Email requerido',
        'mail_fake.required' => 'Email falso requerido',
        'mail_fake.email' => 'Ingrese Email falso valido',
        'mail.unique' => 'Emal ya existe',
        'mail_fake.unique' => 'Email falso ya existe',
        'surname.required' => 'Apellido requerido',
        'name.required' => 'Nombre requerido',
        'pass.required' => 'Password requerido'
      ];
      // Validamos
      $v = Validator::make($request->all(), [
          'mail' => 'required|unique:cuentas,mail,'.$id,
          'surname' => 'required',
          'name' => 'required',
          'mail_fake' => 'required|email|unique:cuentas,mail_fake,'.$id,
          'pass' => 'required'
      ], $msgs);


      // Si hay errores retornamos a la pantalla anterior con los mensajes
      if ($v->fails())
      {
          return redirect()->back()->withErrors($v->errors());
      }


      try {
        $notas = $this->buildNoteBeforeUpdate($request, $id);
        
        $account = [];
        $account['mail_fake'] = $request->mail_fake;
        $account['mail'] = $request->mail;
        $account['name'] = $request->name;
        $account['surname'] = $request->surname;
        $account['pass'] = $request->pass;
        $this->acc->updateAccount($account,$id);


        $date = date('Y-m-d H:i:s', time());
        $account['cuentas_id'] = $id;
        $account['Day'] = $date;
        $account['usuario'] = session()->get('usuario')->Nombre;
        $account['verificado'] = (\Helper::validateAdministrator(session()->get('usuario')->Level)) ? 'si' : 'no';
        $this->acc->createAccountMod($account);

        $note['cuentas_id'] = $id;
        $note['Notas'] = $notas;
        $note['Day'] = date('Y-m-d H:i:s');
        $note['usuario'] = session()->get('usuario')->Nombre;
        DB::table('cuentas_notas')->insert($note);

        // Mensaje de notificacion
        \Helper::messageFlash('Cuentas','Cuenta editada','alert_cuenta');
        return redirect('cuentas/'.$id);
      } catch (\Exception $e) {
        return redirect('cuentas/'.$id)->withErrors('Intentelo nuevamente');
      }
    }

    private function buildNoteBeforeUpdate($values,$id_account)
    {
      $notas = [];
      $account = DB::table('cuentas')->where('ID',$id_account)->first();
      
      if ($values->mail != $account->mail) {
        $notas[] = "Email ha cambiado de {$account->mail} a {$values->mail}";
      }
      if ($values->mail_fake != $account->mail_fake) {
        $notas[] = "Email Fake ha cambiado de {$account->mail_fake} a {$values->mail_fake}";
      }
      if ($values->name != $account->name) {
        $notas[] = "Nombre ha cambiado de {$account->name} a {$values->name}";
      }
      if ($values->surname != $account->surname) {
        $notas[] = "Apellido ha cambiado de {$account->surname} a {$values->surname}";
      }
      if ($values->pass != $account->pass) {
        $notas[] = "Contraseña ha cambiado de {$account->pass} a {$values->pass}";
      }

      return implode(" | ", $notas);
    }


    public function editAddressAccount(Request $request,$id){
      $obj = new \stdClass;
      $obj->column = 'ID';
      $obj->word = $id;

      $account = Account::accountByColumnWord($obj)->first();

      return view('account.edit_address',compact('account'));
    }


    public function updateAddressAccount(Request $request,$id){
      // Mensajes de alerta
      $msgs = [
        'country.required' => 'Ingrese el país',
        'state.required' => 'Ingrese el estado',
        'city.required' => 'Ingrese la ciudad',
        'pc.required' => 'PC es requerido',
        'address.required' => 'Dirección es requerida'
      ];
      // Validamos
      $v = Validator::make($request->all(), [
          'country' => 'required',
          'state' => 'required',
          'city' => 'required',
          'pc' => 'required',
          'address' => 'required'
      ], $msgs);


      // Si hay errores retornamos a la pantalla anterior con los mensajes
      if ($v->fails())
      {
          return redirect()->back()->withErrors($v->errors());
      }


      try {
        $account = [];
        $account['country'] = $request->country;
        $account['state'] = $request->state;
        $account['city'] = $request->city;
        $account['pc'] = $request->pc;
        $account['address'] = $request->address;
        $this->acc->updateAccount($account,$id);

        $account['mail_fake'] = $request->mail_fake;
        $account['mail'] = $request->mail;
        $account['name'] = $request->name;
        $account['surname'] = $request->surname;
        $account['Notas'] = $request->Notas;
        $account['pass'] = $request->pass;

        $account['address'] = $request->address;

        $date = date('Y-m-d H:i:s', time());
        $account['cuentas_id'] = $id;
        $account['Day'] = $date;
        $account['usuario'] = session()->get('usuario')->Nombre;
        $account['verificado'] = (\Helper::validateAdministrator(session()->get('usuario')->Level)) ? 'si' : 'no';
        $this->acc->createAccountMod($account);


        // Mensaje de notificacion
        \Helper::messageFlash('Cuentas','Dirección editada','alert_cuenta');
        return redirect('cuentas/'.$id);
      } catch (\Exception $e) {
        return redirect('cuentas/'.$id)->withErrors('Intentelo nuevamente');
      }
    }


    public function updatePassword($id, $param = null, Request $request){
      try {
        $npass = \Helper::getRandomPass();
        $date = date('Y-m-d H:i:s', time());

        $data = [];
        $data['cuentas_id'] = $id;
        $data['new_pass'] = $npass;
        $data['Day'] = $date;
        $data['usuario'] = "cp".session()->get('usuario')->Nombre;
        $this->acc->createAccPass($data,$id);


        $account = [];
        $account['pass'] = $npass;
        $this->acc->updateAccount($account,$id);

        if ($request->sony_solicita && $request->sony_solicita === 'yes') {
            DB::table('cuentas_notas')->insert([
                "cuentas_id" => $id,
                "Notas" => "Sony solicitó cambiar pass",
                "Day" => date('Y-m-d H:i:s'),
                "usuario" => session()->get('usuario')->Nombre
            ]);
        }

        if ($param != null) {
          $stocks = DB::table('stock')->select(DB::raw('GROUP_CONCAT(ID) AS stocks_ids'))->where('cuentas_id',$id)->groupBy('cuentas_id')->value('stocks_ids');
          $stocks = explode(",", $stocks);

          $venta = DB::table('ventas')->select('ID')->whereIn('stock_id',$stocks)->where('slot','Secundario')->value('ID');

          if ($venta) {
            $data = [];
            $data['id_ventas'] = $venta;
            $data['Notas'] = "Intento recuperar secu";
            $data['Day'] = date('Y-m-d H:i:s');
            $data['usuario'] = session()->get('usuario')->Nombre;

            DB::table('ventas_notas')->insert($data);
          }
        }
        // Mensaje de notificacion
        \Helper::messageFlash('Cuentas','Password editado','alert_cuenta');
        return redirect('cuentas/'.$id);
      } catch (\Exception $e) {
        return redirect('cuentas/'.$id)->withErrors('Intentelo nuevamente');
      }

    }

    public function createNote($id){
      $account = $id;

      $clientes_sales = $this->acc->getClientsSales($id)->get();

      return view('ajax.account.create_note',compact('account','clientes_sales'));
    }

    // Guardar nota
    public function storeNote(Request $request,$id){
      $msgs = [
        'notes.required' => 'Nota requerida',
      ];
      // Validamos
      $v = Validator::make($request->all(), [
          'notes' => 'required'
      ], $msgs);
      // Si hay errores retornamos a la pantalla anterior con los mensajes
      if ($v->fails())
      {
          return redirect()->back()->withErrors($v->errors());
      }
      $date = date('Y-m-d H:i:s', time());
      $obj = new \stdClass;
      $obj->column = 'ID';
      $obj->word = $id;
      $account = Account::accountByColumnWord($obj)->first();
      if ($account) {
        $nota = $request->notes;
        if ($request->cliente != "") {
         $cliente = DB::table('clientes')->where('ID',$request->cliente)->first();
         $nota = "<a class=\"alert-link\" href=\"".url('clientes',$cliente->ID)."\">#".$cliente->ID." ".$cliente->nombre." ".$cliente->apellido."</a>: " . $nota;
        }

        DB::beginTransaction();
        try {
          $data = [
                    'cuentas_id' => $id,
                    'Notas'=> $nota,
                    'Day'=>$date,
                    'usuario'=>session()->get('usuario')->Nombre
                  ];

          $this->ac->storeNote($data);

          if ($request->cliente != "") {
              $nota = $request->notes;
              $nota = "<a class=\"alert-link\" href=\"".url('cuentas',$id)."\">Cuenta #".$id."</a>: " . $nota;
              $data = [];
              $data['clientes_id'] = $cliente->ID;
              $data['Notas'] = $nota;
              $data['Day'] = $date;
              $data['usuario'] = session()->get('usuario')->Nombre;

              DB::table('clientes_notas')->insert($data);
          }

          DB::commit();
          // Mensaje de notificacion
          \Helper::messageFlash('Cuentas','Nota Creada','alert_cuenta');
          return redirect('cuentas/'.$id);
        } catch (\Exception $e) {
          DB::rollback();
          return redirect('/cuentas')->withErrors('Intentelo nuevamente');
        }
      }

      return redirect('/cuentas')->withErrors('Intentelo nuevamente');
    }


    // verifica si existe el dato en la columna
    public function accountCtrlColumn(Request $request){
      // Creando objeto para Buscar
      $obj = new \stdClass;
      $obj->column = $request->column;
      $obj->word = $request->word;
      $account = Account::accountByColumnWord($obj)->first();

      if (count($account) > 0) {
        echo true;
      }else{
        echo false;
      }
    }

    public function modifyDateOperations($id, $tipo, $account_id)
    {
      $id = $id;
      $tipo = $tipo;
      $day = '';

      switch ($tipo) {
        case 'contra':
          $day = DB::table('cta_pass')->where('ID',$id)->value('Day');
          break;
        
        case 'reset':
          $day = DB::table('reseteo')->where('ID',$id)->value('Day');
          break;
        case 'resetear':
          $day = DB::table('resetear')->where('ID',$id)->value('Day');
          break;
        case 'notas':
          $day = DB::table('cuentas_notas')->where('ID',$id)->value('Day');
          break;
        case 'venta':
          $day = DB::table('ventas')->where('ID',$id)->value('Day_modif');
          break;
      }

      $day = date('Y-m-d', strtotime($day));

      return view('ajax.account.modificar_fecha_operaciones',compact('id','tipo', 'day', 'account_id'));
    }

    public function modifyDateOperationsStore(Request $request)
    {
      DB::beginTransaction();
      $nota = '';
      try {
        switch ($request->tipo) {
          case 'contra':
            $nota = "Se modificó <b>cambio de pass</b> #{$request->id} de {$request->current_day} a {$request->Day}";
            DB::table('cta_pass')->where('ID',$request->id)->update(['Day' => $request->Day]);
            break;
          
          case 'reset':
            $nota = "Se modificó <b>reseteo</b> #{$request->id} de {$request->current_day} a {$request->Day}";
            DB::table('reseteo')->where('ID',$request->id)->update(['Day' => $request->Day]);
            break;
          case 'resetear':
            $nota = "Se modificó <b>solicitud de reseteo</b> #{$request->id} de {$request->current_day} a {$request->Day}";
            DB::table('resetear')->where('ID',$request->id)->update(['Day' => $request->Day]);
            break;
          case 'notas':
            $nota = "Se modificó <b>nota de la cuenta</b> #{$request->id} de {$request->current_day} a {$request->Day}";
            DB::table('cuentas_notas')->where('ID',$request->id)->update(['Day' => $request->Day]);
            break;
          case 'venta':
            $nota = "Se modificó <b>ventas</b> #{$request->id} de {$request->current_day} a {$request->Day}";
            DB::table('ventas')->where('ID',$request->id)->update(['Day_modif' => $request->Day]);
            break;
        }

        DB::table('cuentas_notas')->insert([
            "cuentas_id" => $request->account_id,
            "Notas" => $nota,
            "Day" => date('Y-m-d H:i:s'),
            "usuario" => session()->get('usuario')->Nombre
        ]);

        DB::commit();

        \Helper::messageFlash('Cuentas','Fecha de la operación modificada','alert_cuenta');
        return redirect()->back();
      } catch (Exception $e) {
        DB::rollback();
        return redirect()->back()->withErrors(['Intentelo nuevamente']);
      }
    }

    public function deleteOperation($id, $tipo)
    {
      DB::beginTransaction();

      try {
        switch ($tipo) {
          case 'contra':
            DB::table('cta_pass')->where('ID',$id)->delete();
            break;
          
          case 'reset':
            DB::table('reseteo')->where('ID',$id)->delete();
            break;
          case 'resetear':
            DB::table('resetear')->where('ID',$id)->delete();
            break;
          case 'notas':
            DB::table('cuentas_notas')->where('ID',$id)->delete();
            break;
          case 'ventas':
            DB::table('ventas')->where('ID',$id)->delete();
            break;
        }
        DB::commit();

        \Helper::messageFlash('Cuentas','Registro de la operación eliminada','alert_cuenta');
        return redirect()->back();
      } catch (Exception $e) {
        DB::rollback();
        return redirect()->back()->withErrors(['Intentelo nuevamente']);
      }
    }

    public function indexReseteados(Request $request)
    {
      $fecha_fin = isset($request->fecha_fin) ? $request->fecha_fin : date('Y-m-d');
      $fecha_ini = isset($request->fecha_ini) ? $request->fecha_ini : $this->defaultFechaIni();
      $usuario = isset($request->usuario) ? $request->usuario : '';

      $reseteados = Reset::getDatosReseteados($fecha_ini, $fecha_fin, $usuario)->paginate(50);
      $data = Reset::getDatosReseteados($fecha_ini, $fecha_fin, $usuario)->get();
      $total_reseteados = 0;
      foreach ($data as $item) $total_reseteados += $item->Q;
      $usuarios = Reset::getUsersReset()->get();

      return view('account.index_reseteados', compact('reseteados','fecha_fin','fecha_ini','usuarios','total_reseteados'));
    }

    private function defaultFechaIni()
    {
      $hoy = strtotime(date('Y-m-d'));
      $fecha_ini = strtotime('-7 days', $hoy);
      $fecha_ini = date('Y-m-d', $fecha_ini);

      return $fecha_ini;
    }

    public function repetirGiftAndJuego($account_id)
    {
      $giftsCharged = $this->getLastGiftsCharged();

      $guardar = true;

      if (count($giftsCharged) > 0) {
        
        foreach ($giftsCharged as $gift) {
          # PROCESO PARA GUARDADO DE LAS ULTIMAS GIFT CARGADAS POR ESTE USUARIO A UNA MISMA CUENTA_ID.
          $stock_valido = \Helper::availableStock($account_id,$gift,'');

          if (is_array($stock_valido)) { 
            $stock_valido_id = $stock_valido[0]->ID_stk;
            $stock = Stock::stockDetail($stock_valido_id)->first();

            $date = date('Y-m-d H:i:s');
            $data = [
              'cuentas_id'=>$account_id,
              'ex_stock_id'=>$stock->ID,
              'titulo'=>$gift,
              'consola'=>$stock->consola,
              'medio_pago'=>$stock->medio_pago,
              'costo_usd'=>$stock->costo_usd,
              'costo'=>$stock->costo,
              'code'=>$stock->code,
              'code_prov'=>$stock->code_prov,
              'n_order'=>$stock->n_order,
              'Day'=>$date,
              'ex_Day_stock'=>$stock->Day,
              'usuario'=>session()->get('usuario')->Nombre,
              'ex_usuario'=>$stock->usuario
            ];

            try {
              $this->blc->storeBalanceAccount($data);

              // Eliminando stock
              $stock = Stock::where('ID',$stock->ID)->delete();
              // Mensaje de notificacion
              // \Helper::messageFlash('Cuentas','Saldo agregado','alert_cuenta');
              // return redirect('cuentas/'.$account);
              $band = true;
            } catch (\Exception $e) {
              // return redirect('/cuentas')->withErrors('Intentelo nuevamente');
              $band = false;
            }

          } else {
            return redirect()->back()->withErrors(['No hay gift "'.$gift.'" en stock para repetir la ultima(s) gift cargada con este usuario.']);
          }
        }

        #---------

        $lastAccountGames = $this->tks->lastAccountUserGames(session()->get('usuario')->Nombre); // Ultimo juego cargado por este usuario.

        if (count($lastAccountGames) > 0) {
          $ultimo_juego = $lastAccountGames[0]->ID;
          $lastGames = $this->tks->lastAccountByIdAndUser(session()->get('usuario')->Nombre,$ultimo_juego);
          if (!$lastGames)
            return redirect()->back()->withErrors(['Intentelo nuevamente. Ha ocurrido un error inesperado.']);

          $data = [];

          $saldos = $this->getSaldosCuenta($account_id);

          foreach ($lastGames as $key => $game) {
            $costo = ($game->costo_usd / $saldos['saldo']) * $saldos['saldoARS'];

            // Arreglo que se guarda en $data para guardar multiples juegos de una sola vez
            $data[$key] = [
              'cuentas_id' => $account_id,
              'consola' => $game->consola,
              'titulo' => $game->titulo,
              'medio_pago' => 'Saldo',
              'costo_usd' => $game->costo_usd,
              'costo_usd_modif' => $game->costo_usd,
              'costo' => $costo,
              'Day' => $this->dte,
              'usuario' => session()->get('usuario')->Nombre,
            ];

            if ($costo == 0) {
              $guardar = false;
              break;
            }
          }

          try {
            // mandamos a guardar el arreglo de juegos

            if ($guardar) { // Validación que solo guarde cuando no haya ningun costo 0.
              $this->tks->storeCodes($data);

              // Mensaje de notificacion
              \Helper::messageFlash('Cuentas','Gift y Juego Cargado','alert_cuenta');


              return redirect('cuentas/'.$account_id);
            } else {
              return redirect()->back()->withErrors(['Oops! Se ha detectado que hubo un calculo del stock con costo cero (0). Por favor revisar.']);
            }


          } catch (\Exception $e) {
            dd($e->getMessage());
            return redirect()->back()->withErrors(['Intentelo nuevamente. Ocurrió un error en el proceso.']);

          }
        } else {
          return redirect()->back()->withErrors(['No hay stock para repetir el ultimo juego cargado con este usuario.']);
        }

      } else {
        return redirect()->back()->withErrors(['No se ha encontrado las ultimas gift cargadas con este vendedor.']);
      }
    }

    private function getLastGiftsCharged()
    {
      $vendedor = session()->get('usuario')->Nombre;

      $lastAccountGames = $this->tks->lastAccountUserGames(session()->get('usuario')->Nombre);
      $giftsCargadas = [];

      if ($lastAccountGames) {
        $id_cuenta = $lastAccountGames[0]->cuentas_id;
        $last_date_stock = date('Y-m-d', strtotime($lastAccountGames[0]->Day));
        $lastGiftsCharged = Balance::lastGiftsCharged($vendedor, $id_cuenta, $last_date_stock)->get();

        // dd($lastGiftsCharged);

        if (count($lastGiftsCharged) > 0) {
          
          foreach ($lastGiftsCharged as $value) {
            $giftsCargadas[] = $value->titulo;
          }
        } 
      }

      return $giftsCargadas; 
    }

    private function getSaldosCuenta($account_id)
    {
      $saldo = 0.00;
      $saldoARS = 0.00;

      $accountBalances = Balance::accountBalance($account_id);

      foreach ($accountBalances as $balance) {
        $saldo = $saldo + $balance->costo_usd;
        $saldoARS = $saldoARS + $balance->costo;
      }

      $saldos = [
        'saldo' => $saldo,
        'saldoARS' => $saldoARS
      ];

      return $saldos;
    }

    public function listaYopmail()
    {
      $datos = Account::listaYopmail()->paginate(100);

      return view('account.lista_yopmail', compact('datos'));
    }

    public function changeEmailDixgamer($account_id)
    {
      $vendedor = strtolower(session()->get('usuario')->Nombre);
      $emailcuenta1 = substr($vendedor, 0, 2);
      $emailcuenta2 = $account_id;
      $emailcuenta = "y".$emailcuenta1 . "." . $emailcuenta2 . "@game24hs.com";

      $cta = DB::table('cuentas')->where('ID',$account_id)->first();

      DB::beginTransaction();

      try {
        DB::table('cuentas')->where('ID',$account_id)->update(["mail_fake" => $emailcuenta]);

        $nota = "E-mail actualizado, antes $cta->mail_fake";
        
        $data = [];
        $data['cuentas_id'] = $account_id;
        $data['Notas'] = $nota;
        $data['Day'] = date('Y-m-d H:i:s');
        $data['usuario'] = session()->get('usuario')->Nombre;

        DB::table('cuentas_notas')->insert($data);

        $pass_actual = $cta->pass;
        $vendedor = session()->get('usuario')->Nombre;

        $data = [];
        $data['cuentas_id'] = $account_id;
        $data['new_pass'] = $pass_actual;
        $data['Day'] = date('Y-m-d: H:i:s');
        $data['usuario'] = $vendedor;

        DB::table('cta_pass')->insert($data);

        $stocks = DB::table('stock')->select(DB::raw('GROUP_CONCAT(ID) AS stocks_ids'))->where('cuentas_id',$account_id)->groupBy('cuentas_id')->value('stocks_ids');
        $stocks = explode(",", $stocks);

        $venta = DB::table('ventas')->select('ID')->whereIn('stock_id',$stocks)->where('slot','Secundario')->value('ID');

        if ($venta) {
          $data = [];
          $data['id_ventas'] = $venta;
          $data['Notas'] = "Intento recuperar secu";
          $data['Day'] = date('Y-m-d H:i:s');
          $data['usuario'] = session()->get('usuario')->Nombre;

          DB::table('ventas_notas')->insert($data);
        }

        DB::commit();


        \Helper::messageFlash('Cuentas',"E-mail actualizado correctamente.",'alert_cuenta');


        return redirect()->back();
      } catch (Exception $e) {
        DB::rollback();
        return redirect()->back()->withErrors(['Ha ocurrido un error inesperado. Intentalo nuevamente.']);
      }

      
    }

    public function sigueJugando($account_id)
    {
      $stocks = DB::table('stock')->select(DB::raw('GROUP_CONCAT(ID) AS stocks_ids'))->where('cuentas_id',$account_id)->groupBy('cuentas_id')->value('stocks_ids');
      $stocks = explode(",", $stocks);

      $venta = DB::table('ventas')->select('ventas.*','c.nombre','c.apellido')->whereIn('stock_id',$stocks)->where('slot','Secundario')
      ->join('clientes AS c','c.ID','=','ventas.clientes_id')
      ->first();

      $vendedor = session()->get('usuario')->Nombre;

      DB::beginTransaction();

      try {
        if ($venta) {
          $data = [];
          $data['cuentas_id'] = $account_id;
          $data['Notas'] = "Cliente secundario #$venta->clientes_id $venta->nombre $venta->apellido sigue jugando";
          $data['Day'] = date('Y-m-d H:i:s');
          $data['usuario'] = session()->get('usuario')->Nombre;

          DB::table('cuentas_notas')->insert($data);

          DB::table('ventas')->where('ID',$venta->ID)->update(['recup' => 1,'Day_modif' => date('Y-m-d H:i:s')]);

          $data = [];
          $data['id_ventas'] = $venta->ID;
          $data['Notas'] = "Cliente sigue jugando";
          $data['Day'] = date('Y-m-d H:i:s');
          $data['usuario'] = session()->get('usuario')->Nombre;

          DB::table('ventas_notas')->insert($data);

          $operators_especials = \Helper::getOperatorsEspecials('Secu');

          ## OBTENER EL ULTIMO REGISTRO DE CAMBIO DE CONTRASEÑA POR UN OPERADOR ESPECIAL
          $cta_pass = DB::table('cta_pass')->where('cuentas_id', $account_id)->whereIn('usuario', $operators_especials)->orderBy('ID','DESC')->get();

          if ($cta_pass) { // Si existe el registro

            foreach ($cta_pass as $cta) {
              DB::table('cta_pass')->where('ID',$cta->ID)->update(['usuario' => "ex-$cta->usuario"]);
            }
          }

          DB::commit();

          \Helper::messageFlash('Cuentas',"Nota generada de sigue jugando.",'alert_cuenta');


          return redirect()->back();
        } else {
          return redirect()->back()->withErrors(['Ha ocurrido un error al completar toda la información correspondiente para generar la nota.']);
        }
      } catch (Exception $e) {
        DB::rollback();
        return redirect()->back()->withErrors(['Ha ocurrido un error inesperado. Intentalo nuevamente.']);
      }

      
    }

    public function sigueJugandoPri($account_id)
    {
      $stocks = DB::table('stock')->select(DB::raw('GROUP_CONCAT(ID) AS stocks_ids'))->where('cuentas_id',$account_id)->groupBy('cuentas_id')->value('stocks_ids');
      $stocks = explode(",", $stocks);

      $venta = DB::table('ventas')->select('ventas.*','c.nombre','c.apellido')->whereIn('stock_id',$stocks)->where('slot','Primario')->where('cons','ps4')
      ->join('clientes AS c','c.ID','=','ventas.clientes_id')
      ->first();

      $vendedor = session()->get('usuario')->Nombre;

      DB::beginTransaction();

      try {
        if ($venta) {
          $data = [];
          $data['cuentas_id'] = $account_id;
          $data['Notas'] = "Cliente Primario #$venta->clientes_id $venta->nombre $venta->apellido sigue jugando";
          $data['Day'] = date('Y-m-d H:i:s');
          $data['usuario'] = session()->get('usuario')->Nombre;

          DB::table('cuentas_notas')->insert($data);

          DB::table('ventas')->where('ID',$venta->ID)->update(['recup' => 1,'Day_modif' => date('Y-m-d H:i:s')]);

          $data = [];
          $data['id_ventas'] = $venta->ID;
          $data['Notas'] = "Cliente sigue jugando";
          $data['Day'] = date('Y-m-d H:i:s');
          $data['usuario'] = session()->get('usuario')->Nombre;

          DB::table('ventas_notas')->insert($data);

          $operators_especials = \Helper::getOperatorsEspecials('Pri');

          ## OBTENER EL ULTIMO REGISTRO DE RESETEO POR UN OPERADOR ESPECIAL
          $cta_reset = DB::table('reseteo')->where('cuentas_id', $account_id)->whereIn('usuario', $operators_especials)->orderBy('ID','DESC')->get();

          ## OBTENER EL ULTIMO REGISTRO DE CAMBIO DE CONTRASEÑA POR UN OPERADOR ESPECIAL
          //$cta_pass = DB::table('cta_pass')->where('cuentas_id', $account_id)->whereIn('usuario', $operators_especials)->orderBy('ID','DESC')->get();

          if ($cta_reset) { // Si existreseteoe el registro

            foreach ($cta_reset as $cta) {
              DB::table('reseteo')->where('ID',$cta->ID)->update(['usuario' => "ex-$cta->usuario"]);
            }
          }

          ## SE COMENTA EL DÍA 29/02/2020 POR KENDRY. RAZÓN --> SOLO DEBE ACTUALIZAR EL/LOS REGISTROS DE RESETEO PARA QUE FUNCIONE EL BOTON RECUP CONJ

          /*if ($cta_pass) { // Si existe el registro

            foreach ($cta_pass as $cta) {
              DB::table('cta_pass')->where('ID',$cta->ID)->update(['usuario' => "ex-$cta->usuario"]);
            }
          }*/

          DB::commit();

          \Helper::messageFlash('Cuentas',"Nota generada de sigue jugando pri.",'alert_cuenta');


          return redirect()->back();
        } else {
          return redirect()->back()->withErrors(['Ha ocurrido un error al completar toda la información correspondiente para generar la nota.']);
        }
      } catch (Exception $e) {
        DB::rollback();
        return redirect()->back()->withErrors(['Ha ocurrido un error inesperado. Intentalo nuevamente.']);
      }

      
    }

    public function intentoRecuperar($account_id)
    {
      $stocks = DB::table('stock')->select(DB::raw('GROUP_CONCAT(ID) AS stocks_ids'))->where('cuentas_id',$account_id)->groupBy('cuentas_id')->value('stocks_ids');
      $stocks = explode(",", $stocks);

      $venta = DB::table('ventas')->select('ventas.*','c.nombre','c.apellido')->whereIn('stock_id',$stocks)->where('slot','Secundario')
      ->join('clientes AS c','c.ID','=','ventas.clientes_id')
      ->first();

      $vendedor = session()->get('usuario')->Nombre;

      $account_pass_act = DB::table('cuentas')->where('ID',$account_id)->value('pass');

      DB::beginTransaction();

      try {
        if ($venta) {
          $data = [];
          $data['cuentas_id'] = $account_id;
          $data['new_pass'] = $account_pass_act;
          $data['Day'] = date('Y-m-d H:i:s');
          $data['usuario'] = session()->get('usuario')->Nombre;

          DB::table('cta_pass')->insert($data);

          $data = [];
          $data['id_ventas'] = $venta->ID;
          $data['Notas'] = "Intento recuperar secu";
          $data['Day'] = date('Y-m-d H:i:s');
          $data['usuario'] = session()->get('usuario')->Nombre;

          DB::table('ventas_notas')->insert($data);

          DB::commit();

          \Helper::messageFlash('Cuentas',"Nota generada de intento recuperar.",'alert_cuenta');


          return redirect()->back();
        } else {
          return redirect()->back()->withErrors(['Ha ocurrido un error al completar toda la información correspondiente para generar la nota.']);
        }
      } catch (Exception $e) {
        DB::rollback();
        return redirect()->back()->withErrors(['Ha ocurrido un error inesperado. Intentalo nuevamente.']);
      }

      
    }

    private function intentoRecuperarPri($account_id) {
      
      $venta = $this->ventaPs4Pri($account_id);

      $vendedor = session()->get('usuario')->Nombre;

      #########

      if ($venta) {

        DB::table('ventas')->where('ID',$venta->ID)->update(['recup' => 2]);
        
        $data = [];
        $data['id_ventas'] = $venta->ID;
        $data['Notas'] = "Intento recuperar pri";
        $data['Day'] = date('Y-m-d H:i:s');
        $data['usuario'] = $vendedor;

        DB::table('ventas_notas')->insert($data);
      }
      return;
    }
    
    private function intentoRecuperarSecu($account_id) {
      
      $venta = $this->ventaPs4Secu($account_id);

      $vendedor = session()->get('usuario')->Nombre;

      #########

      if ($venta) {

        DB::table('ventas')->where('ID',$venta->ID)->update(['recup' => 2]);
        
        $data = [];
        $data['id_ventas'] = $venta->ID;
        $data['Notas'] = "Intento recuperar secu";
        $data['Day'] = date('Y-m-d H:i:s');
        $data['usuario'] = $vendedor;

        DB::table('ventas_notas')->insert($data);
      }
      return;
    }

    private function ventaPs4Pri($account_id) {
      $stocks = DB::table('stock')->select(DB::raw('GROUP_CONCAT(ID) AS stocks_ids'))->where('cuentas_id',$account_id)->groupBy('cuentas_id')->value('stocks_ids');

      if ($stocks) {
        $stocks = explode(",", $stocks);

        $venta = DB::table('ventas')->select('ventas.*','c.nombre','c.apellido')->whereIn('stock_id',$stocks)->where('slot','Primario')->where('cons','ps4')->where('auto','!=','re')
        ->join('clientes AS c','c.ID','=','ventas.clientes_id')
        ->first();

        if ($venta) {
          $fecha_venta = date('Y-m-d',strtotime($venta->Day_modif));
          $fecha_referencia = DB::table('configuraciones')->where('ID',1)->value('fecha_referencia');
          if ($fecha_venta < $fecha_referencia) {
            $tituloStock = DB::table('stock')->where('ID',$venta->stock_id)->value('titulo');
            $isExcluido = DB::table('configuraciones')->where('ID',1)->where('prod_excluidos_pri','like',"%$tituloStock%")->first();
            if ($isExcluido) {
              return null;
            }
          } else {
            return null;
          }
        }
        
        return $venta;
      }

      return null;
    }
    
    private function ventaPs4Secu($account_id) {
      $stocks = DB::table('stock')->select(DB::raw('GROUP_CONCAT(ID) AS stocks_ids'))->where('cuentas_id',$account_id)->groupBy('cuentas_id')->value('stocks_ids');

      if ($stocks) {
        $stocks = explode(",", $stocks);
        
        $venta = DB::table('ventas')->select('ventas.*','c.nombre','c.apellido')->whereIn('stock_id',$stocks)->where('slot','Secundario')->where('cons','ps4')->where('auto','!=','re')
        ->join('clientes AS c','c.ID','=','ventas.clientes_id')
        ->first();

        if ($venta) {
          $fecha_venta = date('Y-m-d',strtotime($venta->Day_modif));
          $fecha_referencia = DB::table('configuraciones')->where('ID',1)->value('fecha_referencia');
          if ($fecha_venta < $fecha_referencia) {
            $tituloStock = DB::table('stock')->where('ID',$venta->stock_id)->value('titulo');
            $isExcluido = DB::table('configuraciones')->where('ID',1)->where('prod_excluidos_secu','like',"%$tituloStock%")->first();
            if ($isExcluido) {
              return null;
            }
          } else {
            return null;
          }
        }

        return $venta;
      }

      return null;
    }

    private function updatePassRecu($account_id, $tipo_rec = null) {
      $npass = \Helper::getRandomPass();

      ## CAMBIAR LA CONTRASEÑA
      
      $usuario = '';
      if ($tipo_rec == "pri") {
        $usuario = "rp" . session()->get('usuario')->Nombre;
      } elseif ($tipo_rec == "reset_pass" && \Helper::operatorsRecoverPri(session()->get('usuario')->Nombre)) {
        $usuario = "rn" . session()->get('usuario')->Nombre;
      } else {
        $usuario = session()->get('usuario')->Nombre;
      }

      $data = [];
      $data['cuentas_id'] = $account_id;
      $data['new_pass'] = $npass;
      $data['Day'] = date('Y-m-d H:i:s');
      $data['usuario'] = $usuario;
      $this->acc->createAccPass($data,$account_id);


      $account = [];
      $account['pass'] = $npass;
      $this->acc->updateAccount($account,$account_id);

      return;
    }

    public function product20off($account,$title,$console)
    {
      $stock_valido = \Helper::availableStock($account,$title,$console);

      if ($stock_valido) {
        $stock_valido_id = $stock_valido[0]->ID_stk;
        $stock = Stock::stockDetail($stock_valido_id)->first();

        $date = date('Y-m-d H:i:s');
        $data = [
          'cuentas_id'=>$account,
          'ex_stock_id'=>$stock->ID,
          'titulo'=>$title,
          'consola'=>$console,
          'medio_pago'=>$stock->medio_pago,
          'costo_usd'=>$stock->costo_usd,
          'costo'=>$stock->costo,
          'code'=>$stock->code,
          'code_prov'=>$stock->code_prov,
          'n_order'=>$stock->n_order,
          'Day'=>$date,
          'ex_Day_stock'=>$stock->Day,
          'usuario'=>session()->get('usuario')->Nombre,
          'ex_usuario'=>$stock->usuario
        ];

        try {
          $this->blc->storeBalanceAccount($data);

          // Eliminando stock
          $stock = Stock::where('ID',$stock->ID)->delete();
          // Mensaje de notificacion
          \Helper::messageFlash('Cuentas','Producto 20 off playstation agregado.','alert_cuenta');
          return redirect('cuentas/'.$account);
        } catch (\Exception $e) {
          return redirect('/cuentas')->withErrors('Intentelo nuevamente');
        }
      } else {
        return redirect('/cuentas')->withErrors(['Ha ocurrido un error, no es un stock valido.']);
      }
    }

    public function indexCuentasNotas(Request $request)
    {
      // Columnas de la base de datos
      $columns = Schema::getColumnListing('cuentas');
      // Traer la lista de cuentas

      // cuentas con filtro
      $obj = new \stdClass;
      $obj->column = $request->column;
      $obj->word = $request->word;


      $accounts_notes = Account::cuentasNotas($obj)->paginate(50);


      return view('account.index_account_notes',
                  compact(
                    'accounts_notes',
                    'columns'
                  ));
    }

    public function notasPredefinidas(Request $request)
    {
      $opt = $request->opt; // Tipo de nota predefinida

      switch ($opt) {
        case 1: // Nota (cambiaron id)
          $clientes = $request->clientes;
          $account_id = $request->id;

          if ($clientes === null) {
            return redirect()->back()->withErrors(['No puedes generar la nota sin seleccionar algún cliente.']);
          }

          DB::beginTransaction();

          try {

            $nros_clientes = [];

            ## INSERTAR EN CLIENTES NOTAS

            foreach ($clientes as $cliente) {

              $nros_clientes[] = '<a href="'.url('clientes',$cliente).'" class="alert-link" target="_blank">#'.$cliente.'</a>';

              $data = [];
              $data['clientes_id'] = $cliente;
              $data['Notas'] = 'Posiblemente cambió el ID de la cuenta <a href="'.url('cuentas',$account_id).'" class="alert-link" target="_blank">#'.$account_id.'</a>';
              $data['Day'] = date('Y-m-d H:i:s');
              $data['usuario'] = session()->get('usuario')->Nombre;

              DB::table('clientes_notas')->insert($data);
            }

            $data = [];
            $data['cuentas_id'] = $account_id;
            $data['Notas'] = 'Cambiaron el ID de la cuenta. Cliente(s): ' . implode(", ", $nros_clientes);
            $data['Day'] = date('Y-m-d H:i:s');
            $data['usuario'] = session()->get('usuario')->Nombre;

            DB::table('cuentas_notas')->insert($data);

            ## REGISTRAR CUENTA ROBADA

            $data = [];
            $data['cuentas_id'] = $account_id;
            $data['Day'] = date('Y-m-d H:i:s');
            $data['usuario'] = session()->get('usuario')->Nombre;

            DB::table('cuentas_robadas')->insert($data);

            $this->addOrRemoveExcludedAccount('Add', $account_id);

            DB::commit();

            \Helper::messageFlash('Cuentas',"Nota generada predefinida",'alert_cuenta');


            return redirect()->back();
          } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['Ha ocurrido un error inesperado. Intentalo nuevamente.']);
          }
          break;
        
        default:
          # code...
          break;
      }
    }

    private function addOrRemoveExcludedAccount($accion, $id_account) {
      $cuentas_excluidas = DB::table('configuraciones')->where('ID',1)->value('cuentas_excluidas');
      $cuentasExcluidas = explode(",",$cuentas_excluidas);
      
      if ($accion == 'Add') {
        array_push($cuentasExcluidas, $id_account);

        $data['cuentas_excluidas'] = implode(",",$cuentasExcluidas);
        DB::table('configuraciones')->where('ID',1)->update($data);
      } elseif ($accion == 'Remove') {
        $index = array_search($id_account, $cuentasExcluidas);
        if ($index !== false) {
          unset($cuentasExcluidas[$index]);
          $data['cuentas_excluidas'] = implode(",",$cuentasExcluidas);
          DB::table('configuraciones')->where('ID',1)->update($data);
        }
      }
    }

    private function validarGift($stock_id) {
      $stock = DB::table('stock')->where('ID',$stock_id)->first();

      return $stock ? true : false;
    }

    public function modoContinuo(Request $request) {
      $modo_continuo = $request->modo_continuo;
      $user = session()->get('usuario')->ID;

      DB::table('usuarios')->where('ID',$user)->update(['modo_continuo' => $modo_continuo]);

      $infoUser = DB::table('usuarios')->where('ID',$user)->first();

      $request->session()->put('usuario', $infoUser);

      echo json_encode(['status' => 202]);
    }

    private function insertStockPlus12Meses($id_account){
      
      $expense = Stock::stockExpensesByAccountId($id_account)->first();
      $accountBalance = Balance::totalBalanceAccount($id_account)->first();

      if (empty($expense)) {
        $expense = new \stdClass;
        $expense->costo_usd = 0;
        $expense->costo = 0;
      }
      /// SI EL COSTO EN USD ES 9.99, 19.99, etc... LE SUMO UN CENTAVO
      $costo_usd = round(($accountBalance->costo_usd - $expense->costo_usd), 1);

      //// CALCULO EL SALDO LIBRE DE LA CUENTA EN USD Y EN ARS
      $saldo_libre_usd = ($accountBalance->costo_usd - $expense->costo_usd);
      $saldo_libre_ars = ($accountBalance->costo - $expense->costo);

      /// SI EL SALDO A QUEDAR LUEGO DE INSERTAR UN PRODUCTO ES MAYOR O IGUAL A 9.99, CARGO COSTO ARS PROPORCIONAL
      if (($saldo_libre_usd - $costo_usd) >= 9.99) {
        $costo_ars = ($saldo_libre_ars * ($costo_usd/$saldo_libre_usd));
      } else {
      /// SI EL SALDO A QUEDAR ES MENOR A 9.99 LE ASIGNO EL TOTAL EN PESOS LIBRES > ABSORBO TODO EL COSTO EN PESOS
        $costo_ars = $saldo_libre_ars;
      }

      $titulo = 'plus-12-meses-slot';
      $consola = 'ps';

      if ($this->validarStock($id_account, $titulo, $consola)) {
        return redirect('cuentas/'.$id_account)->withErrors("Ya existe el juego $titulo ($consola) en esta cuenta.");
      } 

      $data = [];
      $data['titulo'] = $titulo;
      $data['consola'] = $consola;
      $data['cuentas_id'] = $id_account;
      $data['costo_usd'] = $costo_usd;
      $data['costo_usd_modif'] = $costo_usd;
      $data['medio_pago'] = 'Saldo';
      $data['costo'] = $costo_ars;
      $data['Day'] = $this->dte;
      $data['usuario'] = session()->get('usuario')->Nombre;

      $this->tks->storeStockAccount($data);


      return;

    }

    public function cuentasRobadas(Request $request) {
      // Columnas de la base de datos
      $columns = Schema::getColumnListing('cuentas');
      // Traer la lista de cuentas

      // cuentas con filtro
      $obj = new \stdClass;
      $obj->column = $request->column;
      $obj->word = $request->word;

      $cuentas_excluidas = DB::table('configuraciones')->where('ID',1)->value('cuentas_robadas_excluidas');

      $accounts = Account::accountStolen($obj, $cuentas_excluidas)->paginate(50);

      return view('account.index_stolen',
                  compact(
                    'accounts',
                    'columns',
                    'cuentas_excluidas'
                  ));
    }

    public function recoverAccount(Request $request) {
      DB::beginTransaction();

      try {
        $data = [];
        $data['cuentas_id'] = $request->account_id;
        $data['Notas'] = "Cuenta Recuperada!";
        $data['Day'] = date('Y-m-d H:i:s');
        $data['usuario'] = session()->get('usuario')->Nombre;

        DB::table('cuentas_notas')->insert($data);

        DB::table('cuentas_robadas')->where('cuentas_id',$request->account_id)->delete();

        $this->addOrRemoveExcludedAccount('Remove', $request->account_id);

        DB::commit();

        \Helper::messageFlash('Cuentas',"Cuenta Recuperada Exitosamente!",'alert_cuenta');

        return redirect()->back();
      } catch (Exception $e) {
        DB::rollback();
        return redirect()->back()->withErrors(['Ha ocurrido un error inesperado. Intentalo nuevamente.']);
      }
    }

    public function congelarTC(Request $request) 
    {
      DB::beginTransaction();
      $dias = DB::table('configuraciones')->where('ID',1)->value('dias_congelar_tc');

      try {
        $fechaStock = DB::table('saldo')->where('ID',$request->id)->value('Day');
        $new_fecha = strtotime("+$dias days", strtotime($fechaStock));
        $new_fecha = date('Y-m-d H:i:s', $new_fecha);

        DB::table('saldo')->where('ID', $request->id)->update(['Day' => $new_fecha]);

        DB::commit();

        \Helper::messageFlash('Cuentas',"Se ha congelado satisfactoriamente.",'alert_cuenta');

        return redirect()->back();
      } catch (Exception $e) {
        DB::rollback();
        return redirect()->back()->withErrors(['Ha ocurrido un error inesperado. Intentalo nuevamente.']);
      }
    }

    public function conTCSinJuego()
    {
      $cuentas = Balance::conTcSinJuego()->get();

      return view('account.tc_sin_juego', compact('cuentas'));
    }

    public function dominiosByUser(Request $request)
    {
      $usuario = session()->get('usuario')->Nombre;
      $testData = DB::table('dominios_usuarios')->where('id_dominio', $request->ID)->where('usuario', $usuario)->first();

      $data['indicador_habilitado'] = $request->indicador_habilitado;
      $data['update_at'] = date('Y-m-d H:i:s');

      if ($testData) {
        DB::table('dominios_usuarios')->where('ID', $testData->ID)->update($data);
      } else {
        $data['create_at'] = date('Y-m-d H:i:s');
        $data['id_dominio'] = $request->ID;
        $data['usuario'] = $usuario;

        DB::table('dominios_usuarios')->insert($data);
      }


      \Helper::messageFlash('Cuentas',"Estado del dominio actualizado.",'alert_cuenta');

      return redirect()->back();
    }

    public function ctasResetear() {
      $cuentas = Account::ctasResetear()->get();

      return view('account.ctas_resetear', compact('cuentas'));
    }

    public function ctasVacias($user = null)
    {
      $cuentas = Account::ctasVacias()->get();
      $usuarios = [];

      foreach ($cuentas as $value) { 
        if (!in_array($value->usuario,$usuarios)) {
          $usuarios[] = $value->usuario;
        }
      }
      $data_user = session()->get('usuario');
      $user = $user != null ? $user : (\Helper::validateAdministrator($data_user->Level) ? null : $data_user->Nombre);
      
      $cuentas = Account::ctasVacias($user)->get();

      return view('account.ctas_vacias', compact('cuentas','usuarios','user'));

    }

    public function ctaFornite($id_account)
    {
      $data = [
        "cuentas_id" => $id_account,
        "Notas" => "Fortnite comprado",
        "Day" => date('Y-m-d H:i:s'),
        "usuario" => session()->get('usuario')->Nombre
      ];

      DB::table('cuentas_notas')->insert($data);

      \Helper::messageFlash('Cuentas',"Nota de Fornite comprado agregada.",'alert_cuenta');

      return redirect()->back();
    }

    public function emailMsjReactPass($type, $cliente_id, $account_id, $id_venta, $id_stock)
    {
      $cliente = DB::table('clientes')->where('ID',$cliente_id)->first();
      $account = DB::table('cuentas')->where('ID',$account_id)->first();
      $sales = DB::table('ventas')->where('ID',$id_venta)->first();
      $stock = DB::table('stock')->where('ID',$id_stock)->first();

      $correo = $cliente->email;
      $nombre = $cliente->nombre . " " . $cliente->apellido;
      $subject = "";
      
      if (strpos($type,"msj") !== false) {
        $subject = "🔥 [Nuevos Datos] ".ucwords(\Helper::strTitleStock($stock->titulo))." ({$stock->consola}) (wc-{$sales->order_id_web}-s$id_stock)";
      } elseif (strpos($type,"btns") !== false) {
        $subject = "🔥 [Información Importante] ".ucwords(\Helper::strTitleStock($stock->titulo))." ({$stock->consola}) (wc-{$sales->order_id_web}-s$id_stock)";
      }

      $data = [
        "cliente" => $cliente,
        "account" => $account,
        "stock" => $stock,
        "oferta_fortnite" => DB::table('configuraciones')->where('ID',1)->value('oferta_fortnite')
        ];

      try {
        Mail::send("emails.$type", $data, function($message) use ($correo,$nombre,$subject)
        {
            $message->to($correo, $nombre)->subject($subject);
        });

        return response()->json(["status" => "success"]);
      } catch (\Exception $e) {
        return response()->json(["status" => "error", "message" => $e->getMessage()]);
      }
    }

    public function activaCuenta($id_account, $valor_activa) {
        $account = Account::find($id_account);

        if ($account) {
            DB::table('cuentas')->where('ID',$id_account)->update(['activa' => $valor_activa]);

            $accion = $valor_activa == 1 ? "restaurado" : "descartado";
            $mensaje = "La cuenta se ha $accion exitosamente";

            \Helper::messageFlash('Cuentas',$mensaje,'alert_cuenta');

            return redirect()->back();
        }

        return redirect()->back()->withErrors(['Ha ocurrido un error al obtener la cuenta']);

    }

    public function balanceSony($account_id) {
        return view('ajax.account.balance_sony',compact('account_id'));
    }

    public function balanceSonyStore(Request $request) {

        // Mensajes de alerta
        $msgs = [
            'balance.required' => 'El balance es obligatorio',
            'balance.numeric' => 'El valor del balance debe ser numerico',
        ];
        // Validamos
        $v = Validator::make($request->all(), [
            'balance' => 'required|numeric'
        ], $msgs);

        // Si hay errores retornamos a la pantalla anterior con los mensajes
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }

        $data['cuentas_id'] = $request->cuenta_id;
        $data['balance'] = $request->balance;
        $data['usuario'] = session()->get('usuario')->Nombre;
        $data['Day'] = date('Y-m-d H:i:s');

        DB::table('cuentas_balance_sony')->insert($data);

        \Helper::messageFlash('Cuentas','Balance sony insertado exitosamente','alert_cuenta');

        return redirect()->back();
    }

    public function saldoLibreSony(Request $request)
    {
        $rows = Account::getSaldoLibreSony()->paginate(50);

        return view('account.index_amount_sony',compact('rows'));
    }

    public function descartarStock($stock_id) {
        $stock = Stock::find($stock_id);

        return view('ajax.account.descartar_juego',compact('stock'));

    }

}

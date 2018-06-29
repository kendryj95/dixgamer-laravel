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

      $accounts = Account::accountGames($obj)->paginate(50);


      return view('account.index',
                  compact(
                    'accounts',
                    'columns'
                  ));
    }

    public function accountAmount(Request $request)
    {
      // Traer la lista de cuentas
      $accounts = Account::accountAmounts($request->console)->paginate(50);

      return view('account.index_amount',
                  compact(
                    'accounts'
                  ));
    }


    // Retorna vista para modal de solicitud de reseteo
    public function requestReset($id){
      $account = Account::where('ID',$id)->first();

      if (count($account)<1)
        return redirect('/cuentas')->withErrors('Intentelo nuevamente');


      return view('ajax.account.request_reset',compact('account'));

    }

    // Retorna vista para modal de solicitud de reseteo
    public function storeRequestReset($id,Request $request){
      $account = Account::where('ID',$id)->first();

      if (count($account)<1)
        return redirect('/cuentas')->withErrors('Intentelo nuevamente');


      try {
        $data = [];
        $data['cuentas_id'] = $id;
        $data['Day'] = $this->dte;
        $data['Notas'] = $request->note;
        $data['usuario'] = Auth::user()->Nombre;

        $this->rst->storeRequestResetAccount($data);

        // Mensaje de notificacion
        \Helper::messageFlash('Cuentas','Solicitud de reseteo creada');

        return redirect('cuentas/'.$id);
      } catch (\Exception $e) {
        return redirect()->back()->withErrors(['Intentelo nuevamente']);
      }



    }


    // juegos de cuentas ps3
    public function accountGamePs3(Request $request)
    {
      // Traer la lista de cuentas
      $accounts = Account::accountGamesPs3()->paginate(50);

      return view('account.index_account_ps3',
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
        return view('account.create');
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
        'mail.email' => 'Ingrese Email valido',
        'mail_fake.email' => 'Ingrese Email falso valido',
        'mail.unique' => 'Emal ya existe',
        'mail_fake.unique' => 'Email falso ya existe',
        'surname.required' => 'Apellido requerido',
        'name.required' => 'Nombre requerido',
        'pass.required' => 'Password requerido'
      ];
      // Validamos
      $v = Validator::make($request->all(), [
          'mail' => 'required|email|unique:cuentas,mail',
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
        $account['usuario'] = Auth::user()->Nombre;
        $account['address'] = $request->address;
        $account['reg_date'] = Date('Y-m-d H:i:s');
        $ac->createAccount($account);



        // Mensaje de notificacion
        \Helper::messageFlash('Cuentas','Cuenta guardada');


        return redirect('/cuentas?column=mail&word='.$request->mail);
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


      // Si hay errores retornamos a la pantalla anterior con los mensajes
      if ($v->fails())
      {
          return redirect()->back()->withErrors($v->errors());
      }

      $lastGames = $this->tks->lastAccountByIdAndUser(Auth::user()->Nombre,$request->last_account);
      if (count($lastGames) < 1)
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
          'costo' => $costo,
          'Day' => $this->dte,
          'usuario' => Auth::user()->Nombre,
        ];
      }

      try {
        // mandamos a guardar el arreglo de juegos
        $this->tks->storeCodes($data);

        // Mensaje de notificacion
        \Helper::messageFlash('Cuentas','Cuenta copiada');


        return redirect('cuentas/'.$account_id);
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
    public function show(Account $account,$id)
    {
      $account = Account::resetAccountDetail($id)->first();
      if (count($account) < 1)
        return redirect('/cuentas')->withErrors('Cuenta no encontrada');

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
      $lastAccountGames = $this->tks->lastAccountUserGames(Auth::user()->Nombre);

      // dd($lastAccountGames);

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
                'lastAccountGames'
      ));

    }

    public function createStockAccount($id){
      $account = Account::accountStockId($id)->first();
      $expense = Stock::stockExpensesByAccountId($id)->first();
      $titles = $this->wp_pst->lastGameStockTitles();
      $accountBalance = Balance::totalBalanceAccount($id)->first();
      return view('ajax.account.insert_stock',compact(
        'account',
        'expense',
        'titles',
        'accountBalance'
      ));
    }

    public function editStockAccount($stock_id,$account_id){
      $obj = new \stdClass;
      $obj->column = 'ID';
      $obj->word = $account_id;

      // traemos la cuenta por el ID
      $account = Account::AccountByColumnWord($obj)->first();

      // Traemos el stock
      $stock = Stock::where('ID',$stock_id)->first();
      $titles = $this->wp_pst->lastGameStockTitles();

      return view('ajax.account.update_stock',compact(
        'account',
        'stock',
        'titles'
      ));
    }

    public function updateStockAccount(Request $request,$account_id){
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

      $obj = new \stdClass;
      $obj->column = 'ID';
      $obj->word = $account_id;

      // traemos la cuenta por el ID
      $account = Account::AccountByColumnWord($obj)->first();

      // Traemos el stock
      $stock = Stock::where('ID',$request->stock_id)->first();

      if (count($account) > 0 && count($stock) > 0) {
        try {

          $notes = [];
          $notes['cuentas_id'] = $account_id;
          $notes['Notas'] = "Modificacion de juego #$stock->ID, antes $stock->titulo ($stock->consola)";
          $notes['usuario'] = Auth::user()->Nombre;
          $notes['Day'] = $this->dte;
          DB::table('cuentas_notas')->insert([$notes]);


          $data = [];
          $data['titulo'] = $request->titulo;
          $data['consola'] = $request->consola;
          $this->tks->updateStockById($request->stock_id,$data);


          // Mensaje de notificacion
          \Helper::messageFlash('Cuentas','Stock actualizado');
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
        $data = [];
        $data['titulo'] = $request->titulo;
        $data['consola'] = $request->consola;
        $data['cuentas_id'] = $request->cuentas_id;
        $data['costo_usd'] = $request->costo_usd;
        $data['medio_pago'] = 'Saldo';
        $data['costo'] = $request->titulo;
        $data['Day'] = $this->dte;
        $data['Notas'] = $request->Notas;
        $data['usuario'] = Auth::user()->Nombre;

        $this->tks->storeStockAccount($data);
        \Helper::messageFlash('Cuentas','Stock agregado');
        return redirect('cuentas/'.$request->cuentas_id);
      } catch (\Exception $e) {
        return redirect('/cuentas')->withErrors('Intentelo nuevamente');
      }

    }

    public function rechargeBalance($id){
      $gifts = $this->blc->reChargeGifCards();
      $account_id = $id ;
      return view('ajax.account.recharge_balance',compact(
        'gifts',
        'account_id'
      ));
    }

    public function storeBalanceAccount($account,$title,$console){
      if (!empty($account) && !empty($title) && !empty($console)) {
        // cargo el stock disponible en este mismo segundo y busco el producto que quiero asignar
        $stock_valido = \Helper::availableStock($account,$title,$console);
        $stock_valido_id = $stock_valido[0]->ID_stk;
        $stock = Stock::stockDetail($stock_valido_id)->first();

        $date = date('Y-m-d H:i:s', time());
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
          'Notas'=>$stock->Notas,
          'usuario'=>Auth::user()->Nombre,
          'ex_usuario'=>$stock->usuario
        ];

        try {
          $this->blc->storeBalanceAccount($data);

          // Eliminando stock
          $stock = Stock::where('ID',$stock->ID)->delete();
          // Mensaje de notificacion
          \Helper::messageFlash('Cuentas','Saldo agregado');
          return redirect('cuentas/'.$account);
        } catch (\Exception $e) {
          return redirect('/cuentas')->withErrors('Intentelo nuevamente');
        }



      }
    }


    public function createLastStock($id){
      $account = Account::where('ID',$id)->first();

      if (!count($account) > 0)
        return redirect('/cuentas')->withErrors('Intentelo nuevamente');

      // Ultimos stocks
      $accountStocks = $this->tks->lastStockUser(Auth::user()->Nombre);

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
        $data['medio_pago'] = 'Saldo';
        $data['costo'] = $costo;
        $data['Day'] = $this->dte;
        $data['usuario'] = Auth::user()->Nombre;

        $this->tks->storeStockAccount($data);
        \Helper::messageFlash('Cuentas','Stock masivo agregado');
        return redirect('cuentas/'.$id);

      } catch (\Exception $e) {
        return redirect('/cuentas')->withErrors('Intentelo nuevamente');
      }

    }


    public function resetAccount($id){
      $account = Account::where('ID',$id)->first();

      if (count($account)<1)
        return redirect('/cuentas')->withErrors('Intentelo nuevamente');

      $date = date('Y-m-d H:i:s', time());
      try {
        $data = [];
        $data['cuentas_id']=$id;
        $data['Day']=$date;
        $data['usuario']= Auth::user()->Nombre;
        $this->rst->storeResetAccount($data);

        \Helper::messageFlash('Cuentas','Cuenta reseteada');
        return redirect('cuentas/'.$id);

      } catch (\Exception $e) {
        return redirect('cuentas/'.$id)->withErrors('Intentelo nuevamente');
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
      $date = date('Y-m-d H:i:s', time());
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
        $data['costo'] = $balance->costo;
        $data['code'] = $balance->code;
        $data['code_prov'] = $balance->code_prov;
        $data['n_order'] = $balance->n_order;
        $data['Day'] = $date;
        $data['Notas'] = "Devuelto de cta #$request->c_id";
        $data['usuario'] = $balance->ex_usuario;
        $saving = $this->tks->storeCodes($data);

        // Eliminando saldo actual
        DB::table('saldo')->where('ex_stock_id',$request->id)->delete();
        // Mensaje de notificacion
        \Helper::messageFlash('Cuentas','Saldo retornado');
        return redirect('cuentas/'.$request->c_id);
      } catch (\Exception $e) {
        return redirect('cuentas/'.$request->c_id)->withErrors('Intentelo nuevamente');
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
      // Mensajes de alerta
      $msgs = [
        'mail.required' => 'Email requerido',
        'mail_fake.required' => 'Email falso requerido',
        'mail.email' => 'Ingrese Email valido',
        'mail_fake.email' => 'Ingrese Email falso valido',
        'mail.unique' => 'Emal ya existe',
        'mail_fake.unique' => 'Email falso ya existe',
        'surname.required' => 'Apellido requerido',
        'name.required' => 'Nombre requerido',
        'pass.required' => 'Password requerido'
      ];
      // Validamos
      $v = Validator::make($request->all(), [
          'mail' => 'required|email|unique:cuentas,mail,'.$id,
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
        $account['usuario'] = Auth::user()->Nombre;
        $account['verificado'] = (\Helper::validateAdministrator(Auth::user()->Level)) ? 'si' : 'no';
        $this->acc->createAccountMod($account);


        // Mensaje de notificacion
        \Helper::messageFlash('Cuentas','Cuenta editada');
        return redirect('cuentas/'.$id);
      } catch (\Exception $e) {
        return redirect('cuentas/'.$id)->withErrors('Intentelo nuevamente');
      }
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
        $account['usuario'] = Auth::user()->Nombre;
        $account['verificado'] = (\Helper::validateAdministrator(Auth::user()->Level)) ? 'si' : 'no';
        $this->acc->createAccountMod($account);


        // Mensaje de notificacion
        \Helper::messageFlash('Cuentas','Dirección editada');
        return redirect('cuentas/'.$id);
      } catch (\Exception $e) {
        return redirect('cuentas/'.$id)->withErrors('Intentelo nuevamente');
      }
    }


    public function updatePassword($id){
      try {
        $npass = \Helper::getRandomPass();
        $date = date('Y-m-d H:i:s', time());

        $data = [];
        $data['cuentas_id'] = $id;
        $data['new_pass'] = $npass;
        $data['Day'] = $date;
        $data['usuario'] = Auth::user()->Nombre;
        $this->acc->createAccPass($data,$id);


        $account = [];
        $account['pass'] = $npass;
        $this->acc->updateAccount($account,$id);
        // Mensaje de notificacion
        \Helper::messageFlash('Cuentas','Password editado');
        return redirect('cuentas/'.$id);
      } catch (\Exception $e) {
        return redirect('cuentas/'.$id)->withErrors('Intentelo nuevamente');
      }

    }

    public function createNote($id){
      $account = $id;
      return view('ajax.account.create_note',compact('account'));
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
      if (count($account) > 0) {
        try {
          $data = [
                    'cuentas_id' => $id,
                    'Notas'=>$request->notes,
                    'Day'=>$date,
                    'usuario'=>Auth::user()->Nombre
                  ];

          $this->ac->storeNote($data);
          // Mensaje de notificacion
          \Helper::messageFlash('Cuentas','Nota Creada');
          return redirect('cuentas/'.$id);
        } catch (\Exception $e) {
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
}

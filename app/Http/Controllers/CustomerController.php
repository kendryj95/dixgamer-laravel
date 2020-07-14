<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Sale;
use App\Expenses;
use App\Stock;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Validator;
use Auth;
use Schema;
use DB;
use Mail;
class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $revendedor = null)
    {
      // Email
      $email = $request->email;
      $paginate = 50;

      // Columnas de la base de datos
      $columns = Schema::getColumnListing('clientes');


      // Clientes con filtro
      $obj = new \stdClass;
      $obj->column = $request->column;
      $obj->word = $request->word;

      // Pasamos los filtros a la busqueda
      $customers = Customer::customerByEmail($email)->customerByCustomColumn($obj, $revendedor)->orderBy('ID','DESC')->paginate($paginate);

      if (count($customers) == 1) { // Si es un solo registro, redireccionar al cliente en coincidencia.
        $id_cliente = $customers[0]->ID;

        return redirect('clientes/'.$id_cliente);
      }

      return view('customer.index',compact('customers','columns','revendedor'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('customer.create');
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
        'email.required' => 'Email requerido',
        'email.email' => 'Ingrese Email valido',
        'email.unique' => 'Emal ya existe',
        'apellido.required' => 'Apellido requerido',
        'nombre.required' => 'Nombre requerido',
        'pais.required' => 'Pais requerido',
        'provincia.required' => 'Provincia requerido',
        'carac.required' => 'Carac requerido',
        'ciudad.required' => 'Ciudad requerido',
        'tel.required' => 'Tel requerido',
        'cel.required' => 'Cel requerido'
      ];
      // Validamos
      $validaciones = [
          'email' => 'required|email|unique:clientes,email',
          'apellido' => 'required',
          'nombre' => 'required',
          'pais' => 'required',
          'provincia' => 'required',
      ];

      if ($request->ml_user != "") {
        $validaciones['ml_user'] = "unique:clientes,ml_user";
        $msgs['email.unique'] = 'Usuario mercado libre ya existe';
      }

      $v = Validator::make($request->all(), $validaciones, $msgs);

      // Si hay errores retornamos a la pantalla anterior con los mensajes
      if ($v->fails())
      {
          return redirect()->back()->withErrors($v->errors());
      }


      try {
        // Creamos el arreglo del usuario
        $customer = [];
        $customer['email'] = strtolower($request->email);
        $customer['nombre'] = $request->nombre;
        $customer['apellido'] = $request->apellido;
        $customer['pais'] = $request->pais;
        $customer['provincia'] = $request->provincia;
        $customer['ciudad'] = $request->ciudad;
        $customer['carac'] = $request->carac;
        $customer['ml_user'] = $request->ml_user;
        $customer['tel'] = $request->tel;
        $customer['cel'] = $request->cel;
        $customer['Level'] = 'Cliente';
        $customer['auto'] = 'no';
        $customer['usuario'] = session()->get('usuario')->Nombre;

        // Creamos objeto de customer para llamar a la funcion creadora
        $c = new Customer();
        $cliente_id = $c->storeCustomer($customer);

        DB::table('clientes_email')->insert(['clientes_id' => $cliente_id, 'email' => strtolower($request->email)]);

        if ($request->ml_user != "") {
          DB::table('clientes_ml_user')->insert(['clientes_id' => $cliente_id, 'ml_user' => $request->ml_user]);
        }

        // Mensaje de notificacion
        \Helper::messageFlash('Clientes','Cliente guardado');
        return redirect('clientes?column=email&word='.strtolower($request->email));
      } catch (\Exception $e) {
        return redirect()->back()->withErrors(['Intentelo nuevamente']);
      }



    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

      if (!(\Helper::validateAdministrator(session()->get('usuario')->Level)) && $id == 371) { // Si no es administrador y el id del cliente es el 371, lo devuelvo a la pagina anterior.
        return redirect()->back();
      }

      $customer  = Customer::customerId($id)->first();
      $dataCustomers = Customer::dataCustomerId($id)->get();
      $salesByCustomer= Sale::salesByCustomerId($id)->first();
      $lowSalesByCustomerIds= Customer::salesLowByCustomerId($id)->get();
      $expensesIncome = Expenses::expensesIncome();
      $customerNotes = Customer::customerNotesByCustomerId($id)->get();

      $id_ventas = [];
      foreach ($dataCustomers as $data) {
        $id_ventas[] = $data->ID_ventas;
      }

      $othersEmails = DB::table('clientes_email')
      ->where('email','<>',$customer->email)
      ->where('clientes_id',$customer->ID)
      ->get();

      $ventas_notas = $this->buildNotesSales($id_ventas);

      $venta_plus_sec = $this->customerWithSalesPlusSecund($id);

      return view('customer.show',compact(
        'customer',
        'dataCustomers',
        'salesByCustomer',
        'lowSalesByCustomerIds',
        'expensesIncome',
        'customerNotes',
        'ventas_notas',
        'othersEmails',
        'venta_plus_sec'
      ));
    }

    private function customerWithSalesPlusSecund($id_cliente)
    {
      return DB::table(DB::raw("(
        (SELECT v.clientes_id FROM ventas v INNER JOIN stock s ON s.ID = v.stock_id WHERE (`titulo` LIKE '%plus%' AND v.slot = 'Secundario' AND v.clientes_id = $id_cliente) LIMIT 1)
        UNION ALL
        (SELECT v.clientes_id FROM ventas v INNER JOIN stock s ON s.ID = v.stock_id WHERE (`titulo` NOT LIKE '%plus%' AND v.slot = 'Secundario' AND v.clientes_id = $id_cliente) LIMIT 1)
        ) as r"))
        ->select(DB::raw("COUNT(*) as ventas"),"r.clientes_id")
        ->groupBy("r.clientes_id")
        ->having('ventas', '>', 1)
        ->first();
    }

    private function buildNotesSales($idsVentas, $others = false) 
    {
      $ventas_notas = DB::table('ventas_notas')->whereIn('id_ventas',$idsVentas)->orderBy('Day','DESC');
      if ($others) {
        $ventas_notas = $ventas_notas->offset(3)->limit(20);
      }
      $ventas_notas = $ventas_notas->get();
      $notas = [];

      foreach ($ventas_notas as $venta_nota) {
        $nota_producto = false;
        $datos = [];
        $nota = '';
        if (!array_key_exists(strval($venta_nota->id_ventas),$notas)) {
          $notas[strval($venta_nota->id_ventas)] = [];
        }

        if (strpos($venta_nota->Notas, "Antes asignado a cliente") !== false) {
          $cliente = substr($venta_nota->Notas, 26);
          $nota = 'Antes asignado a cliente <a href="'.url('clientes', $cliente).'" class="alert-link" target="_blank">#'.$cliente.'</a>';
          $datos = [
            "ID" => $venta_nota->ID,
            "Day" => $venta_nota->Day,
            "usuario" => $venta_nota->usuario,
            "nota" => $nota,
            "nota_producto" => false,
            "titulo" => '',
            "consola" => '',
            "slot" => '',
            "Day_format" => date("d M 'y", strtotime($venta_nota->Day)),
            "administrador" => \Helper::validateAdministrator(session()->get('usuario')->Level)
          ];
        } elseif(strpos($venta_nota->Notas, "Antes tenía") !== false) { // Solo notas para cambios de productos
          if (strpos($venta_nota->Notas, "#", 14) !== false) { // Esta validación que funcione las notas anteriores antes de colocar el link para las cuentas
            $string = $venta_nota->Notas;
            $pos = strripos($string, "#"); // calculando la posicion de ultima aparicion de cuenta_id
            $cuenta = substr($string, $pos+1);
            $nota_vta = substr($string, 0, $pos);
            $title = '';
            $cons = '';
            $slot = '';
            if ($cuenta != "") {
                $data_nota =  explode(" ",substr($nota_vta,14));
                $id_stock = $data_nota[0];
                $title = $data_nota[1];
                if (count($data_nota) > 2) {
                  $cons = $data_nota[2];
                  $slot = $data_nota[3];
                  $nota_producto = true;
                }
            }

            
            if ($cuenta != '') {
              $nota = $nota_vta . '<a href="'.url('cuentas',$cuenta).'" target="_blank" class="alert-link">#'.$cuenta.'</a>';
            } else {
              $nota = $nota_vta;
            }

            $datos = [
              "ID" => $venta_nota->ID,
              "Day" => $venta_nota->Day,
              "usuario" => $venta_nota->usuario,
              "nota" => $nota,
              "nota_producto" => $nota_producto,
              "titulo" => $title,
              "consola" => $cons,
              "slot" => $slot,
              "Day_format" => date("d M 'y", strtotime($venta_nota->Day)),
              "administrador" => \Helper::validateAdministrator(session()->get('usuario')->Level)
            ];
          } else {
            $nota = $venta_nota->Notas;
            $datos = [
              "ID" => $venta_nota->ID,
              "Day" => $venta_nota->Day,
              "usuario" => $venta_nota->usuario,
              "nota" => $nota,
              "nota_producto" => false,
              "titulo" => '',
              "consola" => '',
              "slot" => '',
              "Day_format" => date("d M 'y", strtotime($venta_nota->Day)),
              "administrador" => \Helper::validateAdministrator(session()->get('usuario')->Level)
            ];
          }
        } else {
          $nota = $venta_nota->Notas;
          $datos = [
            "ID" => $venta_nota->ID,
            "Day" => $venta_nota->Day,
            "usuario" => $venta_nota->usuario,
            "nota" => $nota,
            "nota_producto" => $nota_producto,
            "titulo" => '',
            "consola" => '',
            "slot" => '',
            "Day_format" => date("d M 'y", strtotime($venta_nota->Day)),
            "administrador" => \Helper::validateAdministrator(session()->get('usuario')->Level)
          ];
        }
        
        if (!array_key_exists(strval($venta_nota->id_ventas),$notas)) {
          $notas[strval($venta_nota->id_ventas)] = [$datos];
        } else {
          $notas[strval($venta_nota->id_ventas)][] = $datos;
        }
      }

      return $notas;
    }

    public function edit(Customer $customer,$id)
    {
      // Traemos el cliente
      $customer = $customer->where('ID',$id)->first();

      // Retornamos a la vista
      return view('ajax.customer.edit_name_customer',compact('customer'));

    }

    public function updateStatusReseller(Request $request)
    {
        $data = null;
        $cliente = DB::table('clientes')->where('ID',$request->id)->first();
        if ($request->datos == "bloq" || $cliente->auto == "bloq") {
          $data = [
            "clientes_id" => $request->id,
            "Notas" => $request->datos == "bloq" ? "El cliente fue bloqueado porque ".$request->nota : "El cliente fue desbloqueado porque ".$request->nota,
            "Day" => date('Y-m-d H:i:s'),
            "usuario" => session()->get('usuario')->Nombre
          ];
        }
        DB::table('clientes')->where('ID',$request->id)->update(['auto' => $request->datos]);

        if ($data)
          DB::table('clientes_notas')->insert($data);

        return Response()->json('ola k ase');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        //
    }


    public function customerCtrlEmail(Request $request){
      $customer = Customer::customerEmail($request->email)->first();
      if ($customer) {
        echo true;
      }else{
        echo false;
      }
    }

    public function customerCtrlMlUsr(Request $request){

      if (isset($request->ml_user) && $request->ml_user != "") {
        // Pasamos los filtros a la busqueda
        $customer = DB::table('clientes AS c')->leftjoin('clientes_ml_user AS cml','c.id','=','cml.clientes_id')
                      ->where('cml.ml_user',$request->ml_user)->first();
        if ($customer) {
          echo true;
        }else{
          echo false;
        }
      }
    }

    public function ventasModificar($id, $opt)
    {
      $data = DB::table('clientes')->select(DB::raw("CONCAT('[ ',ID,' ] ',nombre,' ',apellido,' - ',email) as nombre"))->orderBy('ID','DESC')->get();

      $clientes = Customer::infoCustomerVentas($id);
      $opt = $opt; //Opción

      return view('ajax.customer.ventas_modificar', compact('clientes', 'opt', 'data'));
    }

    public function ventasModificarStore(Request $request)
    {
      
      DB::beginTransaction();

      $date = date('Y-m-d H:i:s');
      $verificado = 'no';
      if (\Helper::validateAdministrator(session()->get('usuario')->Level)) {
        $verificado = 'si';
      }
      $vendedor = session()->get('usuario')->Nombre;

      switch ($request->opt) {
        case 1:
          try {
            DB::insert("INSERT INTO ventas_modif(ventas_id, clientes_id, stock_id, order_item_id, cons, slot, medio_venta, Day, Notas, verificado, usuario ) SELECT ID, clientes_id, stock_id, order_item_id, cons, slot, medio_venta, '$date', Notas, '$verificado', '$vendedor' FROM ventas WHERE ID=?", [$request->ID]);

            $cliente_anterior = DB::table('ventas')->where('ID', $request->ID)->value('clientes_id');

            $data = [];
            $data['clientes_id'] = $request->clientes_id;

            DB::table('ventas')->where('ID', $request->ID)->update($data);

            $data = [];
            $data['id_ventas'] = $request->ID;
            $data['Notas'] = "Antes asignado a cliente #$cliente_anterior";
            $data['Day'] = date('Y-m-d H:i:s');
            $data['usuario'] = session()->get('usuario')->Nombre;

            DB::table('ventas_notas')->insert($data);

            DB::commit();

            \Helper::messageFlash('Clientes','Venta asignada.', 'alert_cliente');

            return redirect('clientes/'.$request->clientes_id);

          } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['Ha ocurrido un error inesperado. Vuelva a intentarlo por favor.']);
          }
          break;
        
        case 2:
          
          try {
            DB::insert("INSERT INTO ventas_modif(ventas_id, clientes_id, stock_id, order_item_id, cons, slot, medio_venta, Day, Notas, verificado, usuario ) SELECT ID, clientes_id, stock_id, order_item_id, cons, slot, medio_venta, '$date', Notas, '$verificado', '$vendedor' FROM ventas WHERE ID=?", [$request->ID]);

            $data = [];
            $data['medio_venta'] = $request->medio_venta;

            DB::table('ventas')->where('ID', $request->ID)->update($data);
            DB::commit();

            \Helper::messageFlash('Clientes','Venta modificada.', 'alert_cliente');

            return redirect()->back();

          } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['Ha ocurrido un error inesperado. Vuelva a intentarlo por favor.']);
          }

          break;
        case 3:

          try {
            DB::insert("INSERT INTO ventas_modif(ventas_id, clientes_id, stock_id, order_item_id, cons, slot, medio_venta, Day, Notas, verificado, usuario ) SELECT ID, clientes_id, stock_id, order_item_id, cons, slot, medio_venta, '$date', Notas, '$verificado', '$vendedor' FROM ventas WHERE ID=?", [$request->ID]);

            $order_item_id = isset($request->order_item_id) ? $request->order_item_id : NULL;
            $order_id_web = isset($request->order_id_web) ? $request->order_id_web : NULL;
            $order_id_ml = isset($request->order_id_ml) ? $request->order_id_ml : NULL;
            $fecha_venta = isset($request->Day) ? $request->Day : NULL;
            $fecha_modif_venta = isset($request->Day_modif) ? $request->Day_modif : NULL;
            $hora = date('H:i:s', strtotime($request->fecha_old));
            $hora_mod = date('H:i:s', strtotime($request->fecha_modif_old));

            $venta = DB::table('ventas')->where('ID', $request->ID)->first();

            $nota = "Antes tenía";
            $band = false;

            if ($venta->medio_venta != $request->medio_venta) {
              $band = true;
              $nota .= " Medio venta $venta->medio_venta";
            }

            if (($venta->order_item_id != $request->order_item_id) && $venta->order_item_id != null) {
              $band = true;
              $nota .= " Order_item_id #$venta->order_item_id";
            }

            if (($venta->order_id_web != $request->order_id_web) && $venta->order_id_web != null) {
              $band = true;
              $nota .= " Order_id_web #$venta->order_id_web";
            }

            if (($venta->order_id_ml != $request->order_id_ml) && $venta->order_id_ml != null) {
              $band = true;
              $nota .= " Order_id_ml #$venta->order_id_ml";
            }

            $data = [];
            $data['medio_venta'] = $request->medio_venta;
            $data['Day'] = $fecha_venta . " $hora";
            $data['Day_modif'] = $fecha_modif_venta . " $hora_mod";
            if ($request->medio_venta == 'Mail') {
              $data['order_item_id'] = NULL;
              $data['order_id_web'] = NULL;
              $data['order_id_ml'] = NULL;
            } elseif ($request->medio_venta == 'MercadoLibre') {
              $data['order_item_id'] = $order_item_id;
              $data['order_id_web'] = $order_id_web;
              $data['order_id_ml'] = $order_id_ml;
            } else {
              $data['order_item_id'] = $order_item_id;
              $data['order_id_web'] = $order_id_web;
              $data['order_id_ml'] = NULL;
            }
            

            DB::table('ventas')->where('ID', $request->ID)->update($data);

            if ($band) {
              $data = [];
              $data['id_ventas'] = $request->ID;
              $data['Notas'] = $nota;
              $data['Day'] = date('Y-m-d H:i:s');
              $data['usuario'] = session()->get('usuario')->Nombre;

              DB::table('ventas_notas')->insert($data);
            }

            DB::commit();

            \Helper::messageFlash('Clientes','Venta modificada.', 'alert_cliente');

            return redirect()->back();

          } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['Ha ocurrido un error inesperado. Vuelva a intentarlo por favor.']);
          }

          break;
        case 4:

          try {
            DB::insert("INSERT INTO ventas_modif(ventas_id, clientes_id, stock_id, order_item_id, cons, slot, medio_venta, Day, Notas, verificado, usuario ) SELECT ID, clientes_id, stock_id, order_item_id, cons, slot, medio_venta, '$date', Notas, '$verificado', '$vendedor' FROM ventas WHERE ID=?", [$request->ID]);

            $data = [];
            $data['id_ventas'] = $request->ID;
            $data['Notas'] = $request->Notas;
            $data['Day'] = date('Y-m-d H:i:s');
            $data['usuario'] = session()->get('usuario')->Nombre;

            DB::table('ventas_notas')->insert($data);
            DB::commit();

            \Helper::messageFlash('Clientes','Venta modificada.', 'alert_cliente');

            return redirect()->back();

          } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['Ha ocurrido un error inesperado. Vuelva a intentarlo por favor.']);
          }

          break;
        case 5:
          try {

            $stock_anterior = DB::table('stock AS s')
                                ->select(
                                  'v.stock_id',
                                  's.titulo',
                                  's.cuentas_id',
                                  'v.cons',
                                  'v.slot'
                                )
                                ->leftjoin('ventas AS v', 's.ID', '=', 'v.stock_id')
                                ->where('v.ID',$request->ID)->first();

            $stock = Stock::find($request->stock);

            if ($stock) {
              $validate_stock = DB::table('ventas')->where('stock_id',$request->stock)->first();

              if ($validate_stock)
                return redirect()->back()->withErrors(['Opps! El Stock ID ya está relacionado a una venta.']);
            } else {
              return redirect()->back()->withErrors(['Opps! El Stock ID no existe.']);
            }
            
            $data = [];
            $data['slot'] = $request->slot;
            $data['stock_id'] = $request->stock;
            $data['Day_modif'] = date('Y-m-d H:i:s');
            $data['recup'] = 1;

            DB::table('ventas')->where('ID',$request->ID)->update($data);

            $nota = '';

            if ($stock_anterior->cons == "ps4") {
              $nota = "Antes tenía #$stock_anterior->stock_id $stock_anterior->titulo $stock_anterior->cons $stock_anterior->slot #$stock_anterior->cuentas_id";
            } else {
              $nota = "Antes tenía #$stock_anterior->stock_id $stock_anterior->titulo $stock_anterior->cons #$stock_anterior->cuentas_id";
            }

              $data = [];
              $data['id_ventas'] = $request->ID;
              $data['Notas'] = $nota;
              $data['Day'] = date('Y-m-d H:i:s');
              $data['usuario'] = session()->get('usuario')->Nombre;

              DB::table('ventas_notas')->insert($data);

            DB::commit();

            \Helper::messageFlash('Clientes','Stock modificado manualmente', 'alert_cliente');

            return redirect()->back();
          } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['Ha ocurrido un error inesperado. Vuelva a intentarlo por favor.']);
          }

          break;
      }
    }

    public function duplicarVenta($id)
    {

      /***  Stock Nuevo  ***/
      $obj = new \stdClass;
      $obj->console_1 = 'ps4';
      $obj->console_2 = 'ps3';
      $obj->title = 'plus-12-meses-slot';

      $stocks = Stock::showStock($obj)->get();
      /***     PS4 PRIMARIO     ***/

      $obj = new \stdClass;
      $obj->console = 'ps4';
      $obj->title = 'plus-12-meses-slot';
      $obj->type = 'primary';
      $ps4_primary_stocks =  Stock::primaryOrSecundaryConsole($obj)->get();

      //dd($stocks);
      /***     PS4 SECUNDARIO     ***/

      $obj = new \stdClass;
      $obj->console = 'ps4';
      $obj->title = 'plus-12-meses-slot';
      $obj->type = 'secundary';
      $ps4_secundary_stocks =  Stock::primaryOrSecundaryConsole($obj)->get();

      /***     PS3     ***/
      $ps3_stocks =  Stock::ps3();

      /***     PS3 Resetear    ***/
      $ps3_reset_stocks =  Stock::ps3Resetear();

      $id_ventas = $id;
      return view('ajax.customer.venta_duplicar_producto',
            compact(
              'stocks',
              'ps4_primary_stocks',
              'ps4_secundary_stocks',
              'ps3_reset_stocks',
              'ps3_stocks',
              'id_ventas'
            ));
    }

    public function ventasModificarProductos($id)
    {

      /***  Stock Nuevo  ***/
      $obj = new \stdClass;
      $obj->console_1 = 'ps4';
      $obj->console_2 = 'ps3';
      $obj->title = 'plus-12-meses-slot';

      $stocks = Stock::showStock($obj)->get();
      /***     PS4 PRIMARIO     ***/

      $obj = new \stdClass;
      $obj->console = 'ps4';
      $obj->title = 'plus-12-meses-slot';
      $obj->type = 'primary';
      $ps4_primary_stocks =  Stock::primaryOrSecundaryConsole($obj)->get();

      //dd($stocks);
      /***     PS4 SECUNDARIO     ***/

      $obj = new \stdClass;
      $obj->console = 'ps4';
      $obj->title = 'plus-12-meses-slot';
      $obj->type = 'secundary';
      $ps4_secundary_stocks =  Stock::primaryOrSecundaryConsole($obj)->get();

      /***     PS3     ***/
      $ps3_stocks =  Stock::ps3();

      /***     PS3 Resetear    ***/
      $ps3_reset_stocks =  Stock::ps3Resetear();

      $id_ventas = $id;
      return view('ajax.customer.ven_modificar_producto',
            compact(
              'stocks',
              'ps4_primary_stocks',
              'ps4_secundary_stocks',
              'ps3_reset_stocks',
              'ps3_stocks',
              'id_ventas'
            ));
    }

    public function confirmDuplicarVenta($consola, $titulo, $slot, $id_ventas)
    {
      $row_rsSTK = Stock::StockDisponible($consola,$titulo, $slot);
      $errors = [];
      $stk_ID = '';
      $consola = $consola;
      $titulo = $titulo;
      $slot = $slot;
      $id_ventas = $id_ventas;

      // dd($row_rsSTK);

      if(!is_array($row_rsSTK)) {
          $errors[] = "No hay stock disponibles.";
      } else {
        $stk_ID = $row_rsSTK[0]->ID_stk;
      }

      return view('ajax.customer.confirmDuplicarVenta', compact('errors', 'stk_ID', 'consola', 'titulo', 'slot', 'id_ventas'));

      
    }

    public function duplicarVentaStore($consola, $titulo, $slot, $id_ventas)
    {
      
      $row_rsSTK = Stock::StockDisponible($consola,$titulo, $slot);
      
      $stk_ID = $row_rsSTK[0]->ID_stk;
      
      // Obtengo los datos de la venta anterior y ventas_cobro para duplicar los datos.
      $venta_anterior = DB::table('ventas')->where('ID', $id_ventas)->first();
      $venta_cobro_anterior = DB::table('ventas_cobro')->where('ventas_id', $id_ventas)->first();

      DB::beginTransaction();

      $date = date('Y-m-d H:i:s');
      $vendedor = session()->get('usuario')->Nombre;

      try {
        
        $data = [];
        $data['clientes_id'] = $venta_anterior->clientes_id;
        $data['stock_id'] = $stk_ID;
        $data['order_item_id'] = $venta_anterior->order_item_id;
        $data['cons'] = $consola;
        $data['slot'] = $slot;
        $data['medio_venta'] = $venta_anterior->medio_venta;
        $data['order_id_ml'] = $venta_anterior->order_id_ml;
        $data['order_id_web'] = $venta_anterior->order_id_web;
        $data['estado'] = $venta_anterior->estado;
        $data['recup'] = 1;
        $data['Day'] = $date;
        $data['Day_modif'] = $date;
        $data['usuario'] = $vendedor;

        $idventa = DB::table('ventas')->insertGetId($data);

        $data = [];
        $data['ventas_id'] = $idventa;
        $data['medio_cobro'] = $venta_cobro_anterior->medio_cobro;
        $data['ref_cobro'] = $venta_cobro_anterior->ref_cobro;
        $data['precio'] = 0;
        $data['comision'] = 0;
        $data['Day'] = $date;
        $data['usuario'] = $vendedor;

        DB::table('ventas_cobro')->insert($data);


        DB::commit();

        \Helper::messageFlash('Clientes','Venta duplicada.', 'alert_cliente');

        return redirect()->back();

      } catch (Exception $e) {
        DB::rollback();
        return redirect()->back()->withErrors(['Ha ocurrido un error inesperado. Vuelva a intentarlo por favor.']);
      }
    }

    public function ventasModificarProductosStore($consola, $titulo, $slot, $id_ventas)
    {
      
      $row_rsSTK = Stock::StockDisponible($consola,$titulo, $slot);
      $link_PS = '';
      
      // $stk_ID = $row_rsSTK[0]->ID_stk;

      $errors = [];

      if(!is_array($row_rsSTK)) {
          // $errors[] = "No hay stock disponibles.";
        // return redirect()->back()->withErrors(["No hay stock disponible para el Juego: $titulo ($consola)"]);

        $venta = DB::table('ventas')->select('ID','order_item_id','order_id_web AS order_id','clientes_id')->where('ID',$id_ventas)->first();

        $existEmailCliente = DB::table('clientes')
                                   ->select(
                                       'ID',
                                       'nombre',
                                       'apellido',
                                       'email',
                                       'auto'
                                   )
                                   ->where('ID', $venta->clientes_id)
                                   ->first();

        $rsLink_PS = DB::table('cbgw_postmeta')
        ->select(
            DB::raw("GROUP_CONCAT(meta_value) as meta_value")
        )
        ->where('post_id', $venta->order_item_id)
        ->where('meta_key', 'link_ps')
        ->groupBy('post_id')
        ->first();

        $link_PS = '';
        
        if ($rsLink_PS) {
            
            $link_PS = $rsLink_PS->meta_value;
        }

        if (!$request->previousUrl) {
          $datos['venta'] = $venta;
          $datos['consola'] = $consola;
          $datos['titulo'] = $titulo;
          $datos['slot'] = $slot;
          $datos['tipo'] = "Reasignar";
          
          Mail::send('emails.sin_stock', $datos, function($message) use ($venta,$consola,$titulo,$slot)
          {
              $message->to("contacto@dixgamer.com", "Contacto")->subject(session()->get('usuario')->Nombre.", falta stock (reasignar)- $titulo ($consola) $slot - Pedido {$venta->order_id}");
          });
        }

        return view('sales.salesUpdateWeb', [
          "venta" => $venta,
          "consola" => $consola,
          "titulo" => $titulo,
          "slot" => $slot,
          "clientes" => $existEmailCliente,
          "linkPS" => $link_PS
        ]);
      } else {
        $stk_ID = $row_rsSTK[0]->ID_stk;
      }

      // dd($row_rsSTK);

      $stock_anterior = DB::table('stock AS s')
                          ->select(
                            'v.stock_id',
                            's.titulo',
                            's.cuentas_id',
                            'v.cons',
                            'v.slot',
                            'v.clientes_id'
                          )
                          ->leftjoin('ventas AS v', 's.ID', '=', 'v.stock_id')
                          ->where('v.ID',$id_ventas)->first();
      
      $clientes_id = $stock_anterior->clientes_id;

      $nota = '';

      if ($stock_anterior->cons == "ps4") {
        $nota = "Antes tenía #$stock_anterior->stock_id $stock_anterior->titulo $stock_anterior->cons $stock_anterior->slot #$stock_anterior->cuentas_id";
      } else {
        $nota = "Antes tenía #$stock_anterior->stock_id $stock_anterior->titulo $stock_anterior->cons #$stock_anterior->cuentas_id";
      }

      DB::beginTransaction();

      $date = date('Y-m-d H:i:s');
      $verificado = 'no';
      if (\Helper::validateAdministrator(session()->get('usuario')->Level)) {
        $verificado = 'si';
      }
      $vendedor = session()->get('usuario')->Nombre;

      $stock_anterior = DB::table('ventas')->where('ID',$id_ventas)->value('stock_id');

      try {
        DB::insert("INSERT INTO ventas_modif(ventas_id, clientes_id, stock_id, order_item_id, cons, slot, medio_venta, Day, Notas, verificado, usuario ) SELECT ID, clientes_id, stock_id, order_item_id, cons, slot, medio_venta, '$date', Notas, '$verificado', '$vendedor' FROM ventas WHERE ID=?", [$id_ventas]);

        $data = [];
        $data['stock_id'] = $stk_ID;
        $data['cons'] = $consola;
        $data['slot'] = $slot;
        $data['recup'] = 1;
        $data['Day_modif'] = date('Y-m-d H:i:s');

        DB::table('ventas')->where('ID', $id_ventas)->update($data);

          $data = [];
          $data['id_ventas'] = $id_ventas;
          $data['Notas'] = $nota;
          $data['Day'] = date('Y-m-d H:i:s');
          $data['usuario'] = session()->get('usuario')->Nombre;

          DB::table('ventas_notas')->insert($data);
          


        DB::commit();

        \Helper::messageFlash('Clientes','Venta de producto modificado.', 'alert_cliente');

        return redirect("clientes/$clientes_id");

      } catch (Exception $e) {
        DB::rollback();
        return redirect()->back()->withErrors(['Ha ocurrido un error inesperado. Vuelva a intentarlo por favor.']);
      }
    }

    public function ventasEliminar($id)
    {
      $ventas = DB::table('ventas')->select('ID','clientes_id')->where('ID',$id)->first();

      $ventasBaja = DB::table('ventas_baja')->select('ID')->where('ventas_id',$id)->first();

      return view('ajax.customer.ventas_eliminar', compact('ventas', 'ventasBaja'));
    }

    public function ventas_delete(Request $request)
    {

      DB::beginTransaction();

      $date = date('Y-m-d H:i:s');
      $verificado = 'no';
      if (\Helper::validateAdministrator(session()->get('usuario')->Level)) {
        $verificado = 'si';
      }
      $vendedor = session()->get('usuario')->Nombre;

      switch ($request->opt) {
        case 1:

          try {
            DB::insert("INSERT INTO ventas_baja(ventas_id, clientes_id, stock_id, order_item_id, cons, slot, medio_venta, Day, Notas, Day_baja, Notas_baja, verificado, usuario ) SELECT ID, clientes_id, stock_id, order_item_id, cons, slot, medio_venta, Day, Notas, '$date',?, '$verificado', '$vendedor' FROM ventas WHERE ID=?", [$request->Notas_baja, $request->ID]);

            DB::table('ventas')->where('ID', $request->ID)->delete();

            $data = [];
            $data['precio']='0';
            $data['comision']='0';

            DB::table('ventas_cobro')->where('ventas_id',$request->ID)->update($data);

            DB::commit();

            \Helper::messageFlash('Clientes','Venta y cobro eliminado.', 'alert_cliente');

            return redirect()->back();

          } catch (Exception $e) {
            DB::rollback();

            return redirect()->back()->withErrors(['Ha ocurrido un error inesperado. Vuelva a intentarlo por favor.']);
          }

          break;
        
        case 2:
          
          try {
            DB::insert("INSERT INTO ventas_modif(ventas_id, clientes_id, stock_id, order_item_id, cons, slot, medio_venta, Day, Notas, verificado, usuario ) SELECT ID, clientes_id, stock_id, order_item_id, cons, slot, medio_venta, '$date', Notas, '$verificado', '$vendedor' FROM ventas WHERE ID=?", [$request->ID]);

            DB::insert("INSERT INTO ventas_baja(ventas_id, clientes_id, stock_id, order_item_id, cons, slot, medio_venta, Day, Notas, Day_baja, Notas_baja, verificado, usuario ) SELECT ID, clientes_id, stock_id, order_item_id, cons, slot, medio_venta, Day, Notas, '$date',?, '$verificado', '$vendedor' FROM ventas WHERE ID=?", [$request->Notas_baja, $request->ID]);

            $data = [];
            $data['precio']='0';
            $data['comision']='0';

            DB::table('ventas_cobro')->where('ventas_id',$request->ID)->update($data);

            DB::commit();

            \Helper::messageFlash('Clientes','Cobro eliminado.', 'alert_cliente');

            return redirect()->back();

          } catch (Exception $e) {
            DB::rollback();

            return redirect()->back()->withErrors(['Ha ocurrido un error inesperado. Vuelva a intentarlo por favor.']);
          }

          break;

        case 3:

          try {

            DB::table('ventas')->where('ID', $request->ID)->delete();
            DB::commit();

            \Helper::messageFlash('Clientes','Venta eliminada.', 'alert_cliente');

            return redirect()->back();
          } catch (Exception $e) {
            DB::rollback();

            return redirect()->back()->withErrors(['Ha ocurrido un error inesperado. Vuelva a intentarlo por favor.']);
          }

          break;

      case 4:

          try {

            ## ELIMINANDO PRODUCTO.

            $stock_anterior = DB::table('stock AS s')
                                ->select(
                                  'v.stock_id',
                                  's.titulo',
                                  's.cuentas_id',
                                  'v.cons',
                                  'v.slot'
                                )
                                ->leftjoin('ventas AS v', 's.ID', '=', 'v.stock_id')
                                ->where('v.ID',$request->ID)->first();

            $nota = '';

            if ($stock_anterior->cons == "ps4") {
              $nota = "Antes tenía #$stock_anterior->stock_id $stock_anterior->titulo $stock_anterior->cons $stock_anterior->slot #$stock_anterior->cuentas_id";
            } else {
              $nota = "Antes tenía #$stock_anterior->stock_id $stock_anterior->titulo $stock_anterior->cons #$stock_anterior->cuentas_id";
            }

            if ($this->isNotStockDefault($stock_anterior->stock_id)) {
              $data = [];
              $data['stock_id'] = 1;
              $data['recup'] = 1;
              $data['cons'] = 'ps';
              $data['slot'] = 'No';
              $data['Day_modif'] = date('Y-m-d H:i:s');

              DB::table('ventas')->where('ID',$request->ID)->update($data);

              
              $data = [];
              $data['id_ventas'] = $request->ID;
              $data['Notas'] = $nota;
              $data['Day'] = date('Y-m-d H:i:s');
              $data['usuario'] = session()->get('usuario')->Nombre;

              DB::table('ventas_notas')->insert($data);
            }


            ## ELIMINANDO COBROS

            $cobros = DB::table('ventas_cobro')->where('ventas_id', $request->ID)->get();

            foreach ($cobros as $cobro) {

              if ($cobro->precio != 0 && $cobro->comision != 0) {
                $data = [];
                $data['precio']='0';
                $data['comision']='0';
                DB::table('ventas_cobro')->where('ID', $cobro->ID)->update($data);

                $notas = "Cobro eliminado #$cobro->ID ($cobro->medio_cobro), ref #$cobro->ref_cobro, +$cobro->precio - $cobro->comision";

                $data = [];
                $data['id_ventas'] = $cobro->ventas_id;
                $data['Notas'] = $notas;
                $data['Day'] = date('Y-m-d H:i:s');
                $data['usuario'] = session()->get('usuario')->Nombre;

                DB::table('ventas_notas')->insert($data);
              }
            }

            DB::commit();

            \Helper::messageFlash('Clientes','Venta y cobros eliminados.', 'alert_cliente');

            return redirect()->back();
          } catch (Exception $e) {
              DB::rollback();

              return redirect()->back()->withErrors(['Ha ocurrido un error inesperado. Vuelva a intentarlo por favor.']);
          }

          break;
      }
    }

    public function ventarQuitarProducto(Request $request, $id)
    {
      DB::beginTransaction();

      $slot = isset($request->slot) ? $request->slot : '';
      $consola = isset($request->cons) ? $request->cons : '';

      try {

        $stock_anterior = DB::table('stock AS s')
                            ->select(
                              'v.stock_id',
                              's.titulo',
                              's.cuentas_id',
                              'v.cons',
                              'v.slot'
                            )
                            ->leftjoin('ventas AS v', 's.ID', '=', 'v.stock_id')
                            ->where('v.ID',$id)->first();

        $nota = '';

        if ($stock_anterior->cons == "ps4") {
          $nota = "Antes tenía #$stock_anterior->stock_id $stock_anterior->titulo $stock_anterior->cons $stock_anterior->slot #$stock_anterior->cuentas_id";
        } else {
          $nota = "Antes tenía #$stock_anterior->stock_id $stock_anterior->titulo $stock_anterior->cons #$stock_anterior->cuentas_id";
        }

        $data = [];
        if ($slot != '' || $consola != '') {
          $data['stock_id'] = 2;
        } else {
          $data['stock_id'] = 1;
        }
        $data['cons'] = 'x';
        $data['slot'] = 'No';
        $data['recup'] = 1;
        $data['Day_modif'] = date('Y-m-d H:i:s');

        DB::table('ventas')->where('ID',$id)->update($data);

          
          $data = [];
          $data['id_ventas'] = $id;
          $data['Notas'] = $nota;
          $data['Day'] = date('Y-m-d H:i:s');
          $data['usuario'] = session()->get('usuario')->Nombre;

          DB::table('ventas_notas')->insert($data);

          if ($slot != '') {
            if ($slot == 'Primario') {
              $data = [];
              $data['id_ventas'] = $id;
              $data['Notas'] = 'PS4 no estaba activa';
              $data['Day'] = date('Y-m-d H:i:s');
              $data['usuario'] = session()->get('usuario')->Nombre;

              DB::table('ventas_notas')->insert($data);
            } elseif($slot == 'Secundario') {
              $data = [];
              $data['id_ventas'] = $id;
              $data['Notas'] = 'Posiblemente no está usando';
              $data['Day'] = date('Y-m-d H:i:s');
              $data['usuario'] = session()->get('usuario')->Nombre;

              DB::table('ventas_notas')->insert($data);
            }
          } elseif ($consola != '') {
            $data = [];
            $data['id_ventas'] = $id;
            $data['Notas'] = 'Parece que no usó slot de descarga';
            $data['Day'] = date('Y-m-d H:i:s');
            $data['usuario'] = session()->get('usuario')->Nombre;

            DB::table('ventas_notas')->insert($data);
          }


        DB::commit();

        \Helper::messageFlash('Clientes','Producto removido.', 'alert_cliente');

        return redirect()->back();
      } catch (Exception $e) {
          DB::rollback();

          return redirect()->back()->withErrors(['Ha ocurrido un error inesperado. Vuelva a intentarlo por favor.']);
      }
    }

    public function confirmUpdateProduct($consola, $titulo, $slot, $id_ventas)
    {
      $row_rsSTK = Stock::StockDisponible($consola,$titulo, $slot);
      $errors = [];
      $stk_ID = '';
      $consola = $consola;
      $titulo = $titulo;
      $slot = $slot;
      $id_ventas = $id_ventas;

      // dd($row_rsSTK);

      if(!is_array($row_rsSTK)) {
          $errors[] = "No hay stock disponibles.";
      } else {
        $stk_ID = $row_rsSTK[0]->ID_stk;
      }

      return view('ajax.customer.confirmUpdateProduct', compact('errors', 'stk_ID', 'consola', 'titulo', 'slot', 'id_ventas'));

      
    }

    public function storeML(Request $request)
    {
      $this->validate($request,[
        "ml_user" => "required"
      ],
      [
        "ml_user.required" => "Campo ML es obligatorio."
      ]);

      $data = [];
      $data['ml_user'] = strtoupper($request->ml_user);

      DB::beginTransaction();

      try {
        DB::table('clientes')->where('id',$request->id_cliente)->update($data);

        DB::commit();

        \Helper::messageFlash('Clientes','Usuario ML agregado.', 'alert_cliente');

        return redirect()->back();
      } catch (Exception $e) {
        DB::rollback();

        return redirect()->back()->withErrors(['Ha ocurrido un error inesperado. Vuelva a intentarlo por favor.']);
      }
    }

    public function ventasCobroModificar($id)
    {
      $ventas_cobro = DB::table('ventas_cobro AS vc')
                        ->select(
                          'vc.ID',
                          'medio_cobro',
                          'ref_cobro',
                          'precio',
                          'comision',
                          'vc.Notas',
                          'vc.ventas_id',
                          'v.clientes_id',
                          'v.medio_venta',
                          'vc.Day'
                        )
                        ->leftjoin('ventas AS v', 'vc.ventas_id', '=', 'v.ID')
                        ->where('vc.ID',$id)
                        ->first();

      return view('ajax.customer.ventas_modificar_cobro', compact('ventas_cobro'));
    }

    public function ventasCobroModificarStore(Request $request)
    {
      $this->validate($request,[
        "precio" => "required|numeric",
        "comision" => "required|numeric",
      ],
      [
        "precio.required" => "Precio es obligatorio.",
        "precio.numeric" => "Precio no es valido.",
        "comision.required" => "Comision es obligatorio.",
        "comision.numeric" => "Precio no es valido."
      ]);

      if (strpos($request->medio_cobro, "Mercado") !== false) {
        $this->validate($request,[
          "ref_cobro" => "required|numeric",
        ],
        [
          "ref_cobro.required" => "Ref. de cobro es obligatorio para MercadoPago",
          "ref_cobro.numeric" => "Ref. de cobro no es valido.",
        ]);
      }

      DB::beginTransaction();

      try {
        $data = [];
        $hora = date('H:i:s', strtotime($request->fecha_old));
        $data['medio_cobro'] = $request->medio_cobro;
        $data['Day'] = $request->Day . " $hora";
        $data['ref_cobro'] = $request->ref_cobro;
        $data['precio'] = $request->precio;
        $data['comision'] = $request->comision;
        $data['Notas'] = $request->Notas_cobro;

        DB::table('ventas_cobro')->where('ID',$request->ID)->update($data);

        DB::commit();

        \Helper::messageFlash('Clientes','Cobro de venta modificado.', 'alert_cliente');

        return redirect('clientes/'.$request->clientes_id);
      } catch (Exception $e) {
        DB::rollback();

        return redirect()->back()->withErrors(['Ha ocurrido un error inesperado. Vuelva a intentarlo por favor.']);
      }
    }

    public function addVentasCobro($id_ventas, $id_cliente)
    {
      $venta_stock = DB::table('ventas AS v')
                      ->select(
                        'v.ID',
                        'v.medio_venta',
                        'v.Day',
                        'v.clientes_id',
                        'v.stock_id',
                        'titulo',
                        'consola'
                      )
                      ->leftjoin('stock AS s', 'v.stock_id', '=', 's.ID')
                      ->where('v.ID',$id_ventas)
                      ->first();

      $cliente = DB::table('clientes')->where('ID',$id_cliente)->first();

      return view('ajax.customer.ventas_cobro_insertar', compact('venta_stock', 'cliente'));
    }

    public function addVentasCobroStore(Request $request)
    {
      $this->validate($request,[
        "precio" => "required|numeric",
        "comision" => "required|numeric",
      ],
      [
        "precio.required" => "Precio es obligatorio.",
        "precio.numeric" => "Precio no es valido.",
        "comision.required" => "Comision es obligatorio.",
        "comision.numeric" => "Precio no es valido."
      ]);

      if (strpos($request->medio_cobro, "Mercado") !== false) {
        $this->validate($request,[
          "ref_cobro" => "required|numeric",
        ],
        [
          "ref_cobro.required" => "Nº de cobro es obligatorio para MercadoPago",
          "ref_cobro.numeric" => "Nº de cobro no es valido.",
        ]);
      }

      DB::beginTransaction();

      try {
        $data = [];
        $data['ventas_id'] = $request->ventas_id;
        $data['medio_cobro'] = $request->medio_cobro;
        $data['ref_cobro'] = $request->ref_cobro;
        $data['precio'] = $request->precio;
        $data['comision'] = $request->comision;
        if (isset($request->Day)) {
          $hora = date('H:i:s');
          $data['Day'] = $request->Day . " " . $hora;
        } else {
          $data['Day'] = date('Y-m-d H:i:s');
        }
        
        $data['Notas'] = $request->Notas_cobro;
        $data['usuario'] = session()->get('usuario')->Nombre;

        DB::table('ventas_cobro')->insert($data);

        DB::commit();

        \Helper::messageFlash('Clientes','Cobro de venta agregada.', 'alert_cliente');

        return redirect('clientes/'.$request->clientes_id);
      } catch (Exception $e) {
        DB::rollback();

        return redirect()->back()->withErrors(['Ha ocurrido un error inesperado. Vuelva a intentarlo por favor.']);
      }
    }

    public function setEmailPrimary($id, $id_cliente)
    {
      $email = DB::table('clientes_email')->where('id', $id)->first();

      
      DB::beginTransaction();
      
      try {
        DB::table('clientes')->where('ID',$id_cliente)->update(['email' => $email->email]);
        
        DB::commit();
        \Helper::messageFlash('Clientes','Email actualizado.', 'alert_cliente');

        return redirect()->back();
      } catch (Exception $e) {
        DB::rollback();

        return redirect()->back()->withErrors(['Ha ocurrido un error inesperado. Vuelva a intentarlo por favor.']);
      }
    }

    public function createCustomerWeb($oii)
    {
      $cliente = DB::table('cbgw_woocommerce_order_items AS wco')
                    ->select(
                      'wco.order_item_id',
                      'wco.order_id',
                      'p.ID as post_id',
                      'p.post_status as estado',
                      DB::raw("(SELECT meta_value FROM cbgw_postmeta WHERE meta_key='_billing_email' AND post_id=p.ID) AS email"),
                      DB::raw("(SELECT meta_value FROM cbgw_postmeta WHERE meta_key='_billing_first_name' AND post_id=p.ID) AS nombre"),
                      DB::raw("(SELECT meta_value FROM cbgw_postmeta WHERE meta_key='_billing_last_name' AND post_id=p.ID) AS apellido"),
                      DB::raw("(SELECT meta_value FROM cbgw_postmeta WHERE meta_key='_billing_country' AND post_id=p.ID) AS pais"),
                      DB::raw("(SELECT meta_value FROM cbgw_postmeta WHERE meta_key='_billing_state' AND post_id=p.ID) AS provincia"),
                      DB::raw("(SELECT meta_value FROM cbgw_postmeta WHERE meta_key='_billing_city' AND post_id=p.ID) AS ciudad"),
                      DB::raw("(SELECT meta_value FROM cbgw_postmeta WHERE meta_key='_billing_phone' AND post_id=p.ID) AS tel"),
                      DB::raw("(SELECT meta_value FROM cbgw_postmeta WHERE meta_key='user_id_ml' AND post_id=p.ID) AS user_id_ml")
                    )
                    ->leftjoin('cbgw_posts AS p','wco.order_id','=','p.ID')
                    ->leftjoin('cbgw_postmeta AS pm','wco.order_id','=','pm.post_id')
                    ->leftjoin('cbgw_woocommerce_order_itemmeta AS wcom','wco.order_item_id','=','wcom.order_item_id')
                    ->where('wco.order_item_id',$oii)
                    ->first();

      if ($cliente) {
        $apellido = ucwords(strtolower($cliente->apellido));
        $nombre = ucwords(strtolower($cliente->nombre));;
        if ($cliente->pais == "AR"): $pais = "Argentina"; else: $pais = $cliente->pais; endif;
        if ($cliente->provincia === "C"): $prov = "Ciudad Autónoma de Buenos Aires";
        elseif ($cliente->provincia === "B"): $prov = "Buenos Aires";
        elseif ($cliente->provincia === "K"): $prov = "Catamarca";
        elseif ($cliente->provincia === "H"): $prov = "Chaco";
        elseif ($cliente->provincia === "U"): $prov = "Chubut";
        elseif ($cliente->provincia === "X"): $prov = "Córdoba";
        elseif ($cliente->provincia === "W"): $prov = "Corrientes";
        elseif ($cliente->provincia === "E"): $prov = "Entre Ríos";
        elseif ($cliente->provincia === "P"): $prov = "Formosa";
        elseif ($cliente->provincia === "Y"): $prov = "Jujuy";
        elseif ($cliente->provincia === "L"): $prov = "La Pampa";
        elseif ($cliente->provincia === "F"): $prov = "La Rioja";
        elseif ($cliente->provincia === "M"): $prov = "Mendoza";
        elseif ($cliente->provincia === "N"): $prov = "Misiones";
        elseif ($cliente->provincia === "Q"): $prov = "Neuquén";
        elseif ($cliente->provincia === "R"): $prov = "Río Negro";
        elseif ($cliente->provincia === "A"): $prov = "Salta";
        elseif ($cliente->provincia === "J"): $prov = "San Juan";
        elseif ($cliente->provincia === "D"): $prov = "San Luis";
        elseif ($cliente->provincia === "Z"): $prov = "Santa Cruz";
        elseif ($cliente->provincia === "S"): $prov = "Santa Fe";
        elseif ($cliente->provincia === "G"): $prov = "Santiago del Estero";
        elseif ($cliente->provincia === "V"): $prov = "Tierra del Fuego";
        elseif ($cliente->provincia === "T"): $prov = "Tucumán"; 
        else: $prov = ucwords(strtolower($cliente->provincia)); endif;
        $ciudad = ucwords(strtolower($cliente->ciudad));
        $tel = $cliente->tel;
        $email = strtolower($cliente->email);
        $user_id_ml = $cliente->user_id_ml;

        $data = [];
        $data['apellido'] = $apellido;
        $data['nombre'] = $nombre;
        $data['pais'] = $pais;
        $data['provincia'] = $prov;
        $data['ciudad'] = $ciudad;
        $data['tel'] = $tel;
        $data['email'] = strtolower($email);
        if ($user_id_ml && $user_id_ml != "") {
          $data['ml_user'] = $user_id_ml;
        }
        $data['usuario'] = session()->get('usuario')->Nombre;

        DB::beginTransaction();

        try {
          $clientes_id = DB::table('clientes')->insertGetId($data);
          DB::table('clientes_email')->insert(["clientes_id"=> $clientes_id, "email" => strtolower($email)]);
          DB::commit();

          \Helper::messageFlash('Clientes','Cliente creado exitosamente.');

          return redirect("web/sales/".$cliente->post_id);
        } catch (Exception $e) {
          DB::rollback();
          return redirect()->back()->withErrors(['Ha ocurrido un error inesperado. Vuelva a intentarlo por favor']);
        }
      }
    }

    public function emails($ventas_id, $tipo, $consola=null, $slot=null, $cuentas_id=null)
    {

      $vista = '';
      $data = [];
      $correo = '';
      $subject = '';
      $nombre = '';

      switch ($tipo) {
        case 'Juegos':
          $vista = 'mail_datos_'.$consola.$slot;
          $row_rsClient = DB::table('stock AS s')
                              ->select(
                                'ID AS ID_stock',
                                'titulo',
                                'consola',
                                'cuentas_id',
                                'c.*'
                              )
                              ->rightjoin(DB::raw("(SELECT ventas.ID AS ID_ventas, clientes_id, stock_id, slot, estado, Day, clientes.ID AS ID_clientes, apellido, nombre, email, order_id_web AS pedido
                                FROM ventas
                                LEFT JOIN
                                clientes
                                ON ventas.clientes_id = clientes.ID) AS c"), 's.ID','=','c.stock_id')
                              ->where('ID_ventas', $ventas_id)
                              ->orderBy('c.Day', 'DESC')
                              ->first();

          $row_rsCuenta = DB::table('cuentas')->select('ID','mail_fake','pass')->where('ID',$cuentas_id)->first();

          $titulo = ucwords(preg_replace('/([-])/'," ",$row_rsClient->titulo));
          $subject = '🔥 [Nueva Compra] '.$titulo.' ('.$row_rsClient->consola.') (wc-' . $row_rsClient->pedido . '-s' . $row_rsClient->ID_stock.')';

          $correo = $row_rsClient->email;
          $nombre = $row_rsClient->nombre . ' ' . $row_rsClient->apellido;

          $data['row_rsClient'] = $row_rsClient;
          $data['row_rsCuenta'] = $row_rsCuenta;

          break;
        
        case 'Gift':
          
          $vista = 'mail_datos_gift';
          $row_rsClient = DB::table('stock AS s')
                              ->select(
                                'ID AS ID_stock',
                                'titulo',
                                'consola',
                                'code',
                                'cuentas_id',
                                'c.*'
                              )
                              ->rightjoin(DB::raw("(SELECT ventas.ID AS ID_ventas, clientes_id, stock_id, slot, estado, Day, clientes.ID AS ID_clientes, apellido, nombre, email, order_id_web AS pedido
                                FROM ventas
                                LEFT JOIN
                                clientes
                                ON ventas.clientes_id = clientes.ID) AS c"), 's.ID','=','c.stock_id')
                              ->where('ID_ventas', $ventas_id)
                              ->orderBy('c.Day', 'DESC')
                              ->first();

          $titulo = ucwords(preg_replace('/([-])/'," ",$row_rsClient->titulo));
          $subject = '🔥 [Nueva Compra] '.$titulo.' ('.$row_rsClient->consola.') (wc-' . $row_rsClient->pedido . '-s' . $row_rsClient->ID_stock.')';

          $correo = $row_rsClient->email;
          $nombre = $row_rsClient->nombre . ' ' . $row_rsClient->apellido;

          $data['row_rsClient'] = $row_rsClient;

            break;
        case 'Plus':

          $vista = 'mail_datos_plus';
          $row_rsClient = DB::table('stock AS s')
                              ->select(
                                'ID AS ID_stock',
                                'titulo',
                                'consola',
                                'code',
                                'cuentas_id',
                                'c.*'
                              )
                              ->rightjoin(DB::raw("(SELECT ventas.ID AS ID_ventas, clientes_id, stock_id, slot, estado, Day, clientes.ID AS ID_clientes, apellido, nombre, email, order_id_web AS pedido
                                FROM ventas
                                LEFT JOIN
                                clientes
                                ON ventas.clientes_id = clientes.ID) AS c"), 's.ID','=','c.stock_id')
                              ->where('ID_ventas', $ventas_id)
                              ->orderBy('c.Day', 'DESC')
                              ->first();

          $titulo = ucwords(preg_replace('/([-])/'," ",$row_rsClient->titulo));
          $subject = '🔥 [Nueva Compra] '.$titulo.' ('.$row_rsClient->consola.') (wc-' . $row_rsClient->pedido . '-s' . $row_rsClient->ID_stock.')';

          $correo = $row_rsClient->email;
          $nombre = $row_rsClient->nombre . ' ' . $row_rsClient->apellido;

          $data['row_rsClient'] = $row_rsClient;

          break;

        case 'FifaPoints':

          $vista = 'mail_datos_fifapoints_ps4';
          $row_rsClient = DB::table('stock AS s')
                              ->select(
                                'ID AS ID_stock',
                                'titulo',
                                'consola',
                                'code',
                                'cuentas_id',
                                'c.*'
                              )
                              ->rightjoin(DB::raw("(SELECT ventas.ID AS ID_ventas, clientes_id, stock_id, slot, estado, Day, clientes.ID AS ID_clientes, apellido, nombre, email, order_id_web AS pedido
                                FROM ventas
                                LEFT JOIN
                                clientes
                                ON ventas.clientes_id = clientes.ID) AS c"), 's.ID','=','c.stock_id')
                              ->where('ID_ventas', $ventas_id)
                              ->orderBy('c.Day', 'DESC')
                              ->first();

          $titulo = ucwords(preg_replace('/([-])/'," ",$row_rsClient->titulo));
          $subject = '🔥 [Nueva Compra] '.$titulo.' ('.$row_rsClient->consola.') (wc-' . $row_rsClient->pedido . '-s' . $row_rsClient->ID_stock.')';

          $correo = $row_rsClient->email;
          $nombre = $row_rsClient->nombre . ' ' . $row_rsClient->apellido;

          $data['row_rsClient'] = $row_rsClient;

          break;
      }

      try {

        DB::table('mailer')->insert([
          'ventas_id' => $ventas_id,
          'concepto' => 'datos1',
          'Day' => date('Y-m-d H:i:s'),
          'usuario' => session()->get('usuario')->Nombre
        ]);

        Mail::send('emails.'.$vista, $data, function($message) use ($correo, $nombre, $subject)
        {
            $message->to($correo, $nombre)->subject($subject);
        });

        \Helper::messageFlash('Clientes','Correo electronico enviado.', 'alert_cliente');
        return redirect()->back();
      } catch (Exception $e) {
        return redirect()->back()->withErrors(['Ha ocurrido un error inesperado en el envío del email.']);
      }

      

      
    }

    public function updateAmounts($cobro,$cliente_id)
    {

      $ref_cobro_count = DB::table('ventas_cobro')->where('ref_cobro', $cobro)->count();
      /*$cliente_id = DB::table('ventas_cobro')->select('v.clientes_id')->join('ventas AS v','v.ID','=','ventas_cobro.ventas_id')->where('ref_cobro', $cobro)->value('clientes_id');*/
      $amounts = DB::table('mercadopago')->where('ref_op', $cobro)->get();

      if ($ref_cobro_count > 1) {
        /*\Helper::messageFlash('Clientes',"Existe más de un registro con ésta ref. de cobro", "warning", 'alert_cliente');
        return redirect('clientes/'.$cliente_id);*/

        if (count($amounts) > 0) {
          $data = [];
          foreach ($amounts as $amount) {
            if (strpos($amount->concepto, 'Costo') !== false || strpos($amount->concepto, 'Comisión') !== false) {
              $comision = ((-1) * $amount->importe);
            } elseif(strpos($amount->concepto, 'Cobro') !== false) {

              $precio = $amount->importe;
            }
          }

          if (!isset($comision)) { // Por si no existe la comision en mercadopago, le coloco 0 (cero)
            $comision = 0;
          }

          $precio = $precio / $ref_cobro_count;
          $comision = $comision / $ref_cobro_count;

          DB::beginTransaction();

          try {
            $ventas = DB::table('ventas_cobro')->where('ref_cobro', $cobro)->get();

            $ventas_ids = [];

            foreach ($ventas as $venta) {
              $ventas_ids[] = $venta->ventas_id;
            }

            $data['precio'] = $precio;
            $data['comision'] = $comision;

            DB::table('ventas_cobro')->whereIn('ventas_id', $ventas_ids)->update($data);
            DB::commit();

            $mensaje = 'Importes de MP actualizados y dividido en las ventas';

            foreach ($ventas_ids as $value) {
              $mensaje .= " #$value,";
            }

            \Helper::messageFlash('Clientes',trim($mensaje, ","), 'alert_cliente');
            return redirect('clientes/'.$cliente_id);

          } catch (Exception $e) {
            DB::rollback();
            return redirect('clientes/'.$cliente_id)->withErrors(['Ha ocurrido un error inesperado. Por favor intentalo de nuevo.']);
          }
        } else {
          return redirect('clientes/'.$cliente_id)->withErrors(["La referencia de cobro no existe en nuestra BD de mercado pago"]);
        }


      } else {
        if (count($amounts) > 0) {
          $data = [];
          foreach ($amounts as $amount) {
            if (strpos($amount->concepto, 'Costo') !== false || strpos($amount->concepto, 'Comisión') !== false) {
              $data['comision'] = ((-1) * $amount->importe);
            } elseif(strpos($amount->concepto, 'Cobro') !== false) {

              $data['precio'] = $amount->importe;
            }
          }

          if (!isset($data['comision'])) { // Por si no existe la comision en mercadopago, le coloco 0 (cero)
            $data['comision'] = 0;
          }

          DB::beginTransaction();

          try {
            DB::table('ventas_cobro')->where('ref_cobro', $cobro)->update($data);
            DB::commit();

            \Helper::messageFlash('Clientes','Importes de MP actualizados al cobro #'.$cobro, 'alert_cliente');
            return redirect('clientes/'.$cliente_id);

          } catch (Exception $e) {
            DB::rollback();
            return redirect('clientes/'.$cliente_id)->withErrors(['Ha ocurrido un error inesperado. Por favor intentalo de nuevo.']);
          }
        } else {
          return redirect('clientes/'.$cliente_id)->withErrors(["La referencia de cobro no existe en nuestra BD de mercado pago"]);
        }

        
      }
    }

    public function deleteAmount($id)
    {
      DB::beginTransaction();

      try {
        $cobro = DB::table('ventas_cobro')->where('ID', $id)->first();
        DB::table('ventas_cobro')->where('ID', $id)->delete();

        $notas = "Cobro eliminado #$cobro->ID ($cobro->medio_cobro), ref #$cobro->ref_cobro, +$cobro->precio - $cobro->comision";

        $data = [];
        $data['id_ventas'] = $cobro->ventas_id;
        $data['Notas'] = $notas;
        $data['Day'] = date('Y-m-d H:i:s');
        $data['usuario'] = session()->get('usuario')->Nombre;

        DB::table('ventas_notas')->insert($data);
        DB::commit();

        \Helper::messageFlash('Clientes','Cobro eliminado exitosamente.', 'alert_cliente');
        return redirect()->back();
      } catch (Exception $e) {
        return redirect()->back()->withErrors(['Ha ocurrido un error inesperado. Por favor intentalo de nuevo.']);
      }
    }

    private function isNotStockDefault($stock_id)
    {
      return $stock_id != 1 || false;
    }

    public function deleteNotes($id,$tipo)
    {
      if ($id && $tipo) {
        DB::table($tipo."_notas")->where('ID',$id)->delete();

        \Helper::messageFlash('Clientes','Nota de '.ucwords($tipo).' eliminada exitosamente.', 'alert_cliente');
        return redirect()->back();
      } else {
        return redirect()->back()->withErrors(['Ha ocurrido un error inesperado. Por favor intentalo de nuevo.']);
      }
    }

    public function saleToClient($id_stock, $consola, $slot) {
      $cliente_id = $consola == 'ps3' ? 2 : 1;

      DB::beginTransaction();

      try {
        # DATA PARA TABLA VENTAS.
        $data['clientes_id'] = $cliente_id;
        $data['stock_id'] = $id_stock;
        $data['cons'] = $consola;
        $data['slot'] = $slot;
        $data['medio_venta'] = 'Mail';
        $data['estado'] = 'listo';
        $data['Day'] = date('Y-m-d H:i:s');
        $data['Day_modif'] = date('Y-m-d H:i:s');
        $data['usuario'] = session()->get('usuario')->Nombre;

        $venta_ID = DB::table('ventas')->insertGetId($data);

        #DATA PARA TABLA VENTAS COBRO.
        $data = [
          'ventas_id'     => $venta_ID,
          'medio_cobro'   => 'Banco',
          'precio'        => '0',
          'comision'      => '0',
          'Day'           => date('Y-m-d H:i:s'),
          'usuario'       => session()->get('usuario')->Nombre
        ];

        DB::table('ventas_cobro')->insert($data);

        $data = [
          "id_ventas" => $venta_ID,
          "Notas" => "Venta generada por sistema.",
          "Day" => date('Y-m-d H:i:s'),
          "usuario" => session()->get('usuario')->Nombre
        ];

        DB::table('ventas_notas')->insert($data);

        DB::commit();

        \Helper::messageFlash('Clientes','Venta agregada a Cliente #'.$cliente_id, 'alert_cliente');
        return redirect('clientes/'.$cliente_id);
      } catch (Exception $e) {
        DB::rollback();
        return redirect()->back()->withErrors(['Ha ocurrido un error inesperado. Por favor intentalo de nuevo.']);
      }

    }

    public function marcarEnviadoVenta($id_venta) {
        DB::beginTransaction();

        try {
          $data['ventas_id'] = $id_venta;
          $data['concepto'] = "datos1";
          $data['Day'] = date('Y-m-d H:i:s');
          $data['usuario'] = session()->get('usuario')->Nombre;

          DB::table('mailer')->insert($data);

          $data2['id_ventas'] = $id_venta;
          $data2['Notas'] = "Marcar como enviado";
          $data2['Day'] = date('Y-m-d H:i:s');
          $data2['usuario'] = session()->get('usuario')->Nombre;

          DB::table('ventas_notas')->insert($data2);

          DB::commit();

          \Helper::messageFlash('Clientes',"La venta se ha marcado como enviada exitosamente.", 'alert_cliente');

          return redirect()->back();

        } catch (Exception $th) {
          DB::rollback();
          return redirect()->back()->withErrors(['Ha ocurrido un error inesperado. Por favor intentalo de nuevo.']);
        }
    }

    public function loadNotesSales($id_venta)
    {
      $ids = [$id_venta];

      $notas = $this->buildNotesSales($ids,true);

      echo json_encode(["notas"=>$notas]);
    }
}

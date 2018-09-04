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
class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
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
      $customers = Customer::customerByEmail($email)->customerByCustomColumn($obj)->orderBy('ID','DESC')->paginate($paginate);

      return view('customer.index',compact('customers','columns'));
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
        'ml_user.unique' => 'Usuario mercado libre ya existe',
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
      $v = Validator::make($request->all(), [
          'email' => 'required|email|unique:clientes,email',
          'apellido' => 'required',
          'nombre' => 'required',
          'ml_user' => 'unique:clientes,ml_user',
          'pais' => 'required',
          'provincia' => 'required',
      ], $msgs);

      // Si hay errores retornamos a la pantalla anterior con los mensajes
      if ($v->fails())
      {
          return redirect()->back()->withErrors($v->errors());
      }


      try {
        // Creamos el arreglo del usuario
        $customer = [];
        $customer['email'] = $request->email;
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
        $c->storeCustomer($customer);

        // Mensaje de notificacion
        \Helper::messageFlash('Clientes','Cliente guardado');
        return redirect('clientes?column=email&word='.$request->email);
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


      $ventas_notas = DB::table('ventas_notas')->whereIn('id_ventas',$id_ventas)->orderBy('Day','DESC')->get();

      return view('customer.show',compact(
        'customer',
        'dataCustomers',
        'salesByCustomer',
        'lowSalesByCustomerIds',
        'expensesIncome',
        'customerNotes',
        'ventas_notas',
        'othersEmails'
      ));
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
        $cliente = DB::table('clientes')->where('ID',$request->id)->update(['auto' => $request->datos]);

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

      // Pasamos los filtros a la busqueda
      $customer = DB::table('clientes AS c')->leftjoin('clientes_ml_user AS cml','c.id','=','cml.clientes_id')
                    ->where('cml.ml_user',$request->ml_user)->first();
      if ($customer) {
        echo true;
      }else{
        echo false;
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

            \Helper::messageFlash('Clientes','Venta asignada.');

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

            \Helper::messageFlash('Clientes','Venta modificada.');

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

            \Helper::messageFlash('Clientes','Venta modificada.');

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

            \Helper::messageFlash('Clientes','Venta modificada.');

            return redirect()->back();

          } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['Ha ocurrido un error inesperado. Vuelva a intentarlo por favor.']);
          }

          break;
      }
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

    public function ventasModificarProductosStore($consola, $titulo, $slot, $id_ventas)
    {
      
      $row_rsSTK = Stock::StockDisponible($consola,$titulo, $slot);
      
      $stk_ID = $row_rsSTK[0]->ID_stk;

      $stock_anterior = DB::table('stock AS s')
                          ->select(
                            'v.stock_id',
                            's.titulo',
                            'v.cons',
                            'v.slot'
                          )
                          ->leftjoin('ventas AS v', 's.ID', '=', 'v.stock_id')
                          ->where('v.ID',$id_ventas)->first();

      $nota = '';

      if ($stock_anterior->cons == "ps4") {
        $nota = "Antes tenía #$stock_anterior->stock_id $stock_anterior->titulo $stock_anterior->cons $stock_anterior->slot";
      } else {
        $nota = "Antes tenía #$stock_anterior->stock_id $stock_anterior->titulo $stock_anterior->cons";
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

        DB::table('ventas')->where('ID', $id_ventas)->update($data);

        $data = [];
        $data['id_ventas'] = $id_ventas;
        $data['Notas'] = $nota;
        $data['Day'] = date('Y-m-d H:i:s');
        $data['usuario'] = session()->get('usuario')->Nombre;

        DB::table('ventas_notas')->insert($data);

        DB::commit();

        \Helper::messageFlash('Clientes','Venta de producto modificado.');

        return redirect()->back();

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

            \Helper::messageFlash('Clientes','Venta y cobro eliminado.');

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

            \Helper::messageFlash('Clientes','Cobro eliminado.');

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

            \Helper::messageFlash('Clientes','Venta eliminada.');

            return redirect()->back();
          } catch (Exception $e) {
            DB::rollback();

            return redirect()->back()->withErrors(['Ha ocurrido un error inesperado. Vuelva a intentarlo por favor.']);
          }

          break;
      }
    }

    public function ventarQuitarProducto($id)
    {
      DB::beginTransaction();

      try {

        $stock_anterior = DB::table('stock AS s')
                            ->select(
                              'v.stock_id',
                              's.titulo',
                              'v.cons',
                              'v.slot'
                            )
                            ->leftjoin('ventas AS v', 's.ID', '=', 'v.stock_id')
                            ->where('v.ID',$id)->first();

        $nota = '';

        if ($stock_anterior->cons == "ps4") {
          $nota = "Antes tenía #$stock_anterior->stock_id $stock_anterior->titulo $stock_anterior->cons $stock_anterior->slot";
        } else {
          $nota = "Antes tenía #$stock_anterior->stock_id $stock_anterior->titulo $stock_anterior->cons";
        }

        $data = [];
        $data['stock_id'] = 1;
        $data['cons'] = 'ps';
        $data['slot'] = 'No';

        DB::table('ventas')->where('ID',$id)->update($data);

        $data = [];
        $data['id_ventas'] = $id;
        $data['Notas'] = $nota;
        $data['Day'] = date('Y-m-d H:i:s');
        $data['usuario'] = session()->get('usuario')->Nombre;

        DB::table('ventas_notas')->insert($data);

        DB::commit();

        \Helper::messageFlash('Clientes','Producto removido.');

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
      $data['ml_user'] = $request->ml_user;

      DB::beginTransaction();

      try {
        DB::table('clientes')->where('id',$request->id_cliente)->update($data);

        DB::commit();

        \Helper::messageFlash('Clientes','Usuario ML agregado.');

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
                          'v.medio_venta'
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
        $data['medio_cobro'] = $request->medio_cobro;
        $data['ref_cobro'] = $request->ref_cobro;
        $data['precio'] = $request->precio;
        $data['comision'] = $request->comision;
        $data['Notas'] = $request->Notas_cobro;

        DB::table('ventas_cobro')->where('ID',$request->ID)->update($data);

        DB::commit();

        \Helper::messageFlash('Clientes','Cobro de venta modificado.');

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
        $data['Day'] = date('Y-m-d H:i:s');
        $data['Notas'] = $request->Notas_cobro;
        $data['usuario'] = session()->get('usuario')->Nombre;

        DB::table('ventas_cobro')->insert($data);

        DB::commit();

        \Helper::messageFlash('Clientes','Cobro de venta agregada.');

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
        \Helper::messageFlash('Clientes','Email actualizado.');

        return redirect()->back();
      } catch (Exception $e) {
        DB::rollback();

        return redirect()->back()->withErrors(['Ha ocurrido un error inesperado. Vuelva a intentarlo por favor.']);
      }
    }
}

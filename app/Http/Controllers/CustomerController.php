<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Sale;
use App\Expenses;
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
        'ml_user.required' => 'ml_user requerido',
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
          'ml_user' => 'required|unique:clientes,ml_user',
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




      return view('customer.show',compact(
        'customer',
        'dataCustomers',
        'salesByCustomer',
        'lowSalesByCustomerIds',
        'expensesIncome',
        'customerNotes'
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
      if (count($customer) > 0) {
        echo true;
      }else{
        echo false;
      }
    }

    public function customerCtrlMlUsr(Request $request){
      // Clientes con filtro
      $obj = new \stdClass;
      $obj->column = 'ml_user';
      $obj->word = $request->ml_user;

      // Pasamos los filtros a la busqueda
      $customer = Customer::customerCustomColumn($obj)->first();
      if (count($customer) > 0) {
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

            $data = [];
            $data['clientes_id'] = $request->clientes_id;

            DB::table('ventas')->where('ID', $request->ID)->update($data);
            DB::commit();

            \Helper::messageFlash('Clientes','Venta modificada.');

            return redirect()->back();

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

            $order_item_id = isset($order_item_id) ? $request->order_item_id : '';
            $order_id_web = isset($order_id_web) ? $request->order_id_web : '';
            $order_id_ml = isset($order_id_ml) ? $request->order_id_ml : '';

            $data = [];
            $data['order_item_id'] = $order_item_id;
            $data['order_id_web'] = $order_id_web;
            $data['order_id_ml'] = $order_id_ml;

            DB::table('ventas')->where('ID', $request->ID)->update($data);
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
            $data['Notas'] = $request->Notas;

            DB::table('ventas')->where('ID', $request->ID)->update($data);
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
      $clientes = Customer::infoCustomerVentasProductos($id);
      $stocks = DB::table('stock')->select('ID')->orderBy('ID')->get();
      $data_set = array();

      foreach ($stocks as $stock) {
        $data_set[] = $stock->ID;
      }

      return view('ajax.customer.ventas_modificar_producto', compact('clientes', 'data_set'));
    }

    public function ventasModificarProductosStore(Request $request)
    {
      $this->validate($request,[
        "stock_id" => "required"
      ],
      [
        "stock_id.required" => "ID stock es un campo obligatorio."
      ]);
      
      DB::beginTransaction();

      $date = date('Y-m-d H:i:s');
      $verificado = 'no';
      if (\Helper::validateAdministrator(session()->get('usuario')->Level)) {
        $verificado = 'si';
      }
      $vendedor = session()->get('usuario')->Nombre;

      try {
        DB::insert("INSERT INTO ventas_modif(ventas_id, clientes_id, stock_id, order_item_id, cons, slot, medio_venta, Day, Notas, verificado, usuario ) SELECT ID, clientes_id, stock_id, order_item_id, cons, slot, medio_venta, '$date', Notas, '$verificado', '$vendedor' FROM ventas WHERE ID=?", [$request->ID]);

        $data = [];
        $data['stock_id'] = $request->stock_id;
        $data['cons'] = $request->cons;
        $data['slot'] = $request->slot;
        $data['Notas'] = $request->Notas;

        DB::table('ventas')->where('ID', $request->ID)->update($data);
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

        $data = [];
        $data['stock_id'] = 1;
        $data['cons'] = 'ps';
        $data['slot'] = 'No';

        DB::table('ventas')->where('ID',$id)->update($data);

        DB::commit();

        \Helper::messageFlash('Clientes','Producto removido.');

        return redirect()->back();
      } catch (Exception $e) {
          DB::rollback();

          return redirect()->back()->withErrors(['Ha ocurrido un error inesperado. Vuelva a intentarlo por favor.']);
      }
    }
}

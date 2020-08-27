<div class="container">
    <h1 style="color: #000">Insertar Balance Sony</h1>

    <div class="row">

        <div class="col-md-4"></div>
        <div class="col-md-4">
            <form action="{{route('balance-sony-store')}}" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="cuenta_id" value="{{$account_id}}">

                <div class="input-group form-group">
                    <span class="input-group-addon"><i class="fa fa-dollar fa-fw"></i></span>
                    <input type="number" class="form-control" name="balance" id="balance" placeholder="Ingrese Balance Sony">
                </div>

                <button class="btn btn-primary btn-block btn-lg" type="submit">Insertar</button>
                <br>
            </form>
        </div>
        <div class="col-md-4"></div>
    </div>
</div>
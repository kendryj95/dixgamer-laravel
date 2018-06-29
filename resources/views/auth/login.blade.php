@extends('layouts.login-layouts')

@section('container')
<div class="container">
    <div class="row">
        @if (count($errors) > 0)
              <div class="alert alert-danger text-center">
                <ul>
                  @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
        @endif
        <div class="col-md-4 col-md-offset-4">
            <div class="panel panel-default">
                <div class="panel-heading text-center">DixGamer</div>

                <div class="panel-body">
                    <form class="form-horizontal" method="POST" action="">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label for="Nombre" class="col-md-4 control-label">Nombre</label>

                            <div class="col-md-6">
                                <input type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus>


                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label">Contraseña</label>

                            <div class="col-md-6">
                                <input type="password" class="form-control" name="password" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-12">
                                <button name"submit" class="btn btn-lg btn-primary btn-block" type="submit">Ingresar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

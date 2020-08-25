@extends('layouts.master-layouts')

@section('title', 'Enviar Email')

@section('container')
    <div class="container text-center">
        <h1>Enviar Email</h1>
        @if (count($errors) > 0)
            <div class="alert alert-danger text-center">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="row">
            <div class="col-sm-3">
            </div>

            <div class="col-sm-6">
                <form method="post" name="form1" action='{{ route('enviar-email-post') }}'>
                    {{ csrf_field() }}
                    <div id="user-result-div" class="input-group form-group">
                        <span class="input-group-addon"><i class="fa fa-user fa-fw"></i></span>
                        <input class="form-control" type="text" name="name" id="name" autocomplete="off" spellcheck="false" placeholder="Nombre y Apellido" autofocus>
                    </div>

                    <div id="user-result-div" class="input-group form-group">
                        <span class="input-group-addon"><i class="fa fa-at fa-fw"></i></span>
                        <input class="form-control" type="email" name="email" id="email" autocomplete="off" spellcheck="false" placeholder="Email">
                    </div>

                    <div class="input-group form-group">
                        <span class="input-group-addon"><i class="fa fa-lock fa-fw"></i></span>
                        <input class="form-control" type="text" name="subject" autocomplete="off" placeholder="Asunto">
                    </div>

                    <div class="form-group">
                        <label for="">Descripción:</label>
                        <textarea name="description" id="form" cols="30" rows="10" class="form-control"></textarea>
                    </div>

                    <button class="btn btn-primary btn-block btn-lg" type="submit">Enviar</button>
                    <br>
                </form>
            </div>
            <div class="col-sm-3">
            </div>
        </div>
    </div><!--/.container-->
@endsection



@section('scripts')
    <script type="text/javascript">

        $(document).ready(function () {
            tinymce.init({
                selector: '#form',
                height: 150,
                plugins: [
                    'advlist lists preview',
                    'visualblocks',
                    'contextmenu paste link'
                ], //3 media 1 link image autolink
                toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link',
                language: 'es'
            });
        })

    </script>
@stop

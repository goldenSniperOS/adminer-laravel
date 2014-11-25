@extends('layouts.master')
@section('cabecera')
    @parent
    {{HTML::style("css/login.css")}}
@stop

@section('principal')
{{Form::open(array('url' => 'conectar', 'method' => 'post', 'class' => 'login well','id' =>'connect'))}}
            <p class="alert alert-danger hide" id="report">Configure bien su conexión</p>

            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
                    <input type="text" placeholder="Servidor" name="server" required autofocus class="form-control">
                </div>
            </div>
            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
                    <input type="text" placeholder="Usuario" name="user" required autofocus class="form-control">
                </div>
            </div>
            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
                    <input type="password" class="form-control" id="password" placeholder="Contraseña" name="password">
                </div>
            </div>
            <button class="btn btn-lg btn-primary btn-block btn-success" type="submit">Iniciar Sesión</button>
            <hr>
{{Form::close()}}
@stop

@section('scripts')
    <script type="text/javascript">
    $(function(){
        $('#connect').on('submit',function(){
            var server = $('input[name="server"]').val(),
            user = $('input[name="user"]').val(),
            pasword = $('input[name="pasword"]').val();
            $.ajax({
                url: '/conectar',
                method: 'POST',
                data:{
                        server: server,
                        user: user,
                        pasword: pasword
                    },
                error: function(data){
                    $('#report').removeClass('hide');
                    console.log(data);
                },
                success: function(data){
                    if (data.request = true) {
                        window.location = '/interfaz';
                    };
                    //
                    console.log(data);
                }
            });
            return false;
        });
    });
    </script>
@stop
@extends('layouts.master')
@section('cabecera')
    @parent
    {{HTML::style("css/login.css")}}
@stop


@section('principal')
{{Form::open(array('url' => 'conectar', 'method' => 'post', 'class' => 'login well','id' =>'connect'))}}
            <p class="alert alert-danger hide" id="report-connection">Configure bien su conexión</p>
            <p class="alert alert-danger hide" id="report-fields">Los campos de Servidor y Usuario son necesarios</p>
            <p class="alert alert-success hide" id="report-success">Conexión Realizada Correctamente</p>
            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-addon"><span class="glyphicon glyphicon-tasks"></span></span>
                    <input type="text" placeholder="Servidor" name="server" autofocus class="form-control">
                </div>
            </div>
            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
                    <input type="text" placeholder="Usuario" name="user" autofocus class="form-control">
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

        //Ajax de Respuesta para que el Servidor conteste
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
                    $('#report-fields').addClass('hide');
                    $('#report-connection').removeClass('hide');
                },
                success: function(data){
                    if(data.server != "" || data.user != ""){
                        $('#report-connection').addClass('hide');
                        $('#report-fields').removeClass('hide');      
                    }
                    if (data.request == true) {
                        $('#report-fields').addClass('hide');
                        $('#report-connection').addClass('hide');
                        $('#report-success').removeClass('hide');
                        window.location = '/';
                    }
                }
            });
            return false;
        });
    });
    </script>
@stop
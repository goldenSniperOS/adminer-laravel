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
            <p class="alert alert-warning hide" id="loading">
                <span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Loading...
            </p>

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
             <div class="form-group">
                <div class="input-group">
                    <span class="input-group-addon"><span class="glyphicon glyphicon-send"></span></span>
                    <input type="text" class="form-control" id="port" placeholder="Puerto" name="port"value="3306">
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
            if (!$('#report-fields').hasClass('hide')) {$('#report-fields').addClass('hide');}
                    if (!$('#report-connection').hasClass('hide')) {$('#report-connection').addClass('hide');}
                    if (!$('#report-success').hasClass('hide')) {$('#report-success').addClass('hide');}
            var server = $('input[name="server"]').val(),
            user = $('input[name="user"]').val(),
            port = $('input[name="port"]').val(),
            pasword = $('input[name="pasword"]').val();
            $.ajax({
                url: '/conectar',
                method: 'POST',
                data:{
                        server: server,
                        user: user,
                        pasword: pasword,
                        port: port
                    },
                beforeSend: function(data){
                    $('#loading').removeClass('hide');
                },
                error: function(data){
                    $('#loading').addClass('hide');
                    console.log(data);
                    $('#report-fields').addClass('hide');
                    $('#report-connection').removeClass('hide');
                },
                success: function(data){
                    $('#loading').addClass('hide');
                    console.log(data);
                    /*if(data.server != "" || data.user != ""){
                        $('#report-connection').addClass('hide');
                        $('#report-fields').removeClass('hide');      
                    }*/
                    if (data.request == true) {
                        $('#report-fields').addClass('hide');
                        $('#report-connection').addClass('hide');
                        $('#report-success').removeClass('hide');
                        window.location = '/';
                    }else{
                        $('#report-connection').html(data.message).removeClass('hide');
                    }
                }
            });
            return false;
        });
    });
    </script>
@stop
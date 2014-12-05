
@extends('layouts.master')

@section('cabecera')
	@parent
	{{HTML::style("css/terminal.css")}}
	{{HTML::style("js/codemirror-4.8/lib/codemirror.css")}}
	{{HTML::style("js/codemirror-4.8/theme/twilight.css")}}
	{{HTML::style("js/codemirror-4.8/addon/hint/show-hint.css")}}
	{{HTML::style("https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,800,700,600,300")}}
	{{HTML::style("css/font-awesome.min.css")}}
@stop

@section('principal')
	<div class="content">
		<div class="help">
			<h4 class="title">Adminer
			<small>v 2.0</small></h4>
			<div class="help-body">
				<p class="padding"><small>Aquí obtendrás la ayuda para usar todos los comandos.</small></p>
				<div class="list-group">
				  <a href="#" class="list-group-item"><i class="fa fa-angle-double-right"></i> Crear</a>
				  <a href="#" class="list-group-item"><i class="fa fa-angle-double-right"></i> Seleccionar</a>
				  <a href="#" class="list-group-item"><i class="fa fa-angle-double-right"></i> Insertar</a>
				  <a href="#" class="list-group-item"><i class="fa fa-angle-double-right"></i> Modificar</a>
				  <a href="#" class="list-group-item"><i class="fa fa-angle-double-right"></i> Eliminar</a>
				  <a href="#" class="list-group-item"><i class="fa fa-angle-double-right"></i> Ver</a>
				</div>

				<div class="padding">
					<h4><i class="fa fa-angle-double-right"></i> Teclado</h4>
					<p><code>CTRL + S</code>: <small>Permite guardar los comandos.</small></p>
					<p><code>CTRL + Espacio</code>: <small>Muestra la lista de comandos.</small></p>
				</div>
			</div>
			<div class="padding help-send">
				<a href=""><i class="fa fa-github"></i> Proyecto</a> Por <b>White Elephant Team</b>
			</div>
			
		</div>
		<div class="col-sm-10 shell-wrap">
			<p class="shell-top-bar">
				<i class="fa fa-terminal"></i>
				Ventana de Comandos ( <small class="text-info">Conectado a {{Session::get('user')}}&#64;{{Session::get('server')}}</small> )
				<a href="/cerrar" class="btn btn-xs btn-danger pull-right"><i class="glyphicon glyphicon-remove"></i>
				</a>
			</p>
			<ul class="shell-body">
				
			</ul>

			<div class="help-send hide">
				<button id="send" class="btn btn-success"><i class="fa fa-flask"></i> Enviar Comando</button>
			</div>
			<div class="shell-input">
				<textarea id="command"  name="command" class="form-control" style="resize:none;" placeholder="Para ejecutar comandos usa CTRL + S"></textarea>
			</div>
		</div>
		
	</div>
@stop	

@section('scripts')
	{{HTML::script("js/codemirror-4.8/lib/codemirror.js")}}
	{{HTML::script("js/codemirror-4.8/mode/sql/sql.js")}}
	{{HTML::script("js/codemirror-4.8/addon/hint/show-hint.js")}}
	{{HTML::script("js/codemirror-4.8/addon/hint/anyword-hint.js")}}
	{{HTML::script("js/codemirror-4.8/addon/hint/sql-hint.js")}}
	{{HTML::script("js/typed.js")}}
	{{HTML::script("js/placeholder.js")}}

	<script>
	var last_command,lines=1;
	CodeMirror.commands.autocomplete = function(cm) {
    	CodeMirror.showHint(cm,CodeMirror.hint.sql,{
    		
    	});
  	}
	var myCodeMirror = CodeMirror.fromTextArea(document.getElementById("command"), {
	    lineNumbers: true,
	    mode: "text/x-sql",
	    theme: 'twilight',
	    extraKeys: {
	    	"Ctrl-Space": "autocomplete",
			"Ctrl-S": function(cm) {
		            enviar_comando(); //function called when 'ctrl+s' is used when instance is in focus
		    },
		},
  	});
  	function enviar_comando(){
  		var command = myCodeMirror.getValue().trim();
    	if (command == 'LIMPIAR' || command == 'limpiar') {
    		$(".shell-body").empty();
    		command = "";
    		myCodeMirror.setValue("");
    	};

    	if (command != "") {
	    	myCodeMirror.setValue("");
			$(".shell-body").append("<li id='"+lines+"' class='command' style ='color: #45D40C;'>"+command+"</li>");
			lines++;
				$.ajax({

				url: 'consola',
				method:'POST',
				data:{command:command},
				beforeSend:function(){
					$(".shell-body").append("<li id='"+lines+"' class='text-info'><span class='glyphicon glyphicon-refresh glyphicon-refresh-animate'></span> Cargando...</li>");
				},
				success:function(data){
					console.log(data);
					if (data.message != undefined) {
						$("#"+lines).html(data.message);
						$("#"+lines).removeAttr('class');
						$("#"+lines).attr('class', '');
						$('#'+lines).addClass(data.type);
						lines++;
					}
				},
				error:function(data){
					console.log(data);
					$("#"+lines).html('Ocurrio un error');
					$("#"+lines).removeAttr('class');
					$("#"+lines).attr('class', '');
					$("#"+lines).addClass('text-danger');
					lines++;
				}
			});
			$('.shell-body').animate({scrollTop: $('.shell-body').prop("scrollHeight")}, 500);
		};
		myCodeMirror.focus();
  	};
	$(function(){
		 $("#title").typed({
	        strings: ["First sentence.", "Second sentence."],
	        typeSpeed: 0
	      });
		myCodeMirror.focus();
	});
	
	
	</script>
@stop
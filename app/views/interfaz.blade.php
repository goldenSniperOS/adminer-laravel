
@extends('layouts.master')

@section('cabecera')
	@parent
	{{HTML::style("css/terminal.css")}}
	{{HTML::style("js/codemirror-4.8/lib/codemirror.css")}}
@stop

@section('principal')



		<textarea id="command"  name="command" class="form-control" style="resize:none;"></textarea>
		<button id="send"  class="btn btn-danger form-control">Enviar Comando</button>

		<a href="/cerrar" class="btn btn-info center-block">Cambiar de conexion</a>
		<div class="shell-wrap">
			<p class="shell-top-bar">Ventana de Comandos</p>
			<ul class="shell-body">
			  
			</ul>
		</div>
@stop	

@section('scripts')
	{{HTML::script("js/codemirror-4.8/lib/codemirror.js")}}
	{{HTML::script("js/codemirror-4.8/mode/javascript/javascript.js")}}
	<script>
	var myCodeMirror = CodeMirror.fromTextArea(document.getElementById("command"), {
	    lineNumbers: true,
	    mode: "sql"
  	});

	var dia = new Date();
	$("#infos").append(dia);
	$('#command').keydown(function(e){
		var command = $('#command').val();
		var etapa = 0 ;
		
		if(e.which == 9) {
			key = command.substr(command.length-1,command.length);
			switch(key){
				case 'c':
				case 'C': $('#command').val('CREAR ');
				etapa = 1;
				break;
				case 'S':
				case 's':$('#command').val('SELECCIONAR ');
				etapa = 1;
				case 'b':
				case 'B':
				$('#command').val(command+'BASEDEDATOS');
				etapa = 2;
			}
		}
		
	});
	$('#send').click(function() {    
	    	var command = myCodeMirror.getValue();
	    	myCodeMirror.setValue("");
        	$('#command').val("");
			$(".shell-body").append("<li style ='color: #45D40C;''>"+command+"</li>");
			
					$.ajax({
	    			url: 'consola',
	    			method:'POST',
	    			data:{command:command},
	    			success:function(data){
	    				console.log(data);
	    				$(".shell-body").append(data.message);
	    			},
	    			error:function(data){
	    				console.log(data);
	    				if (data.message != undefined) {
	    					$(".shell-body").append("<li class='text-danger'>"+data.message+"</li>");
	    				}else{
	    					$(".shell-body").append("<li class='text-danger'>Ocurrio un error</li>");
	    				};
	    			}
	    		});
			
	});
	
	</script>
@stop
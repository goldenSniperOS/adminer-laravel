
@extends('layouts.master')

@section('cabecera')
	@parent
	{{HTML::style("css/terminal.css")}}
	{{HTML::style("js/codemirror-4.8/lib/codemirror.css")}}
	{{HTML::style("js/codemirror-4.8/theme/twilight.css")}}

@stop

@section('principal')

	<div class="shell-wrap">
		<p class="shell-top-bar">Ventana de Comandos 
			<a href="/cerrar" class="btn btn-xs btn-danger pull-right"><i class="glyphicon glyphicon-remove"></i>
			</a>
		</p>
		<ul class="shell-body">
		  
		</ul>
		<textarea id="command"  name="command" class="form-control" style="resize:none;"></textarea>
	<button id="send"  class="btn btn-danger form-control">Enviar Comando</button>
	</div>
@stop	

@section('scripts')
	{{HTML::script("js/codemirror-4.8/lib/codemirror.js")}}
	{{HTML::script("js/codemirror-4.8/mode/javascript/javascript.js")}}
	<script>
	var dia = new Date();
	$("#infos").append(dia);
	var last_command,lines=1;
	$(".shell-body").append("<li class='text-info'>Conectado a {{Session::get('user')}}&#64;{{Session::get('server')}}</li>");
	var myCodeMirror = CodeMirror.fromTextArea(document.getElementById("command"), {
	    lineNumbers: true,
    lineWrapping: true,
    autofocus: true,
	    mode: "sql",
	    theme: 'twilight'
  	});
	myCodeMirror.setSize(800, 60);
	
	$(document).keyup(function (event) {
	
        if (event.keyCode == 37) {
        	if (last_command != "") {
        		myCodeMirror.setValue(last_command);
        	};
        }
    });

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
    	var command = myCodeMirror.getValue().trim();
    	if (command == 'limpiar' || command == 'LIMPIAR') {
    		$(".shell-body").empty();
    		command = "";
    		myCodeMirror.setValue("");
    	};

    	if (command != "") {
	    	myCodeMirror.setValue("");
	    	last_command = command;
			$(".shell-body").append("<li id='"+lines+"' style ='color: #45D40C;'>"+command+"</li>");
			lines++;
				$.ajax({

				url: 'consola',
				method:'POST',
				data:{command:command},
				beforeSend:function(){
					$(".shell-body").append("<li id='"+lines+"' class='text-info'>Cargando...</li>");
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
		
	});
	
	</script>
@stop
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Document</title>
	<link rel="stylesheet" href="css/bootstrap.min.css">
	{{HTML::style("js/codemirror-4.8/lib/codemirror.css")}}
	{{HTML::style("js/codemirror-4.8/theme/twilight.css")}}
	{{HTML::style("js/codemirror-4.8/addon/hint/show-hint.css")}}
</head>
<body>
	
<textarea id="commando"  name="command" class="form-control" style="resize:none;"></textarea>
<button id="send"  class="btn btn-info form-control">Enviar Comando</button>

{{HTML::script("js/jquery-1.11.1.min.js")}}
{{HTML::script("js/bootstrap.min.js")}}
{{HTML::script("js/codemirror-4.8/lib/codemirror.js")}}
{{HTML::script("js/codemirror-4.8/mode/sql/sql.js")}}
{{HTML::script("js/codemirror-4.8/addon/hint/show-hint.js")}}
{{HTML::script("js/codemirror-4.8/addon/hint/anyword-hint.js")}}
{{HTML::script("js/codemirror-4.8/addon/hint/sql-hint.js")}}
<script>
	var last_command,lines=1;
	$(".shell-body").append("<li class='text-info'>Conectado a {{Session::get('user')}}&#64;{{Session::get('server')}}</li>");
	CodeMirror.commands.autocomplete = function(cm) {
    	CodeMirror.showHint(cm,CodeMirror.hint.sql,{
    		
    	});
  	}
	var myCodeMirror = CodeMirror.fromTextArea(document.getElementById("commando"), {
	    lineNumbers: true,
	    mode: "text/x-sql",
	    theme: 'twilight',
	    extraKeys: {"Ctrl-Space": "autocomplete"},
        viewportMargin: Infinity
  	});

  	$('#send').click(function(){
  		var command = myCodeMirror.getValue().trim();
  		$.ajax({
				url: 'conexionprueba',
				method:'POST',
				data:{command:command},
				
				success:function(data){
					console.log(data);
				},
				error:function(data){
					console.log("Error al momento de Enviar");
				}
			});
  	});
</script>
</body>
</html>
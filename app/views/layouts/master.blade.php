<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Adminer</title>
	{{HTML::style("css/bootstrap.min.css")}}
	{{HTML::style("css/font-awesome.min.css")}}
	{{HTML::style("css/jquery.dataTables.min.css")}}
	{{HTML::style("css/morris.css")}}
	@yield('cabecera')
</head>
<body>
	<div class="container">

		@yield('principal')
		
	</div>

	{{HTML::script("js/jquery-1.11.1.min.js")}}
	{{HTML::script("js/bootstrap.min.js")}}
	{{HTML::script("js/Chart.min.js")}}
	{{HTML::script("js/morris.min.js")}}
	{{HTML::script("js/bootstrap.min.js")}}
	@yield('scripts')
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Adminer</title>
	{{HTML::style("css/bootstrap.min.css")}}	
	@yield('cabecera')
</head>
<body>
	@yield('principal')
	{{HTML::script("js/jquery-1.11.1.min.js")}}
	{{HTML::script("js/bootstrap.min.js")}}
	@yield('scripts')
</body>
</html>
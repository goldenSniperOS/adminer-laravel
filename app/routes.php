<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

//Route::get('/', 'HomeController@home');


Route::post('conectar',function(){
	$rules = array();
	$validator = Validator::make(Input::all(), $rules);

	if ($validator->fails()) {
		echo 'corrige datos';
	}else{
	    $conn = mysqli_connect(Input::get('server'), Input::get('user'), Input::get('password'));
		if ($conn) {
			Session::put('server',Input::get('server'));
			Session::put('user',Input::get('user'));
			Session::put('password',Input::get('password'));
			return Response::json(array('request' => true));
		}
		return Response::json(array('request' => fase));

	}

});

Route::get('/',function(){
	if (Session::has('server')) {
		return Redirect::to('interfaz');
	}
	return View::make('login');
});

Route::get('interfaz',function(){
	if (!Session::has('server')) {
		return Redirect::to('/');
	}
	//echo Config::get('config.database_host');
	return View::make('interfaz');
});

Route::get('cerrar',function(){
	Session::flush();
	return Redirect::to('/');
});
Route::post('consola',function(){
	$tiposcastellano = array(
	'CADENA' => 'VARCHAR(255)', 
	'TEXTO' => 'TEXT', 
	'ENTERO' => 'INT', 
	'REAL' => 'FLOAT',
	'MONEDA' => 'FLOAT(11,2)',
	'FECHA' => 'DATE',
	'HORA' => 'TIME',
	'LOGICO' => 'TINYINT',
	);
	$val = Input::get('command');
	$data = explode( ' ', $val);
	$data[0] = trim($data[0]);
	//return Response::json($row);
	if ($data[0] == 'crear' || $data[0] == 'CREAR') {
		if (isset($data[1])) {
			$data[1] = trim($data[1]);
			switch ($data[1]) {
				case 'TABLA':
				case 'tabla':
					$data[2] = trim($data[2]);
					$nombres = explode('.',$data[2]);
					$nombretabla = explode('(',$nombres[1]);
					Session::put('database',trim($nombretabla[0]));
					$atributos = explode('(',$val);
					for ($i=1; $i < count($atributos); $i++) { 
						$atributos[$i] = str_replace(")", "", $atributos[$i]);
						$atributos[$i] = trim($atributos[$i]);						
					}
					$row = explode(",", $atributos[1]);
					for ($i=0; $i < count($row); $i++) { 
						$row[$i] = trim($row[$i]);
						$separa = explode(' ', $row[$i]);
						if($row[$i] == ""){
							unset($row[$i]);
						}else{
							$row[$i] = $separa[0].' '.$tiposcastellano[strtoupper($separa[1])];
						}
					}
					$conn = mysqli_connect(Session::get('server'), Session::get('user'), Session::get('password'),$nombres[0]);
					if ($conn) {
					    $sql = "CREATE TABLE ".$nombretabla[0].' (';
					    	for ($i=0; $i < count($row); $i++) { 
				    			$sql.= $row[$i];
					    		if ($i < count($row)-1) {
					    			$sql.=',';
					    		}
					    	}
					    	$sql = $sql.');';
						if (mysqli_query($conn,$sql)) {
							return Response::json( array('message' => 'Se inserto la tabla <b>'.trim($nombretabla[0]).'</b> en la base de datos '.$nombres[0],'type' => 'text-success'));
						}
						return Response::json( array('message' => "Hubo un error al insertar la base de datos <b>".$nombretabla[0].'</b> en la base de datos '.$nombres[0],'type' => 'text-danger'));
						
					}
				break;
				case 'BASEDATOS':
				case 'basedatos':
					if(isset($data[2])){
						$data[2] = trim($data[2]);
						$conn = mysqli_connect(Session::get('server'), Session::get('user'), Session::get('password'));
						if ($conn) {
						    $sql = "CREATE DATABASE ".$data[2];
							if (mysqli_query($conn,$sql)) {
								return Response::json( array('message' => "Se inserto la base de datos <b>".trim($data[2]).'</b>','type' => 'text-success'));
							}
							return Response::json( array('message' => "Hubo un error al insertar la base de datos <b>".$data[2].'</b>','type' => 'text-danger'));
						}
						mysqli_close($conn);
					}
					return Response::json(array('message' => 'Especifique <b>NOMBRE</b> de la base de datos','type' => 'text-danger'));
				break;
				default:
					return Response::json(array('message' => 'Usted solo puede <b>CREAR</b> (BASEDATOS o TABLA)','type' => 'text-danger'));
				break;
			}
		}
		return Response::json(array('message' => 'Especifique que desea <b>CREAR</b>','type' => 'text-danger'));
	}

	if($data[0] == 'modificar' || $data[0] == 'MODIFICAR')
	{
		if (isset($data[1])) {
			$data[1] = trim($data[1]);
			switch($data[1])
			{
				case 'TABLA':
				case 'tabla':
					$nombres = explode('.',$data[2]);
					Session::put('database',trim($nombres[0]));
					$conn = mysqli_connect(Session::get('server'), Session::get('user'), Session::get('password'),Session::get('database'));
					if ($conn) {
						if($data[3]=='AGREGAR' || $data[3]=='agregar')
						{
							$sql = "ALTER TABLE ".$nombres[1]." ADD ".$data[5]." ".$tiposcastellano[strtoupper($data[6])]." NOT NULL";
							if (mysqli_query($conn,$sql)) {
								return Response::json( array('message' => 'Se inserto la columna <b>'.trim($data[5]).'</b>','type' => 'text-success'));
							}
							return Response::json( array('message' => 'Hubo un error al insertar la columna a la base de datos <b>'.$nombres[1].'</b>','type' => 'text-danger'));
						}
						elseif ($data[3]=='ELIMINAR' || $data[3]=='eliminar') 
						{
							$sql = "ALTER TABLE ".$nombres[1]." DROP ".$data[5];
							if (mysqli_query($conn,$sql)) {
								return Response::json( array('message' => 'Se elimino la columna <b>'.trim($data[5]).'</b>','type' => 'text-success'));
							}
							return Response::json( array('message' => 'Hubo un error al eliminar la columna a la base de datos <b>'.$nombres[1].'</b>','type' => 'text-danger'));				
						}			
					}

					mysqli_close($conn);
				break;

			}
		}
		return Response::json(array('message' => 'Especifique que desea <b>MODIFICAR</b>','type' => 'text-danger'));
	}

	if($data[0] == 'eliminar' || $data[0] == 'ELIMINAR')
	{
		switch($data[1])
		{
			case 'TABLA':
			case 'tabla':
				$nombres = explode('.',$data[2]);
				Session::put('database',trim($nombres[0]));
				$conn = mysqli_connect(Session::get('server'), Session::get('user'), Session::get('password'),Session::get('database'));
				if ($conn) {
					$sql = "DROP TABLE ".$nombres[1];
					if (mysqli_query($conn,$sql)) {
						return Response::json( array('message' => 'Se Elimino la Tabla <b>'.trim($nombres[1]).'</b>','type' => 'text-success'));
					}else{
						return Response::json( array('message' => 'Hubo un error al eliminar la tabla de la base de datos <b>'.$nombres[0].'</b>','type' => 'text-danger'));
					}
				}
				mysqli_close($conn);
			break;
			case 'BASEDATOS':
			case 'basedatos':
				$nombres = explode('.',$data[2]);
				Session::put('database',trim($data[2]));
				$conn = mysqli_connect(Session::get('server'), Session::get('user'), Session::get('password'),Session::get('database')) or die("error");
				if ($conn) {
					$sql = "DROP DATABASE ".$data[2];
					if (mysqli_query($conn,$sql)) {
						return Response::json( array('message' => 'Se Elimino la base de datos <b>'.trim($data[2]).'</b>','type' => 'text-success'));
					}else{
						return Response::json( array('message' => 'Hubo un error al eliminar la base de datos <b>'.$data[2].'</b>','type' => 'text-danger'));
					}
				}else{
						return Response::json( array('message' => 'NO existe la base de datos <b>'.$data[2].'</b>','type' => 'text-danger'));
					}
				mysqli_close($conn);

			break;

		}
	}

	if ($data[0] == 'ver' || $data[0] == 'VER') {
		if (isset($data[1])) {
			$data[1] = trim($data[1]);
			switch ($data[1]) {
				case 'BASEDATOS':
				case 'basedatos':
					$conn = mysqli_connect(Session::get('server'), Session::get('user'), Session::get('password'));
					if ($conn) {
					    $sql = "SHOW DATABASES";
						if ($resultado = mysqli_query($conn,$sql)) {
							 $tableList = '<ul>';
							 while($cRow = mysqli_fetch_array($resultado))
							  {
							  	if (($cRow[0]!="information_schema") && ($cRow[0]!="mysql")) {
								    $tableList.= '<li>'.$cRow[0].'</li>';
								}
							  }
							  $tableList.='</ul>';
							return Response::json(array('message' => $tableList,'type' => 'text-info'));
						}
					}
					break;
				case 'TABLAS':
				case 'tablas':
					$data[2] = trim($data[2]);
					$nombres = explode('.',$data[2]);
 					$conn = mysqli_connect(Session::get('server'), Session::get('user'), Session::get('password'),$nombres[0]);
					if ($conn) {
					    $sql = "SHOW TABLES";
						if ($resultado = mysqli_query($conn,$sql)) {
							 $tableList = '<ul>';

							 while($cRow = mysqli_fetch_array($resultado))
							  {
							  	
								    $tableList.= '<li>'.$cRow[0].'</li>';
								
							  }
							  $tableList.='</ul>';
							return Response::json(array('message' => $tableList,'type' => 'text-info'));
						}
					}
				break;
			}
		}
		return Response::json(array('message' => 'Especifique que desea <b>VER	</b>','type' => 'text-danger'));
	}

	if ($data[0] == 'ayuda' || $data[0] == 'AYUDA') {
		$text = '<p>========= Comandos ========</p>
				<ol>
					<li><b>CREAR</b>: [BASEDATOS,TABLA]</li>
					<li><b>VER</b>: [BASEDATOS,TABLAS]</li>
					
				</ol>';
		if (isset($data[1])) {
			$data[1] = trim($data[1]);
			switch ($data[1]) {
				case 'VER':
				case 'ver':
					$text = '<p>Comando <b>VER</b></p>
					<p>Ejemplo: <b>VER BASEDATOS</b> ejemplo</p>';
					break;
				case 'CREAR':
				case 'crear':
					$text = '<p>Comando <b>CREAR</b></p>
					<p>Ejemplo: <b>CREAR BASEDATOS</b> ejemplo</p>';
					break;
			}
		}
		
		return Response::json(array('message' => $text,'type' => 'text-info'));
	}
	return Response::json(array('message' => 'El comando ingresado no es valido','type' => 'text-danger'));
});
<?php

include 'Table.php';
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


/*
TODO List
- Que todos los errores no siempre dirijaan a "Ocurrio un Error" sino salga el error del comando al que Pertenece
- 


*/

Route::post('conectar',function(){

	//Se Agregaron Validaciones por Backend
	$rules = array(
		'server' => 'required',
		'user' => 'required',
		'port' => 'required|integer'
		);

	$validator = Validator::make(Input::all(), $rules);

	if ($validator->fails()) {
		$messages = $validator->messages();
		return Response::json(array('request' => false,'message' => $messages->first('user').'<br>'.$messages->first('server').'<br>'.$messages->first('port')));
	}else{
		try {
		    $gbd = new PDO('mysql:host='.Input::get('server').';port='.Input::get('port').'', Input::get('user'), Input::get('password'));
		    Session::put('server',Input::get('server'));
			Session::put('user',Input::get('user'));
			Session::put('password',Input::get('password'));	
		    $gbd = null;
		    return Response::json(array('request' => true));
		} catch (PDOException $e) {
		    return Response::json(array('request' => false,'message' => $e->getMessage()));
		    die();
		}
	
	}
});


Route::get('/',function(){
	if (Session::has('server')) {
		return View::make('interfaz');
	}
	return View::make('login');
});

Route::get('cerrar',function(){
	Session::flush();
	return Redirect::to('/');
});


Route::post('consola',function(){
	$tiposcastellano = array(
	'CADENA' => 'VARCHAR', 
	'TEXTO' => 'TEXT', 
	'ENTERO' => 'INT', 
	'REAL' => 'FLOAT',
	'MONEDA' => 'FLOAT',
	'FECHA' => 'DATE',
	'HORA' => 'TIME',
	'LOGICO' => 'TINYINT',
	);
	$val = Input::get('command');
	
	$data = explode( ' ', $val);



	//Comando Principal a Mayusculas y Sin Espacios
	$data[0] = trim($data[0]);
	$data[0] = strtoupper($data[0]);

	//Comando Secundario a Mayusculas y Sin Espacios
	if(isset($data[1])){
		$data[1] = trim($data[1]);
		$data[1] = strtoupper($data[1]);
	}

	if ($data[0] == 'AYUDA'){
		$text = '<p>========= Comandos ========</p><ol><li><b>CREAR</b>: [BASEDATOS,TABLA]</li><li><b>VER</b>: [BASEDATOS,TABLAS]</li></ol>';
		if (isset($data[1])) {
			switch ($data[1]) {
				case 'VER':
					$text = '<p>Comando <b>VER</b></p>
					<p>Ejemplo: <b>VER BASEDATOS</b> ejemplo</p>';
				break;
				case 'CREAR':
					$text = '<p>Comando <b>CREAR</b></p>
					<p>Ejemplo: <b>CREAR BASEDATOS</b> ejemplo</p>';
					break;
			}
		}	
		return Response::json(array('message' => $text,'type' => 'text-info'));
	}

	if ($data[0] == 'CREAR') {
		if (isset($data[1])) {
		switch ($data[1]) {
				case 'TABLA':
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
							$row[$i] = $separa[0].' '.$tiposcastellano[strtoupper($separa[1])].((isset($separa[2]))?'('.$separa[2].')':'');
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

	if ($data[0] == 'MODIFICAR'){
		if (isset($data[1])) {
			$data[1] = trim($data[1]);
			switch($data[1])
			{
				case 'TABLA':
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

	if ($data[0] == 'ELIMINAR'){
		switch($data[1])
		{
			case 'TABLA':
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

	if ($data[0] == 'VER'){
		if (isset($data[1])) {
			$data[1] = trim($data[1]);
			switch ($data[1]) {
				case 'BASEDATOS':
					$conn = mysqli_connect(Session::get('server'), Session::get('user'), Session::get('password'));
					if ($conn) {
					    $sql = "SHOW DATABASES";
						if ($resultado = mysqli_query($conn,$sql)) {
							$table = new Console_Table();
							while($cRow = mysqli_fetch_array($resultado))
							{
							 	if (($cRow[0]!="information_schema") 
							 		&& ($cRow[0]!="mysql") 
							 		&& ($cRow[0]!="performance_schema") 
							 		&& ($cRow[0]!="test")) {
								   $table->addRow(array($cRow[0]));
								}
							}
							return Response::json(array('message' => '<pre>'.$table->getTable().'</pre>','type' => 'text-info'));
						}
					}
					break;
				case 'TABLAS':
					$data[2] = trim($data[2]);
 					$conn = mysqli_connect(Session::get('server'), Session::get('user'), Session::get('password'),$data[2]);
					if ($conn) {
					    $sql = "SHOW TABLES";
						if ($resultado = mysqli_query($conn,$sql)) {
							$table = new Console_Table();
							while($cRow = mysqli_fetch_array($resultado))
						   	{
								$table->addRow(array($cRow[0]));
						  	}
							return Response::json(array('message' => '<pre>'.$table->getTable().'</pre>','type' => 'text-info'));
						}
					}
				break;
				case 'COLUMNAS':
					$data[2] = trim($data[2]);
					$nombres = explode('.',$data[2]);
 					$conn = mysqli_connect(Session::get('server'), Session::get('user'), Session::get('password'),$nombres[0]);
					if ($conn) {
					    $sql = "SHOW COLUMNS FROM ".trim($nombres[1]);
						if ($resultado = mysqli_query($conn,$sql)) {
							$table = new Console_Table();
							 while($cRow = mysqli_fetch_array($resultado))
							  {
							  		$table->addRow(array($cRow[0]));
								
							  }
							return Response::json(array('message' => '<pre>'.$table->getTable().'</pre>','type' => 'text-info'));
						}
					}
				break;

			}
		}
		return Response::json(array('message' => 'Especifique que desea <b>VER	</b>','type' => 'text-danger'));
	}

	if ($data[0] == 'INSERTAR'){
		$nombres = explode('.',$data[1]);
		$conn = mysqli_connect(Session::get('server'), Session::get('user'), Session::get('password'),$nombres[0]);
		if ($conn) {
			$columns = explode('(', $val);
			$columns[1] = str_replace(")", "", $columns[1]);
			$columns[1] = str_replace(" ", "", $columns[1]);
			$columns[1] = str_replace("VALORES", "", $columns[1]);
			$col = explode(',', $columns[1]);
			$sql = "INSERT INTO ".$nombres[1].' (';
			for ($i=0; $i < count($col); $i++) { 
				if ($col[$i] == "") {
					unset($col[$i]);
				}
	    	}
	    	for ($i=0; $i < count($col); $i++) { 
	    		$sql.= $col[$i];
	    		if ($i < count($col)-1) {
	    			$sql.=',';
	    		}
	    	}
	    	$sql .=') VALUES (';
	    	
	    	if (!isset($values[1])) {
	    		$values = explode('VALORES', $val);
	    	}
	    	$values[1] = str_replace("(", "", $values[1]);
	    	$values[1] = str_replace(")", "", $values[1]);
	    	$values[1] = trim($values[1]);
	    	$value = explode(',', $values[1]);
			for ($i=0; $i < count($value); $i++) { 
				if ($value[$i] == "") {
					unset($value[$i]);
				}
	    	}
	    	for ($i=0; $i < count($value); $i++) { 
	    		$sql.= $value[$i];
	    		if ($i < count($value)-1) {
	    			$sql.=',';
	    		}
	    	}
	    	$sql.=')';
			if (mysqli_query($conn,$sql)) {
				return Response::json( array('message' => "Se insertaron los valores a la tabla <b>".trim($nombres[1]).'</b>','type' => 'text-success'));
			}
			return Response::json( array('message' => "Hubo un error al insertar los valores a la tabla <b>".$nombres[1].'</b>','type' => 'text-danger'));
		}

	}

	if ($data[0] == 'SELECCIONAR'){
		$nombres = explode('.',$data[1]);
		$conn = new PDO('mysql:host='.Session::get('server').';port='.Session::get('port').';dbname='.$nombres[0].'', Session::get('user'), Session::get('password'));
		    
		if ($conn) {
			$columns = explode('[', $val);
			$data = explode("]", $columns[1]);
			$data[0] = str_replace(" ", "", $data[0]);
			$col = explode(',', $data[0]);
				$sql = "SELECT ";
			if ($data[0] != '*') {
				
				for ($i=0; $i < count($col); $i++) { 
					if ($col[$i] == "") {
						unset($col[$i]);
					}
		    	}
		    	for ($i=0; $i < count($col); $i++) { 
		    		$sql.= $col[$i];
		    		if ($i < count($col)-1) {
		    			$sql.=',';
		    		}
		    	}
			}else{
				$sql.= $data[0];
			}
				
	    	$sql .= ' FROM '.$nombres[1];

    		$where = explode('DONDE', $val);
    		if (!isset($where[1])) {
    			$where = explode('donde', $val);
    		}
    		if (isset($where[1])) {
    			$sql.=' WHERE ';
    			$where[1] = trim($where[1]);
		    	$value = explode(' ', $where[1]);
		    	for ($i=0; $i < count($value); $i++) { 
					if ($value[$i] == "") {
						unset($value[$i]);
					}
		    	}
		    	$sql .= $value[0].$value[1].$value[2];
		    	
    		}

			if ($resultado = $conn->query($sql)) {
				$table = new Console_Table();
				foreach(range(0, $resultado->columnCount() - 1) as $column_index)
				{
				  $meta[] = $resultado->getColumnMeta($column_index)['name'];
				}
				$table->setHeaders($meta);
				while($cRow = $resultado->fetch(PDO::FETCH_ASSOC))
				{
					$todo[] = $cRow;
				}
				if (isset($todo)) {
					$table->addData($todo);
				}
				return Response::json( array('message' => '<pre>'.$table->getTable().'</pre>','type' => 'text-info'));
			}
			return Response::json( array('message' => 'Hubo un error al insertar los valores a la tabla <b></b>','type' => 'text-danger'));
		}

	}
	return Response::json(array('message' => 'El comando ingresado no es valido','type' => 'text-danger'));
});
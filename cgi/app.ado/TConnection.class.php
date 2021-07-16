<?php
$nivel = "";
$arquivo = "app.config/driver.inc.php";
for($i=0; $i<3; $i++){
	if($i>0){
		$nivel .= "../";
	}
	if(file_exists($nivel.$arquivo)){
		include_once $nivel.$arquivo;
		break;
	}
}

/**
 * class TConnection
 * gerencia conexções com o banco de dados através de arquivos de configuração. 
 */
final class TConnection {
	
	/*
	 * método __construct()
	 * não existirão instâncias de TConnection, por isto está marcado como private
	 */
	private function __construct() {}
	
	/*
	 * método open()
	 * recebe o nome do banco de dados e instancia o objeto PDO correspondente
	 */
	public static function open($name){
		//verifica se existe arquivo de configuração para este banco de dados

		/*
		* Desenvolvi o sistema para que ele contesse uma organização de pastas de até 3 identações,
		* ou seja, os arquivos php q irão buscar uma conexão com o banco de dados podem está
		* no maximo até 3 pastas a dentro da raiz do sistema.
		* Optei para não passar como parametro o local exato do arquivo, e sim apenas o seu nome
		* antes do .ini, pois quero setar a localização desses arquivos de conexão apenas para 3
		* lugares (app.config e individual).
		*/
		$nivel = "";
		$arquivo_APP = "app.config/{$name}.ini";
		$arquivo_IND = "individual/{$name}.ini";
		for($i=0, $max=3; $i<$max; $i++){
			if($i>0){
				$nivel .= "../";
			}
			if(file_exists($nivel.$arquivo_APP)){
				//lê o INI e retorna um array
				$db = parse_ini_file($nivel.$arquivo_APP);
				break;
			}elseif(file_exists($nivel.$arquivo_IND)){
				//lê o INI e retorna um array
				$db = parse_ini_file($nivel.$arquivo_IND);
				break;
			}elseif($i-1 == $max){
				//se não existir, lança um erro
				throw new Exception("Arquivo '$name' não encontrado $nivel");
			}
		}
		
		
		//lê informações contidas no arquivo
		$user = $db['user'];
		$pass = $db['pass'];
		$name = $db['name'];
		$host = $db['host'];
		
		
		//descobre qual o tipo (driver) de banco de dados a ser utilizado
		switch (DRIVER) {
			case 'pgsql':
				$conn = new PDO("pgsql:dbname={$name};user{$user}; password={$pass};host=$host");
				break;
			
			case 'mysql':
				$conn = new PDO("mysql:dbname={$name};host={$host};port=3306;charset=utf8", $user, $pass);
				break;
		}
		
		//define para que o PDO lance exceções na ocorrência de erros
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
		
		//retorna o objeto instanciado.
		return $conn;
	}
}








?>
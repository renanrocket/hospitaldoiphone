<?php

class usuario{

	private $args;

	function __construct(){

		$conn = TConnection::open(DB);

		$criterio = new TCriteria;
		$criterio->add(new TFilter('email', '=', $_COOKIE['email']));

		$sql = new TSqlSelect;
		$sql->setEntity('usuario');
		$sql->addColumn('*');
		$sql->setCriteria($criterio);

		$result = $conn->query($sql->getInstruction());

		if($result->rowCount()){
			$this->args = $result->fetch(PDO::FETCH_NAMED);
		}
	}

	function get_usuario($column){
		return $this->args[$column];
	}

	function vardump(){
		return var_dump($this->args);
	}

	function get_acesso(){

		$pag = request_uri();

		$id_ferramenta = registro($pag, 'sistema_ferramentas', 'id', 'link');
		$id_usuario = $this->get_usuario('id');
		
		$conn = TConnection::open(DB);

		$criterio = new TCriteria;
		$criterio->add(new TFilter('id_sistema_ferramentas', '=', $id_ferramenta));
		$criterio->add(new TFilter('id_usuario', '=', $id_usuario));

		$sql = new TSqlSelect;
		$sql->setEntity('usuario_credenciais');
		$sql->addColumn('*');
		$sql->setCriteria($criterio);

		$result = $conn->query($sql->getInstruction());

		if($result->rowCount()){
			return true;
		}else{
			return false;
		}
	}

}







?>
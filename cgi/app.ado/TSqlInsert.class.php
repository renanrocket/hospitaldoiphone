<?php

/**
 * classe TSqlInsert
 * Esta classe provê meios para manipulação de uma instrução de INSERT no banco de dados
 */
final class TSqlInsert extends TSqlInstruction {
	
	
	/*
	 * método setRowData()
	 * Atribui valres à determinadas colunas no banco de dados que serão inseridas
	 * @param $column = coluna da tabela
	 * @param $value = valor a ser armazenado
	 */
	public function setRowData($column, $value){
		//retirando os ' da string
		$value = str_replace("'", " ", $value);
		
		//monta um array indexado pelo nome da coluna
		if($value==''){
			//caso seja nulo
			$this->columnValues[$column] = "NULL";
		}elseif(is_string($value)){
			//adciona \ em aspas
			$value = addslashes($value);
			//caso seja uma string
			$this->columnValues[$column] = "'$value'";
		}elseif(is_bool($value)){
			//caso seja um boolean
			$this->columnValues[$column] = $value ? 'TRUE' : 'FALSE';
		}elseif(isset($value)){
			//caso seja outro tipo de dado
			$this->columnValues[$column] = $value;
		}
	}
	
	/*
	 * método setCriteria()
	 * não existe no contexo desta classe, logo, irá lançar um erro caso executado
	 */
	public function setCriteria(TCriteria $criteria){
		//lança o erro
		throw new Exception("Não pode chamar setCriteria da ". __CLASS__);
	}
	
	/*
	 * método getInstruction()
	 * retorna a instrução de INSERT em forma de string
	 */
	public function getInstruction(){
		$this->sql = "INSERT INTO {$this->entity} (";
		//monta uma string contendo os nomes de colunas
		$columns = implode(', ', array_keys($this->columnValues));
		//monta uma string contendo os valores
		$values = implode(', ', array_values($this->columnValues));
		$this->sql .= $columns . ')';
		$this->sql .= " values ({$values})";
		
		return $this->sql;
	}
}






?>
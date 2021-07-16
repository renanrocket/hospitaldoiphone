<?php

/**
* classe TSqlUpdate
* Esta classe provê meios para manipulação de uma instrução de UPDATE no banco de dados
*/
final class TSqlUpdate extends TSqlInstruction {
	
	/*
	* método setRowData()
	* Atribui valrores à determinadas colunas no bano de dados que serão modificadas
	* @param $colum = coluna da tabela
	* @param $value = valor a ser armazenado
	*/
	public function setRowData($column, $value){
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
	* método getInstruction()
	* retorna a instrução de UPDATE em forma de string
	*/
	public function getInstruction(){
		//mostra a string de UPDATE
		$this->sql = "UPDATE {$this->entity} ";
		//monta os pares: coluna=valor,...
		if($this->columnValues){
			foreach ($this->columnValues as $column => $value) {
				$set[] = "{$column} = {$value}";
			}
		}
		$this->sql .= 'SET ' . implode(', ', $set);

		//retorna a cláusula do objeto $this->criteria
		if($this->criteria){
			$this->sql .= ' WHERE ' . $this->criteria->dump();
		}

		return $this->sql;
	}


}


?>
<?php

/**
 * classe TSqlSelect
 * Essa classe provê meios para manipulação de uma instrução de SELECT no banco de dados
 */
final class TSqlSelect extends TSqlInstruction {
	
	private $columns;	//array de colunas a serem retornadas.
	
	/*
	 * método addColumn
	 * adiciona uma coluna a ser retornada pelo SELECT
	 * @param $column = coluna da tabela
	 */
	public function addColumn($column){
		//adiciona a columna no array
		$this->columns[] = $column;
	}
	
	/*
	 * método getInstruction()
	 * retorna a instrução de SELECT em forma de string
	 */
	public function getInstruction(){
		
		//monta a instrução de SELECT
		$this->sql = 'SELECT ';
		//monta string com os nomes de colunas
		$this->sql .= implode(', ', $this->columns);
		//adiciona na cláusula FROM o nome da tabela
		$this->sql .= ' FROM ' . $this->entity;
		
		//obtém a cláusula WHERE do objeto criteria.
		if($this->criteria){
			$expression = $this->criteria->dump();
			if($expression != "()"){
				$this->sql .= ' WHERE ' . $expression;
			}
			
			//obtem as propriedades do critério
			$order 		= $this->criteria->getProperty('order');
			$group 		= $this->criteria->getProperty('group');
			$desc_asc 	= $this->criteria->getProperty('desc_asc');
			$limit 		= $this->criteria->getProperty('limit');
			$offset 	= $this->criteria->getProperty('offset');
			
			//obtém a ordenação do SELECT
			if($group){
				$this->sql .= ' GROUP BY ' . $group;
			}
			if($order){
				$this->sql .= ' ORDER BY ' . $order;
			}
			if($desc_asc){
				$this->sql .= ' ' . $desc_asc;
			}
			if($limit){
				$this->sql .= ' LIMIT ' . $limit;
			}
			if($offset){
				$this->sql .= ' OFFSET ' . $offset;
			}
		}
		return $this->sql;
	}
}







?>
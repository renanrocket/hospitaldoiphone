<?php

/**
 * classe TFilter
 * Essa classe provê uma interface para definição de filtros de seleção
 */
class TFilter extends TExpression {
	
	private $variable; //variável
	private $operator; //operador
	private $value;    //valor
	
	/*
	 * método __construct()
	 * instancia um novo filtro
	 * @param $variable = variável
	 * @param $operator = operador (>,<)
	 * @param $value    = valor a ser comparado
	 */
	
	public function __construct($variable, $operator, $value) {
		//armazena as propriedades
		$this->variable = $variable;
		$this->operator = $operator;
		//transforma o valor de acordo com certas regras
		//atens de atribuir à propriedade $this->value
		$this->value	= $this->transform($value);
	}
	
	/*
	 * método transform()
	 * recebe um valor e faz as modificações necessárias
	 * para ele ser interpertado pelo banco de dados
	 * podendo ser um interger/string/boolean ou array.
	 * @param $value = valor a ser transformado
	 */
	 private function transform($value){
	 	//caso seja um array
	 	if(is_array($value)){
	 		//percorre os valores
	 		foreach($value as $x){
	 			//se for um inteiro
	 			if(is_integer($x)){
	 				$foo[] = $x;
	 			}elseif(is_string($x)){
	 				//se for string acrescenta aspas simples
	 				$foo[] = "'$x'";
	 			}
	 		}
			//converte o array em string separada por ","
			$result = '('. implode(',', $foo) . ')';
	 	}elseif(is_string($value)){
	 		//caso seja uma string
	 		//adiciona aspas
	 		//se caso for uma query não faz nada
	 		if(isset($value[0])){
	 			if(strstr($value[0], '(') and strstr($value[(strlen($value)-1)], ')')){
		 			$result = $value;
		 		}else{
		 			$result = "'$value'";
		 		}	
	 		}else{
	 			$result = "'$value'";
	 		}
	 		

	 	}elseif(is_null($value)){
	 		//caso seja um valor nulo
	 		$result = 'NULL';
	 	}elseif(is_bool($value)){
	 		//caso seja booleano
	 		//armazena TRUE ou FALSE
	 		$result = $value ? 'TRUE' : 'FALSE';
	 	}else{
	 		$result = $value;
	 	}
		
		//retorna o valor
		return $result;
	 }

	/*
	 * método dump()
	 * retorna o filtro em forma de expressão
	 */
	 public function dump(){
	 	//concatena a expressão
	 	return "{$this->variable} {$this->operator} {$this->value}";
	 }

}






?>
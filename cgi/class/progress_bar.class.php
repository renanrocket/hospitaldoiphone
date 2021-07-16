<?php

class progress_bar{

	private $concluido;
	private $cor; //primary, success, warning, danger
	private $msg;

	function __construct($concluido, $cor = 'primary', $msg = null){

		$this->concluido 	= $concluido;
		$this->cor 			= $cor;
		$this->msg 			= $msg;

		if($this->msg == null){

			$this->msg = $this->concluido.'% Completo';

		}

	}


	function get_bar(){

		$cod = '
		<p>'.$this->msg.'</p>
		<div class="progress active">
            <div class="progress-bar progress-bar-'.$this->cor.' progress-bar-striped" role="progressbar" aria-valuenow="'.$this->concluido.'" aria-valuemin="0" aria-valuemax="100" style="width: '.$this->concluido.'%">
              <span class="sr-only">'.$this->msg.'</span>
            </div>
          </div>';


		return $cod;
	}


}



?>
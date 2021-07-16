<?php

class popup{

	private $nivel;
	public $texto;
	public $display;

	function __construct($texto, $nivel = 'verde'){
		$this->nivel = $nivel;
		$this->texto = $texto;
		$this->display = 'show';
		$this->id_pop = rand(1000,9999);
	}

	function get_pop(){
		if($this->nivel=='vermelho'){
			$alert = 'danger';
			$icon = 'ban';
		}elseif($this->nivel=='amarelo'){
			$alert = 'warning';
			$icon = 'warning';
		}elseif($this->nivel=='azul'){
			$alert = 'info';
			$icon = 'info';
		}else{
			$alert = 'success';
			$icon = 'check';
		}
		$cod = '<div id="pop_'.$this->id_pop.'" class="alert alert-background alert-dismissible" style="display:'.$this->display.';" onclick="'.$this->get_action_hide().'">';
		$cod.= '<div class="alert-body alert-'.$alert.' alert-dismissible">';
		$cod.= '<button type="button" class="close" aria-hidden="true" onclick="'.$this->get_action_hide().'">&times;</button>';
		$cod.= '<h4><i class="icon fa fa-'.$icon.'"></i> Alerta!</h4>';
		$cod.= $this->texto;
		$cod.= '</div>';
		$cod.= '</div>';

		$cod.= '
		<script type="text/javascript">
			function popup_show_'.$this->id_pop.'(){
				$("#pop_'.$this->id_pop.'").fadeIn();
			}
			function popup_hide_'.$this->id_pop.'(){
				$("#pop_'.$this->id_pop.'").fadeOut();
			}
		</script>
		';

		return $cod;
	}

	public function get_action_show(){
		return 'popup_show_'.$this->id_pop.'();';
	}

	public function get_action_hide(){
		return 'popup_hide_'.$this->id_pop.'();';
	}


}





?>
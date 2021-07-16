<?php

class form{

	private $name;
	private $action;
	private $op;
	private $method;
	private $enctype;
	private $arg;
	private $js_action;
	private $js_script;
	public $mascara;
	public $div_id;
	public $div_class;
	public $max;
	public $colspan;
	public $error;
	public $id;
	public $tabela;
	public $coluna;
	public $button_ativar_deletar;
	public $button_editar;
	public $ajax_valores;
	public $ajax_index;
	private $ajax_cod;
	public $tooltip;
	public $input_group_addon_front;
	public $input_group_addon_back;


	function __construct($name, $action, $op, $method = 'post', $enctype = 'multipart/form-data'){
		$this->name 					= $name;
		$this->action 					= $action;
		$this->op 						= $op;
		$this->method 					= $method;
		$this->enctype					= $enctype;
		$this->colspan					= null;
		$this->js_action 				= null;
		$this->js_script				= null;
		$this->error 					= false;
		$this->max 						= 50;
		$this->status 					= 1;
		$this->div_id 					= null;
		$this->div_class				= null;
		$this->id 						= null;
		$this->tabela 					= null;
		$this->mascara 					= null;
		$this->coluna 					= 'status';
		$this->button_ativar_deletar	= true;
		$this->button_editar			= true;
		$this->ajax_index				= null;
		$this->ajax_valores				= null;
		$this->ajax_cod					= null;
		$this->tooltip					= null;
		$this->input_group_addon_front  = null;
		$this->input_group_addon_back   = null;
	}
	
	public function set_arg($tipo, $nome, $placeholder = null, $valor = null, $mascara = null){

		$name_input = strtolower(strtr($nome, unserialize(CHAR_MAP)));
		
		if($this->colspan)
			$this->colspan = 'col-xs-'.$this->colspan;
		
		if($this->error)
			$this->error = 'has-error';

		if($tipo!='hidden'){
			$cod = "<div id='$this->div_id' class='form-group $this->error $this->colspan $this->div_class";
			if($tipo=='checkbox_array'){
				$cod .= " checkbox' style='position: relative; display: block; margin-top: 10px; margin-bottom: 10px;'>";
			}else{
				if($tipo=='button'){
					$cod .= "' style='margin-top: 25px;'>";
				}else{
					$cod .= "'>";
				}
			}
		}else{
			$cod = null;
		}
		$cod .= $this->js_script;

		if($tipo!='hidden' and $tipo!="checkbox_array"){
			if($tipo=='h3'){
				$cod .= "<label style='display:block;'>";
				$cod .= '<center><h3>';
			}else{
				$cod .= '<label for="id_'.$name_input.'">';
			}
			if($this->tooltip)
				$cod.= '<span data-toggle="tooltip" title="'.$this->tooltip.'">';
			$cod .= $nome;
			if($this->tooltip){
				$cod.= '</span>';
				$this->tooltip = null;
			}
			if($tipo=='h3')
				$cod .= '</h3></center>';
			$cod .= "</label>";
		}
		
		if($this->input_group_addon_front or $this->input_group_addon_back){
			$cod .= '<div class="input-group">';
			if($this->input_group_addon_front)
				$cod .= '<span class="input-group-addon">'.$this->input_group_addon_front.'</span>';
		}

		if($tipo=='text' or $tipo=='ajaxtext' or $tipo=='dinheiro'){
			
			if($tipo=='ajaxtext'){
				
				$this->ajax_cod .= '<script type="text/javascript">
							$(function(){
    							var availableTags_'.$name_input.' = [';
				for($i=0; $i<count($this->ajax_valores); $i++){
					if($i>0)
						$this->ajax_cod .= ',';

					if($this->ajax_index){
						$this->ajax_cod .= $this->ajax_valores[$i];
					}else{
						$this->ajax_cod .= '"'.$this->ajax_valores[$i].'"';
					}
				}
				$this->ajax_cod .= '	];';

				if($this->ajax_index){
					$this->ajax_cod .= '	
						$("#id_'.$name_input.'").autocomplete({
							source: function( request, response){
								response($.map(availableTags_'.$name_input.', function (item){
									var n = item.'.$this->ajax_index[0].'.indexOf(request.term);
									
									if(n>-1){
										return{';

					for($i=0; $i<count($this->ajax_index); $i++){
						if($i>0)
							$this->ajax_cod .= ',';
						$this->ajax_cod .= $this->ajax_index[$i].': item.'.$this->ajax_index[$i];

					}

					$this->ajax_cod .= '
										}
									}
								}))
							},
							select: function(event, ui){';
					for($i=0; $i<count($this->ajax_index); $i++){
						$this->ajax_cod .= '$("#id_'.$this->ajax_index[$i].'").val(ui.item.'.$this->ajax_index[$i].');';
					}
					$this->ajax_cod .= '
							},
							minLength:0,
							autoFocus: true
						});
					';
				}else{
					$this->ajax_cod .= '	
						$("#id_'.$name_input.'").autocomplete({
							source: availableTags_'.$name_input.'
						});
					';
				}


				$this->ajax_cod .= '
						});
					</script>';
				$this->ajax_valores = $this->ajax_index = null;
			}

			if($tipo=='dinheiro'){
				$cod .= '<div class="input-group"><span class="input-group-addon">R$</span>';
			}

			$cod .= "<input type='text' class='form-control' name='$name_input' placeholder='$placeholder' value='$valor' ";

			if($tipo=='ajaxtext')
				$cod.= "autocomplete='off' ";
			
		}elseif($tipo=='longtext'){

			$cod .= "<textarea class='textarea' name='$name_input' placeholder='$placeholder' style='width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;''>$valor</textarea>";

		}elseif($tipo=='select' or $tipo=='select2'){

			if($tipo=='select2'){
				$select2 = 'select2';
			}else{
				$select2 = null;
			}

			$cod.= "<select class='form-control ".$select2."' name='$name_input' style='width: 100%;' ".$this->js_action.">";
            for($i=0; $i<count($placeholder); $i++){
            	if($valor==$placeholder[$i][0]){		
            		$cod.= '<option value="'.$placeholder[$i][0].'" selected="yes">'.$placeholder[$i][1].'</option>';
            	}else{
            		$cod.= '<option value="'.$placeholder[$i][0].'">'.$placeholder[$i][1].'</option>';	
            	}
            	
            }
            $cod.= "</select>";

		}elseif($tipo=='checkbox_array'){


			if($placeholder == $valor){
				$cod .= "<label><input type='checkbox' name='$name_input' value='$valor' checked='yes'> $mascara</label>";
			}else{
				$cod .= "<label><input type='checkbox' name='$name_input' value='$valor'> $mascara</label>";
			}



		}elseif($tipo=='email'){
			
			$cod .= "<input type='email' class='form-control' name='$name_input' placeholder='$placeholder' value='$valor' ";
			
		}elseif($tipo=='file'){

			if($valor){
				$cod.= "<br><img src='$valor' style='max-width:150px; max-height:150px;'>";
			}
			$cod .= "<input type='file' class='form-control' name='$name_input' accept='image/jpeg,image/png' ".$this->js_action.">";
			$cod .= "<span class='sugestao' style='color:grey;'>$placeholder<span>";

		}elseif($tipo=='button'){

			$cod .= "<a href='$valor' class='form-control btn btn-primary' ".$this->js_action.">$placeholder</a>";

		}elseif($tipo=='hidden'){

			$cod .= "<input type='hidden' name='$name_input' id='id_".$name_input."' value='$valor' ".$this->js_action.">";

		}elseif($tipo=='texthidden'){
			$cod .= "<br>$valor<input type='hidden' name='$name_input' value='$valor'>";			
		}

		
		if($tipo != 'checkbox_array'){

			if($mascara=='data'){
				//$this->mascara .= '$("input[name=\''.$name_input.'\'").inputmask({alias: "dd/mm/yyyy"}); ';
				$this->mascara .= '$("input[name=\''.$name_input.'\']").attr("id", "datemask"); ';
				$this->mascara .= '
				$("input[name=\''.$name_input.'\']").datepicker({
				    format: "dd/mm/yyyy",
				    autoclose: true,
				    todayHighlight: true
				});';
				$cod .= " onKeyDown='Mascara(this,Data);' onKeyPress='Mascara(this,Data);' onKeyUp='Mascara(this,Data);' ";
			}elseif($mascara=='tel'){
				$this->mascara .= '$("input[name=\''.$name_input.'\'").inputmask(\'(99) 9999.9999[9]\'); ';
				$this->mascara .= '$("input[name=\''.$name_input.'\'").blur(function(event){ 
					if($(this).val().length != 15){
						$("input[name=\''.$name_input.'\'").inputmask(\'(99) 9999.9999[9]\')
					}else{
						$("input[name=\''.$name_input.'\'").inputmask(\'(99) 99999.999[9]\')
					}
				});';
			}elseif($mascara=='cpf'){
				$this->mascara .= '$("input[name=\''.$name_input.'\'").inputmask(\'999.999.999-99\'); ';
			}elseif($mascara=='cnpj'){
				$this->mascara .= '$("input[name=\''.$name_input.'\'").inputmask(\'99.999.999/9999-99\'); ';
			}elseif($mascara=='integer'){
				$this->mascara .= '$("input[name=\''.$name_input.'\'").inputmask(\'9[99999]\'); ';
			}elseif($mascara=='cep'){
				$this->mascara .= '$("input[name=\''.$name_input.'\'").inputmask(\'99999-999\'); ';
			}elseif($mascara=='ano'){
				$this->mascara .= '$("input[name=\''.$name_input.'\'").inputmask(\'9999[.9]\'); ';
			}elseif($mascara=='instagram'){
				$this->mascara .= '$("input[name=\''.$name_input.'\'").focus(function(){
			        if($("input[name=\''.$name_input.'\'").val() == \'\'){
			            $("input[name=\''.$name_input.'\'").val(\'@\');
			        }
			    });
			    $("input[name=\''.$name_input.'\'").keyup(function(){
			        if($("input[name=\''.$name_input.'\'").val() == \'\'){
			            $("input[name=\''.$name_input.'\'").val(\'@\');
			        }
			    });
			    $("input[name=\''.$name_input.'\'").blur(function(){
			        if($("input[name=\''.$name_input.'\'").val() == \'@\'){
			            $("input[name=\''.$name_input.'\'").val(\'\');
			        }
			    });';
			}elseif($mascara=='facebook'){
				$this->mascara .= '$("input[name=\''.$name_input.'\'").focus(function(){
			        if($("input[name=\''.$name_input.'\'").val() == \'\'){
			            $("input[name=\''.$name_input.'\'").val(\'http://www.facebook.com/\');
			        }
			    });
			    $("input[name=\''.$name_input.'\'").keyup(function(){
			        if($("input[name=\''.$name_input.'\'").val() == \'\'){
			            $("input[name=\''.$name_input.'\'").val(\'http://www.facebook.com/\');
			        }
			    });
			    $("input[name=\''.$name_input.'\'").blur(function(){
			        if($("input[name=\''.$name_input.'\'").val() == \'http://www.facebook.com/\'){
			            $("input[name=\''.$name_input.'\'").val(\'\');
			        }
			    });';
			}elseif($mascara!=null){
				$cod .= " onKeyDown='Mascara(this,$mascara);' onKeyPress='Mascara(this,$mascara);' onKeyUp='Mascara(this,$mascara);' ";
			}
			
		}


		if($tipo=='text' or $tipo=='ajaxtext' or $tipo=='email' or $tipo=='dinheiro'){
			if($tipo=='ajaxtext' or $mascara=='data')
				$cod .= ' autocomplete="off" ';
			$cod .= " id='id_".$name_input."' maxlength='".$this->max."' ".$this->js_action.">";

			if($this->input_group_addon_front or $this->input_group_addon_back){
				if($this->input_group_addon_back)
					$cod .= '<span class="input-group-addon">'.$this->input_group_addon_back.'</span>';
				$cod .= '</div>';
				$this->input_group_addon_front = null;
				$this->input_group_addon_back = null;
			}
		}

		if($tipo=='dinheiro'){
			$cod .= '</div>';
		}			

		if($tipo!='hidden')
		$cod .= "</div>";
		$this->arg[] = $cod;
		
		$this->error 		= false;
		$this->colspan 		= null;
		$this->js_action 	= null;
		$this->js_script	= null;
		$this->div_id		= null;
		$this->div_class	= null;
		$this->max 			= 50;
	}

	public function set_arg_generic($cod){

		if($this->colspan)
			$this->colspan = 'col-xs-'.$this->colspan;

		$cod = "<div class='form-group $this->colspan $this->div_class'>".$cod.'</div>';

		$this->arg[] = $cod;

		$this->colspan = null;
	}

	public function set_js($cod, $action, $script = true){
		$this->js_action = $action;
		
		if($script)
			$this->js_script = '<script type="text/javascript">';
		$this->js_script .= $cod;
		
		if($script)
			$this->js_script .= '</script>';
	}

	public function inputEndereco(
			$rua = array(
				'colspan' => 4, 
				'erro' => false,
				'valor' => ''
				),
			$bairro = array(
				'colspan' => 4,
				'erro' => false,
				'valor' => ''
				)
			){
		
		$rua['colspan'] = 'col-xs-'.$rua['colspan'];
		if($rua['erro'])
			$rua['erro'] = 'has-error';

		$cod = "<div class='form-group ".$rua['colspan']." ".$rua['erro']."'>";
		$cod .= "<label>Rua</label>";
		$cod .= "<input type='text' class='form-control' name='rua' placeholder='Rua, Av., Travessa, Logradouro...' value='".$rua['valor']."'>";
		$cod .= "</div>";
	}

	public function get_form(){
		$nome_formulario = strtr(strtolower($this->name), unserialize(CHAR_MAP));
		$form = "<form role='form' action='$this->action' name='$nome_formulario' method='$this->method' enctype='$this->enctype'>";
		$form.= "<input type='hidden' name='op' value='".$this->op."'>";
		for($i=0; $i<count($this->arg); $i++){
			$form .= $this->arg[$i];
		}
		


		if($this->op == 'novo'){
			$form.= "<div class='box-footer'>";
			if($this->button_ativar_deletar){
				$form.= "<button type='reset' class='btn btn-default pull-left'>Cancelar</button>";
			}
			if($this->button_editar){
				$form.= "<button type='submit' class='btn btn-primary pull-right'>Salvar</button>";
			}
			$form.= "</div>";
			$form.= "</form>";
		}elseif($this->op == 'editar'){
			$form.= "<input type='hidden' name='id' value='".base64_encode($this->id)."'>";
			$form.= "<div class='box-footer'>";
			if($this->button_ativar_deletar){
				if(registro($this->id, $this->tabela, $this->coluna)==1){

					$popup_deletar = new popup(null, 'amarelo');
					
					$msg = "Deseja mesmo deletar este registro?<br><br>";
					$msg.= "<button type='button' onclick='document.".$nome_formulario."_deletar.submit();' class='btn btn-danger'>Sim</button>";
					$msg.= "<button type='button' onclick='".$popup_deletar->get_action_hide()."' class='btn btn-primary pull-right'>Não</button>";
					$popup_deletar->texto = $msg;
					$popup_deletar->display = 'none';

					$form.= $popup_deletar->get_pop();

					$form.= "<button type='button' onclick='".$popup_deletar->get_action_show()."' class='btn btn-danger pull-left'>Deletar</button>";
				}else{
					$form.= "<button type='button' onclick='document.".$nome_formulario."_ativar.submit();' class='btn btn-warning pull-left'>Ativar</button>";
				}
			}

			if($this->button_editar){
				$form.= "<button type='submit' class='btn btn-primary pull-right'>Editar</button>";
			}
			$form.= "</div>";
			$form.= "</form>";
			$form.= "<form action='".$this->action."' name='".$nome_formulario."_deletar' method='".$this->method."' enctype='".$this->enctype."'>";
			$form.= "<input type='hidden' name='id' value='".base64_encode($this->id)."'>";
			$form.= "<input type='hidden' name='op' value='deletar'>";
			$form.= "</form>";
			$form.= "<form action='".$this->action."' name='".$nome_formulario."_ativar' method='".$this->method."' enctype='".$this->enctype."'>";
			$form.= "<input type='hidden' name='id' value='".base64_encode($this->id)."'>";
			$form.= "<input type='hidden' name='op' value='ativar'>";
			$form.= "</form>";
		}elseif($this->op == 'pesquisar'){
			$form.= "<div class='box-footer'>";
			$form.= "<button type='submit' class='btn btn-primary pull-right'>Pesquisar</button>";
			$form.= "</div>";
			$form.= "</form>";
		}elseif($this->op == 'deletar'){
			$form.= "<div class='box-footer'>";
			$form.= "<button type='reset' class='btn btn-default pull-left'>Cancelar</button>";
			$form.= "<button type='submit' class='btn btn-primary pull-right'>Salvar</button>";
			$form.= "</div>";
			$form.= "</form>";

			$form.= redirecionar($this->action, 1);
		}

		
		if($this->mascara){
			$mascara = $this->mascara;
			$this->mascara = '<script type="text/javascript"> ';
				$this->mascara.= '$(function(){';
					$this->mascara.= $mascara;
				$this->mascara.= '});';
			$this->mascara.= '</script>';
		}
		

		return $this->ajax_cod.$form;
	}



}


?>
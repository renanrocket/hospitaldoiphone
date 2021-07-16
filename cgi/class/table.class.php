<?php
	
	class table{

		private $title;
		private $column;
		private $entries;
    private $id;
    public $focus;
    public $focus_color;
    public $footer;


		function __construct($title, $column, $entries, $id = 'table'){

			$this->title        = $title;
			$this->column       = $column;
			$this->entries      = $entries;
      $this->id           = $id;
      $this->footer       = true;
      $this->focus_color  = 'red';

		}

    function set_focus($entrada){
      if($this->focus_color!='red'){
        $focus_style = 'style="color:'.$this->focus_color.';"';
      }else{
        $focus_style = 'class="focus"';
      }
      if($this->focus){
        $entrada = str_ireplace($this->focus, '<b '.$focus_style.'>'.strtoupper($this->focus).'</b>', $entrada);
      }

      return $entrada;

    }

		function set_table(){


      
      $cod = '<div class="row">';
        $cod.= '<div class="col-xs-12">';
          $cod.= '<div class="box">';
            $cod.= '<div class="box-header">';
              $cod.= '<h3 class="box-title">'.$this->title.'</h3>';
            $cod.= '</div>';
            $cod.= '<div class="box-body">';
            $cod.= '<table id="'.$this->id.'" class="table table-bordered table-striped">';
            # Header da table
              $cod.= '<thead>';
              $cod.= $this->columns($this->column);
              $cod.= '</thead>';                  
              # Corpo da table
              $cod.= '<tbody>';
                if($this->entries){

                  for($i=0; $i<count($this->entries); $i++){
                  
                    if(is_array($this->entries[$i])){
                      $cod.= '<tr>';
                      for($j=0; $j<count($this->entries[$i]); $j++){
                        //para o focus n atrapalhar no codigo para id
                        if($j==0){
                          $cod.= '<td>'.$this->entries[$i][$j].'</td>';
                        }else{
                          $cod.= '<td>'.$this->set_focus($this->entries[$i][$j]).'</td>';
                        }
                      }
                      $cod.= '</tr>';
                    }else{
                      
                      if($i==0)
                        $cod.= '<tr>';
                        if($i==0){
                          $cod.= '<td>'.$this->entries[$i].'</td>';
                        }else{
                          $cod.= '<td>'.$this->set_focus($this->entries[$i]).'</td>';
                        }
                      
                      if(($i+1)==count($this->entries))
                        $cod.= '</tr>';

                    }
                  }  
                }else{

                  $cod.= '<tr>';
                    if(!$this->focus){
                      $cod.= '<td style="text-align: center;" colspan="'.(count($this->column)+1).'"><b class="focus">Nenhum resultado encontrado</b>';
                    }else{
                      $cod.= '<td style="text-align: center;" colspan="'.(count($this->column)+1).'"><b class="focus">Nenhum resultado encontrado para '.$this->focus.'</b>';
                    }
                  $cod.= '</tr>';

                }
                
              $cod.= '</tbody>';
              # Footer da table
              if($this->footer){
                $cod.= '<tfoot>';
                $cod.= $this->columns($this->column);
                $cod.= '</tfoot>';
              }
              $cod.= '</table>';
            $cod.= '</div>';
          $cod.= '</div>';
        $cod.= '</div>';
      $cod.= '</div>';

      return $cod;
          
    }


    function get_script(){
      $cod = '<script type="text/javascript">
          $(function () {
            $("#'.$this->id.'").DataTable({
              "paging": true,
              "lengthChange": true,
              "searching": true,
              "ordering": true,
              "info": true,
              "autoWidth": true
            });
          });
        </script>';

        return $cod;
    }

    function columns($var){
        $cod = '<tr>';
          for($i=0; $i<count($var); $i++){
            $cod.= '<th>'.$var[$i].'</th>';
          }
        $cod.= '</tr>';
        return $cod;
      }


  }



?>
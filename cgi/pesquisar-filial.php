<?php

include 'inc/functions.inc.php';

is_logged_usuario();

extract($_POST);
extract($_GET);

if(isset($id))
    if(!is_numeric($id))
        $id = base64_decode($id);


$conn = TConnection::open(DB);



$sql = new TSqlSelect;
$sql->setEntity('filiais');
$sql->addColumn('id');
$sql->addColumn('nome');
$sql->addColumn('telefone_1');
$sql->addColumn('telefone_2');


$result = $conn->query($sql->getInstruction());
if($result->rowCount()){
	for($i=0; $i<$result->rowCount(); $i++){
		$entries[$i] = $result->fetch(PDO::FETCH_NUM);		
		if(registro($entries[$i][0], 'filiais', 'status')==1){
			$entries[$i][0] = '<a class="btn btn-primary" href="cadastrar-filial.php?op=visualizar&id='.base64_encode($entries[$i][0]).'">'.$entries[$i][0].'</a>';
		}else{
			$entries[$i][0] = '<a class="btn btn-danger" href="cadastrar-filial.php?op=visualizar&id='.base64_encode($entries[$i][0]).'">'.$entries[$i][0].'</a>';
		}
		
	}
	
}else{
	$entries = null;
}
$column = array('ID','Nome', 'Telefone 1', 'Telefone 2');

$table = new table('Filiais', $column, $entries);
$table->focus = null;
$script = $table->get_script();
$cod_pag = $table->set_table();

	
	

$html = new template;
$cod = $cod_pag;
$html->set_html($cod);
$html->get_html('Pesquisar Filiais', 'Pesquise por Filiais ao preencher este formulário');


?>

<?php echo $script; ?>


<?php

include 'inc/functions.inc.php';

is_logged_usuario();

extract($_POST);
extract($_GET);

if(isset($id))
    if(!is_numeric($id))
        $id = base64_decode($id);


$conn = TConnection::open(DB);


if(!isset($op)){
	
	$nome = $descricao = $categoria = $sub_categoria = $img_destaque = $preco = $desconto_maximo = $preco_promocional = $data_inicio_promocao = $data_termino_promocao = $garantia = null;
    $produto_img_qtd = 1;
    
    $popup = null;
    $op = 'novo';
    $id = $idProduto = 0;


}elseif($op=='novo' or $op=='editar'){
	
	$cont = count($_POST);
	$array = array_keys($_POST);
    $valida = true;
    

    $erro = 'Produto não cadastrado. <b>Motivo:</b><br>';
    //filtros
    if($nome==''){
        $valida = false;
        $erro.= 'Nome não descrita.<br>';
        $error['nome'] = true;
    }elseif($op=='novo'){
        $criterio = new TCriteria;
        $criterio->add(new TFilter('nome', '=', $nome));
        if($op=='editar'){
            $criterio->add(new TFilter('id', '<>', $id));
        }
        $sql = new TSqlSelect();
        $sql->setEntity('produto');
        $sql->addColumn('*');
        $sql->setCriteria($criterio);
        $result = $conn->query($sql->getInstruction());
        if($result->rowCount()){
            $valida = false;
            $erro.= 'Nome do produto informado já está cadastrado.<br>';
            $error['nome'] = true;
        }
    }
    

    if($preco==''){
        $valida = false;
        $erro.= 'Preço não descrita.<br>';
        $error['preco'] = true;
    }
    
    if($data_inicio_promocao!='' and $data_termino_promocao==''){
        $valida = false;
        $erro.= 'Você precisa determinar o termino da promoção.<br>';
        $error['data_termino_promocao'] = true;
    }

    if($data_inicio_promocao=='' and $data_termino_promocao!=''){
        $valida = false;
        $erro.= 'Você precisa determinar o inicio da promoção.<br>';
        $error['data_inicio_promocao'] = true;
    }

    if($preco_promocional=='' and $data_inicio_promocao!=''){
        $valida = false;
        $erro.= 'Você precisa determinar o preço da promoção.<br>';
        $error['preco_promocional'] = true;
    }

    if($categoria==''){
        $valida = false;
        $erro.= 'Categoria não descrita.<br>';
        $error['categoria'] = true;
    }

    if($sub_categoria==''){
        $valida = false;
        $erro.= 'Sub categoria não descrita.<br>';
        $error['sub_categoria'] = true;
    }
    if($_FILES['imagem_de_destaque']['size']==0 and $op=='novo'){
        $valida = false;
        $erro.= 'É necessário pelo menos a imagem de destaque deste produto.<br>';
        $error['imagem_de_destaque'] = true;
    }
    

    if($valida){
        
        $array_permitido = array('nome', 'descricao', 'categoria', 'sub_categoria', 'preco', 'preco_promocional', 'data_inicio_promocao', 'data_termino_promocao', 'garantia', 'desconto_maximo');

        if($op=='novo'){

            $sql = new TSqlInsert();
            $sql->setEntity('produto');
            for($i=$check=0; $i<$cont; $i++){
                if(in_array($array[$i], $array_permitido)){
                    if($array[$i]=='preco' or $array[$i] == 'preco_promocional' or $array[$i] == 'desconto_maximo'){
                        $sql->setRowData($array[$i], real($_POST[$array[$i]], 'float'));
                    }elseif($array[$i]=='data_inicio_promocao' or $array[$i] == 'data_termino_promocao'){
                        $sql->setRowData($array[$i], formataDataInv($_POST[$array[$i]]));
                    }else{
                        $sql->setRowData($array[$i], $_POST[$array[$i]]);
                    }
                }
            }
            $sql->setRowData('data', date('Y-m-d'));
            $sql->setRowData('status', 1);  
            
            $result = $conn->query($sql->getInstruction());

            if($result->rowCount()){

                $idProduto = $conn->lastInsertId();

                for($i=0; $i<=3; $i++){
                    if($i==0){
                        $var = 'imagem_de_destaque';
                    }else{
                        $var = 'imagem_da_galeria_'.$i;
                    }

                    if(isset($_FILES[$var])){
                        //movendo arquivo do produto
                        $pasta = END_PRODUTO;
                        $nome_imagem = $_FILES[$var]['name'];
                        $ext = strtolower(strrchr($nome_imagem, "."));
                        $nome_atual = strtr(strtolower($idProduto.'_'.$var), unserialize(CHAR_MAP)) . $ext;
                        $tmp = $_FILES[$var]['tmp_name'];
                        if (!is_dir($pasta)) {
                            mkdir($pasta, 0755, true);
                            chmod($pasta, 0755);
                        }
                        

                        if(move_uploaded_file($tmp, $pasta . $nome_atual)){
                            
                            $img_canvas = new canvas();
                            $img_canvas->carrega( $pasta.$nome_atual )
                              ->redimensiona( 540, 580, 'crop' )
                              ->hexa( '#000' )
                              ->grava($pasta.$nome_atual);
                            //540 x 580;

                            $sql = new TSqlInsert();
                            $sql->setEntity('produto_imagem');
                            $sql->setRowData('produto_id', $idProduto);
                            $sql->setRowData('end', $pasta . $nome_atual);
                            $sql->setRowData('tumb', "$i");
                            $result = $conn->query($sql->getInstruction());
                            

                        }

                    }


                }

                $pop = new popup(
                "Produto cadastrado com sucesso.");
                $popup = $pop->get_pop();

                $op = 'editar';
                $id = $idProduto;
                                              

            }else{

                $pop = new popup(
                "Não foi possível cadastrar o Produto.<br>
                Motivo: " . var_dump($conn->errorInfo()), 'vermelho');
                $popup = $pop->get_pop();

            }
     
       

        }elseif($op=='editar'){


            
            if(!is_numeric($id))
                $id = base64_decode($id);

            $criterio = new TCriteria;
            $criterio->add(new TFilter('id', '=', $id));
            $idProduto = $id;

            $sql = new TSqlUpdate();
            $sql->setEntity('produto');
            for($i=$check=0; $i<$cont; $i++){
                if(in_array($array[$i], $array_permitido)){
                    if($array[$i]=='preco' or $array[$i] == 'preco_promocional' or $array[$i] == 'desconto_maximo'){
                        $sql->setRowData($array[$i], real($_POST[$array[$i]], 'float'));
                    }elseif($array[$i]=='data_inicio_promocao' or $array[$i] == 'data_termino_promocao'){
                        $sql->setRowData($array[$i], formataDataInv($_POST[$array[$i]]));
                    }else{
                        $sql->setRowData($array[$i], $_POST[$array[$i]]);
                    }
                }
            }
            $sql->setCriteria($criterio);
            
            $result = $conn->query($sql->getInstruction());

            $resultImagem = false;
            for($i=0; $i<=3; $i++){
                if($i==0){
                    $var = 'imagem_de_destaque';
                }else{
                    $var = 'imagem_da_galeria_'.$i;
                }

                if(isset($_FILES[$var])){
                    //movendo arquivo da logomarca
                    $pasta = END_PRODUTO;
                    $nome_imagem = $_FILES[$var]['name'];
                    $ext = strtolower(strrchr($nome_imagem, "."));
                    $nome_atual = strtr(strtolower($idProduto.'_'.$var), unserialize(CHAR_MAP)) . $ext;
                    $tmp = $_FILES[$var]['tmp_name'];
                    if (!is_dir($pasta)) {
                        mkdir($pasta, 0755, true);
                        chmod($pasta, 0755);
                    }
                    
                    if(move_uploaded_file($tmp, $pasta . $nome_atual)){

                        $img_canvas = new canvas();
                        $img_canvas->carrega( $pasta.$nome_atual )
                          ->redimensiona( 540, 580, 'crop' )
                          ->hexa( '#000' )
                          ->grava($pasta.$nome_atual);
                        //540 x 580;
                        
                        $criterio = new TCriteria;
                        $criterio->add(new TFilter('produto_id', '=', $id));
                        $criterio->add(new TFilter('tumb', '=', $i));

                        $sql = new TSqlSelect();
                        $sql->setEntity('produto_imagem');
                        $sql->addColumn('*');
                        $sql->setCriteria($criterio);

                        $resultSelectImg = $conn->query($sql->getInstruction());

                        if($resultSelectImg->rowCount()){
                            $sql = new TSqlUpdate();
                            $sql->setCriteria($criterio);                            
                        }else{
                            $sql = new TSqlInsert();
                        }

                        $sql->setEntity('produto_imagem');
                        $sql->setRowData('produto_id', $idProduto);
                        $sql->setRowData('end', $pasta . $nome_atual);
                        $sql->setRowData('tumb', "$i");
                        $result = $conn->query($sql->getInstruction());

                        $resultImagem = true;


                    }

                }


            }
            
            if($result->rowCount() or $resultImagem){
                
                $pop = new popup(
                "Produto editado com sucesso.");
                $popup = $pop->get_pop();

                $op = 'editar';
                $id = $idProduto;


            }else{
              
                $pop = new popup(
                "Não foi possível editar o Produto.<br>
                Motivo: " . implode(' | ', $conn->errorInfo()), 'amarelo');
                $popup = $pop->get_pop();
                
            }

        }


        


    }else{
        $pop = new popup($erro, 'vermelho');
        $popup = $pop->get_pop();
        if($op=='novo'){
            $id = $idProduto = 0;
        }else{
            $idProduto = $id;
        }
    }
        




}elseif($op=='deletar' or $op=="ativar" or $op=="visualizar"){

    $nome = $descricao = $categoria = $sub_categoria = null;
    $produto_preco_qtd = $produto_img_qtd = 1;

    if(!is_numeric($id))
        $id = base64_decode($id);

    if($op=='deletar' or $op=='ativar'){

        if($op=="deletar"){
            $operacao = 'deletado';
        }elseif($op=="ativar"){
            $operacao = 'ativado';
        }
        
        $criterio = new TCriteria;
        $criterio->add(new TFilter('id', '=', $id));
        
        $sql = new TSqlUpdate();
        $sql->setEntity('produto');
        if($op=="deletar"){
            $sql->setRowData('status', '0');
        }elseif($op=="ativar"){
            $sql->setRowData('status', '1');
        }
        $sql->setCriteria($criterio);
        $result = $conn->query($sql->getInstruction());

        if($result->rowCount()){
            $pop = new popup("Produto $operacao com sucesso.<br>");
            $popup = $pop->get_pop();
        }else{
            $pop = new popup("Produto não pode ser $operacao.<br>", 'vermelho');
            $popup = $pop->get_pop();
        }
    }

    $criterio = new TCriteria();
    $criterio->add(new TFilter('id', '=', $id));

    $sql = new TSqlSelect();
    $sql->setEntity('produto');
    $sql->addColumn('*');
    $sql->setCriteria($criterio);
    $result = $conn->query($sql->getInstruction());

    if($result->rowCount()){
        extract($result->fetch(PDO::FETCH_ASSOC));

        $idProduto = $id;

        $preco = real($preco, 'real');
        $preco_promocional = real($preco_promocional, 'real');

    }

    if($op=="visualizar")
        $popup = null;
    if($op!="deletar")
        $op = 'editar';

}


//preparando todos os ajax da pagina num unico select para otimizar a velocidade

$sql = new TSqlSelect();
$sql->setEntity('produto');
$sql->addColumn('nome as produtoNome');
$sql->addColumn('categoria as produtoCategoria');
$sql->addColumn('sub_categoria as produtoSubCategoria');

$result = $conn->query($sql->getInstruction());

$produtoNomes = $produtoCategorias = $produtoSubCategoria = null;

for($i=0; $i<$result->rowCount(); $i++){
    extract($result->fetch(PDO::FETCH_ASSOC));

    $produtoNomes[] = $produtoNome;
    $produtoCategorias[] = $produtoCategoria;
    $produtoSubCategorias[] = $produtoSubCategoria;

}

if($result->rowCount()){

    /*
    $produtoCategorias[] = 'Brindes';
    $produtoCategorias[] = 'Gráfica';
    $produtoCategorias[] = 'Serigrafia';

    $produtoUnds[] = 'Und';
    $produtoUnds[] = 'Metro quadrado';
    */

    //todos os arrays unicos dos ajax da pagina
    $produtoNomes = array_unique($produtoNomes);
    $produtoCategorias = array_unique($produtoCategorias);
    $produtoSubCategorias = array_unique($produtoSubCategorias);

}else{

    $produtoNomes = $produtoCategorias = $produtoSubCategorias = array();

}



$form = new form('Cadastrar Produto', 'cadastrar-produto.php', $op);
$form->id = $idProduto;
$form->tabela = 'produto';


if($op=='editar'){
    $visualizar = '<span style="float:right;"><a target="blank_" href="../single-product.php?p='.base64_encode($idProduto).'"><span class="fa fa-file-photo-o"></span> visualizar</a></span>';
}else{
    $visualizar = null;
}

$form->colspan = 12;

$form->set_arg('h3', $visualizar);

$form->colspan = 3;
if(isset($error['nome']))
    $form->error = true;
$form->max = 250;
$form->ajax_valores = array_values($produtoNomes);
$form->set_arg('ajaxtext', 'Nome', 'Nome do produto', $nome);

$form->colspan = 3;
if(isset($error['categoria']))
    $form->error = true;
$form->ajax_valores = array_values($produtoCategorias);
$form->set_arg('ajaxtext', 'Categoria', '', $categoria);

$form->colspan = 3;
if(isset($error['sub_categoria']))
    $form->error = true;
$form->ajax_valores = array_values($produtoSubCategorias);
$form->set_arg('ajaxtext', 'Sub Categoria', '', $sub_categoria);

$form->colspan = 3;
if(isset($error['garantia']))
    $form->error = true;
$garantias[0][] = 'sem garantia';
$garantias[0][] = 'sem garantia';
$garantias[1][] = 3;
$garantias[1][] = '3 meses';
$garantias[2][] = 6;
$garantias[2][] = '6 meses';
$garantias[3][] = 12;
$garantias[3][] = '12 meses';
$garantias[4][] = 24;
$garantias[4][] = '24 meses';
$garantias[5][] = 36;
$garantias[5][] = '36 meses';
$garantias[6][] = 48;
$garantias[6][] = '48 meses';
$garantias[7][] = 60;
$garantias[7][] = '60 meses';
$garantias[8][] = 'infinita';
$garantias[8][] = 'infinita';
$form->set_arg('select', 'Garantia', $garantias, $garantia);


$form->colspan = 12;
$form->set_arg('longtext', 'Descrição', '', $descricao);



$form->colspan = 12;
$form->set_arg('h3', 'Regra de preços');

$promocaoDestaques[0][] = 0;
$promocaoDestaques[0][] = 'Não';
$promocaoDestaques[1][] = 1;
$promocaoDestaques[1][] = 'Sim';

    
if(strstr($data_inicio_promocao, '-'))
    $data_inicio_promocao = formataData($data_inicio_promocao, false, true);

if(strstr($data_termino_promocao, '-'))
    $data_termino_promocao = formataData($data_termino_promocao, false, true);

$form->colspan = 2;
if(isset($error['preco']))
    $form->error = true;
$form->div_id = 'preco';
$form->tooltip = 'Preço deste produto';
$form->input_group_addon_front = 'R$';
$form->set_arg('text', 'Preço', '00,00', $preco, 'Valor2');

$form->colspan = 2;
if(isset($error['preco']))
    $form->error = true;
$form->div_id = 'desconto_maximo';
$form->tooltip = 'Desconto máximo permitido para este produto';
$form->input_group_addon_back = '%';
$form->set_arg('text', 'Desconto Máximo', '10%', $desconto_maximo, 'Valor2');

$form->colspan = 2;
$form->div_id = 'preco_promocional';
$form->tooltip = 'Preço promocional deste produto. Marque o inicio desta promoção preenchendo o campo da data inicio promoção';
$form->input_group_addon_front = 'R$';
$form->set_arg('text', 'Preço promocional', '00,00', $preco_promocional, 'Valor2');

$form->colspan = 3;
$form->div_id = 'data_inicio_promocao';
$form->tooltip = 'Esta data irá acionar o inicio do preço da promoção, se esta data ou o valor do preço da promoção estiverem vazios, a regra não funcionará.';
$form->set_arg('text', 'Data inicio promoção', 'dd/mm/AAAA', $data_inicio_promocao, 'data');

$form->colspan = 3;
$form->tooltip = 'Esta data irá acionar o termino do preço da promoção, se esta data estiver vazia, a promoção irá durar para sempre.';
$form->div_id = 'data_termino_promocao_';
$form->set_arg('text', 'Data termino promoção', 'dd/mm/AAAA', $data_termino_promocao, 'data');




if($idProduto){

    $img_destaque = produto_imagem($idProduto, 'tumb', null, true, false);
    $end_1 = produto_imagem($idProduto, 1, null, true, false);
    $end_2 = produto_imagem($idProduto, 2, null, true, false);
    $end_3 = produto_imagem($idProduto, 3, null, true, false);

    $criterio = new TCriteria();
    $criterio->add(new TFilter('produto_id', '=', $idProduto));

    $sql = new TSqlSelect();
    $sql->setEntity('produto_imagem');
    $sql->addColumn('*');
    $sql->setCriteria($criterio);
    $result = $conn->query($sql->getInstruction());

    $produto_img_qtd = $result->rowCount() - 1;
    if($produto_img_qtd<1)
        $produto_img_qtd = 1;

}



$form->colspan = 12;
$form->set_arg('h3', 'Imagem de destaque');

$form->colspan = 12;
$popup_file = new popup('Tamanho da foto muito grande!<br>Tamanho maximo 3mb.', 'vermelho');
$popup_file->display = 'none';
//tamanho maximo 3 mb
$tamanhoMax = "3000000";
$cod_js = 
"<script type='text/javascript'>
    //função para verificar se o arquivo é maior que 3MB antes de o usuário enviar o arquivo
    function checarTamanho(arq){
        //this.files[0].size gets the size of your file.
        if(arq.files[0].size >= ".$tamanhoMax."){
            ".$popup_file->get_action_show()."
            $(arq).attr('value', '');
        }
    }   
</script>";
$cod_js .= $popup_file->get_pop();
$form->set_js($cod_js, 'onchange="checarTamanho(this);"', false);
!isset($img_destaque) ? $img_destaque = null: false;
if(isset($error['imagem_de_destaque']))
    $form->error = true;
$form->set_arg('file', 'Imagem de destaque', 'Sugestão imagem de 555px x 600px', $img_destaque);


$form->colspan = 12;
$form->set_arg('hidden', 'produto_img_qtd', null, $produto_img_qtd);
$form->colspan = 12;
$cod = "
function show_hide_produto_img(op){

    var produto_img_qtd = parseInt($('input[name=\"produto_img_qtd\"]').val());

    if(op == 'mais'){
        if(produto_img_qtd >= 3){
            alert('Quantidade de imagens maximo atingida.');
        }else{
            produto_img_qtd += 1;
        }
    }else if(op == 'menos'){
        if(produto_img_qtd <= 1){
            alert('Quantidade de imagens mimima atingida.');
        }else{
            produto_img_qtd -= 1;
        }
    }

    for(var i = 1; i <= 3 ; i++ ){
        if(i <= produto_img_qtd){
            
            $('#img_' + i).show();
            

        }else if(i > produto_img_qtd){
            
            $('#img_' + i).hide();

        }
    }

    $('input[name=\"produto_img_qtd\"]').val(produto_img_qtd);
}
";
$form->set_js($cod, null);
$botoes = '<span onclick="show_hide_produto_img(\'mais\');" class="fa fa-plus-circle" style="cursor:pointer;" data-toggle="tooltip" title="Adicionar imagem"></span> Imagens da galeria <span onclick="show_hide_produto_img(\'menos\');" class="fa fa-minus-circle" style="cursor:pointer;" data-toggle="tooltip" title="Remover imagem"></span>';
$form->set_arg('h3', $botoes);




for($i = 1; $i <= 3; $i++){

    $var_end                     = 'end_'.$i;
    

   

    $form->colspan = 12;
    $popup_file = new popup('Tamanho da foto muito grande!<br>Tamanho maximo 3mb.', 'vermelho');
    $popup_file->display = 'none';
    //tamanho maximo 3 mb
    $tamanhoMax = "3000000";
    $cod_js = 
    "<script type='text/javascript'>
        //função para verificar se o arquivo é maior que 3MB antes de o usuário enviar o arquivo
        function checarTamanho_".$i."(arq){
            //this.files[0].size gets the size of your file.
            if(arq.files[0].size >= ".$tamanhoMax."){
                ".$popup_file->get_action_show()."
                $(arq).attr('value', '');
            }
        }   
    </script>";
    $cod_js .= $popup_file->get_pop();
    $form->set_js($cod_js, 'onchange="checarTamanho_'.$i.'(this);"', false);
    !isset($$var_end) ? $$var_end  = null: false;
    $form->div_id = 'img_'.$i;
    $form->set_arg('file', 'Imagem da galeria '.$i, 'Sugestão imagem de 555px x 600px', $$var_end);

    
}



$html = new template;
$cod = $html->set_box('Formulário de cadastramento de produto', $form->get_form().$popup);
$html->set_html($cod);
$html->get_html('Cadastrar produto', 'Cadastre um produto ao preencher esse formulário');

echo $form->mascara;

?>


<script type="text/javascript">
    var visualizar = 1;

    $(function(){

        var produto_img_qtd = parseInt($('input[name="produto_img_qtd"]').val());
        for(i = 1; i <= 3; i++ ){
            if(i <= produto_img_qtd){
                $('#img_' + i).show();
            }else if(i > produto_img_qtd){
                $('#img_' + i).hide();
            }
        }

        $('input[name="qtd_ponta_de_estoque"]').change(function(){
            $('#id_qtd_1').val($(this).val());
            var class_qtd = $('#qtd_1').attr('class') + ' has-warning';
            $('#qtd_1').attr('class', class_qtd);
        });

    });

    
</script>
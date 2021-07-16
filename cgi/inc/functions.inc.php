<?php

function reportarErroPhp($errno, $errstr, $errfile, $errline) {
    $arquivo_erro = 'inc/error.inc.php';
    
    switch ($errno) {
        case E_USER_ERROR :
            if(!file_exists($arquivo_erro)){
                include_once '../'.$arquivo_erro;
            }else{
                include_once $arquivo_erro;
            }
            exit(1);
            break;

        case E_USER_WARNING :
            if(!file_exists($arquivo_erro)){
                include_once '../'.$arquivo_erro;
            }else{
                include_once $arquivo_erro;
            }
            break;

        case E_USER_NOTICE :
            if(!file_exists($arquivo_erro)){
                include_once '../'.$arquivo_erro;
            }else{
                include_once $arquivo_erro;
            }
            break;

        default :
            if(!file_exists($arquivo_erro)){
                include_once '../'.$arquivo_erro;
            }else{
                include_once $arquivo_erro;
            }
            break;
    }

    //Don't execute PHP internal error handler
    return true;
}
// set to the user defined error handler
$old_error_handler = set_error_handler("reportarErroPhp");
//error_reporting(0);

define('LIMIT', 2000);

define('DB', 'hospitaliphone');

define('NOME_EMPRESA', 'Hospital dos Iphones');
define('NOME_EMPRESA_SIGLA', 'HI');
define('NOME_EMPRESA_BLACK', 'Hospital');
define('NOME_EMPRESA_LIGHT', 'Iphones');
define('NOME_EMPRESA_SLOGAN', '');

define('CNPJ_EMPRESA', 'RAZAO SOCIAL / CNPJ');
define('ENDERECO_EMPRESA', 'ENDEREÇO');
define('TELEFONE_EMPRESA', 'TELEFONES');
define('IMG_EMPRESA', '../img/logo.png');


define('END_SITE', 'http://hospitaldosiphones.com.br');
define('HOSTMAIL', 'a2plcpnl0835.prod.iad2.secureserver.net');
define('FROM', 'Contato '.NOME_EMPRESA);
define('EMAIL', 'contato@faiskabrindes.com.br');
define('SENHA', 'faiska1986');
define('HOSTMAIL_SUPORTE', 'mail.rocketsolution.com.br');
define('PORT_SUPORTE', '587');
define('FROM_SUPORTE', 'Contato Rocket Solution');
define('EMAIL_SUPORTE', 'faleco@rocketsolution.com.br');
define('SENHA_SUPORTE', 'ROCKETadmin99');
define('IMGASS', 'http://www.rocketsolution.com.br/images/logo-rocket.png');
//usando a função serialize constantes podem carregar ARRAY, mas devemos lembrar que quando formos utiliza-las devemos
//usar a funcção unserialize para converter novamente em vetor
$map = array('á' => 'a', 'à' => 'a', 'ã' => 'a', 'â' => 'a', 'é' => 'e', 'ê' => 'e', 'í' => 'i', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ú' => 'u', 'ü' => 'u', 'ç' => 'c', 'Á' => 'a', 'À' => 'a', 'Ã' => 'a', 'Â' => 'a', 'É' => 'e', 'Ê' => 'e', 'Í' => 'i', 'Ó' => 'o', 'Ô' => 'o', 'Õ' => 'o', 'Ú' => 'u', 'Ü' => 'u', 'Ç' => 'c',  ' ' => '_', '.' => '', '/' => '_', '-' => '', '?' => '_', '(' => '', ')' => '', '+' => '_', ',' => '');
define('CHAR_MAP', serialize($map));
define('END_CLIENTE', 'img/clientes/');
define('END_USUARIO', 'img/usuarios/');
define('END_PRODUTO', 'img/produtos/');
define('END_OS', 'img/os/');
date_default_timezone_set('America/Fortaleza');

//define ("PS_EMAIL", "renan.the.rock@gmail.com"); // constante para pagamento ao pagSeguro do sistema
//define ("PS_TOKEN", "C2423CC3E7434DC99346A1C0E0DDF373"); //segunda constante para pagamento ao pagSeguro do sistema

//carregar automaticamente as classes
function my_autoload($classe){
    $class_file = "class/{$classe}.class.php";
    $class_file_ado = "app.ado/{$classe}.class.php";
    $class_file_mail = "plugins/PHPMailer-master/class.{$classe}.php";
    $class_file_canvas = "plugins/canvas/{$classe}.php";
    $pasta = "";
    $cgi = "";
    for($j=0; $j<2; $j++){
        if($j==1){
            $cgi = "cgi/";
            $pasta = "";
        }
        for($i=0; $i<4; $i++){
            if($i>0){
                $pasta .= "../";
            }
            if(file_exists($pasta.$cgi.$class_file)){
                include_once $pasta.$cgi.$class_file;
                break;
            }elseif(file_exists($pasta.$cgi.$class_file_ado)){
                include_once $pasta.$cgi.$class_file_ado;
                break;
            }elseif(file_exists($pasta.$cgi.$class_file_mail)){
                include_once $pasta.$cgi.$class_file_mail;
                break;
            }elseif(file_exists($pasta.$cgi.$class_file_canvas)){
                include_once $pasta.$cgi.$class_file_canvas;
                break;
            }
        }
    }
}

spl_autoload_register("my_autoload");

$conn = TConnection::open(DB);
$sql = new TSqlSelect();
$sql->setEntity('administrativo_email');
$sql->addColumn('tax');
$sql->addColumn('valor');
$result = $conn->query($sql->getInstruction());
if($result->rowCount()){
    for($i=0; $i<$result->rowCount(); $i++){
        $var = $result->fetch(PDO::FETCH_ASSOC);
        extract($var);
        define($tax, $valor);
    }
}

//args = array(coluna, operação, valor)
function usuario_get($args){
 
   $conn = TConnection::open(DB);

    foreach($args as $var1){
        $criterio = new TCriteria();
        $criterio->add(new TFilter($var1[0], $var1[1], $var1[2]));
    }


    $sql = new TSqlSelect;
    $sql->setEntity('usuario');
    $sql->addColumn('*');
    if(isset($criterio)){
        $sql->setCriteria($criterio);
    }

    $result = $conn->query($sql->getInstruction());

    if($result->rowCount()){
        $args = null;
        for($i=0; $i<$result->rowCount();$i++){
            $args[] = $result->fetch(PDO::FETCH_ASSOC);
        }
        return $args;
    }else{
        return false;
    }

}

//id do usuario, tipo = 'imagem' ou 'miniatura'
function usuario_img_get($id, $tipo = 'imagem'){

    $conn = TConnection::open(DB);

    $criterio = new TCriteria;
    $criterio->add(new TFilter('id_usuario', '=', $id));



    $sql = new TSqlSelect;
    $sql->setEntity('usuario_imagem');
    $sql->addColumn($tipo);
    if(isset($criterio)){
        $sql->setCriteria($criterio);
    }

    $result = $conn->query($sql->getInstruction());

    if($result->rowCount()){

        $row = $result->fetch(PDO::FETCH_ASSOC);
        extract($row);

        return '<img src="'.$$tipo.'">';

    }else{
        return false;
    }

}

//id = id do usuario, credencial opcional, caso preenchido irá retornar boleano, caso não preenchido
//irá retornar todas as credenciais do usuário
function usuario_get_credenciais($id, $credencial = null){

    $conn = TConnection::open(DB);

    $criterio = new TCriteria();
    $criterio->add(new TFilter('id_usuario', '=', $id));
    if($credencial){
        $criterio->add(new TFilter('ferramenta', 'like', $credencial));
    }

    $sql = new TSqlSelect;
    $sql->setEntity('usuario_credenciais');
    $sql->addColumn('*');
    $sql->setCriteria($criterio);

    $result = $conn->query($sql->getInstruction());

    if($result->rowCount()){
        if($credencial){

            $arg = null;

            for($i=0; $i<$result->rowCount(); $i++){
                $arg[] = $result->fetch(PDO::FETCH_ASSOC);
            }
            return $arg;

        }else{
            return true;
        }
    }else{
        return false;
    }

}

//url, tempo para redirecionar, array(nome do get, variavel do get)
function redirecionar($url, $tempo, $arg = null){
    echo '<meta http-equiv="refresh" content="'.$tempo.';url='.$url;
        if($arg){
            echo '?';
            for($i=0; $i<count($arg); $i++){
                if($i>0){
                    echo '&';
                }
                echo $arg[$i][0].'='.$arg[$i][1];
            }
        }

    echo '">';
}


//funcao q seleciona o atributo da tabela a partir de um resultado do PK
//pk eh o numero do cont
//nomeTabela eh o nome da tabela a qual se refere
//atributo eh o numero do atributo na sequencia q segue a tabela
function registro($pk, $nomeTabela, $atributo_exibir, $atributo_de_busca = "id") {


    $conn = TConnection::open(DB);

    $criterio = new TCriteria;
    $criterio->add(new TFilter($atributo_de_busca, "=", $pk));

    $sql = new TSqlSelect;
    $sql->setEntity($nomeTabela);
    $sql->addColumn($atributo_exibir);
    $sql->setCriteria($criterio);
    $result = $conn->query($sql->getInstruction());
    if($result->rowCount()){
        $row = $result->fetch(PDO::FETCH_ASSOC);
        extract($row);
        return $$atributo_exibir;
    }else{
        return '';
    }
}


//funcao para selecionar a mascara nas input text
##  Tipos de mascara ##
/*
 * Integer Telefone TelefoneCall Cpf Cep Cnpj Romanos Data Hora Valor Valor2 Valor3 Area Placa
 */
function mascara($mascara, $tamanho = null, $codExtra = null , $onKeyDown = null, $onKeyPress = null, $onKeyUp = null) {
    $cod = "onKeyDown='Mascara(this,$mascara); $onKeyDown' onKeyPress='Mascara(this,$mascara); $onKeyPress' onKeyUp='Mascara(this,$mascara); $onKeyUp'";

    if ($tamanho) {
        $cod .= " maxlength='$tamanho'";
    }
    if ($codExtra) {
        $cod .= " " . $codExtra;
    }

    return $cod;
}


//funcao formataDataInv, formata a data para forma do banco de dados. Recebe uma variavel assim DD/MM/YYYY HH:II:SS
//e a transforma assim: YYYY-MM-DD HH:II:SS
function formataDataInv($data) {
    if ($data) {
        if(strstr($data, '/')){
            $data = explode("/", $data);
            if (strstr($data[2], " ", true)) {
                $Data = explode(" " . $data[2]);
                $data = $Data[0] . "-" . $data[1] . "-" . $data[0] . " " . $Data[1];
            } else {
                $data = $data[2] . "-" . $data[1] . "-" . $data[0];
            }
        }
    } else {
        $data = null;
    }
    return $data;
}

//funcao data, formata a data. Recebe uma variavel assim: YYYY-MM-DD HH:II:SS
//e a transforma assim: DD/MM/YYYY HH:II:SS
function formataData($DATA, $somenteHora = false, $somenteData = false) {
    if ($DATA and $DATA <> "0000-00-00") {
        if(strstr($DATA, '-')){
            $data = explode("-", $DATA);
            if (strstr($data[2], " ") == True) {
                $Data = explode(" ", $data[2]);
                if($somenteHora){
                    $hora = explode(':', $Data[1]);
                    return $hora[0].':'.$hora[1];
                }elseif($somenteData){
                    return $Data[0] . "/" . $data[1] . "/" . $data[0];
                }else{
                    return $Data[0] . "/" . $data[1] . "/" . $data[0] . " " . $Data[1];
                }

            } else {
                return $data[2] . "/" . $data[1] . "/" . $data[0];
            }
        }else{
            return $DATA;
        }
    } else {
        return null;
    }
}


//datas do tipo Y-m-d ou Y-m-d H:i:s
function subtrairDatas($data_inicio, $data_termino , $modo = "dias"){

    if(strripos($data_inicio, " ")!=false and strripos($data_termino, " ")!=false){
        $dataI = explode(" ", $data_inicio);
        $dataF = explode(" ", $data_termino);

        $dataIymd = explode("-", $dataI[0]);
        $dataFymd = explode("-", $dataF[0]);

        $dataIhis = explode(":", $dataI[1]);
        $dataFhis = explode(":", $dataF[1]);

        $data_inicial = mktime($dataIhis[0], $dataIhis[1], $dataIhis[2], $dataIymd[1], $dataIymd[2], $dataIymd[0]);
        $data_final = mktime($dataFhis[0], $dataFhis[1], $dataFhis[2], $dataFymd[1], $dataFymd[2], $dataFymd[0]);
    }else{
        $dataI = explode("-", $data_inicio);
        $dataF = explode("-", $data_termino);

        $data_inicial = mktime(0, 0, 0, $dataI[1], $dataI[2], $dataI[0]);
        $data_final = mktime(0, 0, 0, $dataF[1], $dataF[2], $dataF[0]);
    }
    if($modo == "seg"){
        $denominador = 0.979166667/*1.935731786*/;
    }elseif($modo == "min"){
        $denominador = 58.75;
    }elseif($modo == "horas"){
        $denominador = 3525;
    }elseif($modo=="dias"){
        $denominador = 84600;
    }elseif($modo=="meses"){
        $denominador = 2538000;
    }elseif($modo=="anos"){
        $denominador = 30794400;
    }

    return  (floor(($data_final - $data_inicial)/$denominador));
    //return  $data_final." - ".$data_inicial." / ".$denominador;
}

function somarDatas($data, $dias, $meses, $ano){
    $data = explode("/", $data);
    $resData = date("d/m/Y", mktime(0, 0, 0, $data[1] + $meses, $data[0] + $dias, $data[2] + $ano));
    return $resData;
}



//converte numero normal em float (retorna 00.00) ou novamente em real (retorna 00,00)
function real($valor, $tipo){

    if($valor!=''){
        if($tipo=='float'){
            $valor = str_replace('.', '', $valor);
            $valor_array = explode(',', $valor);
            $valor = str_replace(',', '.', $valor);
        }elseif($tipo=='real'){
            $valor = round($valor, 2);
            $valor = str_replace(',', '', $valor);
            $valor_array = explode('.', $valor);
            $valor = str_replace('.', ',', $valor);
        }

        if(isset($valor_array[1])){
            $acrescentar = 2-strlen($valor_array[1]);
            $separador = false;
        }else{
            $acrescentar = 2;
            $separador = true;
        }
        for($i=0; $i<$acrescentar; $i++){
            if($i==0 and $separador){
                if($tipo=='float'){
                    $valor.='.';
                }elseif($tipo=='real'){
                    $valor.=',';
                }
            }
            $valor.= '0';
        }
    }

    return $valor;
}





function diasDaSemana($int, $full = false){
    if($full){
        switch ($int) {
            case 0:
                $cod = 'Domingo';
                break;
            case 1:
                $cod = 'Segunda';
                break;
            case 2:
                $cod = 'Terça';
                break;
            case 3:
                $cod = 'Quarta';
                break;
            case 4:
                $cod = 'Quinta';
                break;
            case 5:
                $cod = 'Sexta';
                break;
            case 6:
                $cod = 'Sábado';
                break;
        }
    }else{
        switch ($int) {
            case 0:
                $cod = 'DOM';
                break;
            case 1:
                $cod = 'SEG';
                break;
            case 2:
                $cod = 'TER';
                break;
            case 3:
                $cod = 'QUA';
                break;
            case 4:
                $cod = 'QUI';
                break;
            case 5:
                $cod = 'SEX';
                break;
            case 6:
                $cod = 'SAB';
                break;
        }
    }
    return $cod;
}

function validaData($date, $format = 'Y-m-d'){
    $d = DateTime::createFromFormat($format, $date);
    while($d && $d->format($format) != $date){
        if($format=='Y-m-d' or $format=='Y-m-d H:i:s'){
            $Data = explode('-', $date);
            if($format=='Y-m-d H:i:s'){
                $DATA = explode(' ', $Data[2]);
                if($DATA[0]==1 or $Data[1]>12){
                    $Data[1]--;
                    $DATA[0]=31;
                }else{
                    $DATA[0]--;
                }
                $Data[2] = $Data[2].' '.$DATA[1];
            }else{
                if($Data[2]==1 or $Data[1]>12){
                    $Data[1]--;
                    $Data[2]=31;
                }else{
                    $Data[2]--;
                }
            }
            $date = $Data[0].'-'.$Data[1].'-'.$Data[2];
        }elseif($format=='d/m/Y' or $format=='d/m/Y H:i:s'){
            $Data = explode('/', $date);
            if($Data[0]==1 or $Data[1]>12){
                $Data[1]--;
                $Data[0]=31;
            }else{
                $Data[0]--;
            }
            $date = $Data[0].'/'.$Data[1].'/'.$Data[2];
        }

        $d = DateTime::createFromFormat($format, $date);
    }
    return $date;
}


function request_uri(){
    $cod = $_SERVER['REQUEST_URI'];
    $cod = explode('/', $cod);
    $cod = $cod[count($cod)-1];
    return $cod;
}


//funcao para detectar se o cpf eh valido ou nao
function validaCPF($cpf) {
    if (!$cpf) {
        $cpf = "000.000.000-00";
    }
    //Etapa 1: Cria um array com apenas os digitos numéricos, isso permite receber o cpf em diferentes formatos como "000.000.000-00", "00000000000", "000 000 000 00" etc...
    $j = 0;
    for ($i = 0; $i < (strlen($cpf)); $i++) {
        if (is_numeric($cpf[$i])) {
            $num[$j] = $cpf[$i];
            $j++;
        }
    }
    //Etapa 2: Conta os dígitos, um cpf válido possui 11 dígitos numéricos.
    if (count($num) != 11) {
        $isCpfValid = false;
    }
    //Etapa 3: Combinações como 00000000000 e 22222222222 embora não sejam cpfs reais resultariam em cpfs válidos após o calculo dos dígitos verificares e por isso precisam ser filtradas nesta parte.
    else {
        for ($i = 0; $i < 10; $i++) {
            if ($num[0] == $i && $num[1] == $i && $num[2] == $i && $num[3] == $i && $num[4] == $i && $num[5] == $i && $num[6] == $i && $num[7] == $i && $num[8] == $i) {
                $isCpfValid = false;
                break;
            }
        }
    }
    //Etapa 4: Calcula e compara o primeiro dígito verificador.
    if (!isset($isCpfValid)) {
        $j = 10;
        for ($i = 0; $i < 9; $i++) {
            $multiplica[$i] = $num[$i] * $j;
            $j--;
        }
        $soma = array_sum($multiplica);
        $resto = $soma % 11;
        if ($resto < 2) {
            $dg = 0;
        } else {
            $dg = 11 - $resto;
        }
        if ($dg != $num[9]) {
            $isCpfValid = false;
        }
    }
    //Etapa 5: Calcula e compara o segundo dígito verificador.
    if (!isset($isCpfValid)) {
        $j = 11;
        for ($i = 0; $i < 10; $i++) {
            $multiplica[$i] = $num[$i] * $j;
            $j--;
        }
        $soma = array_sum($multiplica);
        $resto = $soma % 11;
        if ($resto < 2) {
            $dg = 0;
        } else {
            $dg = 11 - $resto;
        }
        if ($dg != $num[10]) {
            $isCpfValid = false;
        } else {
            $isCpfValid = true;
        }
    }
    //Trecho usado para depurar erros.
    /*
     if($isCpfValid==true)
     {
     echo "<font color=\"GREEN\">Cpf é Válido</font>";
     }
     if($isCpfValid==false)
     {
     echo "<font color=\"RED\">Cpf Inválido</font>";
     }
     */
    //Etapa 6: Retorna o Resultado em um valor booleano.
    return $isCpfValid;
}


function validaCNPJ($cnpj){
    $cnpj = preg_replace('/[^0-9]/', '', (string) $cnpj);
    // Valida tamanho
    if (strlen($cnpj) != 14)
        return false;
    // Valida primeiro dígito verificador
    for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++)
    {
        $soma += $cnpj{$i} * $j;
        $j = ($j == 2) ? 9 : $j - 1;
    }
    $resto = $soma % 11;
    if ($cnpj{12} != ($resto < 2 ? 0 : 11 - $resto))
        return false;
    // Valida segundo dígito verificador
    for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++)
    {
        $soma += $cnpj{$i} * $j;
        $j = ($j == 2) ? 9 : $j - 1;
    }
    $resto = $soma % 11;
    return $cnpj{13} == ($resto < 2 ? 0 : 11 - $resto);
}



//funcao pra checar data dd/mm/YYYY
function checarDataVerdadeira($dat){
    $data = explode("/", $dat); // fatia a string $dat em pedados, usando / como referência
    $d = $data[0];
    $m = $data[1];
    $y = $data[2];

    if(is_numeric($m) and is_numeric($d) and is_numeric($y)){
        // verifica se a data é válida!
        // 1 = true (válida)
        // 0 = false (inválida)
        $res = checkdate($m,$d,$y);
        if ($res == 1){
           return true;
        } else {
           return false;
        }
    } else {
        return false;
    }
}

function delete_directory($dirname) {
    
    if (is_dir($dirname)){
        $dir_handle = opendir($dirname);
    }
    
    if(isset($dir_handle)){
        if (!$dir_handle){
            return false;
        }
    }else{
        return true;
    }

    while($file = readdir($dir_handle)) {
        if ($file != "." && $file != "..") {
            if (!is_dir($dirname."/".$file)){
                unlink($dirname."/".$file);
            }else{
                delete_directory($dirname.'/'.$file);
            }
        }
     }

     closedir($dir_handle);
     rmdir($dirname);
     return true;
}

function is_logged_usuario(){
    if(!isset($_COOKIE['email'])){
        redirecionar('index.php?e='.base64_encode(request_uri()),0);
        exit('Você não está logado.');
    }else{
        session_start();
        $_SESSION['cgi_logado'] = true;
    }
}

function nome_sobrenome($id){
    
    global $conn;

    if(is_numeric($id)){
        $criterio = new TCriteria();
        $criterio->add(new TFilter('id', '=', $id));

        $sql = new TSqlSelect();
        $sql->setEntity('usuario');
        $sql->addColumn('nome');
        $sql->setCriteria($criterio);

        $result = $conn->query($sql->getInstruction());

        if($result->rowCount()){
            extract($result->fetch(PDO::FETCH_ASSOC));
        }else{
            return false;
        }
    }else{
        $nome = $id;
    }

    $nome = explode(' ', $nome);

    return $nome[0].' '.$nome[count($nome) -1];
}

function produto_imagem($idProduto, $tumb, $tamanho = null, $sistema = true, $cod = true){

    global $conn;

    if($sistema){
        $end_pre = null;
    }else{
        $end_pre = 'cgi/';
    }

    $criterio = new TCriteria();
    $criterio->add(new TFilter('produto_id', '=', $idProduto));
    $criterio->add(new TFilter('tumb', '=', $tumb));
    

    $sql = new TSqlSelect();
    $sql->setEntity('produto_imagem');
    $sql->addColumn('end');
    $sql->setCriteria($criterio);

    $result = $conn->query($sql->getInstruction());

    for($i = 0, $imagem = null; $i < $result->rowCount(); $i++){
        extract($result->fetch(PDO::FETCH_ASSOC));

        if($cod){
            $imagem = '<img src="'.$end_pre.$end.'" ';
        }else{
            $imagem = $end_pre.$end;
        }
        

        if($tamanho and $cod){
            $imagem .= 'style="max-width: '.$tamanho.'px;">';
        }

    }

    return $imagem;

}




function descricao($des){
    $des = str_replace('<p>', '', $des);
    $des = str_replace('</p>', '<br>', $des);
    return $des;
}

function dump($var){
    return '<pre>' . var_export($var, true) . '</pre>';
}

function my_file_get_contents( $site_url ){
    $ch = curl_init();
    $timeout = 5; // set to zero for no timeout
    curl_setopt ($ch, CURLOPT_URL, $site_url);
    curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    ob_start();
    curl_exec($ch);
    curl_close($ch);
    $file_contents = ob_get_contents();
    ob_end_clean();
    return $file_contents;
}

//funcao de debug
//exibe variaveis na tela
function debugVariaveis(){
    $cod = "<div class='msg'>";
    $cont = count($_POST);
    $array = array_keys($_POST);
    $cod.= "<h3>VARIAVEIS DO POST</h3>";
    $cod.= "<table>";
    for ($i=0; $i<$cont; $i++){
        if(is_array($_POST[$array[$i]])){
            $cont2= count($_POST[$array[$i]]);
            for($j=0; $j<$cont2;$j++){
                $cod.= "<tr>";
                $cod.= "<td><pre>$i</td>";
                $cod.= "<td><pre>Variavel:</td>";
                $cod.= "<td><pre><b style='color:red;'>". $array[$i]." $j</b></td>";
                $cod.= "<td><pre>Valor:</td>";
                $cod.= "<td><pre><b style='color:red;'>".$_POST[$array[$i]][$j]."</b></td>";
                $cod.= "</tr>";
            }
        }else{
            $cod.= "<tr>";
            $cod.= "<td><pre>$i</td>";
            $cod.= "<td><pre>Variavel:</td>";
            $cod.= "<td><pre><b style='color:red;'>". $array[$i]."</b></td>";
            $cod.= "<td><pre>Valor:</td>";
            $cod.= "<td><pre><b style='color:red;'>".$_POST[$array[$i]]."</b></td>";
            $cod.= "</tr>";
        }
    }
    $cod.= "</table>";
    $cont = count($_GET);
    $array = array_keys($_GET);
    $cod.= "<h3>VARIAVEIS DO GET</h3>";
    $cod.= "<table>";
    for ($i=0; $i<$cont; $i++){
        if(is_array($_GET[$array[$i]])){
            $cont2= count($_GET[$array[$i]]);
            for($j=0; $j<$cont2;$j++){
                $cod.= "<tr>";
                $cod.= "<td><pre>$i</td>";
                $cod.= "<td><pre>Variavel:</td>";
                $cod.= "<td><pre><b style='color:red;'>". $array[$i]." $j</b></td>";
                $cod.= "<td><pre>Valor:</td>";
                $cod.= "<td><pre><b style='color:red;'>".$_GET[$array[$i]][$j]."</b></td>";
                $cod.= "</tr>";
            }
        }else{
            $cod.= "<tr>";
            $cod.= "<td><pre>$i</td>";
            $cod.= "<td><pre>Variavel:</td>";
            $cod.= "<td><pre><b style='color:red;'>". $array[$i]."</b></td>";
            $cod.= "<td><pre>Valor:</td>";
            $cod.= "<td><pre><b style='color:red;'>".$_GET[$array[$i]]."</b></td>";
            $cod.= "</tr>";
        }
    }
    $cod.= "</table>";
    
    /*
    $cont = count($_REQUEST);
    $array = array_keys($_REQUEST);
    $cod.= "<h3>VARIAVEIS DO REQUEST</h3>";
    $cod.= "<table>";
    for ($i=0; $i<$cont; $i++){
        if(is_array($_REQUEST[$array[$i]])){
            $cont2= count($_REQUEST[$array[$i]]);
            for($j=0; $j<$cont2;$j++){
                $cod.= "<tr>";
                $cod.= "<td><pre>$i</td>";
                $cod.= "<td><pre>Variavel:</td>";
                $cod.= "<td><pre><b style='color:red;'>". $array[$i]." $j</b></td>";
                $cod.= "<td><pre>Valor:</td>";
                $cod.= "<td><pre><b style='color:red;'>".$_REQUEST[$array[$i]][$j]."</b></td>";
                $cod.= "</tr>";
            }
        }else{
            $cod.= "<tr>";
            $cod.= "<td><pre>$i</td>";
            $cod.= "<td><pre>Variavel:</td>";
            $cod.= "<td><pre><b style='color:red;'>". $array[$i]."</b></td>";
            $cod.= "<td><pre>Valor:</td>";
            $cod.= "<td><pre><b style='color:red;'>".$_REQUEST[$array[$i]]."</b></td>";
            $cod.= "</tr>";
        }
    }
    $cod.= "</table>";
    */
    
    $cod.= "<table>";
    $cod.= "<h3>VARIAVEIS DO FILE</h3>";
    $cod.= "<tr><td>";
    $cod.= dump($_FILES);
    $cod.= "</td></tr>";
    $cod.= "</table>";
    $cod.= "</div>";

    return $cod;
}

//funcao que transforma um valor em numero extenso. recebe o valor no formato 123.13 onde apois o ponto representa os
//centavos e recebe true ou false para saber se a primeira letra de cada palavra eh maiuscula (true) ou menuscula(false)
function extenso($valor = 0, $maiusculas = false) {

    $singular = array("centavo", "real", "mil", "milh&atilde;o", "bilhão", "trilh&atilde;o", "quatrilh&atilde;o");
    $plural = array("centavos", "reais", "mil", "milh&otilde;es", "bilh&otilde;es", "trilh&otilde;es", "quatrilh&otilde;es");

    $c = array("", "cem", "duzentos", "trezentos", "quatrocentos", "quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
    $d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta", "sessenta", "setenta", "oitenta", "noventa");
    $d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze", "dezesseis", "dezesete", "dezoito", "dezenove");
    $u = array("", "um", "dois", "tr&ecirc;s", "quatro", "cinco", "seis", "sete", "oito", "nove");

    $z = 0;
    $rt = "";

    $valor = number_format($valor, 2, ".", ".");
    $inteiro = explode(".", $valor);
    for ($i = 0; $i < count($inteiro); $i++)
        for ($ii = strlen($inteiro[$i]); $ii < 3; $ii++)
            $inteiro[$i] = "0" . $inteiro[$i];

    $fim = count($inteiro) - ($inteiro[count($inteiro) - 1] > 0 ? 1 : 2);
    for ($i = 0; $i < count($inteiro); $i++) {
        $valor = $inteiro[$i];
        $rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
        $rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
        $ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";

        $r = $rc . (($rc && ($rd || $ru)) ? " e " : "") . $rd . (($rd && $ru) ? " e " : "") . $ru;
        $t = count($inteiro) - 1 - $i;
        $r .= $r ? " " . ($valor > 1 ? $plural[$t] : $singular[$t]) : "";
        if ($valor == "000")
            $z++;
        elseif ($z > 0)
            $z--;
        if (($t == 1) && ($z > 0) && ($inteiro[0] > 0))
            $r .= (($z > 1) ? " de " : "") . $plural[$t];
        if ($r)
            $rt = $rt . ((($i > 0) && ($i <= $fim) && ($inteiro[0] > 0) && ($z < 1)) ? (($i < $fim) ? ", " : " e ") : " ") . $r;
    }

    if (!$maiusculas) {
        return ($rt ? $rt : "zero");
    } else {
        if ($rt)
            $rt = preg_replace("{ E }", " e ", ucwords($rt));
        return (($rt) ? ($rt) : "Zero");
    }

}


?>
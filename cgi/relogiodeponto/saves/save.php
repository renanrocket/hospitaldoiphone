<?php

if (isset($_POST['base_img'])) {
	date_default_timezone_set('America/Sao_Paulo');
	$nome = $_COOKIE["login"];
	$senha = $_COOKIE["senha"];
	$acao = $_COOKIE["acao"];
	$turno = $_COOKIE["turno"];
	$id = $_COOKIE["id"];
	$ano = date('Y');
	$mes = date('m');
	$dia = date('d');
	$dir1 = "../saves/images/$ano-$mes-$dia/";
	if (!(is_dir($dir1))) {
		mkdir($dir1, 0755);
	}

	$jpg = $_POST['base_img'];
	$data = str_replace(" ", "+", $jpg); //O envio do dado pelo XMLHttpRequest tende a trocar o + por espaço, por isso a necessidade de substituir. 
	$data = explode(',', $data);
	
	//$filename = $dir1 . date('Y-m-d_h:i') .'_'. $nome . "_" . $acao . "_" . $turno . ".jpg";
	$filename = $dir1 . md5(time().uniqid()) . ".jpg";
	file_put_contents($filename, base64_decode(trim($data[1])));
	include "../conecta.php";
	$hora = date('H');
	$acao = $_COOKIE["acao"];
	$sql = mysqli_query($conexao, "update relogio_ponto set end_foto='$filename' where id='$id'");
	mysqli_close($conexao);
	//echo "<meta HTTP-EQUIV='refresh' CONTENT='3;URL=../index.php'>";

	//Print Data
	$result['img'] = $filename;
	echo json_encode($result, JSON_PRETTY_PRINT);

	setcookie("login", "");
	setcookie("senha", "");
	setcookie("acao", "");
	setcookie("turno", "");
	setcookie("id", "");
} else {
	die("{\"error\": \" Flopou. Cadê o base_img?\"}");
}
?>
<?php
include_once 'functions.inc.php';

extract($_POST);
extract($_GET);
$conn = TConnection::open(DB);

$criterio = new TCriteria();
$criterio->add(new TFilter('ip', '=', $ip));
$criterio->add(new TFilter('latitude', '<>', ''));
$criterio->add(new TFilter('longitude', '<>', ''));

$sql = new TSqlSelect();
$sql->setEntity('acessos_site');
$sql->addColumn('cidade');
$sql->addColumn('pais');
$sql->addColumn('continente');
$sql->addColumn('latitude');
$sql->addColumn('longitude');
$sql->addColumn('organizacao');
$sql->setCriteria($criterio);

$resultUPDATE = $conn->query($sql->getInstruction());

if($resultUPDATE->rowCount()){
//if(false){

	//olha primeiro no banco de dados se tem o ip ja registrado
	extract($resultUPDATE->fetch(PDO::FETCH_ASSOC));

}else{

	//procura no banco de dados do ipfind (local mais preciso)
	$ip_address = $ip;
	$request_uri = 'https://ipfind.co';
	$auth = '63a8d102-58be-4594-9dbf-6a8e1508ef92';
	$url = $request_uri . "?ip=" . $ip_address . "&auth=" . $auth . "&lang=pt-BR";
	$document = my_file_get_contents($url);
	$resultIP = json_decode($document);

	
	if(!isset($resultIP->error)){

		$cidade = $resultIP->city;
		$pais = $resultIP->country;
		$continente = $resultIP->continent;
		$latitude = $resultIP->latitude;
		$longitude = $resultIP->longitude;
		$organizacao = $resultIP->owner;

	}else{


		//procura no king.host local menos preciso
		$ip_address = $ip;
		$request_uri = 'https://king.host/';
		$auth = '63a8d102-58be-4594-9dbf-6a8e1508ef92';
		$url = $request_uri . "wiki/wp-content/themes/kinghost-wiki/includes/ip-api.php?ip=" . $ip_address;
		$document = my_file_get_contents($url);
		$resultIP = json_decode($document);

		if(isset($resultIP->city)){

			$cidade = $resultIP->city;
			$pais = $resultIP->country;
			$continente = null;
			$latitude = $resultIP->lat;
			$longitude = $resultIP->lon;
			$organizacao = $resultIP->org;

		}else{

			//procura no ipapi local menos preciso
			$request_uri = 'http://api.ipapi.com/';
			$auth = '6c8723986076343bb458d9515d14e9fb';
			$url = $request_uri . $ip_address . "?access_key=" . $auth. "&language=pt-br&hostname=1";

			$document = my_file_get_contents($url);
			$resultIP = json_decode($document);

			$cidade = $resultIP->city;
			$pais = $resultIP->country_name;
			$continente = $resultIP->continent_name;
			$latitude = $resultIP->latitude;
			$longitude = $resultIP->longitude;
			$organizacao = $resultIP->hostname;

		}




	}

}

/*
$data = file_get_contents('https://www.maxmind.com/geoip/v2.1/city/'.$ip.'?demo=1'); // put the contents of the file into a variable
$data = str_replace('pt-BR', 'pt', $data);
$characters = json_decode($data); // decode the JSON feed

if(isset($characters->city->names->pt)){
	$cidade = $characters->city->names->pt;
}elseif(isset($characters->city->names->en)){
	$cidade = $characters->city->names->en;
}else{
	$cidade = null;
}
$pais = $characters->country->names->pt;
$continente = $characters->continent->names->pt;
$latitude = $characters->location->latitude;
$longitude = $characters->location->longitude;
isset($characters->traits->isp) ? $organizacao = $characters->traits->isp : $organizacao = $characters->traits->domain;

*/

$sql = new TSqlInsert();
$sql->setEntity('acessos_site');
$sql->setRowData('session', $session);
$sql->setRowData('ip', $ip);
$sql->setRowData('data', date('Y-m-d H:i:s'));
$sql->setRowData('operacao', $operacao);
$sql->setRowData('valor', base64_decode($valor));

$sql->setRowData('continente', $continente);
$sql->setRowData('pais', $pais);
$sql->setRowData('cidade', $cidade);
$sql->setRowData('latitude', $latitude);
$sql->setRowData('longitude', $longitude);
$sql->setRowData('organizacao', $organizacao);


$conn->query($sql->getInstruction());


?>

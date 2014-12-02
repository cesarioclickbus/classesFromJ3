<?php

	require 'vendor/autoload.php'; 

	function writeClass($string){
		$myFile = "classes.csv";
		$fh = fopen($myFile, 'a') or die("can't open file");
		fwrite($fh, $string);
		fclose($fh);
	}

	function getClasses($origin, $destination){

		$client = new GuzzleHttp\Client();
		$res = $client->get('http://200.155.23.37/xml/int4.pl?op=consulta&agencia=J3&agente=J3&origem=' . $origin  . '&destino=' . $destination . '&data=2014-12-10&grupo=J3&filtropesquisa=terminal&fpag=j0&seguro=S', ['auth' =>  ['rocket', 'j3@70c983','digest']]);
		
		//var_export($res->xml());
		$array = json_decode(json_encode($res->xml()), true);

		//var_dump($array['resumo-viagem']);

		if (!isset($array['resumo-viagem']))
			return;

		if (isset($array['resumo-viagem']['classe'])){
			//var_dump($array['resumo-viagem']['empresa']);
			//var_dump($array['resumo-viagem']['classe']);
			writeClass($origin . ',' . $destination . ',' . $array['resumo-viagem']['empresa'] . ',' . $array['resumo-viagem']['classe'] . PHP_EOL);
			return;
		}

		foreach ($array['resumo-viagem'] as $viagem) {
			//var_dump($viagem['empresa']);
			//var_dump($viagem['classe']);
			writeClass($origin . ',' . $destination . ',' . $viagem['empresa'] . ',' . $viagem['classe'] . PHP_EOL);
		}
	}
	
	$csv = new parseCSV();
	$csv->auto('routes.csv');

	echo 'start\n';

	for($i = 0; $i < count($csv->data); $i++){
		//var_dump($csv->data[$i][origin]);
		//var_dump($csv->data[$i][destination]);
		getClasses($csv->data[$i][origin],$csv->data[$i][destination]);	
			
  	}

  	echo 'finish\n';

	//
?>
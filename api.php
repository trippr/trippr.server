<?php
require 'vendor/autoload.php';

$app = new \Slim\Slim();
$app->get('/hello/:name', function ($name) {
        echo "Hello, $name";
});
$app->get('/destinations/search/:text(/:excluded)', function($text, $excluded="") {
    echo "text is: ". $text;
	echo "<br>";
    $excludedList = explode(',', $excluded);

	$data = array(
		"query" => array(
            "more_like_this" => array(
                "like_text" => $text,
                "min_term_freq" => 1,
                "min_doc_freq" => 1,
                "max_doc_freq" => 10
			)
		)
	);

	$datajson = json_encode($data);

	// Get cURL resource
	$curl = curl_init();
	// Set some options - we are passing in a useragent too here
	curl_setopt_array($curl, array(
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_URL => 'http://54.191.28.250:9200/trippr/_search',
		CURLOPT_USERAGENT => 'Internal API Server',
		CURLOPT_POST => 1,
		CURLOPT_POSTFIELDS => $datajson
	));
	// Send the request & save response to $resp
	$resp = curl_exec($curl);
	// Close request to clear up some resources
	curl_close($curl);

	echo "<br>";
	var_dump($resp);

	$responsejson = json_decode($resp, true);

	var_dump($responsejson['hits']);

	$hits = isset($responsejson['hits']) ? $responsejson['hits'] : array();



	foreach($hits as $hit){
		$data = $hit[0];
		$name = $data['_source']['name'];
		$country = $data['_source']['country'];
		$countrycode = $data['_source']['countrycode'];

		$city = $name." + ".$country." + ".$countrycode;

	}

	echo $city;

});
$app->run();

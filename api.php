<?php
require 'vendor/autoload.php';

$app = new \Slim\Slim();
$app->get('/destinations/search/:text(/:excluded)', function($text, $excluded="") {
    $excludedList = explode(',', $excluded);
    $data = array(
        "query" => array(
        "more_like_this" => array(
        "like_text" => $text,
        "min_term_freq" => 1,
        "min_doc_freq" => 1,
        "max_doc_freq" => 10
        )
     ));
        
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

        $responsejson = json_decode($resp, true);

        $hits = isset($responsejson['hits']['hits']) ? $responsejson['hits']['hits'] : array();

        $city = "";

        foreach($hits as $hit) {
                $name = $hit['_source']['name'];
                $country = $hit['_source']['country'];
                $countrycode = $hit['_source']['countrycode'];
                if(!in_array($name, $excludedList)) {
                        $city = $name . " + " . $country . " + " . $countrycode;
                        return $city;
                }
        }

        return "notfound";
});
$app->run();

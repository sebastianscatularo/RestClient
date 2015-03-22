#!/bin/env php

<?php

require_once __DIR__ . '/../vendor/autoload.php';

$rest = \SebastianScatularo\Rest\Client\RestClient::newRestClient();
$result = $rest->get("http://api.twitter.com/1.1/search/tweets.json?q=rest");
$result = $rest->post("http://localhost:3000", "{}");
var_dump($result);
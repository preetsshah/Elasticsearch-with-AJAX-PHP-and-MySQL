<?php

require_once 'vendor/autoload.php';
use Elasticsearch\ClientBuilder;
$client = ClientBuilder::create()->setHosts(['127.0.0.1:9200'])->build();
//$es = new Elasticsearch\Client([
//	'hosts' => ['127.0.0.1:9200']

?>
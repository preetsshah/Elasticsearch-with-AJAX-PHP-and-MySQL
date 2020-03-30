<?php
//fetch2.php

session_start();

require_once '../vendor/autoload.php';
use Elasticsearch\ClientBuilder;
$client = ClientBuilder::create()->setHosts(['127.0.0.1:9200'])->build();

$output = '';
if(isset($_POST["query"]))
{
 $search = $_POST["query"];
 $query = $client->search([
    'body' => [
      'query' => [
        'bool' => [
          'must' => [
            'multi_match' => [
              "fuzziness" => "AUTO",
              'fields' => ['name','district','countrycode'],
              // 'type' => "phrase_prefix",
              'query' => $search
            ]
          ]
        ]
      ],
      'from' => 0,
      'size' => 100
    ]
    // 'body' => [
    //  'query' => [
    //    'bool' => [
    //      'must' => [
    //        'match' => ['district' => $q],
    //        'match' => ['countrycode' => $q],
    //        'match' => ['name' => $q]
    //      ]
    //    ]
    //  ]
    // ]
  ]);

  if($query['hits']['total']>=1){
    $results = $query['hits']['hits'];
  }

}

if(isset($results))
{
 $output .= '
  <div class="table-responsive">
   <table class="table table bordered">
    <tr>
     
     <th>Name</th>
     <th>District</th>
     <th>Countrycode</th>
     <th>Population</th>
    
    </tr>
 ';
 foreach($results as $r)
 {
  $output .='
   <tr>
    
    <td>'.$r["_source"]["name"].'</td>
    <td>'.$r["_source"]["district"].'</td>
    <td>'.$r["_source"]["countrycode"].'</td>
    <td>'.$r["_source"]["population"].'</td>
   </tr>
  ';
 }
 echo $output;
}
else
{
 echo 'Data Not Found';
}

?>
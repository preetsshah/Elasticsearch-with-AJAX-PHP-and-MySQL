<?php
//fetch2.php


session_start();

require_once '../vendor/autoload.php';
use Elasticsearch\ClientBuilder;
$client = ClientBuilder::create()->setHosts(['127.0.0.1:9200'])->build();

$output = '';
if(isset($_POST["query"]))
{
 $link='';
 // $description='';
 $search = $_POST["query"];
 $query = $client->search([
    'body' => [
      'query' => [
        'bool' => [
          'must' => [
            'multi_match' => [
              //"fuzziness" => "AUTO",
              'fields' => ['title','University','Professor','description'],
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
  <div>Data found:</div>
 ';
 foreach($results as $r)
 {
  if(isset($r["_source"]["link"])){
  $link = $r["_source"]["link"];  
  $output .='
    <br><br>
    <div>'.$r["_source"]["title"].'</div>
    <div>'.$r["_source"]["description"].'</div>
    <div style="color:blue;"><a href="' . $link . '">'.$r["_source"]["link"].'</a></div>
  ';
  }
 }
 echo $output;
}
else
{
 echo 'Data Not Found';
}

?>
  <?php

  session_start();
  require_once './vendor/autoload.php';
  use Elasticsearch\ClientBuilder;
  $client = ClientBuilder::create()->setHosts(['127.0.0.1:9200'])->build();

  $output = '';
  $total_data = 0;
  $page_array[]=1;
  $limit = '15';
  $results = 0;
  $page = 1;
  if($_POST['page'] > 1)
  {
    $start = (($_POST['page'] - 1) * $limit);
    $page = $_POST['page'];
  }
  else
  {
    $start = 0;
  }

if($_POST['query'] != '')
{
   $search = $_POST["query"];
   $params = [
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
      'size' => 4079
    ]
  ];
  $params2 = [
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
      'from' => $start,
      'size' => $limit
    ]
  ];
  $query = $client->search($params);
  $total_data = count($query['hits']['hits']);

  $query = $client->search($params2);
  if($query['hits']['total']>=1){
    $results = $query['hits']['hits'];
  }

  $output.='
  <div style="font-size: 18px; margin-bottom: 15px; margin-top: -10px;">Search results for "<b>'.$search.'</b>"</div>
  <div style="margin-bottom: 5px; margin-top: -10px;">Found '.$total_data.' results</div>
  ';

  if($total_data>0)
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
  }
}

$output .= '
<br>
<div align="center">
<ul class="pagination">
';

$total_links = ceil($total_data/$limit);
$previous_link = '';
$next_link = '';
$page_link = '';

  //echo $total_links;

if($total_links > 4)
{
  if($page < 5)
  {
    for($count = 1; $count <= 5; $count++)
    {
      $page_array[] = $count;
    }
    $page_array[] = '...';
    $page_array[] = $total_links;
  }
  else
  {
    $end_limit = $total_links - 5;
    if($page > $end_limit)
    {
      $page_array[] = 1;
      $page_array[] = '...';
      for($count = $end_limit; $count <= $total_links; $count++)
      {
        $page_array[] = $count;
      }
    }
    else
    {
      $page_array[] = 1;
      $page_array[] = '...';
      for($count = $page - 1; $count <= $page + 1; $count++)
      {
        $page_array[] = $count;
      }
      $page_array[] = '...';
      $page_array[] = $total_links;
    }
  }
}
else
{
  for($count = 1; $count <= $total_links; $count++)
  {
    $page_array[] = $count;
  }
}
for($count = 1; $count < count($page_array); $count++)
{
  if($page == $page_array[$count])
  {
    $page_link .= '
    <li class="page-item active">
    <a class="page-link" href="#">'.$page_array[$count].' <span class="sr-only">(current)</span></a>
    </li>
    ';

    $previous_id = $page_array[$count] - 1;
    if($previous_id > 0)
    {
      $previous_link = '<li class="page-item"><a class="page-link" href="javascript:void(0)" data-page_number="'.$previous_id.'">Previous</a></li>';
    }
    else
    {
      $previous_link = '
      <li class="page-item disabled">
      <a class="page-link" href="#">Previous</a>
      </li>
      ';
    }
    $next_id = $page_array[$count]+1;
    if($next_id > $total_links)
    {
      $next_link = '
      <li class="page-item disabled">
      <a class="page-link" href="#">Next</a>
      </li>
      ';
    }
    else
    {
      $next_link = '<li class="page-item"><a class="page-link" href="javascript:void(0)" data-page_number="'.$next_id.'">Next</a></li>';
    }
  }
  else
  {
    if($page_array[$count] == '...')
    {
      $page_link .= '
      <li class="page-item disabled">
      <a class="page-link" href="#">...</a>
      </li>
      ';
    }
    else
    {
      $page_link .= '
      <li class="page-item"><a class="page-link" href="javascript:void(0)" data-page_number="'.$page_array[$count].'">'.$page_array[$count].'</a></li>
      ';
    }
  }
}

$output .= $previous_link . $page_link . $next_link;
$output .= '
</ul>
</div>
</div>
';
echo $output;
?>

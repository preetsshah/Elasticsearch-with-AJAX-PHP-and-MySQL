<?php

require_once 'init.php';

if(isset($_GET['q'])){

	$q = $_GET['q'];
	$query = $client->search([

		'body' => [
			'query' => [
				'bool' => [
					'should' => [
						'multi_match' => [
							"fuzziness" => "AUTO",
							'fields' => ['district','countrycode','name'],
							// 'type' => "phrase_prefix",
							'query' => $q

						]
					]
				]
			],
			'from' => 0,
			'size' => 100
		]

		// 'body' => [
		// 	'query' => [
		// 		'bool' => [
		// 			'must' => [
		// 				'match' => ['district' => $q],
		// 				'match' => ['countrycode' => $q],
		// 				'match' => ['name' => $q]
		// 			]
		// 		]
		// 	]
		// ]
	]);


	if($query['hits']['total']>=1){
		$results = $query['hits']['hits'];
	}

}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Search | ES</title>
	<link rel="stylesheet" href="css/main.css">
</head>
<body>
	<form action="index2.php" method="get" autocomplete="off">
		<label>
			Search for something
			<input type="text" name="q">
		</label>
		<input type="submit" name="Search">
	</form>

	<?php
		if(isset($results)){
			foreach ($results as $r) {
	?>

	<div class="result">
		<a href="#<?php echo $r['_id']; ?>">district:<?php echo $r['_source']['district']; ?></a>
		<div class="result-keywords">
			countrycode:<?php echo $r['_source']['countrycode']; ?>
		</div>
		<div class="result-keywords">
			name:<?php echo $r['_source']['name']; ?>
		</div>
	</div>

	<?php
		}
	}
	?>

</body>
</html>
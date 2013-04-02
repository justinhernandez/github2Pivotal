<?php
/*
  Simple script to export open github issues into a compatible pivotal tracker csv
 */
// change repo here
$repo = 'rippleFoundation/ripple-client';
// url to pull open issues from
$url = "https://api.github.com/repos/{$repo}/issues?state=open&per_page=10000";
// optional csv export stuff
$story_type = 'Bug';
$owned_by = '';
$requested_by = 'Github';

// create curl connection and retrieve data
//create a new cURL resource
$ch = curl_init();
// set URL and other appropriate options
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
curl_setopt($ch, CURLOPT_HEADER, 0);
// grab URL and pass it to the browser
$issues = json_decode(curl_exec($ch));
// close cURL resource, and free up system resources
curl_close($ch);

// print out csv to web browser
header("Content-type: text/csv");  
header("Cache-Control: no-store, no-cache");  
header('Content-Disposition: attachment; filename="pivotal_import.csv"'); 

// create tmp file
$temp = tmpfile();

// create first row of csv
fputcsv($temp, array(
	'Id',
	'Story',
	'Labels',
	'Story Type',
	'Estimate',
	'Current State',
	'Created At',
	'Accepted At',
	'Deadline',
	'Requested By',
	'Owned By',
	'Description',
	'Note',
	'Note',
)); 

// iterate issues to create csv
foreach ($issues as $i)
	// fill up csv array
	fputcsv($temp, array(
		'', // id
		$i->title, // story
		implode(' ', $i->labels), // labels
		$story_type, // story type
		'', // estimate
		'', // current state
		$i->created_at, // created at
		'', // accepted at
		'', // deadline
		$requested_by, // requested by
		$owned_by, // owned by
		$i->body, // description
		'', // note 1
		'', // note 2
	));

fseek($temp, 0);
fpassthru($temp);
fclose($temp); // this removes the file
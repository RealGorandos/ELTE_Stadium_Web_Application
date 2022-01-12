<?php
// Start session
session_start();

// Loading (all) dependencies
require_once(__DIR__ . "/input.inc.php");
require_once(__DIR__ . "/storage.inc.php");
require_once(__DIR__ . "/auth.inc.php");
require_once(__DIR__ . "/directing.inc.php");
//require_once("../additional/generator.inc.php");
// Load (all) data sources
$userStorage = new Storage(new JsonIO(__DIR__ . "/../data/users.json"));
$commentStorage = new Storage(new JsonIO(__DIR__ . "/../data/comments.json"));
$teamStorage = new Storage(new JsonIO(__DIR__ . "/../data/teams.json"));
$matchStorage = new Storage(new JsonIO(__DIR__ . "/../data/matches.json"));
// Initialize Auth class
$auth = new Auth($userStorage);


//Handy functions and Repeated Variables
$matches = $matchStorage->findMany(function ($match) {
    return $match['home']['score'] != ''; 
});

$matchesIndexed = array_values($matches);
$matches_len = count($matchesIndexed);
function date_sort($a, $b) {
    return strtotime($a['date']) - strtotime($b['date']);
}
usort($matchesIndexed, "date_sort");

function determineWinner($team01Score, $team02Score){
    if($team01Score == '' && $team02Score == ''){
        return "emptyS";
    }
    else if($team01Score > $team02Score){
        return "win";
    }else if($team01Score == $team02Score){
        return "draw";
    }
    return "lose";
}

function getTeamName($teamStorage, $id){
   $temp = $teamStorage->findById($id);
   return $temp["name"];
}

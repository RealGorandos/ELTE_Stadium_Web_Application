<?php

require_once("utils/_init.php");
//require_once("additional/generator.inc.php");
if (verify_get("logout")){

    if($_GET["logout"] == 1){
        $auth->logout();
        redirect("index.php");
    }
}


$teams = $teamStorage->findMany(function ($team) {
    return True; 
});

$teamsIndexed = array_values($teams);
$teams_len = count($teamsIndexed);

if (verify_get("count")){
    $count = $_GET["count"];
    if($count < 5){
        $count = 5;
    }
    
    if($count > count($matchesIndexed)){
        echo $count;
        $count = count($matchesIndexed);
    }
}else{
    $count = 5;
}

if($auth->is_authenticated()){
    $userID = $auth->authenticated_user()["id"];
    $temp = $userStorage->findById($userID);
    $teamsIDs = $temp['favorite'];
    
    $relatedMatches = array();
    for($i = 0; $i < count($teamsIDs); $i++){
    $teamsTempID = $teamsIDs[$i];
    $relatedMatches[] = $matchStorage->findMany(function ($relMatch) use($teamsTempID) {
        return $relMatch["home"]["teamid"] === $teamsTempID || $relMatch["away"]["teamid"] === $teamsTempID ; 
    });
    }

    $relatedMatchesIndexed = array();
    for($x = 0; $x < count($relatedMatches); $x++){
            $relatedMatchesIndexed[] = array_values($relatedMatches[$x]);
    }

    $relatedMatchesIndexedIDs = array();
    for($x = 0; $x < count($relatedMatchesIndexed); $x++){
        for($y = 0; $y < count($relatedMatchesIndexed[$x]); $y++)
        if($relatedMatchesIndexed[$x][$y]['home']['score'] != ''){
        $relatedMatchesIndexedIDs[] = $relatedMatchesIndexed[$x][$y]['id'];
        }
}
$uniqIds = array_unique($relatedMatchesIndexedIDs);
$favMatches = array();
foreach($uniqIds as $val){
    $favMatches[] = $matchStorage->findById($val);
}

$favMatchesIndexed = array_values($favMatches);
$favMatchesIndexed_len = count($favMatchesIndexed);
usort($favMatchesIndexed, "date_sort");
// var_dump($favMatchesIndexed);

}

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/style.css">

    <title>ELTE Stadium by(Asseel Al-Mahdawi)</title>
</head>
<body>
    <!-- Listing page -->
    <div class="header" id="listing_page">
        <video autoplay loop class="back-video" muted plays-inline>
            <source src="res/video/Stadium.mp4" type="video/mp4">
        </video>
        <div id="black_sheet"></div>
        <div class="title">
        <?php require_once("partials/header.inc.php") ?>
        <p>Welcome to ELTE Stadium! In this webpage you can find the latest updates of the matches news.</p>
        </div>
        <div class="content">
            <table id="teams-list">
                <tr>
                <th colspan="2" scope="colgroup">Teams List</th>
                </tr>
                <!-- -->
                <?php
                $i = 0;
                while($i < count($teamsIndexed)): ?>
                <tr>
                    <td><a href="display_team.php?id=<?=$teamsIndexed[$i]["id"]?>"><?=$teamsIndexed[$i++]["name"]?></a></td>
                    <td><a href="display_team.php?id=<?=$teamsIndexed[$i]["id"]?>"><?=$teamsIndexed[$i++]["name"]?></td>
                </tr>
                <?php endwhile;?>
            </table>
           

            <table id="matches">
                <thead>
            <tr>
                <th colspan="5" scope="colgroup">Matches</th>
                </tr>
                <tr>
                <th colspan="2" scope="colgroup">Home</th>
                <th colspan="1" scope="colgroup"></th>
                <th colspan="2" scope="colgroup">Away</th>
                </tr>
                <tr>
                <th>Team</th>
                <th>Score</th>
                <th>Date</th>
                <th>Score</th>
                <th>Team</th>
                </tr>
                </thead>
                <?php if($auth->is_authenticated() && $favMatchesIndexed_len != 0):?>

                    <tbody>
                <?php for($i = $favMatchesIndexed_len - 1; $i >= $favMatchesIndexed_len - $count && $i >= 0; $i-- ):?>
                <tr>
                    <td class="<?= determineWinner($favMatchesIndexed[$i]["home"]["score"], $favMatchesIndexed[$i]["away"]["score"])?>"><?= getTeamName($teamStorage,$favMatchesIndexed[$i]["home"]["teamid"])?></td>
                    <td class="<?= determineWinner($favMatchesIndexed[$i]["home"]["score"], $favMatchesIndexed[$i]["away"]["score"])?>"><?=$favMatchesIndexed[$i]["home"]["score"]?></td>
                    <td class="date"><?=$favMatchesIndexed[$i]["date"]?></td>
                    <td class="<?= determineWinner($favMatchesIndexed[$i]["away"]["score"], $favMatchesIndexed[$i]["home"]["score"])?>"><?=$favMatchesIndexed[$i]["away"]["score"]?></td>
                    <td class="<?= determineWinner($favMatchesIndexed[$i]["away"]["score"], $favMatchesIndexed[$i]["home"]["score"])?>"><?= getTeamName($teamStorage,$favMatchesIndexed[$i]["away"]["teamid"])?></td>
                </tr>
                <?php endfor;?>
                </tbody>

                 <?php else:?>  
                    <tbody>
                <?php for($i = $matches_len - 1; $i >= $matches_len - $count && $i >= 0; $i-- ):?>
                <tr>
                    <td class="<?= determineWinner($matchesIndexed[$i]["home"]["score"], $matchesIndexed[$i]["away"]["score"])?>"><?= getTeamName($teamStorage,$matchesIndexed[$i]["home"]["teamid"])?></td>
                    <td class="<?= determineWinner($matchesIndexed[$i]["home"]["score"], $matchesIndexed[$i]["away"]["score"])?>"><?=$matchesIndexed[$i]["home"]["score"]?></td>
                    <td class="date"><?=$matchesIndexed[$i]["date"]?></td>
                    <td class="<?= determineWinner($matchesIndexed[$i]["away"]["score"], $matchesIndexed[$i]["home"]["score"])?>"><?=$matchesIndexed[$i]["away"]["score"]?></td>
                    <td class="<?= determineWinner($matchesIndexed[$i]["away"]["score"], $matchesIndexed[$i]["home"]["score"])?>"><?= getTeamName($teamStorage,$matchesIndexed[$i]["away"]["teamid"])?></td>
                </tr>
                <?php endfor;?>
                </tbody>
                <?php endif;?>

            </table>
            <?php if($auth->is_authenticated() && $favMatchesIndexed_len != 0):?>

                <div class="extend-reduce" >
                    <button id="show-less" class="extend-navs">Show Less</button>
                    <input hidden id="table-length" value=<?=$favMatchesIndexed_len?>>
                    <button id="show-more" class="extend-navs">Show More</button>
            </div>

            <?php else:?> 
                <div class="extend-reduce" >
                    <button id="show-less" class="extend-navs">Show Less</button>
                    <input hidden id="table-length" value=<?=count($matchesIndexed)?>>
                    <button id="show-more" class="extend-navs">Show More</button>
            </div>
            <?php endif;?>
        </div>
    </div>


    <script src="js/index.js">
        
    </script>
</body>
</html>
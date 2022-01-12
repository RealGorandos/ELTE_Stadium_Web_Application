<?php

require_once("utils/_init.php");

//print_r($_POST);

date_default_timezone_set('Europe/Budapest');
$date = date('m/d/Y', time());




if(verify_get("commentid")){
    $deletedComment = $_GET["commentid"];
    $commentStorage->delete($deletedComment);
}

if(verify_get("id")){


    $teamId = $_GET["id"];
    $teamData = $teamStorage->findById($teamId);
    $count = $teamData['count'];
    $relatedMatches = $matchStorage->findMany(function ($relMatch) use($teamId) {
        return $relMatch["home"]["teamid"] === $teamId || $relMatch["away"]["teamid"] === $teamId ; 
    });

    $relatedMatchesIndexed = array_values($relatedMatches);
    $relatedMatchesLen = count($relatedMatchesIndexed);
    usort($relatedMatchesIndexed, "date_sort");
   // echo(var_dump($teamData));

    if (verify_post("comment")){
        $comment = trim($_POST["comment"]);
        if (empty($comment)) {
            $commentError = "Comment must not be empty";
        }

    if(!isset($commentError)){
            $commentStorage->add([
                "author"  => $auth->authenticated_user()["username"],
                "comment"  => $comment,
                "time" => $date,
                "teamid" => $teamId
            ]);
            redirect("display_team.php?id=". $_GET["id"]);
        }

    }
    $allComments = $commentStorage->findMany(function ($cmnt) use($teamId) {
        return $cmnt["teamid"] === $teamId; 
    });
     $comment_len = count($allComments);

     if(verify_get("favID")){
        $favID = trim($_GET["favID"]);

        $count = $teamStorage->updateCount($teamId, 1);

        $userID = $auth->authenticated_user()["id"];
        $temp = $userStorage->findById($userID);
        $tempIndexed = array_values($temp);
        $favArr =  $temp["favorite"];
        $favArr[] = $favID;

         $storageTemp =[
            "username" => $temp["username"],
            "email" =>  $temp["email"],
            "password" => $temp["password"],
            "roles" => $temp["roles"],
            "favorite" => $favArr,
            "id" => $userID
         ];

         $userStorage->update($userID, $storageTemp);
    }
    
    if(verify_get("delfavID")){
        $delfavID = $_GET["delfavID"];

        $count = $teamStorage->updateCount($teamId, -1);

        $userID = $auth->authenticated_user()["id"];
        $temp = $userStorage->findById($userID);
        $tempIndexed = array_values($temp);
        $favArr =  $temp["favorite"];
        $i = 0;
        while($i < count($favArr) && $favArr[$i] != $delfavID){
            $i+= 1;
        }
        if($i < count($favArr)){
            echo $i;
            \array_splice($favArr, $i, 1);
        }
        
        $storageTemp =[
            "username" => $temp["username"],
            "email" =>  $temp["email"],
            "password" => $temp["password"],
            "roles" => $temp["roles"],
            "favorite" => $favArr,
            "id" => $userID
         ];

        $userStorage->update($userID, $storageTemp);
    }

}

function checkFav($teamId, $auth, $userStorage){
$userID = $auth->authenticated_user()["id"];
$temp = $userStorage->findById($userID);
for($j = 0; $j< count($temp['favorite']); $j++){
    if($temp['favorite'][$j] == $teamId){
        return true;
    }
}
return false;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <title>ELTE Stadium</title>
</head>
<body>
<div class="header" id="listing_page">
        <video autoplay loop class="back-video" muted plays-inline>
            <source src="res/video/Stadium.mp4" type="video/mp4">
        </video>
        <div id="black_sheet"></div>
        <div class="title">
        <?php require_once("partials/header.inc.php") ?>
        <p>All team's details can be found here.</p>
        </div>
        
        <div class="content" id="white-background">  
        <?php if($auth->is_authenticated()):?>
            <div class="user-adv">
            <div id="ppl-counter">Added favorite by: <?=$count?> <i class="fa fa-user" style="font-size:24px;color:#2196F3"></i> ||   </div>
            <span>Add favorite: </span> 
            <label class="switch">     
                
                <input type="hidden" id="favUser" value="<?=$teamId?>" >
                <input id="fav-toggle" type="checkbox" <?php if(checkFav($teamId, $auth, $userStorage)){echo 'checked="checked"';}?>>
                <span class="slider round"></span>
            </label>    
            </div>
            <?php endif;?>    
        
        <a href="index.php"><button type="button" id="backMain">Back to main page</button></a>
            

            <h1 style="text-align: center; color:#f8f9fb;  text-shadow: 1px 1px 2px black, 0 0 25px blue, 0 0 5px darkblue;"><?= $teamData["name"]?></h1>
           
            <div class="logocontainer">
                <img src="res/img/<?=$teamData["logo"]?>" alt="logo" class="logo">   
            </div>

            <table id="details-matches">
                <tr>
                <th colspan="5" scope="colgroup">Matches</th>
                <?php if($auth->authorize(["admin"])):?><th colspan="1" scope="colgroup"></th><?php endif;?>
                </tr>
                <tr>
                <th colspan="2" scope="colgroup">Home</th>
                <th colspan="1" scope="colgroup"></th>
                <th colspan="2" scope="colgroup">Away</th>
                <?php if($auth->authorize(["admin"])):?><th colspan="1" scope="colgroup"></th><?php endif;?>
                </tr>
                <tr>
                <th>Team</th>
                <th>Score</th>
                <th>Date</th>
                <th>Score</th>
                <th>Team</th>
                <?php if($auth->authorize(["admin"])):?><th>Edit</th><?php endif;?>
                </tr>
                <?php foreach(array_reverse($relatedMatchesIndexed) as $match): ?>
                <tr>
                    <td class="<?= determineWinner($match["home"]["score"], $match["away"]["score"])?>"><?=getTeamName($teamStorage,$match["home"]["teamid"])?></td>
                    <td class="<?= determineWinner($match["home"]["score"], $match["away"]["score"])?>"><?=$match["home"]["score"]?></td>
                    <td class="date"><?=$match["date"] ?></td>
                    <td class="<?= determineWinner($match["away"]["score"], $match["home"]["score"])?>"><?=$match["away"]["score"]?></td>
                    <td class="<?= determineWinner($match["away"]["score"], $match["home"]["score"])?>"><?=getTeamName($teamStorage,$match["away"]["teamid"])?></td>
                    <?php if($auth->authorize(["admin"])):?><td id="edittd"><a href="modify_match.php?id=<?=$teamId?>&matchid=<?=$match['id']?>"><button id="editbtn">Edit <i class="fa fa-pencil"></i></button></a></td><?php endif;?>
                </tr>
                <?php endforeach; ?>
            </table>
            <?php if($auth->is_authenticated()):?>
            <form class="commentArea" id="commentform" action="" novalidate method="post">
                 <textarea id='textareaID' placeholder="Enter your comment here..." name="comment"></textarea>
                 <div id="container0" style="display: none;">
                    <span class="commentError">Comment must not be empty or contains only white spaces</span>
                 </div>
                <div class="container">
                
                    <button type="button" class="cancelbtn" onclick="eraseText();">Cancel</button></a>
                    <input type="hidden" id="IDer" value="<?=$teamId?>" >
                    <button class="commentbtn">Comment</button>
                 </div>
            </form>
            <?php else:?>
                <form class="commentArea" id="commentform" action="" novalidate>
                 <textarea disabled>login is needed for this feature!</textarea>
                <div class="container">
                    <button type="button" class="disabledcancelbtn" disabled>Cancel</button>
                    <input type="hidden" id="IDer" value="<?=$teamId?>" >
                    <button type="submit" class="disabledcommentbtn" disabled>Comment</button>
                 </div>
            </form>
            <?php endif?>

            <div class="commentSection" id="commentSec">
                <h2 id="commentsHeader">Comments:-</h2>
                <?php if($comment_len == 0):?> <h2 style="text-align:center; margin-bottom: 20px;">No comments!</h2><?php endif;?>
                <?php $cnt = 0;
                foreach($allComments as $cmnt): ?>
                <?php if($cnt != 0):?> <div class="line"></div> <?php endif;?>
                <div class="comments">

                <?php if($auth->authorize(["admin"])):?>
                    <a onClick="like(this);" rel="display_team.php?id=<?=$teamId?>&commentid=<?=$cmnt["id"]?>"><button id="deletebtn">Delete <i class="fa fa-trash-o"></i></button></a>  
                <?php endif;?>


                    <ul style="list-style-type:none;">
                    <li><b><?= $cmnt["author"]?></b> <sub><?= $cmnt["time"]?></sub>
                        <ul id="innerList">
                            <li><?= $cmnt["comment"]?></li>
                        </ul>
                    </li>
                    </ul>
                </div>
                <?php 
                $cnt++;
                endforeach; ?>
            </div>
        </div>
    </div>
    <script src="js/index.js"></script>
</body>
</html>
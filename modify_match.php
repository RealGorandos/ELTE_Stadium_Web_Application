<?php 
    require_once("utils/_init.php");


    if(verify_get("id")){
        $teamid = $_GET["id"];
        $teamData = $teamStorage->findById($teamid);
        if(verify_get("matchid")){
            $matchid = $_GET["matchid"];
            $match = $matchStorage->findById($matchid);
            $matchIndexed =  array_values($match);
        }
    }

    if (verify_post("Hscore", "Ascore", "matchdate")) {
        $homeTeamScore = trim($_POST["Hscore"]);
        $awayTeamScore = trim($_POST["Ascore"]);
        $date = trim($_POST["matchdate"]);

        if(!empty($homeTeamScore) && empty($awayTeamScore) || empty($homeTeamScore) && !empty($awayTeamScore)){
            $hScoreErr = "Both scores must be numbers or empty";
            $aScoreErr = "Both scores must be numbers or empty";
        }
        if(!is_numeric($homeTeamScore) && !empty($homeTeamScore)){
            $hScoreErr = "Score must be numbers";
        }

        if(!is_numeric($awayTeamScore)&& !empty($awayTeamScore)){
            $aScoreErr = "Score must be numbers";
        }

        if(!isset($aScoreErr) && !isset($hScoreErr)){
            $matchTemp = [
                'home' => [
                        'teamid' => $match["home"]["teamid"],
                        'score' => $homeTeamScore
                ],
                'away' =>[
                    'teamid' => $match["away"]["teamid"],
                    'score' => $awayTeamScore
                ],
                'date' => $date,
                'id' => $matchid 
            ];

            $matchStorage->update($matchid, $matchTemp);
            redirect("display_team.php?id=".$teamid);
        }
    }

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <title>Modify</title>
</head>
<body>
<video autoplay loop class="back-video" muted plays-inline>
            <source src="res/video/Stadium.mp4" type="video/mp4">
        </video>
        <div id="black_sheet"></div>

        <div class="mod-content">
        <h1 style="text-align: center; color:#f8f9fb;  text-shadow: 1px 1px 2px black, 0 0 25px blue, 0 0 5px darkblue;">ELTE Stadium</h1>
            <form class="mod-innercontent" action="" novalidate method="post">
                <div class="box">
                <h1>Modify Match Data</h1>
                <hr>
                <label for="Hteam"><b>Home Team</b></label>
                <input type="text" value="<?=getTeamName($teamStorage,$match["home"]["teamid"])?>" name="Hteam"  disabled>
                

                <label for="Hscore"><b>Home Score</b></label>
                <?php if(isset($homeTeamScore)):?>
                <input type="text" value="<?=$homeTeamScore?>" name="Hscore" required>
                <?php if(isset($hScoreErr)):?> <span class="error"><?=$hScoreErr?></span> <?php endif?>
                <?php else:?>
                <input type="text" value="<?=$match['home']['score']?>" name="Hscore" required>
                <?php endif?>


                <label for="Ateam"><b>Away Team</b></label>
                <input type="text" value="<?=getTeamName($teamStorage,$match["away"]["teamid"])?>" name="Ateam" disabled>


                <label for="Ascore"><b>Away Score</b></label>
                <?php if(isset($awayTeamScore)):?>
                    <input type="text" value="<?=$awayTeamScore?>" name="Ascore" required>
                    <?php if(isset($aScoreErr)):?> <span class="error"><?=$aScoreErr?></span> <?php endif?>
                <?php else:?>
                    <input type="text" value="<?=$match['away']['score']?>" name="Ascore" required>
                <?php endif?>

                <label for="matchdate"><b>Date:</b></label>
                <?php if(isset($date)):?>
                <input type="date" name="matchdate" value="<?=$date?>">
                <?php else:?>
                    <input type="date" name="matchdate" value="<?=$match['date']?>">
                <?php endif?>

                <div class="clearfix">
                    <a href="display_team.php?id=<?=$teamid?>"><button type="button" class="cancelbtn">Cancel</button></a>
                    <button type="submit" class="signupbtn">Save</button>
                </div>
                </div>
            </form>
        </div>
</body>
</html>
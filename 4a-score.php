<?php
// INIT - GET GAME INFO
$gameID = 1; // REMEMBER TO CHANGE THIS
$reload = 5000; // AUTO PAGE RELOAD EVERY X MS
require __DIR__ . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "2a-config.php" ;
require PATH_LIB . "2b-lib-game.php" ;
$gameDB = new Game();
$game = $gameDB->get($gameID);
$score = $gameDB->latestScore($gameID);
$history = $gameDB->getScore($gameID);
if (is_array($score)) {
  $time = $score['score_time'];
  $home = $score['score_home'];
  $away = $score['score_away'];
} else {
  $time = $game['game_start'];
  $home = 0;
  $away = 0;
}
?>
<!DOCTYPE html>
<html>
  <head>
    <title>GAME SCORE DEMO</title>
    <link href="public/4b-score.css" rel="stylesheet">
    <script>
    window.addEventListener("load", function(){
      setTimeout(function(){
       window.location.reload(1);
      }, <?=$reload?>);
    });
    </script>
  </head>
  <body>
    <!-- [SCOREBOARD] -->
    <table id="scoreboard">
      <tr id="scoretime"><td colspan="2">
        <div class="scoredark"><?=$time?></div>
      </td></tr>
      <tr id="scorenumber">
        <td><div class="scoredark"><?=$home?></div></td>
        <td><div class="scoredark"><?=$away?></div></td>
      </tr>
      <tr id="scorename">
        <td><div class="scoregrey"><?=$game['game_home']?></div></td>
        <td><div class="scoregrey"><?=$game['game_away']?></div></td>
      </tr>
    </table>

    <!-- [HISTORY] -->
    <div id="scorehistory"><?php
    foreach ($history as $h) {
      printf("<div>[%s] %s-%s | %s</div>",
        $h['score_time'], $h['score_home'], $h['score_away'], $h['score_comment']
      );
    }
    ?></div>
  </body>
</html>
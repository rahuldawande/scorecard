<?php
class Game {
  /* [DATABASE HELPER FUNCTIONS] */
  protected $pdo = null;
  protected $stmt = null;
  public $error = "";
  public $lastID = null;

  function __construct() {
  // __construct() : connect to the database
  // PARAM : DB_HOST, DB_CHARSET, DB_NAME, DB_USER, DB_PASSWORD

    // ATTEMPT CONNECT
    try {
      $str = "mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET;
      if (defined('DB_NAME')) { $str .= ";dbname=" . DB_NAME; }
      $this->pdo = new PDO(
        $str, DB_USER, DB_PASSWORD, [
          PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
          PDO::ATTR_EMULATE_PREPARES => false
        ]
      );
      return true;
    }

    // ERROR - DO SOMETHING HERE
    // THROW ERROR MESSAGE OR SOMETHING
    catch (Exception $ex) {
      print_r($ex);
      die();
    }
  }

  function __destruct() {
  // __destruct() : close connection when done

    if ($this->stmt !== null) { $this->stmt = null; }
    if ($this->pdo !== null) { $this->pdo = null; }
  }

  function start() {
	// start() : auto-commit off

    $this->pdo->beginTransaction();
  }

  function end($commit=1) {
  // end() : commit or roll back?

    if ($commit) { $this->pdo->commit(); }
    else { $this->pdo->rollBack(); }
  }

  function exec($sql, $data=null) {
  // exec() : run insert, replace, update, delete query
  // PARAM $sql : SQL query
  //       $data : array of data
 
    try {
      $this->stmt = $this->pdo->prepare($sql);
      $this->stmt->execute($data);
      $this->lastID = $this->pdo->lastInsertId();
    } catch (Exception $ex) {
      $this->error = $ex;
      return false;
    }
    $this->stmt = null;
    return true;
  }

  function fetch($sql, $cond=null, $key=null, $value=null) {
  // fetch() : perform select query
  // PARAM $sql : SQL query
  //       $cond : array of conditions
  //       $key : sort in this $key=>data order, optional
  //       $value : $key must be provided, sort in $key=>$value order

    $result = false;
    try {
      $this->stmt = $this->pdo->prepare($sql);
      $this->stmt->execute($cond);
      if (isset($key)) {
        $result = array();
        if (isset($value)) {
          while ($row = $this->stmt->fetch(PDO::FETCH_NAMED)) {
            $result[$row[$key]] = $row[$value];
          }
        } else {
          while ($row = $this->stmt->fetch(PDO::FETCH_NAMED)) {
            $result[$row[$key]] = $row;
          }
        }
      } else {
        $result = $this->stmt->fetchAll();
      }
    } catch (Exception $ex) {
      $this->error = $ex;
      return false;
    }
    $this->stmt = null;
    return $result;
  }
  
  /* [GAME SCORE FUNCTIONS] */
  function getAll() {
  // getAll() : get all games

    $sql = "SELECT * FROM `game`";
    $game = $this->fetch($sql);
    return count($game)==0 ? false : $game ; 
  }

  function get($id) {
  // get() : get selected game

    $sql = "SELECT * FROM `game` WHERE `game_id`=?";
    $cond = [$id];
    $game = $this->fetch($sql, $cond);
    return count($game)==0 ? false : $game[0] ; 
  }

  function add($home, $away) {
  // add() : add a new game
  // PARAM $home : name of home team
  //       $away : name of away team

    $sql = "INSERT INTO `game` (`game_home`, `game_away`) VALUES (?, ?)";
    $cond = [$home, $away];
    return $this->exec($sql, $cond);
  }

  function edit($home, $away, $id) {
  // edit() : edit game
  // PARAM $home : name of home team
  //       $away : name of away team
  //       $id : game id

    $sql = "UPDATE `game` SET `game_home`=?, `game_away`=? WHERE `game_id`=?";
    $cond = [$home, $away, $id];
    return $this->exec($sql, $cond);
  }

  function del($id) {
  // del() : delete game
  // PARAM $id : game id

    $this->start();
    $sql = "DELETE FROM `game_score` WHERE `game_id`=?";
    $cond = [$id];
    $pass = $this->exec($sql, $cond);
    if ($pass) {
      $sql = "DELETE FROM `game` WHERE `game_id`=?";
      $pass = $this->exec($sql, $cond);
    }
    $this->end($pass);
    return $pass;
  }

  /* [GAME SCORES] */
  function latestScore($id) {
  // latestScore() : get only the latest score entry

    $sql = "SELECT * FROM `game_score` WHERE `game_id`=? ORDER BY `score_time` DESC LIMIT 1";
    $cond = [$id];
    $scores = $this->fetch($sql, $cond);
    return count($scores)==0 ? false : $scores[0] ;
  }

  function getScore($id) {
  // getScore() : get game score
  // PARAM $id : game id

    $sql = "SELECT * FROM `game_score` WHERE `game_id`=? ORDER BY `score_time` DESC";
    $cond = [$id];
    $scores = $this->fetch($sql, $cond);
    return count($scores)==0 ? false : $scores ;
  }

  function addScore($id, $home=0, $away=0, $comment="") {
  // addScore() : add game score or comment
  // PARAM $id : game id
  //       $home : home score
  //       $away : away score
  //       $comment : comment

    $sql = "INSERT INTO `game_score` (`game_id`, `score_home`, `score_away`";
    $cond = [$id, $home, $away];
    if ($comment!="") {
      $sql .= ", `score_comment`";
      $cond[] = $comment;
    }
    $sql .= ") VALUES (?, ?, ?" . ($comment=="" ? "" : ", ?") . ")" ;
    return $this->exec($sql, $cond);
  }

  function delScore($id, $date) {
  // delScore() : delete score
  // PARAM $id : game id
  //       $date : score time

    $sql = "DELETE FROM `game_score` WHERE `game_id`=? AND `score_time`=?";
    $cond = [$id, $date];
    return $this->exec($sql, $cond);
  }
}
?>
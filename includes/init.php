<?php
// vvv DO NOT MODIFY/REMOVE vvv

// check current php version to ensure it meets 2300's requirements
function check_php_version()
{
  if (version_compare(phpversion(), '7.0', '<')) {
    define(VERSION_MESSAGE, "PHP version 7.0 or higher is required for 2300. Make sure you have installed PHP 7 on your computer and have set the correct PHP path in VS Code.");
    echo VERSION_MESSAGE;
    throw VERSION_MESSAGE;
  }
}
check_php_version();

function config_php_errors()
{
  ini_set('display_startup_errors', 1);
  ini_set('display_errors', 0);
  error_reporting(E_ALL);
}
config_php_errors();

// open connection to database
function open_or_init_sqlite_db($db_filename, $init_sql_filename)
{
  if (!file_exists($db_filename)) {
    $db = new PDO('sqlite:' . $db_filename);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (file_exists($init_sql_filename)) {
      $db_init_sql = file_get_contents($init_sql_filename);
      try {
        $result = $db->exec($db_init_sql);
        if ($result) {
          return $db;
        }
      } catch (PDOException $exception) {
        // If we had an error, then the DB did not initialize properly,
        // so let's delete it!
        unlink($db_filename);
        throw $exception;
      }
    } else {
      unlink($db_filename);
    }
  } else {
    $db = new PDO('sqlite:' . $db_filename);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $db;
  }
  return null;
}

function exec_sql_query($db, $sql, $params = array())
{
  $query = $db->prepare($sql);
  if ($query and $query->execute($params)) {
    return $query;
  }
  return null;
}
// ^^^ DO NOT MODIFY/REMOVE ^^^

// You may place any of your code here.

// $db = open_or_init_sqlite_db('secure/site.sqlite', 'secure/init.sql');

$db = open_or_init_sqlite_db('secure/gallery.sqlite', 'secure/init.sql');





//THE FOLLOWING LOGIN AND LOGOUT CODE WAS ADAPTED FROM THE ORIGINAL SOURCE: Kyle Harms INFO 2300: Lecture 17 Code
//Handling login and logout
$user_messages = array(); //messages array for the user

function logIn($username, $password){
  //Returns online_user, NULL if none
  //make db, online_user, user_messages global vars
  global $db;
  global $online_user;
  global $user_messages;

  //login button pushed and user and pass given
  if ( isset($username) && isset($password) && $username != "" && $password != "" ){
    //check if username is in the db
    $sql = "SELECT * FROM users WHERE username = :username;";
    $params = array(
      ':username' => $username
    );

    $records = exec_sql_query($db, $sql, $params)->fetchAll();

    if ($records){ //we have a username in the db
      $userAcct = $records[0]; //get the acct for this user
      //check if password is same as hash
      if ( password_verify($password, $userAcct['password'])){
        //create a session
        $session = session_create_id();
        //update session id in database in sessions table for corresponding userid
        // $sql = "UPDATE sessions SET session = :session WHERE user_id = :user_id;";
        $sql = "INSERT INTO sessions (user_id, session) VALUES (:user_id, :session);";

        $params = array(
          ':session' => $session,
          ':user_id' => $userAcct['id']
        );

        $result = exec_sql_query($db, $sql, $params);
        if ($result){
          //session successfully stored in db
          //set cookie
          setcookie("session", $session, time()+3600); //cookie lasts for one hour
          $online_user = find_session_user($session);
          return $online_user;
        } else{ //could not store session
          array_push($user_messages, "Unable to login.");
        }
      } else { //Invalid password
        array_push($user_messages, "Invalid password.");
      }
    } else { //Invalid username
      array_push($user_messages, "Invalid username.");
    }
  } else { //no username or password
    array_push($user_messages, "No username or password provided.");

  }
  //failed login
  $online_user = NULL;
  return NULL;
}

//def find_session_user($session)
function find_session_user($session){
  //Returns the user record that corresponds to the session
  global $db;
  if (isset($session)){ //a session id exists in db
    //get record for user
    //INNER JOIN excludes any NULL entries in the tables
    $sql = "SELECT users.id, users.username, users.password FROM users INNER JOIN sessions ON users.id = sessions.user_id WHERE session = :session;";

    $params = array(
      ':session' => $session
    );

    $records = exec_sql_query($db, $sql, $params)->fetchAll();
    if ($records){
      return $records[0]; //since username is unique
    }

  }
  return NULL; //no session found so no user record found
}

function session_login(){
  //keeping online_user logged in
  global $db;
  global $online_user;
  if (isset($_COOKIE["session"])){
    $session = $_COOKIE["session"];
    $online_user = find_session_user($session);
    if (isset($online_user)){ //if user is logged in
      //renew cookie for an additional hour
      setcookie("session", $session, time()+3600);
    }
    // return $online_user;
  }
  // $online_user = NULL;
  // return NULL;
}

function is_logged_in(){
  //Returns bool if user is logged in or not
  global $online_user;
  return ($online_user != NULL); //online_user not NULL if user logged in
}

function logOut(){
  //Logs out user and expires their cookie
  global $online_user;
  global $db;
  setcookie("session", "", time()-100); //expire session cookie

  //remove session from sessions table
  $online_user_id = $online_user["id"];

  $sql = "DELETE FROM sessions WHERE user_id = :online_user_id;";

  $params = array(
    ':online_user_id' => $online_user_id
  );

  $result = exec_sql_query($db, $sql, $params);

  $online_user = NULL; //no user is logged in
}

//Check LOGIN
//Logging in a user
if (isset($_POST['login']) && isset($_POST['username']) && isset($_POST['password'])){

  $username = trim($_POST['username']); //remove lead and trail whitespace
  $password = trim($_POST['password']); //remove lead and trail whitespace
  logIn($username, $password);

}else{ //didn't press login -> check if already logged in
  session_login();
}

//Check LOGOUT
//Logging OUT a user
if (isset($online_user) && isset($_POST['logout'])){
  logOut();
}


?>

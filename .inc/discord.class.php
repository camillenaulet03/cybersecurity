<?php

include_once("./EnvBuilder.php");
(new Env('../.env'))->load();

class Discord {
    private $OAUTH2_CLIENT_ID;
    private $OAUTH2_CLIENT_SECRET;
    private $BOT_TOKEN = 'NzQ1MzEyNjc4ODA5MTc0MDk4.Xzv8hQ.9WLSocZBQknht_lko84A7q4Fyo0';
    private $authorizeURL = 'https://discordapp.com/api/oauth2/authorize';
    private $tokenURL = 'https://discordapp.com/api/oauth2/token';
    private $apiURLbase = 'https://discordapp.com/api/users/@me';

    private $token;
    private $access_token;
    private $redirect_uri = 'http://127.0.0.1/Gestbot/Connection/login.php';
    private $redirect = '../Connection/loged';

    public function __construct() {
      $this->OAUTH2_CLIENT_ID = getenv("OAUTH2_CLIENT_ID");
      $this->OAUTH2_CLIENT_SECRET = getenv("OAUTH2_CLIENT_SECRET");
      if(isset($_SESSION["discordOAUTH2"])){
        header("Location: ".$this->redirect);
        return $_SESSION["discordOAUTH2"];
        //$this = $_SESSION["discordOAUTH2"];
      } else if($_GET['code']){
        $this->getToken($_GET['code']);
      } else {
        $this->login();
      }
    }

    public function login() {
      $params = array(
        'client_id' => $this->OAUTH2_CLIENT_ID,
        'redirect_uri' => $this->redirect_uri,
        'response_type' => 'code',
        'permissions' => 8,
        'scope' => 'identify email guilds rpc'
      );

      // Redirect the user to Discord's authorization page
      header('Location: https://discordapp.com/api/oauth2/authorize' . '?' . http_build_query($params));
      die();
    }

    public function getToken($code) {
      // Exchange the auth code for a token
      $token = $this->apiRequest($this->tokenURL, array(
            'grant_type' => "authorization_code",
            'client_id' => $this->OAUTH2_CLIENT_ID,
            'client_secret' => $this->OAUTH2_CLIENT_SECRET,
            'redirect_uri' => $this->redirect_uri,
            'code' => $code
      ));

      $this->access_token = $token->access_token;
      $_SESSION['discordOAUTH2'] = $this;
      header("Location: ".$this->redirect);
    }

    public function apiRequest($url, $post=null, $headers=array(), $bot=false) {
      $ch = curl_init($url);
      curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      $response = curl_exec($ch);

      if($post){
          curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
      }

      $headers[] = 'Accept: application/json';

      if($this->access_token){
          $headers[] = 'Authorization: Bearer ' . $this->access_token;
      }

      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

      $response = curl_exec($ch);
      return json_decode($response);
    }
}

/*

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('max_execution_time', 300); //300 seconds = 5 minutes. In case if your CURL is slow and is loading too much (Can be IPv6 problem)

error_reporting(E_ALL);

define('OAUTH2_CLIENT_ID', '745312678809174098'); //Your client Id
define('OAUTH2_CLIENT_SECRET', '-50eDBkESQCiM2LKGH-2-8oA9X3G0fg5'); //Your secret client code
define('BOT_TOKEN', 'NzQ1MzEyNjc4ODA5MTc0MDk4.Xzv8hQ.9WLSocZBQknht_lko84A7q4Fyo0'); //Your secret client code

$authorizeURL = 'https://discordapp.com/api/oauth2/authorize';
$tokenURL = 'https://discordapp.com/api/oauth2/token';
$apiURLBase = 'https://discordapp.com/api/users/@me';
session_start();

// Start the login process by sending the user to Discord's authorization page
if(get('action') == 'login') {

  $params = array(
    'client_id' => OAUTH2_CLIENT_ID,
    'redirect_uri' => 'http://127.0.0.1/Gestbot/discord.class.php',
    'response_type' => 'code',
    'permissions' => 8,
    'scope' => 'identify email guilds rpc'
  );

  // Redirect the user to Discord's authorization page
  header('Location: https://discordapp.com/api/oauth2/authorize' . '?' . http_build_query($params));
  die();
}


// When Discord redirects the user back here, there will be a "code" and "state" parameter in the query string
if(get('code')) {

  // Exchange the auth code for a token
  $token = apiRequest($tokenURL, array(
        'grant_type' => "authorization_code",
        'client_id' => OAUTH2_CLIENT_ID,
        'client_secret' => OAUTH2_CLIENT_SECRET,
        'redirect_uri' => 'http://127.0.0.1/Gestbot/discord.class.php',
        'code' => get('code')
  ));

  var_dump($token);
  $logout_token = $token->access_token;
  $_SESSION['access_token'] = $token->access_token;


  //header('Location: ' . $_SERVER['PHP_SELF']);
}

if(session('access_token')) {

  $user = apiRequest($apiURLBase,array());

  echo '<h3>Logged In</h3>';
  echo '<h4>Welcome, ' . $user->username . '</h4>';
  echo '<pre>';
    //print_r($user);
  echo '</pre>';

  $params = array(
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array('Authorization : Bot ' . BOT_TOKEN),
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_FOLLOWLOCATION => 1,
    CURLOPT_VERBOSE        => 1,
    CURLOPT_SSL_VERIFYPEER => 0,
  );

  $headers[] = 'Authorization: Bot ' . BOT_TOKEN;
  $headers[] = 'Content-Type: application/json';
  $headers[] = 'Content-Length: '.strlen(json_encode($params));

  $createChan = apiRequest("https://discord.com/api/channels/608738035470696460/messages",$params,$headers);

  var_dump($createChan);
} else {
  echo '<h3>Not logged in</h3>';
  echo '<p><a href="?action=login">Log In</a></p>';
}


if(get('action') == 'logout') {
  // This must to logout you, but it didn't worked(

  $params = array(
    'access_token' => $logout_token
  );

  // Redirect the user to Discord's revoke page
  header('Location: https://discordapp.com/api/oauth2/token/revoke' . '?' . http_build_query($params));
  die();
}

function apiRequest($url, $post=FALSE, $headers=array(), $bot=false) {
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  $response = curl_exec($ch);

  if($post)
      curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));

  $headers[] = 'Accept: application/json';

  if(session('access_token'))
      $headers[] = 'Authorization: Bearer ' . session('access_token');

  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

  $response = curl_exec($ch);
  return json_decode($response);
}


function get($key, $default=NULL) {
  return array_key_exists($key, $_GET) ? $_GET[$key] : $default;
}

function session($key, $default=NULL) {
  return array_key_exists($key, $_SESSION) ? $_SESSION[$key] : $default;
}

*/
?>

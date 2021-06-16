<?php

include_once("./.inc/EnvBuilder.php");
(new Env('.env'))->load();

class Discord {
    private $OAUTH2_CLIENT_ID;
    private $OAUTH2_CLIENT_SECRET;
    private $BOT_TOKEN = 'NzQ1MzEyNjc4ODA5MTc0MDk4.Xzv8hQ.9WLSocZBQknht_lko84A7q4Fyo0';
    private $authorizeURL = 'https://discordapp.com/api/oauth2/authorize';
    private $tokenURL = 'https://discordapp.com/api/oauth2/token';
    private $apiURLbase = 'https://discordapp.com/api/users/@me';

    private $token;
    public $access_token;
    private $redirect_uri = 'http://127.0.0.1/mee6/index.php';
    private $redirect = './loged.php';

    public function __construct() {
      $this->OAUTH2_CLIENT_ID = getenv("OAUTH2_CLIENT_ID");
      $this->OAUTH2_CLIENT_SECRET = getenv("OAUTH2_CLIENT_SECRET");
      if(isset($_SESSION["token"])){
        header("Location: ".$this->redirect);
      } else if($_GET['code']){
        $_SESSION["code"] = $_GET['code'];
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
        'scope' => 'identify email guilds'
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
      $_SESSION['token'] = $this->access_token;
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

      var_dump($this->access_token);
      if($this->access_token){
          $headers[] = 'Authorization: Bearer ' . $this->access_token;
      }

      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

      $response = curl_exec($ch);
      return json_decode($response);
    }

    public function getInformations(){
      return $this->apiRequest($this->apiURLbase,array());
    }
}

?>

<?php
require_once "dbh.classes.php";
require_once "signup-contr.classes.php";
require_once "../webhooks/discord.php";


class LoginContr extends Login {
    private $uid;
    private $pwd;

    public function __construct($uid, $pwd)
    {
        $this->uid = $uid;
        $this->pwd = $pwd;
    }

    public function loginUser(){
        $key = $this->getKey($this->uid);
        if(($this->isKeyBlacklisted($key)) == true){
            header("Location: ../index.php?error=keyisblacklisted");
            sendWebhook("https://discordapp.com/api/webhooks/1063562178415841360/fR4P1eZyo1fITzSlmmoauvoKutzzLSUngTzbYSFB8IBfZLO8bBmpM1PL11IWGotyFCj5", "Login Failed - Blacklisted User", "#ED1D24", "Key:", $key );
            die();
        }
        if($this->emptyInput() == false){
            header("Location: index.php?error=emptyinput");
            die();
        }
        
        $this->getUser($this->uid, $this->pwd);
    }

    public function isKeyBlacklisted($key){
  
        $stmt = $this->connect()->prepare('SELECT * FROM keylist WHERE license_key = ?;');

        if(!$stmt->execute(array($key))){
            $stmt = null;
            exit();
        }
        
        $resultCheck;
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($data[0]['key_blacklisted'] == 0){
            $resultCheck = false;
        }
        if ($data[0]['key_blacklisted'] == 1){
            $resultCheck = true;
        }

        return $resultCheck;
    }

    private function emptyInput(){
        $result;
        if(empty($this->uid) || empty($this->pwd))
        {
            $result = false;
        }
        else
        {
            $result = true;
        }
        return $result;
    }
}
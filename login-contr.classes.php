<?php
require_once "dbh.classes.php";
require_once "signup-contr.classes.php";


class LoginContr extends Login {
    private $uid;
    private $pwd;

    public function __construct($uid, $pwd)
    {
        $this->uid = $uid;
        $this->pwd = $pwd;
    }

    public function loginUser(){
        if($this->emptyInput() == false){
            header("Location: index.php?error=emptyinput");
            die();
        }
        
        $this->getUser($this->uid, $this->pwd);
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

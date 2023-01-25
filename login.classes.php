<?php
class Login extends Dbh {


    protected function getUser($uid, $pwd){
        
        $stmt = $this->connect()->prepare('SELECT users_pwd FROM users WHERE users_uid = ? OR users_pwd = ?;');

        if(!$stmt->execute(array($uid, $pwd)))
        {
            $stmt = null;
            // header(?error=stmtfailed)
            exit();
        }

        if($stmt->rowCount() == 0)
        {
            $stmt = null;
            header("location: ../login.php?error=usernotfound");
            exit();
        }


        $pwdHashed = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $checkPwd = password_verify($pwd, $pwdHashed[0]["users_pwd"]);

        if($checkPwd == false)
        {
            $stmt = null;
            header("location: ../login.php?error=wrongpassword");
            
            exit();
        }
        else if($checkPwd == true){
            $stmt = $this->connect()->prepare('SELECT * FROM users WHERE users_uid= ? OR users_email = ? AND users_pwd = ?;'); 

            if(!$stmt->execute(array($uid, $uid, $pwd)))
            {
                $stmt = null;
                // header(?error=stmtfailed)
                exit();
            }

            if($stmt->rowCount() == 0)
            {
                $stmt = null;
                header("location: ../login.php?error=usernotfound");
                
                exit();
            }

            $user = $stmt->fetchAll(PDO::FETCH_ASSOC);

            session_start();
            $_SESSION["userid"] = $user[0]["users_id"];
            $_SESSION["username"] = $user[0]["users_name"];
            $_SESSION["useruid"] = $user[0]["users_uid"];
            $_SESSION["useremail"] = $user[0]["users_email"];
            $_SESSION["userkey"] = $user[0]["users_key"];
            $_SESSION["userjoindate"] = $user[0]["users_joindate"];

            if($_SESSION["userkey"] === null){
                $_SESSION["userkey"] = "·ERROR·";
            }
            $stmt = null;

            
        }

        $stmt = null;
    }
}

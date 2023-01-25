<?php
class Login extends Dbh {


    protected function getUser($uid, $pwd){
        
        $stmt = $this->connect()->prepare('SELECT password FROM accounts WHERE login = ? OR password = ?;');

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
        $checkPwd = password_verify($pwd, $pwdHashed[0]["password"]);

        if($checkPwd == false)
        {
            $stmt = null;
            header("location: ../login.php?error=wrongpassword");
            
            exit();
        }
        else if($checkPwd == true){
            $stmt = $this->connect()->prepare('SELECT * FROM accounts WHERE login= ? OR email = ? AND password = ?;'); 

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
            $_SESSION["userId"] = $user[0]["id"];
            $_SESSION["userName"] = $user[0]["login"];
            $_SESSION["userPassword"] = $user[0]["password"];
            $_SESSION["userEmail"] = $user[0]["email"];

            $stmt = null;

            
        }

        $stmt = null;
    }
}

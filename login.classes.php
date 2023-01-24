<?php
require_once "../webhooks/discord.php";
class Login extends Dbh {

    public function getKey($uid){
        $stmt = $this->connect()->prepare('SELECT * FROM keylist WHERE key_uid = ?;');

         if(!$stmt->execute(array($uid)))
        {
            $stmt = null;
            // header(?error=stmtfailed)
            exit();
        }
        $userinfo = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $key = $userinfo[0]['license_key'];
        return $key;
    }

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
            sendWebhook("https://discordapp.com/api/webhooks/1063562178415841360/fR4P1eZyo1fITzSlmmoauvoKutzzLSUngTzbYSFB8IBfZLO8bBmpM1PL11IWGotyFCj5", "Login Failed", "#ED1D24", "Username", $uid );
            exit();
        }


        $pwdHashed = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $checkPwd = password_verify($pwd, $pwdHashed[0]["users_pwd"]);

        if($checkPwd == false)
        {
            $stmt = null;
            header("location: ../login.php?error=wrongpassword");
            sendWebhook("https://discordapp.com/api/webhooks/1063562178415841360/fR4P1eZyo1fITzSlmmoauvoKutzzLSUngTzbYSFB8IBfZLO8bBmpM1PL11IWGotyFCj5", "Login Failed", "#ED1D24", "Username", $uid );
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
                sendWebhook("https://discordapp.com/api/webhooks/1063562178415841360/fR4P1eZyo1fITzSlmmoauvoKutzzLSUngTzbYSFB8IBfZLO8bBmpM1PL11IWGotyFCj5", "Login Failed", "#ED1D24", "Username", $uid );
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

            sendWebhook("https://discordapp.com/api/webhooks/1063562178415841360/fR4P1eZyo1fITzSlmmoauvoKutzzLSUngTzbYSFB8IBfZLO8bBmpM1PL11IWGotyFCj5", "Login Success", "#1CE815", "Username", $uid );
        }

        $stmt = null;
    }
}
<?PHP
/*
    Registration/Login script from HTML Form Guide
    V1.0

    This program is free software published under the
    terms of the GNU Lesser General Public License.
    http://www.gnu.org/copyleft/lesser.html
    

This program is distributed in the hope that it will
be useful - WITHOUT ANY WARRANTY; without even the
implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.

For updates, please visit:
http://www.html-form-guide.com/php-form/php-registration-form.html
http://www.html-form-guide.com/php-form/php-login-form.html

*/
require_once("class.phpmailer.php");
require_once("formvalidator.php");

class FGMembersite
{
    var $admin_email;
    var $from_address;
    
    var $username;
    var $pwd;
    var $database;
    var $tablename;
    var $connection;
    var $rand_key;
    
    var $error_message;
	
	var $users_table_name;
	var $servers_table_name;
	var $users_servers_table_name;
	var $invites_table_name;
	var $requests_table_name;
    
    //-----Initialization -------
    function FGMembersite()
    {
        $this->sitename = 'YourWebsiteName.com';
        $this->rand_key = '0iQx5oBk66oVZep';
		error_reporting(E_ALL ^ E_DEPRECATED);
    }
    
    function InitDB($host,$uname,$pwd,$database,$tablename)
    {
        $this->db_host  = $host;
        $this->username = $uname;
        $this->pwd  = $pwd;
        $this->database  = $database;
        $this->tablename = $tablename;    
    }
	
    function SetAdminEmail($email)
    {
        $this->admin_email = $email;
    }
    
    function SetWebsiteName($sitename)
    {
        $this->sitename = $sitename;
    }
    
    function SetRandomKey($key)
    {
        $this->rand_key = $key;
    }
	
	function SetUserTableName($name) {
		$this->users_table_name = $name;	
	}
	
	function SetServersTableName($name) {
		$this->servers_table_name = $name;	
	}
	
	function SetUserServerTableName($name) {
		$this->users_servers_table_name = $name;	
	}
	
	function SetInvitesTableName($name) {
		$this->invites_table_name = $name;	
	}
	
	function setRequestsTableName($name) {
		$this->requests_table_name = $name;	
	}
    
    //-------Main Operations ----------------------
    function RegisterUser()
    {
        if(!isset($_POST['submitted']))
        {
           return false;
        }
        
        $formvars = array();
        
        if(!$this->ValidateRegistrationSubmission())
        {
            return false;
        }
        
        $this->CollectRegistrationSubmission($formvars);
        
        if(!$this->SaveToDatabase($formvars))
        {
            return false;
        }
        
        if(!$this->SendUserConfirmationEmail($formvars))
        {
            return false;
        }
		/*
        $this->SendAdminIntimationEmail($formvars);
		*/
        return true;
    }
	
	function OurRegisterUser() {
		if(!isset($_POST['email']))
        {
			echo "<script>console.log('No email received on regist :/');</script>";
           	return false;
        }
        
        $formvars = array();
        
        if(!$this->OurValidateRegistrationSubmission())
        {
			echo "<script>console.log('Validation failed!');</script>";
            return false;
        }
        
        $this->OurCollectRegistrationSubmission($formvars);
        
        if(!$this->AddUserToDatabase($formvars))
        {
			echo "<script>console.log('Failed to add user to database!');</script>";
            return false;
        }
		return true;
	}

    function ConfirmUser()
    {
        if(empty($_GET['code'])||strlen($_GET['code'])<=10)
        {
            $this->HandleError("Please provide the confirm code");
            return false;
        }
        $user_rec = array();
        if(!$this->UpdateDBRecForConfirmation($user_rec))
        {
            return false;
        }
        
        $this->SendUserWelcomeEmail($user_rec);
        
        $this->SendAdminIntimationOnRegComplete($user_rec);
        
        return true;
    }    
    
    function Login()
    {
        if(empty($_POST['username']))
        {
            $this->HandleError("UserName is empty!");
            return false;
        }
        
        if(empty($_POST['password']))
        {
            $this->HandleError("Password is empty!");
            return false;
        }
        
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        
        if(!isset($_SESSION)){ session_start(); }
        if(!$this->CheckLoginInDB($username,$password))
        {
            return false;
        }
        
        $_SESSION[$this->GetLoginSessionVar()] = $username;
        
        return true;
    }
	
	function OurLogin() {
		if(empty($_POST['email'])) {
			$this->HandleError("Email is empty!");
            return false;
        }
        
        if(empty($_POST['password']))
        {
            $this->HandleError("Password is empty!");
            return false;
        }
		
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
		
		if(!isset($_SESSION)){ session_start(); }
        if(!$this->OurCheckLoginInDB($email,$password))
        {
            return false;
        }
        
        $_SESSION[$this->GetLoginSessionVar()] = $email;
        
        return true;
	}
    
    function CheckLogin()
    {
         if(!isset($_SESSION)){ session_start(); }

         $sessionvar = $this->GetLoginSessionVar();
         
         if(empty($_SESSION[$sessionvar]))
         {
            return false;
         }
         return true;
    }
    
    function UserFullName()
    {
        return isset($_SESSION['name_of_user'])?$_SESSION['name_of_user']:'';
    }
    
    function UserEmail()
    {
        return isset($_SESSION['email_of_user'])?$_SESSION['email_of_user']:'';
    }
	
	function UserName() {
		return isset($_SESSION['userName'])?$_SESSION['userName']:'';
	}
	
	function OurUserEmail() {
		return isset($_SESSION['email'])?$_SESSION['email']:'';	
	}
    
    function LogOut()
    {
        session_start();
        
        $sessionvar = $this->GetLoginSessionVar();
        
        $_SESSION[$sessionvar]=NULL;
        
        unset($_SESSION[$sessionvar]);
    }
    
    function EmailResetPasswordLink()
    {
        if(empty($_POST['email']))
        {
            $this->HandleError("Email is empty!");
            return false;
        }
        $user_rec = array();
        if(false === $this->GetUserFromEmail($_POST['email'], $user_rec))
        {
            return false;
        }
        if(false === $this->SendResetPasswordLink($user_rec))
        {
            return false;
        }
        return true;
    }
    
    function ResetPassword()
    {
        if(empty($_GET['email']))
        {
            $this->HandleError("Email is empty!");
            return false;
        }
        if(empty($_GET['code']))
        {
            $this->HandleError("reset code is empty!");
            return false;
        }
        $email = trim($_GET['email']);
        $code = trim($_GET['code']);
        
        if($this->GetResetPasswordCode($email) != $code)
        {
            $this->HandleError("Bad reset code!");
            return false;
        }
        
        $user_rec = array();
        if(!$this->GetUserFromEmail($email,$user_rec))
        {
            return false;
        }
        
        $new_password = $this->ResetUserPasswordInDB($user_rec);
        if(false === $new_password || empty($new_password))
        {
            $this->HandleError("Error updating new password");
            return false;
        }
        
        if(false == $this->SendNewPassword($user_rec,$new_password))
        {
            $this->HandleError("Error sending new password");
            return false;
        }
        return true;
    }
    
    function ChangePassword()
    {
        if(!$this->CheckLogin())
        {
            $this->HandleError("Not logged in!");
            return false;
        }
        
        if(empty($_POST['oldpwd']))
        {
            $this->HandleError("Old password is empty!");
            return false;
        }
        if(empty($_POST['newpwd']))
        {
            $this->HandleError("New password is empty!");
            return false;
        }
        
        $user_rec = array();
        if(!$this->GetUserFromEmail($this->UserEmail(),$user_rec))
        {
            return false;
        }
        
        $pwd = trim($_POST['oldpwd']);
        
        if($user_rec['password'] != md5($pwd))
        {
            $this->HandleError("The old password does not match!");
            return false;
        }
        $newpwd = trim($_POST['newpwd']);
        
        if(!$this->ChangePasswordInDB($user_rec, $newpwd))
        {
            return false;
        }
        return true;
    }
	
	function RegistServer() {
        $formvars = array();
        
        if(!$this->ValidateServerSubmission())
        {
			/*echo "<script>console.log('Validation failed!');</script>";*/
            return false;
        }
        
        $this->CollectServerSubmission($formvars);
        
        if(!$this->AddServerToDatabase($formvars))
        {
			/*echo "<script>console.log('Failed to add server to database!');</script>";*/
            return false;
        }
		return true;	
	}
	
	function GetServerInfo() {
		$formvars = array();
	
		if(!$this->DBLogin()) {
            $this->HandleError("Database login failed!");
			echo "<script>console.log('Failed to login to database');</script>";
            return false;
        }
		if(!$this->CheckLogin())
        {
            $this->HandleError("Not logged in!");
			echo "<script>console.log('User not logged in');</script>";
            return false;
        }
		if(!$this->ConfirmServerRegistTable()) {
			echo "<script>console.log('Failed to confirm server table');</script>";
			return false;
		}
		if(!$this->ConfirmUserServerRegistTable()) {
			echo "<script>console.log('Failed to confirm user's server table');</script>";
			return false;
		}
		$ip = $this->Sanitize($_POST['ip']);
		$port = $this->Sanitize($_POST['port']);
		
		$result = $this->GetServerInfoFromDatabase($ip, $port);
		if (!$result) {
			echo "<script>console.log('Failed to get server info from database');</script>";
			return false;
		}
		return $result;
	}
	
	function GetServerInfoById() {
		if(!$this->DBLogin()) {
            $this->HandleError("Database login failed!");
			echo "<script>console.log('Failed to login to database');</script>";
            return false;
        }
		if(!$this->CheckLogin())
        {
            $this->HandleError("Not logged in!");
			echo "<script>console.log('User not logged in');</script>";
            return false;
        }
		if(!$this->ConfirmServerRegistTable()) {
			echo "<script>console.log('Failed to confirm server table');</script>";
			return false;
		}
		if(!$this->ConfirmUserServerRegistTable()) {
			echo "<script>console.log('Failed to confirm user's server table');</script>";
			return false;
		}
		$id_server = $this->Sanitize($_POST['id_server']);
		
		$result = $this->GetServerInfoFromDatabaseById($id_server);
		if (!$result) {
			echo "<script>console.log('Failed to get server info from database (ID version)');</script>";
			return false;
		}
		$admins = $this->GetServerAdmins($id_server);
		if (!$admins) {
			return false;
		}
		$result['admins'] = json_encode($admins);
		return $result;
	}
	
	function GetServerList() {
		if(!$this->DBLogin()) {
            $this->HandleError("Database login failed!");
			echo "<script>console.log('Failed to login to database');</script>";
            return false;
        }
		if(!$this->CheckLogin())
        {
            $this->HandleError("Not logged in!");
			echo "<script>console.log('User not logged in');</script>";
            return false;
        }
		if(!$this->ConfirmServerRegistTable()) {
			echo "<script>console.log('Failed to confirm server table');</script>";
			return false;
		}
		if(!$this->ConfirmUserServerRegistTable()) {
			echo "<script>console.log('Failed to confirm user's server table');</script>";
			return false;
		}
		$result = $this->GetUserServerListFromDatabase();
		if (!$result) {
			echo "<script>console.log('Failed to get user's server list from database');</script>";
			return false;
		}
		return $result;
	}
	
	function ModifyServerProperty() {
		if(!$this->DBLogin()) {
            $this->HandleError("Database login failed!");
			echo "<script>console.log('Failed to login to database');</script>";
            return false;
        }
		if(!$this->CheckLogin())
        {
            $this->HandleError("Not logged in!");
			echo "<script>console.log('User not logged in');</script>";
            return false;
        }
		if(!$this->ConfirmServerRegistTable()) {
			echo "<script>console.log('Failed to confirm server table');</script>";
			return false;
		}
		if(!$this->ConfirmUserServerRegistTable()) {
			echo "<script>console.log('Failed to confirm user's server table');</script>";
			return false;
		}
		$formvars = array();
		$this->ValidateServerSubmission($formvars);
		$this->CollectServerModifySubmission($formvars);
		$id_server = $this->Sanitize($formvars['id_server']);
		$result = $this->GetServerInfoFromDatabaseById($id_server);
		if (!$result) {
			echo "<script>console.log('Failed to get server info from database (ID version)');</script>";
			return false;
		}
		if(!$this->ModifyServerPropertyToDatabase($formvars)) {
			echo "<script>console.log('Failed to change server info');</script>";
			return false;	
		}
		$this->ModifyServerAdmins($formvars);
		return true;
	}
	
	function ModifyServerClients($id_server, $n_clients, $lastActiveUser) {
		if(!$this->DBLogin()) {
            $this->HandleError("Database login failed!");
			echo "<script>console.log('Failed to login to database');</script>";
            return false;
        }
		if(!$this->CheckLogin())
        {
            $this->HandleError("Not logged in!");
			echo "<script>console.log('User not logged in');</script>";
            return false;
        }
		if(!$this->ConfirmServerRegistTable()) {
			echo "<script>console.log('Failed to confirm server table');</script>";
			return false;
		}
		if(!$this->ConfirmUserServerRegistTable()) {
			echo "<script>console.log('Failed to confirm user's server table');</script>";
			return false;
		}
		$id_server = $this->Sanitize($id_server);
		$n_clients = $this->Sanitize($n_clients);
		$lastActiveUser = $this->Sanitize($lastActiveUser);
		$result = $this->GetServerInfoFromDatabaseById($id_server);
		if (!$result) {
			echo "<script>console.log('Failed to get server info from database (ID version)');</script>";
			return false;
		}
		if(!$this->ModifyServerClientsToDatabase($id_server, $n_clients, $lastActiveUser)) {
			echo "<script>console.log('Failed to change server number clients');</script>";
			return false;	
		}
		return true;
	}
	
	function ModifyServerOnline($id_server, $isOnline, $onlineSince, $lastActiveUser) {
		if(!$this->DBLogin()) {
            $this->HandleError("Database login failed!");
			echo "<script>console.log('Failed to login to database');</script>";
            return false;
        }
		if(!$this->CheckLogin())
        {
            $this->HandleError("Not logged in!");
			echo "<script>console.log('User not logged in');</script>";
            return false;
        }
		if(!$this->ConfirmServerRegistTable()) {
			echo "<script>console.log('Failed to confirm server table');</script>";
			return false;
		}
		if(!$this->ConfirmUserServerRegistTable()) {
			echo "<script>console.log('Failed to confirm user's server table');</script>";
			return false;
		}
		$id_server = $this->Sanitize($id_server);
		$isOnline = $this->Sanitize($isOnline);
		$onlineSince = $this->Sanitize($onlineSince);
		$lastActiveUser = $this->Sanitize($lastActiveUser);
		$result = $this->GetServerInfoFromDatabaseById($id_server);
		if (!$result) {
			/*echo "<script>console.log('Failed to get server info from database (ID version)');</script>";*/
			return false;
		}
		if(!$this->ModifyServerOnlineToDatabase($id_server, $isOnline, $onlineSince, $lastActiveUser)) {
			/*echo "<script>console.log('Failed to change server number clients');</script>";*/
			return false;	
		}
		return true;
	}
	
	function DeleteServer($id_server, $email) {
		if(!$this->DBLogin()) {
            $this->HandleError("Database login failed!");
			echo "<script>console.log('Failed to login to database');</script>";
            return false;
        }
		if(!$this->CheckLogin())
        {
            $this->HandleError("Not logged in!");
			echo "<script>console.log('User not logged in');</script>";
            return false;
        }
		if(!$this->ConfirmServerRegistTable()) {
			echo "<script>console.log('Failed to confirm server table');</script>";
			return false;
		}
		if(!$this->ConfirmUserServerRegistTable()) {
			echo "<script>console.log('Failed to confirm user's server table');</script>";
			return false;
		}
		$id_server = $this->Sanitize($id_server);
		$result = $this->GetServerInfoFromDatabaseById($id_server);
		if (!$result) {
			return true;
		}
		$this->DeleteServerFromDatabase($id_server, $email);
		return true;
	}
	
	function DeleteServerForEveryone($id_server) {
		if(!$this->DBLogin()) {
            $this->HandleError("Database login failed!");
			echo "<script>console.log('Failed to login to database');</script>";
            return false;
        }
		if(!$this->CheckLogin())
        {
            $this->HandleError("Not logged in!");
			echo "<script>console.log('User not logged in');</script>";
            return false;
        }
		if(!$this->ConfirmServerRegistTable()) {
			echo "<script>console.log('Failed to confirm server table');</script>";
			return false;
		}
		if(!$this->ConfirmUserServerRegistTable()) {
			echo "<script>console.log('Failed to confirm user's server table');</script>";
			return false;
		}
		$id_server = $this->Sanitize($id_server);
		$result = $this->GetServerInfoFromDatabaseById($id_server);
		if (!$result) {
			return true;
		}
		$this->DeleteServerForEveryoneFromDatabase($id_server);
		return true;
	}
	
	function GetAllUsers($email) {
		if(!$this->DBLogin()) {
            $this->HandleError("Database login failed!");
			echo "<script>console.log('Failed to login to database');</script>";
            return false;
        }
		if(!$this->CheckLogin())
        {
            $this->HandleError("Not logged in!");
			echo "<script>console.log('User not logged in');</script>";
            return false;
        }
		if(!$this->ConfirmUserRegistTable()) {
			echo "<script>console.log('Failed to confirm server table');</script>";
			return false;
		}
		$email = $this->Sanitize($email);
		$result = $this->GetUsersFromDatabase($email);
		if (!$result) {
			echo "<script>console.log('Failed to get users list from database');</script>";
			return false;
		}
		return $result;
	}
    
    //-------Public Helper functions -------------
    function GetSelfScript()
    {
        return htmlentities($_SERVER['PHP_SELF']);
    }    
    
    function SafeDisplay($value_name)
    {
        if(empty($_POST[$value_name]))
        {
            return'';
        }
        return htmlentities($_POST[$value_name]);
    }
    
    function RedirectToURL($url)
    {
        header("Location: $url");
        exit;
    }
    
    function GetSpamTrapInputName()
    {
        return 'sp'.md5('KHGdnbvsgst'.$this->rand_key);
    }
    
    function GetErrorMessage()
    {
        if(empty($this->error_message))
        {
            return '';
        }
        $errormsg = nl2br(htmlentities($this->error_message));
        return $errormsg;
    }    
    //-------Private Helper functions-----------
    
    function HandleError($err)
    {
        $this->error_message .= $err."\r\n";
    }
    
    function HandleDBError($err)
    {
        $this->HandleError($err."\r\n mysqlerror:".mysql_error());
    }
    
    function GetFromAddress()
    {
        if(!empty($this->from_address))
        {
            return $this->from_address;
        }

        $host = $_SERVER['SERVER_NAME'];

        $from ="SM@$host";
        return $from;
    } 
    
    function GetLoginSessionVar()
    {
        $retvar = md5($this->rand_key);
        $retvar = 'usr_'.substr($retvar,0,10);
        return $retvar;
    }
    
    function CheckLoginInDB($username,$password)
    {
        if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }          
        $username = $this->SanitizeForSQL($username);
        $pwdmd5 = md5($password);
        $qry = "Select name, email from $this->tablename where username='$username' and password='$pwdmd5' and confirmcode='y'";
        
        $result = mysql_query($qry,$this->connection);
        
        if(!$result || mysql_num_rows($result) <= 0)
        {
            $this->HandleError("Error logging in. The username or password does not match");
            return false;
        }
        
        $row = mysql_fetch_assoc($result);
        
        
        $_SESSION['name_of_user']  = $row['name'];
        $_SESSION['email_of_user'] = $row['email'];
        
        return true;
    }
	
	function OurCheckLoginInDB($email, $password) {
		 if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }
		
		$email = $this->SanitizeForSQL($email);
		$pwdmd5 = md5($password);
		$qry = "Select username, email from $this->users_table_name where email='$email' and password='$pwdmd5'";
		$result = mysql_query($qry, $this->connection);
		
		if(!$result || mysql_num_rows($result) <= 0) {
			$this->HandleError("Error logging in. Please check that your email and password are correct.");
			return false;
		}
		
		$row = mysql_fetch_assoc($result);
		$_SESSION['userName'] = $row['username'];
		$_SESSION['email'] = $row['email'];
		return true;
	}
    
    function UpdateDBRecForConfirmation(&$user_rec)
    {
        if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }   
        $confirmcode = $this->SanitizeForSQL($_GET['code']);
        
        $result = mysql_query("Select name, email from $this->tablename where confirmcode='$confirmcode'",$this->connection);   
        if(!$result || mysql_num_rows($result) <= 0)
        {
            $this->HandleError("Wrong confirm code.");
            return false;
        }
        $row = mysql_fetch_assoc($result);
        $user_rec['name'] = $row['name'];
        $user_rec['email']= $row['email'];
        
        $qry = "Update $this->tablename Set confirmcode='y' Where  confirmcode='$confirmcode'";
        
        if(!mysql_query( $qry ,$this->connection))
        {
            $this->HandleDBError("Error inserting data to the table\nquery:$qry");
            return false;
        }      
        return true;
    }
    
    function ResetUserPasswordInDB($user_rec)
    {
        $new_password = substr(md5(uniqid()),0,10);
        
        if(false == $this->ChangePasswordInDB($user_rec,$new_password))
        {
            return false;
        }
        return $new_password;
    }
    
    function ChangePasswordInDB($user_rec, $newpwd)
    {
        $newpwd = $this->SanitizeForSQL($newpwd);
        
        $qry = "Update $this->tablename Set password='".md5($newpwd)."' Where  id_user=".$user_rec['id_user']."";
        
        if(!mysql_query( $qry ,$this->connection))
        {
            $this->HandleDBError("Error updating the password \nquery:$qry");
            return false;
        }     
        return true;
    }
    
    function GetUserFromEmail($email,&$user_rec)
    {
        if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }   
        $email = $this->SanitizeForSQL($email);
        
        $result = mysql_query("Select * from $this->tablename where email='$email'",$this->connection);  

        if(!$result || mysql_num_rows($result) <= 0)
        {
            $this->HandleError("There is no user with email: $email");
            return false;
        }
        $user_rec = mysql_fetch_assoc($result);

        
        return true;
    }
    
    function SendUserWelcomeEmail(&$user_rec)
    {
        $mailer = new PHPMailer();
        
        $mailer->CharSet = 'utf-8';
        
        $mailer->AddAddress($user_rec['email'],$user_rec['name']);
        
        $mailer->Subject = "Welcome to ".$this->sitename;

        $mailer->From = $this->GetFromAddress();        
        
        $mailer->Body ="Hello ".$user_rec['name']."\r\n\r\n".
        "Welcome! Your registration  with ".$this->sitename." is completed.\r\n".
        "\r\n".
        "Regards,\r\n".
        "Webmaster\r\n".
        $this->sitename;

        if(!$mailer->Send())
        {
            $this->HandleError("Failed sending user welcome email.");
            return false;
        }
        return true;
    }
    
    function SendAdminIntimationOnRegComplete(&$user_rec)
    {
        if(empty($this->admin_email))
        {
            return false;
        }
        $mailer = new PHPMailer();
        
        $mailer->CharSet = 'utf-8';
        
        $mailer->AddAddress($this->admin_email);
        
        $mailer->Subject = "Registration Completed: ".$user_rec['name'];

        $mailer->From = $this->GetFromAddress();         
        
        $mailer->Body ="A new user registered at ".$this->sitename."\r\n".
        "Name: ".$user_rec['name']."\r\n".
        "Email address: ".$user_rec['email']."\r\n";
        
        if(!$mailer->Send())
        {
            return false;
        }
        return true;
    }
    
    function GetResetPasswordCode($email)
    {
       return substr(md5($email.$this->sitename.$this->rand_key),0,10);
    }
    
    function SendResetPasswordLink($user_rec)
    {
        $email = $user_rec['email'];
        
        $mailer = new PHPMailer();
        
        $mailer->CharSet = 'utf-8';
        
        $mailer->AddAddress($email,$user_rec['name']);
        
        $mailer->Subject = "Your reset password request at ".$this->sitename;

        $mailer->From = $this->GetFromAddress();
        
        $link = $this->GetAbsoluteURLFolder().
                '/resetpwd.php?email='.
                urlencode($email).'&code='.
                urlencode($this->GetResetPasswordCode($email));

        $mailer->Body ="Hello ".$user_rec['name']."\r\n\r\n".
        "There was a request to reset your password at ".$this->sitename."\r\n".
        "Please click the link below to complete the request: \r\n".$link."\r\n".
        "Regards,\r\n".
        "Webmaster\r\n".
        $this->sitename;
        
        if(!$mailer->Send())
        {
            return false;
        }
        return true;
    }
    
    function SendNewPassword($user_rec, $new_password)
    {
        $email = $user_rec['email'];
        
        $mailer = new PHPMailer();
        
        $mailer->CharSet = 'utf-8';
        
        $mailer->AddAddress($email,$user_rec['name']);
        
        $mailer->Subject = "Your new password for ".$this->sitename;

        $mailer->From = $this->GetFromAddress();
        
        $mailer->Body ="Hello ".$user_rec['name']."\r\n\r\n".
        "Your password is reset successfully. ".
        "Here is your updated login:\r\n".
        "username:".$user_rec['username']."\r\n".
        "password:$new_password\r\n".
        "\r\n".
        "Login here: ".$this->GetAbsoluteURLFolder()."/login.php\r\n".
        "\r\n".
        "Regards,\r\n".
        "Webmaster\r\n".
        $this->sitename;
        
        if(!$mailer->Send())
        {
            return false;
        }
        return true;
    }    
    
    function ValidateRegistrationSubmission()
    {
        //This is a hidden input field. Humans won't fill this field.
        if(!empty($_POST[$this->GetSpamTrapInputName()]) )
        {
            //The proper error is not given intentionally
            $this->HandleError("Automated submission prevention: case 2 failed");
            return false;
        }
        
        $validator = new FormValidator();
        $validator->addValidation("name","req","Please fill in Name");
        $validator->addValidation("email","email","The input for Email should be a valid email value");
        $validator->addValidation("email","req","Please fill in Email");
        $validator->addValidation("username","req","Please fill in UserName");
        $validator->addValidation("password","req","Please fill in Password");

        
        if(!$validator->ValidateForm())
        {
            $error='';
            $error_hash = $validator->GetErrors();
            foreach($error_hash as $inpname => $inp_err)
            {
                $error .= $inpname.':'.$inp_err."\n";
            }
            $this->HandleError($error);
            return false;
        }        
        return true;
    }
	
	function ourValidateRegistrationSubmission()
    { 
        $validator = new FormValidator();
        $validator->addValidation("userName","req","Please provide an username.");
		$validator->addValidation("userName","minlen=3", "Username must be at least 3 characters long.");
		$validator->addValidation("userName","maxlen=30", "Username must be at most 30 characters long.");
		
        $validator->addValidation("email","req","Please provide your email address.");
        $validator->addValidation("email","email","Please provide a valid email address.");
		
        $validator->addValidation("password","req","Please fill in Password");
		$validator->addValidation("repassword","req","Please confirm your password.");
		$validator->addValidation("repassword", "eqelmnt=password", "Passwords don't match.");
		$validator->addValidation("password", "minlen=5", "Password must be at least 5 characters long.");
        
        if(!$validator->ValidateForm())
        {
            $error='';
            $error_hash = $validator->GetErrors();
            foreach($error_hash as $inpname => $inp_err)
            {
                $error .= $inpname.':'.$inp_err."\n";
            }
            $this->HandleError($error);
            return false;
        }        
        return true;
    }
	
	//startsAt and stopsAt must be client-side validated
	function ValidateServerSubmission() {
		$formvars['name'] = $this->Sanitize($_POST['name']);
		$formvars['type'] = $this->Sanitize($_POST['type']);
		$formvars['capacity'] = $this->Sanitize($_POST['capacity']);
		$formvars['ip'] = $this->Sanitize($_POST['ip']);
		$formvars['port'] = $this->Sanitize($_POST['port']);
		$formvars['isOnline'] = $this->Sanitize($_POST['isOnline']);
		$formvars['isSchedule'] = $this->Sanitize($_POST['isSchedule']);
		$formvars['startsAt'] = $this->Sanitize($_POST['startsAt']);
		$formvars['stopsAt'] = $this->Sanitize($_POST['stopsAt']);
		/*
		if (isset($_POST['isSchedule'])) {
			$formvars['isSchedule'] = "on";
		}
		else {
			$formvars['isSchedule'] = "off";
		}
		if ($formvars['isSchedule'] == "on") {
			$formvars['startsAt'] = $this->Sanitize($_POST['startsAt']);
			$formvars['stopsAt'] = $this->Sanitize($_POST['stopsAt']);
		}
		*/
		
		if (!$this->IsValidServerType($formvars['type'])) {
			/*echo "<script>console.log('Invalid server type');</script>";*/
			return false;
		}
		$formvars['nUsers'] = $this->Sanitize($_POST['nUsers']);
		
		$validator = new FormValidator();
        $validator->addValidation("name","req","Please provide a server name.");
		$validator->addValidation("name","minlen=3", "Server name must be at least 3 characters long.");
		$validator->addValidation("name","maxlen=60", "Server name must be at most 60 characters long.");
		
        $validator->addValidation("capacity","req","Please provide a server capacity.");
        $validator->addValidation("capacity","num","Server capacity must be a number.");
		
        $validator->addValidation("ip","req","Please provide a host for the server.");
        $validator->addValidation("port","req","Please provide a port for the server.");
		$validator->addValidation("port", "num", "Port must be a number.");
		$validator->addValidation("port", "lessthan=65536", "Port must be between 1 and 65536.");
		$validator->addValidation("port", "greaterthan=0", "Port must be between 1 and 65536.");
		
		if ($formvars['isSchedule'] == 1) {
			$validator->addValidation("startsAt","req","Please provide a start time for the server.");
			$validator->addValidation("stopsAt","req","Please provide a finish time for the server.");
		}
		
        if(!$validator->ValidateForm())
        {
            $error='';
            $error_hash = $validator->GetErrors();
            foreach($error_hash as $inpname => $inp_err)
            {
                $error .= $inpname.':'.$inp_err."\n";
            }
            $this->HandleError($error);
			echo "<script>console.log('Fail in ValidateForm()');</script>";
            return false;
        }
		if (!$this->IsServerIPValid($formvars['ip'])) {
			return false;
		}
        return true;
	}
    
	function IsServerIPValid($ip) {
		if ($ip == "localhost")
			return true;
		if ($this->startsWith($ip, "http://"))
			return true;
		if ($this->startsWith($ip, "https://"))
			return true;
		if ($this->startsWith($ip, "tcp://"))
			return true;
		if ($this->startsWith($ip, "udp://"))
			return true;
		if ($this->startsWith($ip, "ip://"))
			return true;
		if ($this->startsWith($ip, "pop3://"))
			return true;
		if ($this->startsWith($ip, "smtp://"))
			return true;
		if ($this->startsWith($ip, "ftp://"))
			return true;
		if ($this->startsWith($ip, "ftps://"))
			return true;
		//echo "Yo";
		$total = 0;
		//echo $ip.";".strlen($ip).";";
		$ipLength = strlen($ip);
		for($i = 0; $i < 4; $i++) {
			$current = array();
			$j = 0;
			$currentNum = substr($ip, $total, 1);
			while ($currentNum != "." && $total < $ipLength) {
				if (!is_numeric($currentNum)) {
					/*echo "<script>console.log('Not a num!');</script>";*/
					/*echo "Not num: ".$currentNum;*/
					return $this->HandleError("Invalid IP/URL. Please check that you have typed it propelly.");
				}
				if ($j > 3) {
					/*echo "<script>console.log('Too big!');</script>";*/
					return $this->HandleError("Invalid IP/URL. Please check that you have typed it propelly.");
				}
				if ($j == 3) {
					if ($currentNum[0] > 2) {
						/*echo "<script>console.log('currentNum[0] > 2.');</script>";*/
						return $this->HandleError("Invalid IP/URL. Please check that you have typed it propelly.");
					}
					if ($currentNum[0] == 2 && $currentNum[1] > 5) {
						/*echo "<script>console.log('currentNum[0] > 2 && currentNum[1] > 5');</script>";*/
						return $this->HandleError("Invalid IP/URL. Please check that you have typed it propelly.");
					}
					if ($currentNum[0] == 2 && $currentNum[1] == 5 && $currentNum[2] > 5) {
						/*echo "<script>console.log('currentNum[0] > 2 && currentNum[1] == 5 && currentNum[2] > 5');</script>";*/
						return $this->HandleError("Invalid IP/URL. Please check that you have typed it propelly.");
					}
				}
				$j++;
				$total++;
				/*
				echo $currentNum;
				echo "(".$total.")";
				*/
				$currentNum = substr($ip, $total, 1);
			}
			$total++;
			/*
			echo $currentNum;
			echo "(".$total.")";
			*/
		}
		//echo $currentNum;
		if ($total - 1 != strlen($ip)) {
			/*echo "<script>console.log('Not equal');</script>";*/
			return false;
		}
		/*echo "<script>console.log('IP OK');</script>";*/
		return true;
	}
	
	function startsWith($string, $startString) {
		$length = strlen($startString);
		return (substr($string, 0, $length) === $startString);	
	}
	
    function CollectRegistrationSubmission(&$formvars)
    {
        $formvars['name'] = $this->Sanitize($_POST['name']);
        $formvars['email'] = $this->Sanitize($_POST['email']);
        $formvars['username'] = $this->Sanitize($_POST['username']);
        $formvars['password'] = $this->Sanitize($_POST['password']);
    }
	
	function ourCollectRegistrationSubmission(&$formvars)
    {
		//We don't need the confirm password field
        $formvars['email'] = $this->Sanitize($_POST['email']);
        $formvars['userName'] = $this->Sanitize($_POST['userName']);
        $formvars['password'] = $this->Sanitize($_POST['password']);
    }
    
	function CollectServerSubmission(&$formvars) {
		$formvars['name'] = $this->Sanitize($_POST['name']);
		$formvars['type'] = $this->Sanitize($_POST['type']);
		$formvars['capacity'] = $this->Sanitize($_POST['capacity']);
		$formvars['ip'] = $this->Sanitize($_POST['ip']);
		$formvars['port'] = $this->Sanitize($_POST['port']);
		$formvars['isOnline'] = $this->Sanitize($_POST['isOnline']);
		$formvars['isSchedule'] = $this->Sanitize($_POST['isSchedule']);
		$formvars['startsAt'] = $this->Sanitize($_POST['startsAt']);
		$formvars['stopsAt'] = $this->Sanitize($_POST['stopsAt']);
		$formvars['admins'] = $this->Sanitize($_POST['admins']);
		
		/*
		$formvars['isSchedule'] = '';
		if (isset($_POST['isSchedule']))
			$formvars['isSchedule'] = $this->Sanitize($_POST['isSchedule']);
		else
			$formvars['isSchedule'] = "off";
		if ($formvars['isSchedule'] == "on") {
			$formvars['startsAt'] = $this->Sanitize($_POST['startsAt']);
			$formvars['stopsAt'] = $this->Sanitize($_POST['stopsAt']);
		}
		*/
		if (!isset($_POST['lastActiveUser'])) {
			$formvars['lastActiveUser'] = "NULL";
		}
		else {
			$formvars['lastActiveUser'] = $this->Sanitize($_POST['lastActiveUser']);		
		}
		
		if (!isset($_POST['onlineSince'])) {
			$formvars['onlineSince'] = "NULL";	
		}
		else {
			$formvars['onlineSince'] = $this->Sanitize($_POST['onlineSince']);		
		}
		
	}
	
	function CollectServerModifySubmission(&$formvars) {
		$formvars['id_server'] = $this->Sanitize($_POST['id_server']);
		$formvars['name'] = $this->Sanitize($_POST['name']);
		$formvars['capacity'] = $this->Sanitize($_POST['capacity']);
		$formvars['ip'] = $this->Sanitize($_POST['ip']);
		$formvars['port'] = $this->Sanitize($_POST['port']);
		$formvars['isOnline'] = $this->Sanitize($_POST['isOnline']);
		$formvars['isSchedule'] = $this->Sanitize($_POST['isSchedule']);
		$formvars['startsAt'] = $this->Sanitize($_POST['startsAt']);
		$formvars['stopsAt'] = $this->Sanitize($_POST['stopsAt']);
		$formvars['admins'] = $this->Sanitize($_POST['admins']);
		$formvars['nUsers'] = $this->Sanitize($_POST['nUsers']);
		$formvars['turnOffOn'] = $this->Sanitize($_POST['turnOffOn']);
		//$formvars['isSchedule'] = '';
		/*
		if (isset($_POST['isSchedule']))
			$formvars['isSchedule'] = $this->Sanitize($_POST['isSchedule']);
		else
			$formvars['isSchedule'] = "off";
		if ($formvars['isSchedule'] == "on") {
			$formvars['startsAt'] = $this->Sanitize($_POST['startsAt']);
			$formvars['stopsAt'] = $this->Sanitize($_POST['stopsAt']);
		}
		*/
		if (!isset($_POST['lastActiveUser'])) {
			$formvars['lastActiveUser'] = "NULL";
		}
		else {
			$formvars['lastActiveUser'] = $this->Sanitize($_POST['lastActiveUser']);		
		}
		
		if (!isset($_POST['onlineSince'])) {
			$formvars['onlineSince'] = "NULL";	
		}
		else {
			$formvars['onlineSince'] = $this->Sanitize($_POST['onlineSince']);		
		}
	}
	
	function IsValidServerType($server_type) {
		if ($server_type != "WebServer" && $server_type != "Database")
			return false;
		return true;
	}
	
    function SendUserConfirmationEmail(&$formvars)
    {
        $mailer = new PHPMailer();
        
        $mailer->CharSet = 'utf-8';
        
        $mailer->AddAddress($formvars['email'],$formvars['name']);
        
        $mailer->Subject = "Your registration with ".$this->sitename;

        $mailer->From = $this->GetFromAddress();        
        
        $confirmcode = $formvars['confirmcode'];
        
        $confirm_url = $this->GetAbsoluteURLFolder().'/confirmreg.php?code='.$confirmcode;
        
        $mailer->Body ="Hello ".$formvars['name']."\r\n\r\n".
        "Thanks for your registration with ".$this->sitename."\r\n".
        "Please click the link below to confirm your registration.\r\n".
        "$confirm_url\r\n".
        "\r\n".
        "Regards,\r\n".
        "Webmaster\r\n".
        $this->sitename;

        if(!$mailer->Send())
        {
            $this->HandleError("Failed sending registration confirmation email.");
            return false;
        }
        return true;
    }
    function GetAbsoluteURLFolder()
    {
        $scriptFolder = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) ? 'https://' : 'http://';
        $scriptFolder .= $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']);
        return $scriptFolder;
    }
    
    function SendAdminIntimationEmail(&$formvars)
    {
        if(empty($this->admin_email))
        {
            return false;
        }
        $mailer = new PHPMailer();
        
        $mailer->CharSet = 'utf-8';
        
        $mailer->AddAddress($this->admin_email);
        
        $mailer->Subject = "New registration: ".$formvars['name'];

        $mailer->From = $this->GetFromAddress();         
        
        $mailer->Body ="A new user registered at ".$this->sitename."\r\n".
        "Name: ".$formvars['name']."\r\n".
        "Email address: ".$formvars['email']."\r\n".
        "UserName: ".$formvars['username'];
        
        if(!$mailer->Send())
        {
            return false;
        }
        return true;
    }
    
    function SaveToDatabase(&$formvars)
    {
        if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }
        if(!$this->Ensuretable())
        {
            return false;
        }
        if(!$this->IsFieldUnique($formvars,'email'))
        {
            $this->HandleError("This email is already registered");
            return false;
        }
        
        if(!$this->IsFieldUnique($formvars,'username'))
        {
            $this->HandleError("This UserName is already used. Please try another username");
            return false;
        }        
        if(!$this->InsertIntoDB($formvars))
        {
            $this->HandleError("Inserting to Database failed!");
            return false;
        }
        return true;
    }
	
	function AddUserToDatabase(&$formvars) {
		if(!$this->DBLogin()) {
            $this->HandleError("Database login failed!");
			echo "<script>console.log('Failed to login to database');</script>";
            return false;
        }
		if(!$this->ConfirmUserRegistTable()) {
			echo "<script>console.log('Failed to confirm user table');</script>";
			return false;
		}
		if(!$this->OurIsFieldUnique($formvars,'email', $this->users_table_name)) {
            $this->HandleError("This email is already registered");
			echo "<script>console.log('This email already exists');</script>";
            return false;
        }
		if(!$this->InsertIntoUserDB($formvars)) {
			$this->HandleError("Inserting into user's table failed!");
			echo "<script>console.log('Failed to insert user in database');</script>";
			return false;
		}
		return true;
	}
	
	function AddServerToDatabase(&$formvars) {
		if(!$this->DBLogin()) {
            $this->HandleError("Database login failed!");
			echo "<script>console.log('Failed to login to database');</script>";
            return false;
        }
		if(!$this->CheckLogin())
        {
            $this->HandleError("Not logged in!");
			echo "<script>console.log('User not logged in');</script>";
            return false;
        }
		if(!$this->ConfirmServerRegistTable()) {
			echo "<script>console.log('Failed to confirm server table');</script>";
			return false;
		}
		if(!$this->ConfirmUserServerRegistTable()) {
			echo "<script>console.log('Failed to confirm user's server table');</script>";
			return false;
		}
		if(!$this->ConfirmInvitesRegistTable()) {
			echo "<script>console.log('Failed to confirm invites table');</script>";
			return false;
		}
		if(!$this->ConfirmRequestsRegistTable()) {
			echo "<script>console.log('Failed to confirm requests table');</script>";
			return false;
		}
		$new_server = true;
		if(!$this->isPairOfFieldsUnique($formvars, "ip", "port", "id_server", $this->servers_table_name)) {
			/*echo "<script>console.log('Failed to add server - a server with that IP and port already exist');</script>";
			return false;
			*/
			$user_has_server = $this->userHasServer($formvars, $_SESSION['email'], $formvars['ip'], $formvars['port']);
			if ($user_has_server > 0) {
				echo "Detected that already has server.";
				return false;
			}
			$new_server = false;
		}
		if ($new_server) {
			if(!$this->InsertIntoServerDB($formvars)) {
				$this->HandleError("Inserting into server's table failed!");
				/*echo "<script>console.log('Failed to insert server in database');</script>";*/
				return false;
			}
		}
		if(!$this->InsertIntoUserServerDB($_SESSION['email'], $formvars['ip'], $formvars['port'])) {
			$this->HandleError("Inserting into user's server table failed!");
			echo "<script>console.log('Failed to insert user server in database');</script>";
			return false;
		}
		$formvars['admins'] = json_decode($formvars['admins']);
		foreach ($formvars['admins'] as $id) {
			$this->InsertIntoUserServerDB($id, $formvars['ip'], $formvars['port']);
		}
		$server_id = $this->getServerIdFromIPPort($formvars['ip'], $formvars['port']);
		//don't delete this one, it's really needed.
		echo $server_id;
		if (!$new_server) {
			$server_id = $this->getServerIdFromIPPort($formvars['ip'], $formvars['port']);
			return false;
		}
		return true;
	}
    
    function IsFieldUnique($formvars,$fieldname)
    {
        $field_val = $this->SanitizeForSQL($formvars[$fieldname]);
        $qry = "select username from $this->tablename where $fieldname='".$field_val."'";
        $result = mysql_query($qry,$this->connection);   
        if($result && mysql_num_rows($result) > 0)
        {
            return false;
        }
        return true;
    }
	
	function OurIsFieldUnique($formvars, $fieldname, $tablename) {
		$field_val = $this->SanitizeForSQL($formvars[$fieldname]);
        $qry = "select username from $tablename where $fieldname='".$field_val."'";
        $result = mysql_query($qry,$this->connection);   
        if($result && mysql_num_rows($result) > 0)
        {
            return false;
        }
        return true;
	}
	
	function isPairOfFieldsUnique($formvars, $first_field_name, $second_field_name, $select_field_name, $tablename) {
		$first_field_value = $this->SanitizeForSQL($formvars[$first_field_name]);
		$second_field_value = $this->SanitizeForSQL($formvars[$second_field_name]);
		$qry = "select $select_field_name from $tablename 
		where $first_field_name='".$first_field_value."' AND $second_field_name='".$second_field_value."'";
		$result = mysql_query($qry, $this->connection);
		if ($result && mysql_num_rows($result) > 0) {
			return false;
		}
		return true;
	}
	
	function userHasServer($formvars, $email, $server_ip, $server_port) {
		$user_id = $this->getUserIdFromMail($email);
		if ($user_id == false) {
			return true;
		}
		$server_id = $this->getServerIdFromIPPort($server_ip, $server_port);
		if ($server_id == false) {
			return true;
		}
		$qry = "select id_server from $this->users_servers_table_name where id_server = ".$server_id." and id_user = ".$user_id."";
		$result = mysql_query($qry, $this->connection);
		if ($result && mysql_num_rows($result) > 0) {
			return true;	
		}
		return false;
	}
    
	function isTripleOfFieldsUnique($formvars, $first_field_name, $second_field_name, $third_field_name, $select_field_name, $tablename) {
		$first_field_value = $this->SanitizeForSQL($formvars[$first_field_name]);
		$second_field_value = $this->SanitizeForSQL($formvars[$second_field_name]);
		$third_field_value = $this->SanitizeForSQL($formvars[$third_field_name]);
		$qry = "select $select_field_name from $tablename where $first_field_name='".$first_field_value."' 
		AND $second_field_name='".$second_field_value."' AND $third_field_name='".$thid_field_value."'";
		$result = mysql_query($qry, $this->connection);
		if ($result && mysql_num_rows($result) > 0) {
			return false;
		}
		return true;
	}
	
	function ModifyServerAdmins($formvars) {
		$id_server = $formvars['id_server'];
		$server_owner_email = $_SESSION['email'];
		//echo $id_server;
		$old_admins_ids_qry = "SELECT id_user FROM $this->users_servers_table_name WHERE id_server = ".$id_server."";
		$old_admins_ids_result = mysql_query($old_admins_ids_qry, $this->connection);
		$old_admins = array();
		$i = 0;
		while($row = mysql_fetch_assoc($old_admins_ids_result)) {
			//echo "old admin ID: ".$row['id_user']."; ";
			$id = $row['id_user'];
			$old_admins_email_qry = "SELECT email FROM $this->users_table_name WHERE id_user = ".$id."";
			$old_admins_email_result = mysql_query($old_admins_email_qry, $this->connection);
			while ($row = mysql_fetch_assoc($old_admins_email_result)) {
				//echo "old admin email: ".$row['email']."; ";
				$old_admins[$i] = $row['email'];
				$i++;
			}
		}
		$i = 0;
		
		//Now we have emails on both.
		$new_admins = json_decode($formvars['admins']);
		foreach ($old_admins as $rem_admin) {
			//echo "Old admin: ".$rem_admin."; ";
			$admin_survives = array_search($rem_admin, $new_admins);
			if ($admin_survives === false) {
				if ($server_owner_email != $rem_admin) {
					$user_id = $this->getUserIdFromMail($rem_admin);
					$rem_qry = "DELETE FROM $this->users_servers_table_name WHERE id_server = ".$id_server." AND id_user = ".$user_id;
					mysql_query($rem_qry, $this->connection);
				}
			}
		}
		foreach ($new_admins as $add_admin) {
			//echo "New admin: ".$add_admin."; ";
			$new_admin = array_search($add_admin, $old_admins);
			//echo $new_admin."; ";
			//In this case, it is a new admin (in other words, it failed to find new admin in old admins)
			if ($new_admin === false) {
				$user_id = $this->getUserIdFromMail($add_admin);
				$add_qry  = 'insert into '.$this->users_servers_table_name.'(
                id_user,
                id_server
                )
                values
                (
                "' . $this->SanitizeForSQL($user_id) . '",
                "' . $this->SanitizeForSQL($id_server) . '"
                )';
				$result = mysql_query( $add_qry ,$this->connection);
				//echo $result;
			}
		}
		return true;
	}
	
	function GetServerAdmins($id_server) {
		$qry = "select id_user	from $this->users_servers_table_name where id_server=".$id_server."";
		$result = mysql_query($qry, $this->connection);
		if (!$result || mysql_num_rows($result) <= 0) {
			return false;	
		}
		$returnArray = array();
		$i = 0;
		while($row = mysql_fetch_assoc($result)){
     		$returnArray[$i] = $row['id_user'];
			$i++;
		}
		return $returnArray;
	}
	
	function GetServerInfoFromDatabaseById($id_server) {
		$qry = "select * from $this->servers_table_name where id_server = '".$id_server."'";
		$result = mysql_query($qry, $this->connection);
		$row = mysql_fetch_assoc($result);
		if (!$result || mysql_num_rows($result) <= 0) {
			return false;
		}
		return $row;
	}
	
	function GetUsersFromDatabase($email) {
		$qry = "select * from $this->users_table_name where email != '".$email."'";
		$result = mysql_query($qry, $this->connection);
		$returnArray = array();
		$i = 0;
		while ($row = mysql_fetch_assoc($result)) {
			$returnArray[$i] = $row['username'];
			$returnArray[$i + 1] = $row['email'];
			$returnArray[$i + 2] = $row['id_user'];
			$i += 3;
		}
		return $returnArray;
	}
	
	function GetServerInfoFromDatabase($ip, $port) {
		$qry = "select * from $this->servers_table_name where ip = '".$ip."' and port = '".$port."'";
		$result = mysql_query($qry, $this->connection);
		$row = mysql_fetch_assoc($result);
		/*
        $user_rec['name'] = $row['name'];
        $user_rec['email']= $row['email'];
		*/
		/*
		foreach ($row as $value) {
			echo $value." ";	
		}
		*/
		if (!$result || mysql_num_rows($result) <= 0) {
			return false;
		}
		return $row;
	}
	
	function GetUserServerListFromDatabase() {
		$email = $_SESSION['email'];
		$result = array();
		$user_id = $this->getUserIdFromMail($email);
		//echo $user_id;
		if (!$user_id) {
			return false;
		}
		$this->users_servers_table_name;
		$serverList = mysql_query("select id_server from $this->users_servers_table_name where id_user = '".$user_id."'",$this->connection);
		if(!$serverList || mysql_num_rows($serverList) <= 0)
        {
			return "No servers";
        }
		$serverListResult = '';
		while($row = mysql_fetch_assoc($serverList)){
     		$serverListResult[] = $row['id_server'];
		}
		$result = array();
		foreach ($serverListResult as $key => $value) {
			$currentServer = $this->GetServerInfoFromDatabaseById($value);
			//echo $key.": ".$value;
			$result[$key] = $currentServer;
		}
		return $result;
	}
	
	function GetAllUsersFromDatabase($email) {
		//echo $email;
		$qry = "select * from $this->users_table_name where email != ".$email."";
		//echo $qry;
		//echo $this->connection;
		$result = mysql_query($qry, $this->connection);
		$returnArray = array();
		while ($row = mysql_fetch_assoc($result)) {
			$tmp = array();
			$tmp['username'] = $row['username'];
			$tmp['email'] = $row['email'];
			$returnArray[] = $tmp;
		}
		return $returnArray;
	}
	
	//Ignores lastActiveUser. Sets onlineSince if it's not null
	function ModifyServerPropertyToDatabase($formvars) {
		$id_server = $formvars["id_server"];
		$qry = "select * from $this->servers_table_name where id_server = '".$id_server."'";
		$result = mysql_query($qry, $this->connection);
		$row = mysql_fetch_assoc($result);
		if (!$result || mysql_num_rows($result) <= 0) {
			return false;
		}
		
		$id_server = $this->SanitizeForSQL($formvars['id_server']);
		$name = $this->SanitizeForSQL($formvars['name']);
		$nUsers = $this->SanitizeForSQL($formvars['nUsers']);
		$capacity = $this->SanitizeForSQL($formvars['capacity']);
		$ip = $this->SanitizeForSQL($formvars['ip']);
		$port = $this->SanitizeForSQL($formvars['port']);
		$isOnline = $this->SanitizeForSQL($formvars['isOnline']);
		$isSchedule = $this->SanitizeForSQL($formvars['isSchedule']);
		$startsAt = $this->SanitizeForSQL($formvars['startsAt']);
		$stopsAt = $this->SanitizeForSQL($formvars['stopsAt']);
		$turnOffOn = $this->SanitizeForSQL($formvars['turnOffOn']);
		//echo "turnOffOn: ".$turnOffOn."; ";
		
		if (($turnOffOn == 0 || $turnOffOn == "0") && $nUsers >= 0) {
			//echo "Not turning on server.";
			$qry = "UPDATE $this->servers_table_name SET isOnline = '$isOnline'".
			", name = '$name'".
			", nUsers = '$nUsers'".
			", capacity = '$capacity'".
			", ip = '$ip'".
			", port = '$port'".
			", isSchedule = '$isSchedule'".
			", startsAt = '$startsAt'".
			", stopsAt = '$stopsAt'".
			" WHERE id_server = ".$id_server."";
		}
		else {
			//echo "Turning on server... theorically";
			$onlineSince = $this->SanitizeForSQL($formvars['onlineSince']);
			$lastActiveUser = $this->SanitizeForSQL($formvars['lastActiveUser']);
			$nUsers = 0;
			$qry = "UPDATE $this->servers_table_name SET isOnline = '$isOnline'".
			", name = '$name'".
			", nUsers = '$nUsers'".
			", capacity = '$capacity'".
			", ip = '$ip'".
			", port = '$port'".
			", isSchedule = '$isSchedule'".
			", startsAt = '$startsAt'".
			", stopsAt = '$stopsAt'".
			", onlineSince = '$onlineSince'".
			", lastActiveUser = '$lastActiveUser'".
			" WHERE id_server = ".$id_server."";
		}
		$result = mysql_query($qry,$this->connection);
		if (!$result) {
			echo "modifyServerProperties query failed!";
			return false;	
		}
		return true;
	}
	
	function ModifyServerClientsToDatabase($id_server, $n_clients, $lastActiveUser) {
		$qry = "select * from $this->servers_table_name where id_server = '".$id_server."'";
		$result = mysql_query($qry, $this->connection);
		$row = mysql_fetch_assoc($result);
		if (!$result || mysql_num_rows($result) <= 0) {
			return false;
		}
		/*
		foreach ($row as $key=> $value) {
			echo $key.": ".$value;	
		}
		*/
		$qry;
		if ($n_clients >  0) {
			$qry = "UPDATE $this->servers_table_name SET nUsers = ".$this->SanitizeForSQL($n_clients)
		.", lastActiveUser = '$lastActiveUser'"
		." WHERE id_server = ".$id_server."";
		}
		else {
			$qry = "UPDATE $this->servers_table_name SET nUsers = ".$this->SanitizeForSQL($n_clients)
		." WHERE id_server = ".$id_server."";
		}
		$result = mysql_query($qry,$this->connection);
		if (!$result) {
			return false;	
		}
		return true;
	}
	
	function ModifyServerOnlineToDatabase($id_server, $isOnline, $onlineSince, $lastActiveUser) {
		$qry = "select * from $this->servers_table_name where id_server = '".$id_server."'";
		$result = mysql_query($qry, $this->connection);
		$row = mysql_fetch_assoc($result);
		if (!$result || mysql_num_rows($result) <= 0) {
			return false;
		}
		if ($isOnline == 0) {
			$qry = "UPDATE $this->servers_table_name SET isOnline = ".$this->SanitizeForSQL($isOnline).
			", nUsers = 0 ".
			", onlineSince = ".$onlineSince.
			", lastActiveUser = ".$lastActiveUser.
			" WHERE id_server = ".$id_server."";
		}
		else {
			$qry = "UPDATE $this->servers_table_name SET isOnline = ".$this->SanitizeForSQL($isOnline).
			", onlineSince = ".$onlineSince.
			", lastActiveUser = ".$lastActiveUser.
			" WHERE id_server = ".$id_server."";
		}
		$result = mysql_query($qry,$this->connection);
		if (!$result) {
			/*echo "<script>console.log('Failed to modify server online status');</script>";*/
			return false;
		}
		return true;
	}
	
	function DeleteServerFromDatabase($id_server, $email) {
		$user_id = $this->getUserIdFromMail($email);
		$qry_server_user = "DELETE FROM $this->users_servers_table_name WHERE id_server = ".$id_server." and id_user = ".$user_id."";
		mysql_query($qry_server_user, $this->connection);
		//Check if no one else has the server
		$qry_more_users = "select id_user from $this->users_servers_table_name WHERE id_server = '".$id_server."'";
		$hasMore = mysql_query($qry_more_users, $this->connection);
		//No need to delete server from the server table, since other clients still have it.
		if ($hasMore && mysql_num_rows($hasMore) >= 1) {
			return true;
		}
		$qry_server = "DELETE FROM $this->servers_table_name WHERE id_server = ".$id_server."";
		mysql_query($qry_server, $this->connection);
		return true;
	}
	
	function DeleteServerForEveryoneFromDatabase($id_server) {
		$qry_users = "SELECT id_user FROM $this->users_servers_table_name WHERE id_server = ".$id_server."";
		$users = mysql_query($qry_users, $this->connection);
		if (!$users || mysql_num_rows($users) <= 0) {
			return true;	
		}
		while ($row = mysql_fetch_assoc($users)) {
			$user = $row['id_user'];
			$qry_delete_from_user = "DELETE FROM $this->users_servers_table_name WHERE id_server = ".$id_server."";
			mysql_query($qry_delete_from_user, $this->connection);
		}
		$qry_delete_server = "DELETE FROM $this->servers_table_name WHERE id_server = ".$id_server."";
		mysql_query($qry_delete_server, $this->connection);
		return true;
	}
	
    function DBLogin()
    {

        $this->connection = mysql_connect($this->db_host,$this->username,$this->pwd);

        if(!$this->connection)
        {   
            $this->HandleDBError("Database Login failed! Please make sure that the DB login credentials provided are correct");
            return false;
        }
        if(!mysql_select_db($this->database, $this->connection))
        {
            $this->HandleDBError('Failed to select database: '.$this->database.' Please make sure that the database name provided is correct');
            return false;
        }
        if(!mysql_query("SET NAMES 'UTF8'",$this->connection))
        {
            $this->HandleDBError('Error setting utf8 encoding');
            return false;
        }
        return true;
    }    
    
    function Ensuretable()
    {
        $result = mysql_query("SHOW COLUMNS FROM $this->tablename");   
        if(!$result || mysql_num_rows($result) <= 0)
        {
            return $this->CreateTable();
        }
        return true;
    }
	
	function ConfirmUserRegistTable() {
		$result = mysql_query("SHOW COLUMNS FROM $this->users_table_name");
        if(!$result || mysql_num_rows($result) <= 0)
        {
            return $this->CreateUserTable();
        }
        return true;
	}
	
	
	function ConfirmServerRegistTable() {
		
		$result = mysql_query("SHOW COLUMNS FROM $this->servers_table_name");   
        if(!$result || mysql_num_rows($result) <= 0)
        {
            return $this->CreateServerTable();
        }
        return true;
	}
	
	function ConfirmUserServerRegistTable() {
		$result = mysql_query("SHOW COLUMNS FROM $this->users_servers_table_name");   
        if(!$result || mysql_num_rows($result) <= 0)
        {
            return $this->CreateUserServerTable();
        }
        return true;
	}
	
	function ConfirmInvitesRegistTable() {
		$result = mysql_query("SHOW COLUMNS FROM $this->invites_table_name");   
        if(!$result || mysql_num_rows($result) <= 0)
        {
            return $this->CreateInvitesTable();
        }
        return true;
	}
	
	function ConfirmRequestsRegistTable() {
		$result = mysql_query("SHOW COLUMNS FROM $this->requests_table_name");   
        if(!$result || mysql_num_rows($result) <= 0)
        {
            return $this->CreateRequestsTable();
        }
        return true;
	}
    
    function CreateTable()
    {
		$qry = "Create Table $this->tablename (".
                "id_user INT NOT NULL AUTO_INCREMENT ,".
                "name VARCHAR( 128 ) NOT NULL ,".
                "email VARCHAR( 64 ) NOT NULL ,".
                "username VARCHAR( 16 ) NOT NULL ,".
                "password VARCHAR( 32 ) NOT NULL ,".
                "confirmcode VARCHAR(32) ,".
                "PRIMARY KEY ( id_user )".
                ")";
				
		if(!mysql_query($qry,$this->connection))
        {
            $this->HandleDBError("Error creating the table \nquery was\n $qry");
            return false;
        }
        return true;
	}
	
	function CreateUserTable() {
		$qry = "Create Table $this->users_table_name (".
				"id_user INT NOT NULL AUTO_INCREMENT ,".
                "email VARCHAR( 64 ) NOT NULL ,".
                "username VARCHAR( 32 ) NOT NULL ,".
                "password VARCHAR( 64 ) NOT NULL ,".
				"PRIMARY KEY ( id_user )".
                ")";
                
        if(!mysql_query($qry,$this->connection))
        {
            $this->HandleDBError("Error creating the user table \nquery was\n $qry");
            return false;
        }
        return true;
	}
	
	function CreateServerTable() {
		$qry = "Create Table $this->servers_table_name (".
				"id_server INT NOT NULL AUTO_INCREMENT ,".
                "name VARCHAR( 64 ) NOT NULL ,".
                "type VARCHAR( 32 ) NOT NULL ,".
				"nUsers INT NOT NULL ,".
				"capacity INT NOT NULL ,".
				"ip VARCHAR( 128 ) NOT NULL ,".
				"port VARCHAR ( 8 ) NOT NULL ,".
				"isOnline BOOL NOT NULL ,".
				"isSchedule BOOL NOT NULL ,".
				"startsAt VARCHAR (256) ,".
				"stopsAt VARCHAR (256) ,".
				"lastActiveUser VARCHAR (256) ,".
				"onlineSince VARCHAR (256) ,".
				"PRIMARY KEY ( id_server )".
                ")";
				
		if(!mysql_query($qry,$this->connection))
        {
            $this->HandleDBError("Error creating the server table \nquery was\n $qry");
            return false;
        }
        return true;	
	}
	
	function CreateUserServerTable() {
		$qry = "Create Table $this->users_servers_table_name (".
				"id_user INT NOT NULL ,".
				"id_server INT NOT NULL ,".
				"FOREIGN KEY (id_user) REFERENCES $this->users_table_name(id_user) ,".
				"FOREIGN KEY (id_server) REFERENCES $this->servers_table_name(id_server) ,".
				"PRIMARY KEY ( id_user, id_server )".
                ")";
				
		if(!mysql_query($qry,$this->connection))
        {
            $this->HandleDBError("Error creating the user's server table \nquery was\n $qry");
            return false;
        }
        return true;
	}
	
	function CreateInvitesTable() {
		$qry = "Create Table $this->invites_table_name (".
				"id_inviteUser INT NOT NULL ,".
				"id_targetUser INT NOT NULL ,".
				"id_server INT NOT NULL ,".
				"FOREIGN KEY (id_inviteUser) REFERENCES $this->users_table_name(id_user) ,".
				"FOREIGN KEY (id_targetUser) REFERENCES $this->users_table_name(id_user) ,".
				"FOREIGN KEY (id_server) REFERENCES $this->servers_table_name(id_server) ,".
				"PRIMARY KEY ( id_inviteUser, id_targetUser, id_server )".
                ")";
				
		if(!mysql_query($qry,$this->connection))
        {
            $this->HandleDBError("Error creating the user's invite table \nquery was\n $qry");
            return false;
        }
        return true;
	}
	
	function CreateRequestsTable() {
		$qry = "Create Table $this->requests_table_name (".
				"id_requestUser INT NOT NULL ,".
				"id_targetUser INT NOT NULL ,".
				"id_server INT NOT NULL ,".
				"FOREIGN KEY (id_requestUser) REFERENCES $this->users_table_name(id_user) ,".
				"FOREIGN KEY (id_targetUser) REFERENCES $this->users_table_name(id_user) ,".
				"FOREIGN KEY (id_server) REFERENCES $this->servers_table_name(id_server) ,".
				"PRIMARY KEY ( id_requestUser, id_targetUser, id_server )".
                ")";
				
		if(!mysql_query($qry,$this->connection))
        {
            $this->HandleDBError("Error creating the user's request table \nquery was\n $qry");
            return false;
        }
        return true;
	}
    
    function InsertIntoDB(&$formvars)
    {
    
        $confirmcode = $this->MakeConfirmationMd5($formvars['email']);
        
        $formvars['confirmcode'] = $confirmcode;
        
        $insert_query = 'insert into '.$this->tablename.'(
                name,
                email,
                username,
                password,
                confirmcode
                )
                values
                (
                "' . $this->SanitizeForSQL($formvars['name']) . '",
                "' . $this->SanitizeForSQL($formvars['email']) . '",
                "' . $this->SanitizeForSQL($formvars['username']) . '",
                "' . md5($formvars['password']) . '",
                "' . $confirmcode . '"
                )';      
        if(!mysql_query( $insert_query ,$this->connection))
        {
            $this->HandleDBError("Error inserting data to the table\nquery:$insert_query");
            return false;
        }        
        return true;
    }
	
	function InsertIntoUserDB(&$formvars) {
		$insert_query = 'insert into '.$this->users_table_name.'(
                email,
                username,
                password
                )
                values
                (
                "' . $this->SanitizeForSQL($formvars['email']) . '",
                "' . $this->SanitizeForSQL($formvars['userName']) . '",
                "' . md5($formvars['password']) . '"
                )';      
        if(!mysql_query( $insert_query ,$this->connection))
        {
            $this->HandleDBError("Error inserting data to the user table\nquery:$insert_query");
			echo "<script>console.log('Error in mysql_query (user table)');</script>";
            return false;
        }        
        return true;	
	}
	
	function InsertIntoServerDB(&$formvars) {
		$insert_query;
		/*if ($formvars['isSchedule'] == 1 || $formvars['isSchedule'] == "1") {*/
			$insert_query = 'insert into '.$this->servers_table_name.'(
				name,
				type,
				nUsers,
				capacity,
				ip,
				port,
				isOnline,
				isSchedule,
				startsAt,
				stopsAt,
				lastActiveUser,
				onlineSince
				)
				values
				(
                "' . $this->SanitizeForSQL($formvars['name']) . '",
                "' . $this->SanitizeForSQL($formvars['type']) . '",
                "' . 0 . '",
                "' . $this->SanitizeForSQL($formvars['capacity']) . '",
                "' . $this->SanitizeForSQL($formvars['ip']) . '",
                "' . $this->SanitizeForSQL($formvars['port']) . '",
                "' . $this->SanitizeForSQL($formvars['isOnline']) . '",
                "' . $this->SanitizeForSQL($formvars['isSchedule']) . '",
                "' . $this->SanitizeForSQL($formvars['startsAt']) . '",
                "' . $this->SanitizeForSQL($formvars['stopsAt']) . '",
				"' . $this->SanitizeForSQL($formvars['lastActiveUser']) . '",
				"' . $this->SanitizeForSQL($formvars['onlineSince']) . '"
				)';
		/*}*/
		/*else {
			$formvars['isSchedule'] = 0;
			$insert_query = 'insert into '.$this->servers_table_name.'(
				name,
				type,
				nUsers,
				capacity,
				ip,
				port,
				isOnline,
				isSchedule,
				lastActiveUser,
				onlineSince
				)
				values
				(
                "' . $this->SanitizeForSQL($formvars['name']) . '",
                "' . $this->SanitizeForSQL($formvars['type']) . '",
                "' . 0 . '",
                "' . $this->SanitizeForSQL($formvars['capacity']) . '",
                "' . $this->SanitizeForSQL($formvars['ip']) . '",
                "' . $this->SanitizeForSQL($formvars['port']) . '",
                "' . $this->SanitizeForSQL($formvars['isOnline']) . '",
                "' . $this->SanitizeForSQL($formvars['isSchedule']) . '",
				"' . $this->SanitizeForSQL($formvars['lastActiveUser']) . '",
				"' . $this->SanitizeForSQL($formvars['onlineSince']) . '"
				)';
		}
		*/
        if(!mysql_query( $insert_query ,$this->connection))
        {
            $this->HandleDBError("Error inserting data to the server table\nquery:$insert_query");
			echo "<script>console.log('Error in mysql_query (server table)');</script>";
            return false;
        }        
        return true;	
	}
	
	function InsertIntoUserServerDB($email, $server_ip, $server_port) {
		$user_id = $this->getUserIdFromMail($email);
		if (!$user_id) {
			return false;
		}
		$server_id = $this->getServerIdFromIPPort($server_ip, $server_port);
		if (!$server_id) {
			return false;	
		}
		
		$insert_query = 'insert into '.$this->users_servers_table_name.'(
                id_user,
                id_server
                )
                values
                (
                "' . $this->SanitizeForSQL($user_id) . '",
                "' . $this->SanitizeForSQL($server_id) . '"
                )';
				   
        if(!mysql_query( $insert_query ,$this->connection))
        {
            $this->HandleDBError("Error inserting data to the user table\nquery:$insert_query");
			echo "<script>console.log('Error in mysql_query (user table)');</script>";
            return false;
        }        
        return true;
	}
	
	function getUserIdFromMail($email) {
		$user_query = "Select id_user from $this->users_table_name where email='$email'";
		$user_result = mysql_query($user_query, $this->connection);
		if(!$user_result || mysql_num_rows($user_result) <= 0) {
			$this->HandleError("Error in finding user with that email.");
			return false;
		}
		$row = mysql_fetch_assoc($user_result);
		return $row['id_user'];
	}
	
	function getServerIdFromIPPort($ip, $port) {
		$server_query = "Select id_server from $this->servers_table_name where ip='$ip' AND port='$port'";
		$server_result = mysql_query($server_query, $this->connection);
		if(!$server_result || mysql_num_rows($server_result) <= 0) {
			$this->HandleError("Error in finding server during server creation.");
			return false;
		}
		$row = mysql_fetch_assoc($server_result);
		return $row['id_server'];
	}
	
    function MakeConfirmationMd5($email)
    {
        $randno1 = rand();
        $randno2 = rand();
        return md5($email.$this->rand_key.$randno1.''.$randno2);
    }
    function SanitizeForSQL($str)
    {
        if( function_exists( "mysql_real_escape_string" ) )
        {
              $ret_str = mysql_real_escape_string( $str );
        }
        else
        {
              $ret_str = addslashes( $str );
        }
        return $ret_str;
    }
    
 /*
    Sanitize() function removes any potential threat from the
    data submitted. Prevents email injections or any other hacker attempts.
    if $remove_nl is true, newline chracters are removed from the input.
    */
    function Sanitize($str,$remove_nl=true)
    {
        $str = $this->StripSlashes($str);

        if($remove_nl)
        {
            $injections = array('/(\n+)/i',
                '/(\r+)/i',
                '/(\t+)/i',
                '/(%0A+)/i',
                '/(%0D+)/i',
                '/(%08+)/i',
                '/(%09+)/i'
                );
            $str = preg_replace($injections,'',$str);
        }

        return $str;
    }    
    function StripSlashes($str)
    {
        if(get_magic_quotes_gpc())
        {
            $str = stripslashes($str);
        }
        return $str;
    }    
}
?>
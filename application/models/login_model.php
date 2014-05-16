<?php

	class LoginModel extends BaseModel {

		/*
		*	Constructor
		*/
		public function __construct() {
			parent::__construct(); // creates db connection
		}

		public function login() {

			if( (isset($_POST['email']) && !empty($_POST['email'])) &&
		 	   (isset($_POST['pass']) && !empty($_POST['pass'])))
			{
		
				$email=$_POST['email'];
				$password =$_POST['pass'];
				
				//sanitize input
				$email = $this->db->real_escape_string(trim($email));
				$password = $this->db->real_escape_string(trim($password));
				
				$query ="SELECT * FROM user WHERE email='$email'";
				$result = $this->db->query($query);
				
				if ($result !== false && $result->num_rows > 0) 
				{
					$row = $result->fetch_assoc();
					if(hash('sha256', $row['salt'].$password)==$row['password'])
					{
						Session::init();
						Session::set('logged_in', true);
						Session::set('u_id', $row['u_id']);
						Session::set('first_name', $row['first_name']);
						return true;
					}
					else 
					{
						return false;
					}
				}else{
					return false;
				}
			}
			else 
			{
				return false;
			}
		} // end login function

		public function createNewUser() {
			if (isset($_POST["first"]) && !empty($_POST["first"]) 
				&& isset($_POST["last"]) && !empty($_POST["last"])
				&& isset($_POST["email"]) && !empty($_POST["email"])
				&& isset($_POST["password"]) && !empty($_POST["password"]) 
				&& isset($_POST["password2"]) && !empty($_POST["password2"]))
			{
				$first = $_POST["first"];
				$last = $_POST["last"];
				$email = $_POST["email"];
				$password = $_POST["password"];
				$password2 = $_POST["password2"];
				
				if($password != $password2 || strlen($password) < 6 || !filter_var($email, FILTER_VALIDATE_EMAIL) ){
					return false;
				}else{
					//All good, save data
					
					//Salt the password
					define('SALT_LENGTH', 20);
					$salt='';
					$character = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
					for ($i=0; $i<20; $i++)
					{
						$salt.=$character[rand(0, (strlen($character)-1))];
					}
					
					$password = hash('sha256', $salt.$password);
					
					//sanitize input
					$first = $this->db->real_escape_string(trim($first));
					$last = $this->db->real_escape_string(trim($last));
					$email = $this->db->real_escape_string(trim($email));
					$password = $this->db->real_escape_string(trim($password));
					
					$query = "INSERT INTO user(first_name, last_name, email, password, salt) VALUES('$first', '$last', '$email', '$password', '$salt')";
					$result= $this->db->query($query);
					$last_id= $this->db->insert_id;
					
					$query2 = "INSERT INTO user_info(u_id,hometown,location, school, workplace, birthday, description) VALUES('$last_id','','','','','','')";
					$result2 = $this->db->query($query2);

					// check if row inserted or not
					if (($result && $result2) !== false) 
					{
						// successfully inserted into database
						Session::init();
						Session::set('logged_in', true);
						Session::set('u_id', $last_id);
						Session::set('first_name', $first);

						return true;
					} else {
						return false;
					}
				}
			}else{
				return false;
			}
		} // end createNewUser

	}// end login model
?>
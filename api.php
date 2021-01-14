<?php
	
	class Api extends Rest
	{
		public $dbConnect;
		public function __construct()
		{
			parent::__construct();
			$db=new DbConnect;
			$this->dbConnect=$db->connect();
		}
		public function generateToken()
		{
			//print_r($this->param) ;
			$email=$this->validateParameter('email',$this->param['email'],STRING);
			$pass=$this->validateParameter('pass',$this->param['pass'],STRING);
			$stmt=$this->dbConnect->prepare("SELECT * FROM users WHERE email=:email AND password=:pass");
			$stmt->bindParam(":email",$email);
			$stmt->bindParam(":pass",$pass);
			$stmt->execute();
			$user=$stmt->fetch(PDO::FETCH_ASSOC);
			if (!is_array($user)) {
				$this->throwError(INVALID_USER_PASS,"User Is Not Valid");
			}
			if ($user['actiive']==0) {
				$this->throwError(USER_NOT_ACTIVE,"User Is Not Active Please Contect To Admin");
			}
			$payLoad=[
				'iat'=>time(),
				'iss'=>'localhost',
				'exp'=>time()+(60),
				'userId'=>$user['id']
			];
			$token=JWT::encode($payLoad,SECRETE_KEY);
			$data=['token'=>$token];
			$this->returnResponse(SUCCESS_RESPONSE,$data);
		}
	}
?>
<?php
	session_start();
	include("../settings/connect_datebase.php");
	
	$login_encrypted = $_POST['login'];
	$password_encrypted = $_POST['password'];

	function decryptAES($encryptedData, $key){

		$data = base64_decode($encryptedData);

		if($data === false || strlen($data)<17){
			error_log('Invalid data');
			return false;
		}

		$iv = substr($data,0,16);
		 
		$encrypted = substr($data,16);

		$keyHash = md5($key);
		$keyBytes = hex2bin($keyHash);

		$decrypted = openssl_decrypt(
			$encrypted,
			'aes-128-cbc',
			$keyBytes,
			OPENSSL_RAW_DATA,
			$iv
		);

		return $decrypted;
	}


	$secretKey = "qazalskdjflksjdfks";

	$login = decryptAES($login_encrypted,$secretKey);
	$password = decryptAES($password_encrypted,$secretKey);
	
	// ищем пользователя
	$query_user = $mysqli->query("SELECT * FROM `users`");
	$id = -1;
	$newUser = [];

	while($user_read = $query_user->fetch_row()) {
		if($login == decryptAES($user_read[2],$secretKey)){
			$id = $user_read[0];
			$newUser = $user_read;
		}
	}
	
	if($id != -1){
		echo $id;
	} else {
		$mysqli->query("INSERT INTO `users`(`login`, `password`, `roll`) VALUES ('".$login_encrypted."', '".$password_encrypted."', 0)");
		
		$query_user = $mysqli->query("SELECT * FROM `users`;");
		while($user_read = $query_user->fetch_row()) {
		if($login == decryptAES($user_read[1],$secretKey)){
			$id = $user_read[0];
		}
	}
				$_SESSION['user'] = $id; // запоминаем пользователя
			echo $id;
	}
?>
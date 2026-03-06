<?
    session_start();
	include("../settings/connect_datebase.php");

    $IdUser = $_SESSION['user'];
    $Message_encrypted = $_POST["Message"];
    $IdPost_encrypted = $_POST["IdPost"];

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

	$Message = decryptAES($Message_encrypted,$secretKey);
    $IdPost = decryptAES($IdPost_encrypted,$secretKey);

    $mysqli->query("INSERT INTO `comments`(`IdUser`, `IdPost`, `Messages`) VALUES ({$IdUser}, {$IdPost}, '{$Message}');");
?>
<?php 
	session_start();

	$username = "";
	$email    = "";
	$errors = array(); 
	$_SESSION['success'] = "";

	$db = mysqli_connect('localhost', 'root', '', 'dip');

	if (isset($_POST['reg_user'])) {

		$username = mysqli_real_escape_string($db, $_POST['username']);
		$email = mysqli_real_escape_string($db, $_POST['email']);
		$password_1 = mysqli_real_escape_string($db, $_POST['password_1']);
		$password_2 = mysqli_real_escape_string($db, $_POST['password_2']);


		if (empty($username)) { array_push($errors, "Необходимо ввести логин"); }
		if (empty($email)) { array_push($errors, "Необходимо ввести email"); }
		if (empty($password_1)) { array_push($errors, "Необходимо ввести пароль"); }

		if ($password_1 != $password_2) {
			array_push($errors, "Пароли не совпадают");
		}


		if (count($errors) == 0) {
			$password = md5($password_1);
			$query = "INSERT INTO users (username, email, password, permission) 
					  VALUES('$username', '$email', '$password', 0)";
			mysqli_query($db, $query);
			header('location: auth.php');
		}

	}


	if (isset($_POST['login_user'])) {
		$username = mysqli_real_escape_string($db, $_POST['username']);
		$password = mysqli_real_escape_string($db, $_POST['password']);

		if (empty($username)) {
			array_push($errors, "Необходимо ввести логин");
		}
		if (empty($password)) {
			array_push($errors, "Необходимо ввести пароль");
		}

		if (count($errors) == 0) {
			$password = md5($password);
			$query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
			$results = mysqli_query($db, $query);

			if (mysqli_num_rows($results) == 1) {
				$_SESSION['username'] = $username;
				$_SESSION['success'] = "Вы авторизированы";
				$_SESSION['permission'] = mysqli_fetch_assoc($results)['permission'];
				header('location: index.php');
			}else {
				array_push($errors, "Неверный логин/пароль");
			}
		}
	}

?>
<?php
include('server.php');


if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION['username']);
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <script src="https://kit.fontawesome.com/eee35a830a.js" crossorigin="anonymous"></script>
	<title>Авторизация</title>
</head>
<body>
    <div class="w3-left">
        <div class="w3-bar w3-blue">
            <a href="/dip/index.php" class="w3-bar-item w3-button"><i class="fas fa-home"></i>Главная</a>
            <?php
            if (isset($_SESSION['permission'])) {
                if ($_SESSION['permission'] == '0') {
                    echo '<a href="" class="w3-bar-item w3-button w3-disabled"><i class="fas fa-download"></i>Загрузка документов и классификация</a>';
                    echo '<a href="" class="w3-bar-item w3-button w3-disabled"><i class="fas fa-cog"></i>Настройка классификатора</a>';
                }
                else {
                    echo '<a href="/dip/download.php" class="w3-bar-item w3-button"><i class="fas fa-download"></i>Загрузка документов и классификация</a>';
                    echo '<a href="/dip/cl_settings.php" class="w3-bar-item w3-button"><i class="fas fa-cog"></i>Настройка классификатора</a>';
                }
            }
            else {
                echo '<a href="" class="w3-bar-item w3-button w3-disabled"><i class="fas fa-download"></i>Загрузка документов и классификация</a>';
                echo '<a href="" class="w3-bar-item w3-button w3-disabled"><i class="fas fa-cog"></i>Настройка классификатора</a>';
            }
            ?>
        </div>
    </div>
    <div class="w3-right">
        <div class="w3-bar w3-blue">
            <?php  if (isset($_SESSION['username'])) : ?>
                <a class="w3-bar-item w3-button">Добро пожаловать <strong><?php echo $_SESSION['username']; ?></strong></a>
                <a href="index.php?logout='1'" class="w3-bar-item w3-button" style="color: red;">Выйти</a>
            <?php endif ?>
            <a href="/dip/auth.php" class="w3-bar-item w3-button"><i class="fas fa-sign-in-alt"></i>Авторизация</a>
            <a href="/dip/reg.php" class="w3-bar-item w3-button"><i class="fas fa-user-plus"></i>Регистрация</a>
        </div>
    </div>
    <div class="w3-container w3-blue">
        <h2><i class="fas fa-sign-in-alt"></i>Авторизация</h2>
    </div>
    <div class="w3-container">
        <?php if (isset($_SESSION['success'])) : ?>
            <div class="error success" >
                <h3>
                    <?php
                    echo $_SESSION['success'];
                    unset($_SESSION['success']);
                    ?>
                </h3>
            </div>
        <?php endif ?>

        <?php  if (isset($_SESSION['username'])) : ?>
            <p>Добро пожаловать <strong><?php echo $_SESSION['username']; ?></strong></p>
            <p> <a href="index.php?logout='1'" style="color: red;">Выйти</a> </p>
        <?php else : ?>
        <form class="w3-container" method="post" action="auth.php">

            <?php include('errors.php'); ?>

            <div class="input-group">
                <label class="w3-text-blue"><b>Логин</b></label>
                <input class="w3-input w3-border w3-light-grey" type="text" name="username"  style="width: 250px;">
            </div>
            <div class="input-group">
                <label class="w3-text-blue"><b>Пароль</b></label>
                <input class="w3-input w3-border w3-light-grey" type="password" name="password" style="width: 250px;">
            </div>
            <div class="input-group">
                <button class="w3-btn w3-blue-grey" type="submit" name="login_user">Войти</button>
            </div>
            <p>
                Еще не зарегистрированы? <a href="reg.php">Регистрация</a>
            </p>
        </form>
        <?php endif ?>
    </div>
</body>
</html>
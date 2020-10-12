<?php
session_start();

if (isset($_SESSION['permission'])) {
    if ($_SESSION['permission'] == '0') {
        header('location: index.php');
        die();
    }
}
else {
    header('location: index.php');
    session_destroy();
    die();
}

if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION['username']);
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <script src="https://kit.fontawesome.com/eee35a830a.js" crossorigin="anonymous"></script>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Загрузка документов и классификация</title>
</head>
<body>
    <div class="w3-left">
        <div class="w3-bar w3-blue">
            <a href="/dip/index.php" class="w3-bar-item w3-button"><i class="fas fa-home"></i>Главная</a>
            <a href="/dip/download.php" class="w3-bar-item w3-button"><i class="fas fa-download"></i>Загрузка документов и классификация</a>
            <a href="/dip/cl_settings.php" class="w3-bar-item w3-button"><i class="fas fa-cog"></i>Настройка классификатора</a>
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
        <h2><i class="fas fa-download"></i>Загрузка документов и классификация</h2>
    </div>
    <div class="w3-container">
        <form class="w3-container" id="comp" method="post" action="cl_results.php">
            <label class="w3-text-blue"><b>Введите название компании для загрузки судебных решений:</b></label>
            <input class="w3-input w3-border w3-light-grey" type="text" name="com_name" style="width: 545px;">

            <label class="w3-text-blue"><b>Введите дату документа с: (ввод в формате дд.мм.гггг)</b></label>
            <input class="w3-input w3-border w3-light-grey" type="text" name="date_from" style="width: 545px;">

            <label class="w3-text-blue"><b>Введите дату документа до: (ввод в формате дд.мм.гггг)</b></label>
            <input class="w3-input w3-border w3-light-grey" type="text" name="date_to" style="width: 545px;">

            <button class="w3-btn w3-blue-grey" type="submit">Отправить</button>
        </form>
    </div>
</body>
</html>
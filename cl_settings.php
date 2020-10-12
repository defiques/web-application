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

function get_string_between($string, $start, $end)
{
    $string = " " . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return "";
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

if (isset($_POST['svmbut']))
{
    $input = $_POST['svmpar'];
    $pgm_file = "D:\\wamp64\\www\\dip\\scripts\\svm_training_set.py";
    $myfile = fopen($pgm_file, "r") or die("Unable to open file!");
    $data = fread($myfile, filesize($pgm_file));
    $parsed_param = get_string_between($data, "svmts(", ") #param");
    $datanew1 = str_replace($parsed_param, $input, $data);
    fclose($myfile);

    $myfile = fopen($pgm_file, "w") or die("Unable to open file!");
    fwrite($myfile, $datanew1);
    fclose($myfile);

    $command = escapeshellcmd('python D:\wamp64\www\dip\scripts\svm_training_set.py');
    shell_exec($command);
}

if (isset($_POST['kmeansbut']))
{
    $input = $_POST['kmeanspar'];
    $pgm_file = "D:\\wamp64\\www\\dip\\scripts\\kmeans_training_set.py";
    $myfile = fopen($pgm_file, "r") or die("Unable to open file!");
    $data = fread($myfile, filesize($pgm_file));
    $parsed_param = get_string_between($data, "kmeansts(", ") #param");
    $datanew1 = str_replace($parsed_param, $input, $data);
    fclose($myfile);

    $myfile = fopen($pgm_file, "w") or die("Unable to open file!");
    fwrite($myfile, $datanew1);
    fclose($myfile);

    $command = escapeshellcmd('python D:\wamp64\www\dip\scripts\kmeans_training_set.py');
    shell_exec($command);
}

if (isset($_POST['bayesbut']))
{
    $input = $_POST['bayespar'];
    $pgm_file = "D:\\wamp64\\www\\dip\\scripts\\bayes_training_set.py";
    $myfile = fopen($pgm_file, "r") or die("Unable to open file!");
    $data = fread($myfile, filesize($pgm_file));
    $parsed_param = get_string_between($data, "bayests(", ") #param");
    $datanew1 = str_replace($parsed_param, $input, $data);
    fclose($myfile);

    $myfile = fopen($pgm_file, "w") or die("Unable to open file!");
    fwrite($myfile, $datanew1);
    fclose($myfile);

    $command = escapeshellcmd('python D:\wamp64\www\dip\scripts\bayes_training_set.py');
    shell_exec($command);
}

if (isset($_POST['mpnsbut']))
{
    $input = $_POST['mpnspar'];
    $pgm_file = "D:\\wamp64\\www\\dip\\scripts\\mlp_training_set.py";
    $myfile = fopen($pgm_file, "r") or die("Unable to open file!");
    $data = fread($myfile, filesize($pgm_file));
    $parsed_param = get_string_between($data, "'mpnsts(", ") #param");
    $datanew1 = str_replace($parsed_param, $input, $data);
    fclose($myfile);

    $myfile = fopen($pgm_file, "w") or die("Unable to open file!");
    fwrite($myfile, $datanew1);
    fclose($myfile);
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <script src="https://kit.fontawesome.com/eee35a830a.js" crossorigin="anonymous"></script>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
    <script type="text/javascript" src="js/showForms.js"></script>
    <title>Настройка классификатора</title>
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
    <h2><i class="fas fa-cog"></i>Настройка классификатора</h2>
</div>
<div class="w3-container">
    <h2>Выберите метод классификации</h2>
    <form class="w3-container">
        <p>
            <input class="w3-radio" type="radio" name="method" value="svm">
            <label>Метод опорных векторов</label></p>
        <p>
            <input class="w3-radio" type="radio" name="method" value="kmeans">
            <label>Метод к-ближайших соседей</label></p>
        <p>
            <input class="w3-radio" type="radio" name="method" value="bayes">
            <label>Метод Байеса</label></p>
        <p>
            <input class="w3-radio" type="radio" name="method" value="mpns">
            <label>Многослойный перцептрон</label></p>
    </form>
</div>
<div class="w3-container">
    <h2 id="h2id" style="display: none">Введите параметры классификатора</h2>
    <form method="post" class="w3-container" id="svmform" style="display: none" action="">
        <p>
            <label class="w3-text-blue"><b>Количество эпох</b></label>
            <input class="w3-input w3-border w3-light-grey" type="text" name="svmpar" style="width: 200px;">
        </p>
        <button class="w3-btn w3-blue-grey" type="submit" name="svmbut">Сохранить</button>
    </form>
    <form method="post" class="w3-container" id="kmeansform" style="display: none" action="">
        <p>
            <label class="w3-text-blue"><b>Количество ближайших соседей</b></label>
            <input class="w3-input w3-border w3-light-grey" type="text" name="kmeanspar" style="width: 200px;">
        </p>
        <button class="w3-btn w3-blue-grey" type="submit" name="kmeansbut">Сохранить</button>
    </form>
    <form method="post" class="w3-container" id="bayesform" style="display: none" action="">
        <p>
            <label class="w3-text-blue"><b>Параметр аддитивного сглаживания</b></label>
            <input class="w3-input w3-border w3-light-grey" type="text" name="bayespar" style="width: 200px;">
        </p>
        <button class="w3-btn w3-blue-grey" type="submit" name="bayesbut">Сохранить</button>
    </form>
    <form method="post" class="w3-container" id="mpnsform" style="display: none" action="">
        <p>

            <label class="w3-text-blue"><b>Скорость обучения</b></label>
            <input class="w3-input w3-border w3-light-grey" type="text" name="mpnspar1" style="width: 200px;">
            <label class="w3-text-blue"><b>Количество эпох</b></label>
            <input class="w3-input w3-border w3-light-grey" type="text" name="mpnspar2" style="width: 200px;">
            <label class="w3-text-blue"><b>Размер батча</b></label>
            <input class="w3-input w3-border w3-light-grey" type="text" name="mpnspar3" style="width: 200px;">
        </p>
        <button class="w3-btn w3-blue-grey" type="submit" name="mpnsbut">Сохранить</button>
    </form>
</div>
</body>
</html>

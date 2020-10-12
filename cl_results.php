<?php
session_start();

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

if (!isset($_POST['upload'])) {
    //download

    $comp = $_POST['com_name'];
    $datefrom = $_POST['date_from'];
    $dateto = $_POST['date_to'];

    $pgm_file = "D:\\wamp64\\www\\dip\\scripts\\downloadmodule.py";
    $myfile = fopen($pgm_file, "r") or die("Unable to open file!");
    $data = fread($myfile, filesize($pgm_file));
    $parsed_name = get_string_between($data, "com_name = '", "' #com_name");
    $parsed_datefrom = get_string_between($data, "date_from = '", "' #date_from");
    $parsed_dateto = get_string_between($data, "date_to = '", "' #date_to");
    $datanew1 = str_replace($parsed_name, $comp, $data);
    $datanew2 = str_replace($parsed_datefrom, $datefrom, $datanew1);
    $datanew3 = str_replace($parsed_dateto, $dateto, $datanew2);
    fclose($myfile);

    $myfile = fopen($pgm_file, "w") or die("Unable to open file!");
    fwrite($myfile, $datanew3);
    fclose($myfile);

    $command = escapeshellcmd('python D:\wamp64\www\dip\scripts\downloadmodule.py');
    $output = shell_exec($command);
    $output = substr($output, 0, strlen($output));

    $dates = [];

    for ($i = 0; $i <= (strlen($output) / 11) - 1; $i++){
        $a = '';
        for ($j = $i * 11; $j <= ($i * 11) + 9; $j++){
            $a = $a.$output[$j];
        }
        $dates[] = $a;
    }

    $_SESSION['dates'] = $dates;

//classification

    $defdir = "D:\\wamp64\\www\\dip\\DOCS";
    $linkname = [];
    $names = [];

    $folders = scandir($defdir);
    unset($folders[0]);
    unset($folders[1]);

    foreach ($folders as $fold) {
        $files = scandir($defdir . "\\" . $fold);
        unset($files[0]);
        unset($files[1]);
        foreach ($files as $file) {
            $linkname[] = $file;
            $names[] = $fold;
        }
    }

    $classes1 = [];
    $classes2 = [];
    $classes3 = [];
    $classes4 = [];
    $links = [];

    foreach ($linkname as $link) {
        $links[] = str_replace("!", "/", substr($link, 0, 36));
    }

    $command1 = escapeshellcmd('python D:\wamp64\www\dip\scripts\class_bayes.py');
    $command2 = escapeshellcmd('python D:\wamp64\www\dip\scripts\class_svm.py');
    $command3 = escapeshellcmd('python D:\wamp64\www\dip\scripts\class_kmeans.py');
    $command4 = escapeshellcmd('python D:\wamp64\www\dip\scripts\mlp_training_set.py');

    $output1 = shell_exec($command1);
    $output2 = shell_exec($command2);
    $output3 = shell_exec($command3);
    $output4 = shell_exec($command4);
    $output1 = substr($output1, 0, strlen($output1));
    $output2 = substr($output2, 0, strlen($output2));
    $output3 = substr($output3, 0, strlen($output3));
    $output4 = substr($output4, 0, strlen($output4));

    for ($i = 0; $i <= (strlen($output1) - 1); $i++) {
        $classes1[] = $output1[$i];
        $classes2[] = $output2[$i];
        $classes3[] = $output3[$i];
        $classes4[] = $output4[$i];
    }

    $_SESSION['names'] = $names;
    $_SESSION['links'] = $links;
    $_SESSION['classes1'] = $classes1;
    $_SESSION['classes2'] = $classes2;
    $_SESSION['classes3'] = $classes3;
    $_SESSION['classes4'] = $classes4;

    $docnamefile = fopen("D:\\wamp64\\www\\dip\\txt\\doc_name.txt", "r");
    $doc_name = [];
    if ($docnamefile) {
        while (($buffer = fgets($docnamefile)) !== false) {
            $buffer = iconv("windows-1251", "utf-8", $buffer);
            $doc_name[] = $buffer;
        }
    }
    fclose($docnamefile);
}
else {

    $dates = $_SESSION['dates'];
    $names = $_SESSION['names'];
    $links = $_SESSION['links'];
    $classes1 = $_SESSION['classes1'];
    $classes2 = $_SESSION['classes2'];
    $classes3 = $_SESSION['classes3'];
    $classes4 = $_SESSION['classes4'];

    require 'connection.php';

    // Create connection
    $conn = new mysqli($host, $user, $password, $database);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if ($_POST['method'] == 'svm') {
        for ($i = 0; $i <= (count($links) - 1); $i++) {
            $sql = "INSERT INTO companies (link, com_class, com_name, com_date) VALUES (' " . $links[$i] . " ',$classes2[$i],
    ' " . $names[$i] . " ',' " . $dates[$i] . " ')";
            if ($conn->query($sql) === TRUE) {
                $last_id = $conn->insert_id;
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }
    } elseif ($_POST['method'] == 'kmeans') {
        for ($i = 0; $i <= (count($links) - 1); $i++) {
            $sql = "INSERT INTO companies (link, com_class, com_name, com_date) VALUES (' " . $links[$i] . " ',$classes3[$i],
    ' " . $names[$i] . " ',' " . $dates[$i] . " ')";
            if ($conn->query($sql) === TRUE) {
                $last_id = $conn->insert_id;
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }
    } elseif ($_POST['method'] == 'bayes') {
        for ($i = 0; $i <= (count($links) - 1); $i++) {
            $sql = "INSERT INTO companies (link, com_class, com_name, com_date) VALUES (' " . $links[$i] . " ',$classes1[$i],
    ' " . $names[$i] . " ',' " . $dates[$i] . " ')";
            if ($conn->query($sql) === TRUE) {
                $last_id = $conn->insert_id;
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }
    } elseif ($_POST['method'] == 'mpns') {
        for ($i = 0; $i <= (count($links) - 1); $i++) {
            $sql = "INSERT INTO companies (link, com_class, com_name, com_date) VALUES (' " . $links[$i] . " ',$classes4[$i],
    ' " . $names[$i] . " ',' " . $dates[$i] . " ')";
            if ($conn->query($sql) === TRUE) {
                $last_id = $conn->insert_id;
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }
    }

    $conn->close();
    header('location:index.php');
}


?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <script src="https://kit.fontawesome.com/eee35a830a.js" crossorigin="anonymous"></script>
    <title>Результаты классификации</title>
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
        <h2><i class="fas fa-table"></i>Результаты классификации</h2>
    </div>
    <div class="w3-container">
        <div class="table-responsive">
            <table class="w3-table-all w3-large">
                <thead>
                <tr>
                    <th>Номер судебного дела</th>
                    <th>Ссылка</th>
                    <th>SVM-метод</th>
                    <th>K-ближайших соседей метод</th>
                    <th>Байес метод</th>
                    <th>Многослойный перцептрон</th>
                </tr>
                </thead>
                <tbody>
                    <?php
                    for ($i = 0; $i <= (count($links) - 1); $i++)
                    {
                        echo '<tr>';
                        echo '<th>'.$doc_name[$i].'</th>';
                        echo '<th>'.$links[$i].'</th>';
                        echo '<th>'.$classes2[$i].'</th>';
                        echo '<th>'.$classes3[$i].'</th>';
                        echo '<th>'.$classes1[$i].'</th>';
                        echo '<th>'.$classes4[$i].'</th>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="w3-container">
        <h2>Результаты классификации каким методом загрузить в БД?</h2>
        <form method="post" action="" class="w3-container">
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
            <button class="w3-btn w3-blue-grey" type="submit" name="upload">Загрузить</button>
        </form>
    </div>
</body>
</html>
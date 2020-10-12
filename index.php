<?php
//index.php

$connect = new PDO("mysql:host=localhost;dbname=dip", "root", "");

$query = "SELECT DISTINCT com_name FROM companies ORDER BY com_name ASC";

$statement = $connect->prepare($query);

$statement->execute();

$result = $statement->fetchAll();


session_start();

if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION['username']);
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <script src="https://kit.fontawesome.com/eee35a830a.js" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <script src="js/bootstrap-select.min.js"></script>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" />
    <link href="css/bootstrap-select.min.css" rel="stylesheet" />
    <title>Главная</title>
    <style>
        .row {
            display: flex;
            flex-wrap: wrap;
        }

        .side {
            flex: 25%;
            background-color: #f1f1f1;
            padding: 20px;
        }

        .main {
            flex: 75%;
            background-color: white;
            padding: 20px;
        }
    </style>
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
        <h2><i class="fas fa-home"></i>Главная</h2>
    </div>
    <div class="row">
        <div class="side">
            <h2>Наименования классов</h2>
            <p>1 - взыскание задолженностей, пошлин, неустоек с компаний</p>
            <p>2 - взыскание задолженностей, пошлин, неустоек в пользу компаний</p>
            <p>3 - банкрот</p>
        </div>
        <div class="main">
            <h2>Выберите компанию</h2>
            <select name="multi_search_filter" id="multi_search_filter" multiple class="form-control selectpicker">
                <?php
                foreach($result as $row)
                {
                    echo '<option value="'.$row["com_name"].'">'.$row["com_name"].'</option>';
                }
                ?>
            </select>
            <input type="hidden" name="hidden_country" id="hidden_country" />
            <div style="clear:both"></div>
            <br />
            <div class="table-responsive">
                <table class="w3-table-all w3-large">
                    <thead>
                    <tr>
                        <th>Дата</th>
                        <th>Имя компании</th>
                        <th>Номер класса</th>
                        <th>Сумма</th>
                        <th>Ссылка</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <br />
            <br />
            <br />
        </div>
	</div>	
</body>
</html>


<script>
    $(document).ready(function(){

        load_data();

        function load_data(query='')
        {
            $.ajax({
                url:"fetch.php",
                method:"POST",
                data:{query:query},
                success:function(data)
                {
                    $('tbody').html(data);
                }
            })
        }

        $('#multi_search_filter').change(function(){
            $('#hidden_country').val($('#multi_search_filter').val());
            var query = $('#hidden_country').val();
            load_data(query);
        });

    });
</script>
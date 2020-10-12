<?php

//fetch.php

$connect = new PDO("mysql:host=localhost;dbname=dip", "root", "");

if($_POST["query"] != '')
{
	$search_array = explode(",", $_POST["query"]);
	$search_text = "'" . implode("', '", $search_array) . "'";
	$query = "
	SELECT * FROM companies 
	WHERE com_name IN (".$search_text.") 
	ORDER BY id DESC
	";
}
else
{
	$query = "SELECT * FROM companies ORDER BY com_date DESC";
}

$statement = $connect->prepare($query);

$statement->execute();

$result = $statement->fetchAll();

$total_row = $statement->rowCount();

$output = '';

if($total_row > 0)
{
	foreach($result as $row)
	{
		$output .= '
		<tr>
			<td>'.$row["com_date"].'</td>
			<td>'.$row["com_name"].'</td>
			<td>'.$row["com_class"].'</td>
			<td>'.$row["sum"].'</td>
			<td>'.$row["link"].'</td>
		</tr>
		';
	}
}
else
{
	$output .= '
	<tr>
		<td colspan="5" align="center">No Data Found</td>
	</tr>
	';
}

echo $output;


?>
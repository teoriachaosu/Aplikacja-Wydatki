<?php
class ShowInfo {
private object $con;
private string $form_info;	
private string $file_name;
public function __construct($con, $form_info, $file_name) {
	$this->con = $con;
	$this->file_name = $file_name;
	isset($_POST[$form_info]) ? $this->form_info = $_POST[$form_info] : $this->form_info = '';
	}
// LIST CATEGORIES
public function listCategories(): void {
	try {
$qry = $this->con->query('SELECT id_cat, cat_name FROM category ORDER BY cat_name'); 
echo "<table id='tabl'>";
echo '<tr><td><b>'.'Nazwa kategorii'.'</b></td><td><b>'.'ID'.'</b></td></tr>';
foreach($qry as $row) // while($row = $qry->fetch()) 
	echo '<tr><td>'.$row[1].'</td><td>'.$row[0].'</td></tr>';
	echo '</table>';
	} catch(PDOException $ex) {
		echo "Wystąpił błąd bazy danych".$ex->getMessage();
		}
	}
	
// LIST PLACES OF PURCHASE PRESENT IN BASE
public function listPlaces(): void {
	try {
$qry = $this->con->query('SELECT distinct place from purchase order by place asc');
echo "<table id='tabl'>";
echo '<tr><td><b>'.'Miejsce zakupu'.'</b></td></tr>';
foreach($qry as $row)
	echo '<tr><td>'.$row[0].'</td></tr>';
	
echo '</table>';
	} catch(PDOException $ex) {
		echo "Wystąpił błąd bazy danych".$ex->getMessage();
		}
	}

public function PrintHelp() {
	if($this->form_info) {
		$lines=file($this->file_name); //subtitle file into array
		foreach($lines as $row)
			echo $row.'</br>';
		}
	}
}

<?php
class ShowInfo extends RootOfAllClasses {
// parent method takes arg. Null needed here for class compatibility
public function printMsg($row=null): void { 
	if($this->submit_button) { // print help
		$lines=file($this->msg_file); //subtitle file into array
		foreach($lines as $row)
			echo $row.'</br>';
		}
	}
// LIST CATEGORIES
public function listCategories(string $tab_header=''): void { // table header info as argument
	try {
$qry = $this->con->query('SELECT id_cat, cat_name FROM category ORDER BY cat_name'); 
echo "<table id='tabl'>";
echo '<tr><td><b>'.$tab_header.'</b></td><td><b>'.'ID'.'</b></td></tr>';
foreach($qry as $row) // while($row = $qry->fetch()) 
	echo '<tr><td>'.$row[1].'</td><td>'.$row[0].'</td></tr>';
	echo '</table>';
	} catch(PDOException $ex) {
		echo "Database error occurred";//.$ex->getMessage();
		}
	}
// LIST PLACES OF PURCHASE PRESENT IN BASE
public function listPlaces(string $tab_header=''): void { 
	try {
$qry = $this->con->query('SELECT distinct place from purchase order by place asc');
echo "<table id='tabl'>";
echo '<tr><td><b>'.$tab_header.'</b></td></tr>';
foreach($qry as $row)
	echo '<tr><td>'.$row[0].'</td></tr>';
	
echo '</table>';
	} catch(PDOException $ex) {
		echo "Database error occurred";//.$ex->getMessage();
		}
	}
}
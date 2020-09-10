<?php
class ItemSearch {// $con, 'orderby_form1', 'searchby_form', 'it_srch_form', 'asc1, 'it_srch_submit'  
	private $con;
	private string $it_srch_submit; // submit button
	public string $asc; // choose asc/desc order
	public string $orderby_form; // order search results rollout
	public string $searchby_form; // search by rollout
	public string $it_srch_form; // search field; enter name/item id/cat id.

public function __construct($con, $orderby_form, $searchby_form, $it_srch_form, $asc, $it_srch_submit) {
	$this->con = $con;
	isset($_POST[$it_srch_submit]) ? $this->it_srch_submit = $_POST[$it_srch_submit] : $this->it_srch_submit = '';
	isset($_POST[$it_srch_form]) ? $this->it_srch_form = htmlentities(trim($_POST[$it_srch_form])) : $this->it_srch_form = '';
	if(isset($_POST[$orderby_form])) $this->orderby_form = $_POST[$orderby_form];
	if(isset($_POST[$searchby_form])) $this->searchby_form = $_POST[$searchby_form];
	!empty($_POST[$asc]) ? $this->asc = 'ASC' : $this->asc = 'DESC';
	}

private function prt_res_cnt_records($qry): void { //print results and count the records
if($qry->rowCount()>0) {
	echo 'Liczba znalezionych rekordów: '.$qry->rowCount().'</br>';
	echo "<table id='tabl'>";	
	echo '<tr><td><b>'.'ID'.'</b></td><td><b>'.'Nazwa towaru'.'</b></td><td><b>'.'ID kat.'.'</b></td></tr>';
	foreach($qry as $row) {
		echo '<tr><td>'.$row[0].'</td><td>'.$row[1].'</td><td>'.$row[2].'</td></tr>';
		}
	echo '</table>';
	}
else if($qry->rowCount()==0)
echo '<b>Brak danych spełniających kryteria wyszukiwania</b></br></br>';	
}

public function itemSearch(): void { 

if($this->it_srch_submit) // submit button
	{ 
	$orderby = $this->orderby_form;
	$searchby = $this->searchby_form;
	$ascdesc = $this->asc;
	$this->searchby_form == 'id_item' ? $whereid = 'id_item' : $whereid = 'cat_id';
	$itm_data = $this->it_srch_form;
	$qry = $this->con->prepare("SELECT id_item, item_name, cat_id FROM commodity WHERE item_name LIKE ? ORDER BY $orderby $ascdesc");
	if($searchby == 'item_name') 
		$itm_data = '%'.$this->it_srch_form.'%';
	else if(is_numeric($itm_data) && ($searchby == 'id_item' || $searchby == 'cat_id')) {
		$itm_data = abs(intval($itm_data));  // convert to positive int
		$qry = $this->con->prepare("SELECT id_item, item_name, cat_id FROM commodity WHERE $whereid=? ORDER BY $orderby $ascdesc");
		}
	else
		exit ('<b>Niedozwolona wartość dla wyszukiwania po ID</b>');
		
	$qry->execute([$itm_data]);
	$this->prt_res_cnt_records($qry);		
	}
}
	
}


	

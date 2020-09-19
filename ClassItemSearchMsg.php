<?php
class ItemSearch extends RootOfAllClasses {
// $con, 'it_srch_submit', 'search_item_msg_PL.txt', 'orderby_form1', 'searchby_form', 'it_srch_form', 'asc1'  
	public string $asc; // choose asc/desc order
	public string $orderby_form; // order search results rollout
	public string $searchby_form; // search by rollout
	public string $it_srch_form; // search field; enter name/item id/cat id.
	private array $printout_header = []; // array with result printout header info

public function __construct($con, $submit_button, $msg_file, $orderby_form, $searchby_form, $it_srch_form, $asc) {
	parent::__construct($con, $submit_button, $msg_file);
	isset($_POST[$it_srch_form]) ? $this->it_srch_form = htmlentities(trim($_POST[$it_srch_form])) : $this->it_srch_form = '';
	if(isset($_POST[$orderby_form])) $this->orderby_form = $_POST[$orderby_form];
	if(isset($_POST[$searchby_form])) $this->searchby_form = $_POST[$searchby_form];
	!empty($_POST[$asc]) ? $this->asc = 'ASC' : $this->asc = 'DESC';
	}
// get array with printout header info from txt file
private function get_printout_header(string $header_filename=' '): array { 
	$lines=file($header_filename); 
	for($i=0; $i<count($lines); $i++) {
		$cut = strpos($lines[$i], '/'); 
		$lines[$i] = substr($lines[$i], $cut+1);
		}
	return $lines;
	}
//print results and count records
private function prt_res_cnt_records($qry): void { 
	$header_info = $this->printout_header; // printout description from search result header txt file
if($qry->rowCount()>0) {
	echo $header_info[1].' '.$qry->rowCount().'</br>'; // 'Number of the records found:'
	echo "<table id='tabl'>";	
	echo '<tr><td><b>'.'ID'.'</b></td><td><b>'.$header_info[2].'</b></td><td><b>'.$header_info[3].'</b></td></tr>'; // 'Item Name', 'Cat. ID'
	foreach($qry as $row) {
		echo '<tr><td>'.$row[0].'</td><td>'.$row[1].'</td><td>'.$row[2].'</td></tr>';
		}
	echo '</table>';
	}
else if($qry->rowCount()==0)
$this->printMsg(1); //echo '<b>Brak danych spełniających kryteria wyszukiwania</b></br></br>';	
}

public function itemSearch(string $header_filename=''): void { 
$this->printout_header = $this->get_printout_header($header_filename);
if($this->submit_button) 
	{ 
	$orderby = $this->orderby_form;
	$searchby = $this->searchby_form;
	$ascdesc = $this->asc;
	$this->searchby_form == 'id_item' ? $whereid = 'id_item' : $whereid = 'cat_id';
	$itm_data = $this->it_srch_form;
try {	
	$qry = $this->con->prepare("SELECT id_item, item_name, cat_id FROM commodity WHERE item_name LIKE ? ORDER BY $orderby $ascdesc");
	if($searchby == 'item_name') 
		$itm_data = '%'.$this->it_srch_form.'%';
	else if(is_numeric($itm_data) && ($searchby == 'id_item' || $searchby == 'cat_id')) {
		$itm_data = abs(intval($itm_data));  // convert to positive int
		$qry = $this->con->prepare("SELECT id_item, item_name, cat_id FROM commodity WHERE $whereid=? ORDER BY $orderby $ascdesc");
	}else {
		$this->printMsg(2);
		exit(); //'<b>Niedozwolona wartość dla wyszukiwania po ID</b>');
		}
	$qry->execute([$itm_data]);
	$this->prt_res_cnt_records($qry);		
		}catch (PDOException $ex) {
			$this->printMsg(3); //echo 'Database error occurred'; 
			}
	}
}
	
}


	

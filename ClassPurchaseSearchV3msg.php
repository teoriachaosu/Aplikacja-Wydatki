<?php
class PurchaseSearch extends RootOfAllClasses { 
// $con, 'pu_srch_submit', 'search_purch_msg_PL.txt', 'start_date', 'end_date', 'place', 'itm_name', 'orderby_form', 'asc', 'total_amt', 'cat_id', 'pur_id'   
	public string $start_date;
	public string $end_date;
	public string $placenosp; // place name to trim spaces
	private $cat_id; // search by category id (int or string '%' for all categories) 
	private $pur_id; // search by purchase id (int or string '%' for all ids) 
	public string $asc; // order checkbox 
	public string $orderby_form; // column to order by
	public bool $total_amt; // sum up total amount?
	public string $itm_namenosp; // item name to trim spaces
	private array $printout_header = []; // array with result printout header info

public function __construct($con, $submit_button, $msg_file, $start_date, $end_date, $place, $itm_name, $orderby_form, $asc, $total_amt, $cat_id, $pur_id) {
	parent::__construct($con, $submit_button, $msg_file);
	
	isset($_POST[$start_date]) ? $this->start_date = htmlentities($_POST[$start_date]) : $this->start_date = ''; 
	isset($_POST[$end_date]) ? $this->end_date = htmlentities($_POST[$end_date]) : $this->end_date = ''; 
	isset($_POST[$place]) ? $this->placenosp = htmlentities(trim($_POST[$place])) : $this->placenosp = '%'; 
	isset($_POST[$itm_name]) ? $this->itm_namenosp = htmlentities(trim($_POST[$itm_name])) : $this->itm_namenosp = '%';
	if(isset($_POST[$orderby_form])) $this->orderby_form = $_POST[$orderby_form]; // rollout
	!empty($_POST[$asc]) ? $this->asc = 'ASC' : $this->asc = 'DESC';
	!empty($_POST[$total_amt]) ? $this->total_amt = true : $this->total_amt = false;
	!empty($_POST[$cat_id]) ? $this->cat_id = $_POST[$cat_id] : $this->cat_id = '%';
	!empty($_POST[$pur_id]) ? $this->pur_id = $_POST[$pur_id] : $this->pur_id = '%';
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
// recognize correct date format
private function Gooddate($date): bool { 
if(!preg_match('/^\d{4}-\d\d-\d\d$/', $date))
	return false;
return true;
}
// main search result printout 
private function resPrintout($qry): void {
	$header_info = $this->printout_header; // printout description from search result header txt file
	echo $header_info[1].' '.$qry->rowCount().'</br>'; 
	echo $header_info[2].' '.$this->start_date.' '.$header_info[3].' '.$this->end_date;
	echo "<table id='tabl'>";
	echo '<tr><td><b>'.$header_info[4].'</b></td><td><b>'.$header_info[5].'</b></td><td><b>'.$header_info[6].'</b></td><td><b>'.$header_info[7].'</b></td><td><b>'.$header_info[8].'</b></td><td><b>'.$header_info[9].'</b></td><td><b>'.$header_info[10].'</b></td><td><b>'.$header_info[11].'</b></td><td><b>'.$header_info[12].'</b></td></tr>';
		foreach($qry as $row){//($i=0; $i<$qry->rowCount(); $i++) {
		//$row=$qry->fetch();
		echo '<tr><td>'.$row[0].'</td><td>'.$row[1].'</td><td>'.$row[2].'</td><td>'.$row[3].'</td><td>'.$row[4].'</td><td>'.$row[5].'</td><td>'.$row[6].'</td><td>'.$row[7].'</td><td>'.$row[8].'</td></tr>';
		}
	echo '</table>';
}
// payment & discount sums printouts 
private function sumPrintout($qry): void {
	$row=$qry->fetch();
	$header_info = $this->printout_header;
	if($qry->columnCount()==2 && $row[0] && $row[1]) {	
		echo $header_info[13].' '.$row[0].'<br>'; // 'Payment Sum'
		echo $header_info[14].' '.$row[1].'<br>'; // 'Discount Sum'
		}
	else if($qry->columnCount()==3 && $row[0] && $row[1] && $row[2]) {
		echo $header_info[15].' '.$row[2].'<br>'; // 'Total Amount'
		echo $header_info[13].' '.$row[0].'<br>';
		echo $header_info[14].' '.$row[1].'<br>';
		}
}

//-------PURCHASE SEARCH FORM ------------------
public function purSearch(string $header_filename=''): void {
	// array with printout header info from txt file
	$this->printout_header = $this->get_printout_header($header_filename); 
try {
	//payment, discount, amount from a specified name, place/all places and ALL CATEGORIES in a given period
	$qry1=$this->con->prepare("SELECT SUM(payment), SUM(discount), SUM(amount) from commodity, purchase WHERE commodity.id_item=purchase.item_id AND purchase_date BETWEEN ? AND ? AND place LIKE ? AND item_name LIKE ?");
	
	//payment & discount from a specified place/all places and ALL CATEGORIES in a given period 
	$qry2=$this->con->prepare("SELECT SUM(payment), SUM(discount) FROM commodity, purchase WHERE commodity.id_item=purchase.item_id AND purchase_date BETWEEN ? AND ? AND place LIKE ? AND item_name LIKE ?");
	
	//payment & discount from specified place/all places and a SPECIFIED CATEGORY in a given period
	$qry3=$this->con->prepare("SELECT SUM(payment), SUM(discount) FROM commodity, purchase, category WHERE commodity.id_item=purchase.item_id AND category.id_cat=commodity.cat_id AND purchase_date BETWEEN ? AND ? and place like ? AND item_name LIKE ? AND cat_id=?");
	
if ($this->submit_button) // submit button
	{ 
	// valid start_date + invalid/empty end_date => from start_date to present day
	if($this->Gooddate($this->start_date) && !$this->Gooddate($this->end_date)) 
		$this->end_date = date('Y-m-d');
	// invalid/empty start_date + valid end_date => one day = end_date
	if(!$this->Gooddate($this->start_date) && $this->Gooddate($this->end_date)) 
		$this->start_date = $this->end_date;
	
	// both dates empty => from the beginning to present day 
	if(!$this->start_date && !$this->end_date) {
		$qry = $this->con->query("SELECT MIN(purchase_date) FROM purchase");
		$row = $qry->fetch();
		$this->start_date = $row[0]; // date of 1st purchase in base
		$this->end_date = date('Y-m-d');
		}
	if($this->Gooddate($this->start_date) && $this->Gooddate($this->end_date)) 
	{
	// trim to remove whitespaces added accidentally by copying and pasting
	$this->placenosp == '%' ? $place = $this->placenosp : $place = $this->placenosp.'%'; 
	$this->itm_namenosp == '%' ? $itm_name = $this->itm_namenosp : $itm_name = $this->itm_namenosp.'%'; 
	
	// main search
	$order_by = $this->orderby_form; // column and order to order by 
	$asc_desc = $this->asc;
	
	$srch_qry=$this->con->prepare("SELECT id_purchase, item_name, amount, price, payment, discount, purchase_date, place, cat_name FROM commodity, purchase, category WHERE commodity.id_item=purchase.item_id AND category.id_cat=commodity.cat_id AND purchase_date BETWEEN ? AND ? AND place LIKE ? AND item_name LIKE ? AND cat_id LIKE ? AND id_purchase LIKE ? ORDER BY $order_by $asc_desc");
	$srch_qry->execute([$this->start_date, $this->end_date, $place, $itm_name, $this->cat_id, $this->pur_id]);
	
		if($srch_qry->rowCount() == 0) {
			$this->printMsg(1);
			exit; //('<b>Brak danych spełniających kryteria wyszukiwania</b>');
			}
		// qry1
		if($this->total_amt) {  
			if($itm_name !== '%' && $this->pur_id == '%' && $this->cat_id == '%') {	
				$qry1->execute([$this->start_date, $this->end_date, $place, $itm_name]); 
				$this->sumPrintout($qry1);
			}
			else {
				$this->printMsg(2);
				exit; //("<b>Sumowanie ilości wymaga nazwy towaru i pustych pól ID kategorii/zakupu </b>");
				}
		}
		// qry2
		else if($this->cat_id == '%' && $this->pur_id == '%') {
			$qry2->execute([$this->start_date, $this->end_date, $place, $itm_name]);
			$this->sumPrintout($qry2);
			}	
		// qry3
		else if($this->cat_id && $this->pur_id === '%') {
			$qry3->execute([$this->start_date, $this->end_date, $place, $itm_name, $this->cat_id]);
			$this->sumPrintout($qry3);
			}
		$this->resPrintout($srch_qry);
	}	
	else
		$this->printMsg(3); // echo '<b>Akceptowalny format daty to RRRR-MM-DD</b>';
	}
		}catch (PDOException $ex) {
			$this->printMsg(4); //echo 'Database error occurred'; 
			}
		}
}
	

<?php
class PurchaseSearch { // $con, 'start_date', 'end_date', 'place', 'itm_name', 'orderby_form', 'asc', 'total_amt', 'cat_id', 'pur_id', 'pu_srch_submit'  
	private object $con;
	private string $pu_srch_submit; // submit button
	public string $start_date;
	public string $end_date;
	public string $placenosp; // place name to trim spaces
	private $cat_id; // search by category id (int or string '%' for all categories) 
	private $pur_id; // search by purchase id (int or string '%' for all ids) 
	public string $asc; // order checkbox 
	public string $orderby_form; // column to order by
	public bool $total_amt; // sum up total amount?
	public string $itm_namenosp; // item name to trim spaces

public function __construct($con, $start_date, $end_date, $place, $itm_name, $orderby_form, $asc, $total_amt, $cat_id, $pur_id, $pu_srch_submit) {
	$this->con = $con;
	isset($_POST[$pu_srch_submit]) ? $this->pu_srch_submit = $_POST[$pu_srch_submit] : $this->pu_srch_submit = '';
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

//-------auxiliary functions for purchase searches-------------

// recognizing correct date format
private function Gooddate($date): bool { 
if(!preg_match('/^\d{4}-\d\d-\d\d$/', $date))
	return false;
return true;
}
// main search result printout 
private function resPrintout($qry): void {
	echo 'Liczba znalezionych rekordów: '.$qry->rowCount().'</br>';
	echo 'Okres: '.$this->start_date.' do '.$this->end_date;
	echo "<table id='tabl'>";
	echo '<tr><td><b>'.'ID zak.'.'</b></td><td><b>'.'Nazwa towaru'.'</b></td><td><b>'.'Ilość'.'</b></td><td><b>'.'cena'.'</b></td><td><b>'.'Zapłata'.'</b></td><td><b>'.'rabat'.'</b></td><td><b>'.'Data zakupu'.'</b></td><td><b>'.'miejsce'.'</b></td><td><b>'.'kategoria'.'</b></td></tr>';
		foreach($qry as $row){//($i=0; $i<$qry->rowCount(); $i++) {
		//$row=$qry->fetch();
		echo '<tr><td>'.$row[0].'</td><td>'.$row[1].'</td><td>'.$row[2].'</td><td>'.$row[3].'</td><td>'.$row[4].'</td><td>'.$row[5].'</td><td>'.$row[6].'</td><td>'.$row[7].'</td><td>'.$row[8].'</td></tr>';
		}
	echo '</table>';
}
// payment & discount sums printouts 
private function sumPrintout($qry): void {
	$row=$qry->fetch();
	if($qry->columnCount()==2 && $row[0] && $row[1]) {	
		echo 'Suma zapłaty: '.$row[0].'<br>';
		echo 'Suma rabatu: '.$row[1].'<br>';
		}
	else if($qry->columnCount()==3 && $row[0] && $row[1] && $row[2]) {
		echo 'Łączna ilość: '.$row[2].'<br>';
		echo 'Suma zapłaty: '.$row[0].'<br>';
		echo 'Suma rabatu: '.$row[1].'<br>';
		}
}

//-------PURCHASE SEARCH FORM ------------------
public function purSearch(): void {
try {
	//payment, discount, amount from a specified name, place/all places and ALL CATEGORIES in a given period
	$qry1=$this->con->prepare("SELECT SUM(payment), SUM(discount), SUM(amount) from commodity, purchase WHERE commodity.id_item=purchase.item_id AND purchase_date BETWEEN ? AND ? AND place LIKE ? AND item_name LIKE ?");
	
	//payment & discount from a specified place/all places and ALL CATEGORIES in a given period 
	$qry2=$this->con->prepare("SELECT SUM(payment), SUM(discount) FROM commodity, purchase WHERE commodity.id_item=purchase.item_id AND purchase_date BETWEEN ? AND ? AND place LIKE ? AND item_name LIKE ?");
	
	//payment & discount from specified place/all places and a SPECIFIED CATEGORY in a given period
	$qry3=$this->con->prepare("SELECT SUM(payment), SUM(discount) FROM commodity, purchase, category WHERE commodity.id_item=purchase.item_id AND category.id_cat=commodity.cat_id AND purchase_date BETWEEN ? AND ? and place like ? AND item_name LIKE ? AND cat_id=?");
	
if ($this->pu_srch_submit) // submit button
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
	
		if($srch_qry->rowCount() == 0)
			exit('<b>Brak danych spełniających kryteria wyszukiwania</b>');
		// qry1
		if($this->total_amt) {  
			if($itm_name !== '%' && $this->pur_id == '%' && $this->cat_id == '%') {	
				$qry1->execute([$this->start_date, $this->end_date, $place, $itm_name]); 
				$this->sumPrintout($qry1);
			}
			else exit("<b>Sumowanie ilości wymaga nazwy towaru i pustych pól ID kategorii/zakupu </b>");
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
		echo '<b>Akceptowalny format daty to RRRR-MM-DD</b>';
	}
		}catch (PDOException $ex) {
			echo 'Wystąpił błąd bazy danych'; //.$ex;
			}
		}
}
	

<?php
class UpdatePurchase extends CreatePurchase { // $con, 'item_id1', 'purchase_date1', 'amount1', 'price1', 'payment1', 'discount1', 'place1', 'pu_upd_submit', 'id_purchase'
	
	public int $id_purchase;
	
public function __construct($con, $item_id, $purchase_date, $amount, $price, $payment, $discount, $place, $pu_inp_submit, $id_purchase) {
	parent::__construct($con, $item_id, $purchase_date, $amount, $price, $payment, $discount, $place, $pu_inp_submit, $id_purchase);
	!empty($_POST[$id_purchase]) ? $this->id_purchase = $_POST[$id_purchase] : $this->id_purchase = 0;
	isset($_POST[$item_id]) && $_POST[$item_id] !='' ? $this->item_id = $_POST[$item_id] : $this->item_id = null;
	isset($_POST[$amount]) && $_POST[$amount] !='' ? $this->amount = $_POST[$amount] : $this->amount = null; // enter 0 for 0, empty form - no changes
	isset($_POST[$price]) && $_POST[$price] !='' ? $this->price = ($_POST[$price]) : $this->price = null; 
	isset($_POST[$payment]) && $_POST[$payment] !='' ? $this->payment = $_POST[$payment] : $this->payment = null;
	isset($_POST[$discount]) && $_POST[$discount] !='' ? $this->discount = $_POST[$discount] : $this->discount = null;
	// place and date same as parent class - empty strings  
	}
public function updatePurchase(): void {
try {
	$test_pu_id = $this->con->prepare("SELECT id_purchase FROM purchase WHERE id_purchase=?");
	$test_it_id = $this->con->prepare("SELECT id_item FROM commodity WHERE id_item=?");
	$amt_qry = $this->con->prepare("UPDATE purchase SET amount=? WHERE id_purchase=?");
	$prc_qry = $this->con->prepare("UPDATE purchase SET price=? WHERE id_purchase=?");
	$pmt_qry = $this->con->prepare("UPDATE purchase SET payment=? WHERE id_purchase=?");
	$plc_qry = $this->con->prepare("UPDATE purchase SET place=? WHERE id_purchase=?");
	$pur_date_qry = $this->con->prepare("UPDATE purchase SET purchase_date=? WHERE id_purchase=?");
	$it_id_qry = $this->con->prepare("UPDATE purchase SET item_id=? WHERE id_purchase=?");
	$disc_qry = $this->con->prepare("UPDATE purchase SET discount=? WHERE id_purchase=?");

if($this->pu_inp_submit) // submit button, inherited
	{
	// validate purchase id
	$test_pu_id->execute([$this->id_purchase]);
	if(!$test_pu_id->rowCount())
		echo 'Nie podano lub brak zakupu o podanym ID w bazie. ';
	// validate item id
	if($this->item_id) { 
		$valid_id = true; // without this, empty form field is treated as non-existent item id
		$test_it_id->execute([$this->item_id]);
		if(!$test_it_id->rowCount()) { // validate item ID
			echo 'Brak towaru o podanym ID w bazie. ';
			$valid_id = false;
			}
		}	
	if($this->purchase_date && !preg_match('/^\d{4}-\d\d-\d\d$/', $this->purchase_date))
		echo 'Akceptowalny format daty to RRRR-MM-DD. ';
	
	else { // if any field empty, no changes made
		if($this->amount !== null)
			$amt_qry->execute([$this->amount, $this->id_purchase]);
		
		if($this->price !== null) // enter 0 to set 0, empty field = no change
			$prc_qry->execute([$this->price, $this->id_purchase]);
		
		if($this->payment !== null) 
			$pmt_qry->execute([$this->payment, $this->id_purchase]);
		
		if($this->place)
			$plc_qry->execute([$this->place, $this->id_purchase]);
		
		if($this->purchase_date)
			$pur_date_qry->execute([$this->purchase_date, $this->id_purchase]);
		
		if($this->item_id !== null && $valid_id) // without this, invalid item id + valid purchase id = record removal!!
			$it_id_qry->execute([$this->item_id, $this->id_purchase]);
		
		if($this->discount !== null)
			$disc_qry->execute([$this->discount, $this->id_purchase]);
		}
	$querries = [$amt_qry, $prc_qry, $pmt_qry, $plc_qry, $pur_date_qry, $it_id_qry, $disc_qry];
	$update = false;
	foreach($querries as $qry) {
		if($qry->rowCount()) { 
			$update = true; 
			break;
			}
		}
	if($update == true) echo 'Pomyślnie zmodyfikowano zakup ID '.$this->id_purchase; 
	else echo 'Nie dokonano żadnych zmian';
	}
	else
	echo 'Podaj ID zakupu i nową zawartość pola/pól do poprawy';
	} catch(PDOException $ex) {
		echo "Wystąpił błąd bazy danych";
		}
	
	}
}
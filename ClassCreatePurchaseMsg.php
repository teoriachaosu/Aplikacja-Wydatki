<?php
class CreatePurchase extends RootOfAllClasses { 
// $con, 'pu_inp_submit', 'create_purch_msg_PL.txt', 'item_id', 'purchase_date', 'amount', 'price', 'payment', 'discount', 'place' 
	public ?int $item_id; // '?' can be null in child class UpdatePurchase
	public string $purchase_date;
	public ?float $amount;
	public ?float $price;
	public ?float $payment;
	public ?float $discount;
	public string $place;
	
public function __construct($con, $submit_button, $msg_file, $item_id, $purchase_date, $amount, $price, $payment, $discount, $place) {
	parent::__construct($con, $submit_button, $msg_file);
	!empty($_POST[$item_id]) ? $this->item_id = $_POST[$item_id] : $this->item_id = 0;
	isset($_POST[$purchase_date]) ? $this->purchase_date = htmlentities(trim($_POST[$purchase_date])) : $this->purchase_date = ''; 
	!empty($_POST[$amount]) ? $this->amount = $_POST[$amount] : $this->amount = 0;
	!empty($_POST[$price]) ? $this->price = $_POST[$price] : $this->price =0;
	!empty($_POST[$payment]) ? $this->payment = $_POST[$payment] : $this->payment = 0;
	!empty($_POST[$discount]) ? $this->discount = $_POST[$discount] : $this->discount = 0;
	isset($_POST[$place]) ? $this->place = htmlentities(trim($_POST[$place])) : $this->place = ''; 
	}
public function addPurchase(): void  {
try {
	$test_itm_id = $this->con->prepare("SELECT id_item FROM commodity WHERE id_item=?");
	$insert_qry = $this->con->prepare("INSERT INTO purchase(item_id, purchase_date, place, amount, price, payment, discount) VALUES(?, ?, ?, ?, ?, ?, ?)");
if($this->submit_button) 
	{
	// validate item id
	$test_itm_id->execute([$this->item_id]);
	if(!$test_itm_id->rowCount())	
		$this->printMsg(1, $this->msg_file); //echo 'Item ID not specified or no item with a given ID in database.';
	else 									
		{
		$cnt = 0;
		$values = [$this->item_id, $this->purchase_date, $this->place, $this->amount, $this->price, $this->payment, $this->discount];
		for($i=0; $i<=3; $i++)
			if($values[$i] != 0 || $values[$i] != '') $cnt++;
		if($cnt < 4)
			$this->printMsg(2, $this->msg_file); //echo 'Not all required fields have been filled.';
		else if(!preg_match('/^\d{4}-\d\d-\d\d$/', $this->purchase_date))
			$this->printMsg(3, $this->msg_file); //echo 'Acceptable date format is YYYY-MM-DD.';
		else 
			$insert_qry->execute($values);
		if($this->con->lastInsertId()) { 	//check if the entry has been added 
			$this->printMsg(4, $this->msg_file); //echo 'The purchase has been successfully added, ID:';
			echo $this->con->lastInsertId();
			}
		else 
			$this->printMsg(5, $this->msg_file); //echo 'Operation unsuccessful.';
		}
	}	
	else
		$this->printMsg(6, $this->msg_file); //echo 'The fields: Item ID, Amount, Date, Place must be filled.'; 
		}catch(PDOException $ex) {
			$this->printMsg(7, $this->msg_file); //echo 'Database error occurred'; 
			}
	}
}



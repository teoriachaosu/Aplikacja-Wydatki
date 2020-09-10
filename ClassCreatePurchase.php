<?php
class CreatePurchase { // $con, 'item_id', 'purchase_date', 'amount', 'price', 'payment', 'discount', 'place', 'pu_inp_submit'
	protected object $con;
	protected $pu_inp_submit;
	public ?int $item_id; // '?' can be null in child class UpdatePurchase
	public string $purchase_date;
	public ?float $amount;
	public ?float $price;
	public ?float $payment;
	public ?float $discount;
	public string $place;
	
public function __construct($con, $item_id, $purchase_date, $amount, $price, $payment, $discount, $place, $pu_inp_submit) {
	$this->con = $con;
	isset($_POST[$pu_inp_submit]) ? $this->pu_inp_submit = $_POST[$pu_inp_submit] : $this->pu_inp_submit = '';
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
if($this->pu_inp_submit) // submit button
	{
	// validate item id
	$test_itm_id->execute([$this->item_id]);
	if(!$test_itm_id->rowCount())	
		echo 'Nie podano lub brak towaru o podanym ID w bazie.';
	else 
		{
		$cnt = 0;
		$values = [$this->item_id, $this->purchase_date, $this->place, $this->amount, $this->price, $this->payment, $this->discount];
		for($i=0; $i<=3; $i++)
			if($values[$i] != 0 || $values[$i] != '') $cnt++;
		if($cnt < 4)
			echo 'Pola: ID towaru, Ilość, Data, Miejsce muszą być wypełnione. ';
		else if(!preg_match('/^\d{4}-\d\d-\d\d$/', $this->purchase_date))
			echo 'Akceptowalny format daty to RRRR-MM-DD. ';
		else 
			$insert_qry->execute($values);
		if($this->con->lastInsertId()) 	//check if the entry has been added 
			echo 'Pomyślnie dodano zakup ID '.$this->con->lastInsertId();
		else 
			echo 'Operacja nie powiodła się.';
		}
	}	
	else
		echo 'Pola: ID towaru, Ilość, Data, Miejsce muszą być wypełnione. '; 
		}catch(PDOException $ex) {
			echo 'Wystąpił błąd bazy danych '; // .$ex;
			}
	}
}



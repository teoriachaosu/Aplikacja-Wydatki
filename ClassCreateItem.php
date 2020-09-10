<?php
class CreateItem { // 'item_name1', 'id_cat1', 'it_inp_submit' 

	protected object $con;
	protected string $it_inp_submit; // submit button
	protected string $item_name;
	public int $id_cat; // id_cat in category/cat_id in commodity 
	
public function __construct($con, $item_name, $id_cat, $it_inp_submit) {
	$this->con = $con;
	isset($_POST[$it_inp_submit]) ? $this->it_inp_submit = $_POST[$it_inp_submit] : $this->it_inp_submit = '';
	isset($_POST[$item_name]) ? $this->item_name = htmlentities(trim($_POST[$item_name])) : $this->item_name = '';
	!empty($_POST[$id_cat]) ? $this->id_cat = $_POST[$id_cat] : $this->id_cat = 0; 
	// isset causes error!
	} 
	
public function addItem(): void {
try {
	$test_cat_qry = $this->con->prepare('SELECT id_cat FROM category WHERE id_cat = :id_cat');
	$test_cat_qry->bindParam(':id_cat', $this->id_cat, PDO::PARAM_INT);
	
	$insert_qry=$this->con->prepare("INSERT INTO commodity(item_name, cat_id) VALUES(:it_name, :cat_id)");
	$insert_qry->bindParam(':it_name', $this->item_name);
	$insert_qry->bindParam(':cat_id', $this->id_cat);
if ($this->it_inp_submit) // submit button 
	{
	if(!$this->item_name) 
		echo "Nie podano nazwy towaru";
	else {
		$test_cat_qry->execute();
		if(!$test_cat_qry->rowCount()) 
		echo "Nie podano lub brak kategorii o podanym ID w bazie";
		else {
			$insert_qry->execute();
			if($this->con->lastInsertId()) 
				echo 'Towar został pomyślnie dodany, ID: '.$this->con->lastInsertId();
			else
				echo "Operacja nie powiodła się";
			}
		}
	}
	else
	echo 'Oba pola muszą być wypełnione ';
	}catch(PDOException $ex){
			if ($ex->errorInfo[1] == 1062)
				echo "Podany towar już istnieje w bazie";
			else 
				echo "Wystapił błąd bazy danych "; //.$ex;
			}
	}

}

















	
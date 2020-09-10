<?php								  
class UpdateItem extends CreateItem { // 'item_name4', 'cat_id4', 'it_upd_submit', 'id_item4'
										
	public int $id_item;
	
public function __construct($con, $item_name, $id_cat, $it_inp_submit, $id_item) {
	parent::__construct($con, $item_name, $id_cat, $it_inp_submit, $id_item);
	!empty($_POST[$id_item]) ? $this->id_item = $_POST[$id_item] : $this->id_item = 0; 
	}

public function updateItem(): void {
try {
	$update_cat_id = $this->con->prepare("UPDATE commodity SET cat_id=? WHERE id_item=?");
	$update_name = $this->con->prepare("UPDATE commodity SET item_name=? WHERE id_item=?");
	
	$test_item_id = $this->con->prepare("SELECT id_item FROM commodity WHERE id_item=?");
	$test_cat_id = $this->con->prepare("SELECT id_cat FROM category WHERE id_cat=?");
if($this->it_inp_submit)
{
	$test_item_id->execute([$this->id_item]);
	
	if(!$test_item_id->rowCount())		
			echo 'Nie podano lub brak towaru o podanym ID w bazie. ';
	else {
		$test_cat_id->execute([$this->id_cat]);
		if($this->id_cat && $test_cat_id->rowCount()) 
			$update_cat_id->execute([$this->id_cat, $this->id_item]);
		else if($this->id_cat) echo 'Brak kategorii o podanym ID w bazie. ';
		
		if($this->item_name) 
			$update_name->execute([$this->item_name, $this->id_item]);
		
		if($update_cat_id->rowCount() || $update_name->rowCount())
			echo 'Pomyślnie zmodyfikowano towar ID '.$this->id_item;
		else if (!$update_cat_id->rowCount() && !$update_name->rowCount())
			echo 'Nie dokonano żadnych zmian ';
		}	
}
else
echo 'Podaj ID towaru oraz nową nazwę i/lub ID kategorii ';
	}catch(PDOException $ex){
		if ($ex->errorInfo[1] == 1062)
			echo 'Podany towar już istnieje w bazie ';
		else 
			echo "Wystąpił błąd bazy danych "; //.$ex;
			}
	}
}
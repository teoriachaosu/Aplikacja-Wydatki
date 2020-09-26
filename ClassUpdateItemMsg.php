<?php								  
class UpdateItem extends CreateItem { 
// $con, 'it_upd_submit', 'update_item_msg_PL.txt', 'item_name4', 'id_cat4',  'id_item4'										
	public int $id_item;
	
public function __construct($con, $submit_button, $msg_file, $item_name, $id_cat, $id_item) {
	parent::__construct($con, $submit_button, $msg_file, $item_name, $id_cat);
	!empty($_POST[$id_item]) ? $this->id_item = $_POST[$id_item] : $this->id_item = 0; 
	}

public function updateItem(): void {
try {
	$update_cat_id = $this->con->prepare("UPDATE commodity SET cat_id=? WHERE id_item=?");
	$update_name = $this->con->prepare("UPDATE commodity SET item_name=? WHERE id_item=?");
	
	$test_item_id = $this->con->prepare("SELECT id_item FROM commodity WHERE id_item=?");
	$test_cat_id = $this->con->prepare("SELECT id_cat FROM category WHERE id_cat=?");
if($this->submit_button)
{
	$test_item_id->execute([$this->id_item]);
	
	if(!$test_item_id->rowCount())		
		$this->printMsg(1);//echo 'Item ID not specified or no item matching given ID in database.';
	else {
		$test_cat_id->execute([$this->id_cat]);
		if($this->id_cat && $test_cat_id->rowCount()) 
			$update_cat_id->execute([$this->id_cat, $this->id_item]);
		else if($this->id_cat) 
			$this->printMsg(2);//echo 'No category matching given ID in database.';
		
		if($this->item_name) 
			$update_name->execute([$this->item_name, $this->id_item]);
		
		if($update_cat_id->rowCount() || $update_name->rowCount()) {
			$this->printMsg(3);//echo 'The item has been successfully updated, ID:'
			echo $this->id_item;
			}
		else if (!$update_cat_id->rowCount() && !$update_name->rowCount())
			$this->printMsg(4);//echo 'No changes have been made';
		}	
}
else
$this->printMsg(5);//echo 'Enter item ID and a new item name and/or category ID';
	}catch(PDOException $ex){
		if ($ex->errorInfo[1] == 1062)
			$this->printMsg(6);//echo 'The given item is already in database';
		else 
			$this->printMsg(7);//echo "Database error occurred"; //.$ex;
			}
	}
}
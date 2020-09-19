<?php	
class CreateItem extends RootOfAllClasses { 
	// $con, 'it_inp_submit', 'create_item_msg_PL.txt', 'item_name1', 'id_cat1'
	protected string $item_name;
	public int $id_cat; // id_cat in category/cat_id in commodity 

public function __construct($con, $submit_button, $msg_file, $item_name, $id_cat) {
	parent::__construct($con, $submit_button, $msg_file);
	isset($_POST[$item_name]) ? $this->item_name = htmlentities(trim($_POST[$item_name])) : $this->item_name = '';
	!empty($_POST[$id_cat]) ? $this->id_cat = $_POST[$id_cat] : $this->id_cat = 0; 
	$this->msg_file = $msg_file;
	} 

public function addItem(): void {
try {
	$test_cat_qry = $this->con->prepare('SELECT id_cat FROM category WHERE id_cat = :id_cat');
	$test_cat_qry->bindParam(':id_cat', $this->id_cat, PDO::PARAM_INT);
	
	$insert_qry=$this->con->prepare("INSERT INTO commodity(item_name, cat_id) VALUES(:it_name, :cat_id)");
	$insert_qry->bindParam(':it_name', $this->item_name);
	$insert_qry->bindParam(':cat_id', $this->id_cat);
if ($this->submit_button) 
	{
	if(!$this->item_name) 
		$this->printMsg(1); // echo "Item name not specified"; (msg from txt file)
	else {
		$test_cat_qry->execute();
		if(!$test_cat_qry->rowCount()) 
		$this->printMsg(2); //echo "Category ID not specified or invalid";
		else {				
			$insert_qry->execute();
			if($this->con->lastInsertId()) { 
				$this->printMsg(3); //echo 'Item successfully added, ID:
				echo $this->con->lastInsertId();  
				}
			else
				$this->printMsg(4); //echo "Operation was unsuccessful";
			}
		}
	}
	else
	$this->printMsg(5); //echo 'Both fields must be filled';
	}catch(PDOException $ex){
			if ($ex->errorInfo[1] == 1062)
				$this->printMsg(6); //;echo "The item you entered is already in base";
			else 
				$this->printMsg(7); //echo "Database error occurred"; //.$ex;
			}
	}

}

















	
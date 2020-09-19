<?php
class RootOfAllClasses {
	protected object $con; // PDO connection object
	protected string $submit_button; // form submit button name
	protected string $msg_file; // name of txt file with messages for user

public function __construct($con, $submit_button, $msg_file) {
	$this->con = $con;
	isset($_POST[$submit_button]) ? $this->submit_button = $_POST[$submit_button] : $this->submit_button = '';
	$this->msg_file = $msg_file;
	} 

public function printMsg(int $row): void {
	if($this->msg_file) {
		$lines=file($this->msg_file); //msg text file into array
		$cut = strpos($lines[$row], '/'); 
		echo substr($lines[$row], $cut+1);
		}
	}
}	
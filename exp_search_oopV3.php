<!DOCTYPE HTML>
<html lang="pl">
<head>
<meta charset="utf-8"/>
<meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1"/>
<title>wyszukiwanie zakupów i towarów</title>
<link rel="stylesheet" type="text/css" href="exp_style2V3.css"/>
</head>
<body>
<script> 
if ( window.history.replaceState ) { // prevent form resubmission on page refresh
	window.history.replaceState( null, null, window.location.href );
    }
</script>
<?php require_once 'exp_db_data.php'; 
require_once 'ClassPurchaseSearchV3.php';
require_once 'ClassItemSearch.php';
require_once 'ClassShowInfo.php'; // form fields
$pur_search = new PurchaseSearch($con, 'start_date', 'end_date', 'place', 'itm_name', 'orderby_form', 'asc', 'total_amt', 'cat_id', 'pur_id', 'pu_srch_submit');
$item_search = new ItemSearch($con, 'orderby_form1', 'searchby_form', 'it_srch_form', 'asc1', 'it_srch_submit');
$item_info = new ShowInfo($con, 'it_srch_info', 'it_search_info.txt'); // info button, readme file
$purchase_info = new ShowInfo($con, 'pu_srch_info', 'pu_search_info.txt'); 
?>
<div id="baner1"> 
<form action="" method="post">
<table id="tabl2">
<tr><td colspan="2">WYSZUKIWANIE ZAKUPU</td>
<td colspan="4"><input type="checkbox" name="clr_form"/> Nie czyść pól tekstowych po wyszukaniu</td></tr>

<tr><td>Data od:</td>
<td style "text-align:left"><input type="text" name="start_date" size="8" value="<?php keepData($pur_search->start_date, 'clr_form');?>" /></td>
<td style="text-align:right">Data do:</td>
<td><input type="text" name="end_date" size="8" value="<?php keepData($pur_search->end_date, 'clr_form');?>"/></td>
<td style="text-align:right"><input type="submit" name="pu_srch_info" value="  ?  " id="button0"/></td>
<td><input type="submit" name="pu_srch_submit" value="  Szukaj  " id="button0" /></td></tr>

<tr><td>Miejsce:</td>
<td><input type="text" name="place" value="<?php keepData($pur_search->placenosp, 'clr_form');?>"/></td>
<td style="text-align:right">ID kategorii:</td>
<td><input type="number" name="cat_id" size="3" min="1" max="999" step="1" /></td>
<td colspan="2"> ID zakupu: <input type="number" name="pur_id" size="5" min="1" max="99999" step="1" /></td></tr>

<tr><td>Nazwa:</td>
<td><input type="text" name="itm_name" value="<?php keepData($pur_search->itm_namenosp, 'clr_form');?>"/></td>
<td><input type="checkbox" name="total_amt"/>Sumuj ilość</td>
<td colspan="1"></td></tr>

<tr><td colspan="2" style="text-align:left">Porządkuj według: <select name="orderby_form">
<option value="purchase_date">Data</option>
<option value="item_name">Nazwa</option>
<option value="id_purchase">ID zakupu</option>
<option value="payment">Zapłata</option>
<option value="price">Cena</option>
<option value="discount">Rabat</option>
<option value="amount">Ilość</option>
</select></td>
<td><input type="checkbox" name="asc"/> Rosnąco</td></tr>

</table>
</form>
</div> 

<div id="baner2">
<form method="post" action="">
WYSZUKIWANIE TOWARU (puste pole dla wszystkich towarów)
<table id="tabl2">
<tr><td colspan="3"><input type="text" name="it_srch_form"/></td><td><input type="submit" name="it_srch_info" value="  ?  " id="button0"/></td>
<td style="text-align:right"><input type="submit" name="it_srch_submit" value="  Szukaj  " id="button0"/></td></tr>
<tr><td colspan="2" style="text-align:left">Wyszukaj po:</td><td><colspan="2" style="text-align:left">Porządkuj według:</td>
<td colspan="2"><input type="checkbox" name="asc1"> Rosnąco</td></tr>

<tr><td colspan="2" style="text-align:left">
<select name="searchby_form">
<option value="item_name">Nazwa</option>
<option value="id_item">ID towaru</option>
<option value="cat_id">ID kategorii</option>
</select></td>
<td colspan="1" style="text-align:right">
<select name="orderby_form1">
<option value="item_name">Nazwa</option>
<option value="id_item">ID towaru</option>
<option value="cat_id">ID kategorii</option>
</select></td>
<td colspan="2"></td></tr>
<tr><td colspan="5"><span style="padding:10px;"><a href="exp_input_update_oopV2.php" target="_blank">
Otwórz wprowadzanie i poprawianie w nowej karcie</a></span></td></tr>
</table>
</form>

</div>

<div id="space">
<?php
//LIST CATEGORIES
$item_info->listCategories();
?>
</div>
<div id="result2">
<!--br><b><a href="exp_input_update_oopV2.php">Powrót</a></b-->
<?php
//LIST PLACES OF PURCHASE PRESENT IN BASE
$item_info->listPlaces();
?>

</div>
<div id="result3">
<?php
//-------SEARCHES-----------
$purchase_info->PrintHelp();
$item_info->PrintHelp();
$pur_search->purSearch();
$item_search->itemSearch();
?>
</div>

</body>
</html>

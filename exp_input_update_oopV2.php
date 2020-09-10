<!DOCTYPE HTML>
<html lang="pl">
<head>
<meta charset="utf-8"/>
<meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1"/>
<title>Wydatki_baza</title>
<link rel="stylesheet" type="text/css" href="exp_style1V2.css"/>

</head>
<body>
<script> 
if ( window.history.replaceState ) { // prevent form resubmission on page refresh
	window.history.replaceState( null, null, window.location.href );
    }
</script>
<?php 
require_once 'exp_db_data.php'; 
require_once 'ClassShowInfo.php';
require_once 'ClassCreateItem.php';
require_once 'ClassUpdateItem.php';
require_once 'ClassCreatePurchase.php';
require_once 'ClassUpdatePurchase.php';
$create_item = new CreateItem($con, 'item_name1', 'id_cat1', 'it_inp_submit'); // form fields, submit button
$update_item = new UpdateItem($con, 'item_name4', 'id_cat4', 'it_upd_submit', 'id_item4');
$create_purchase = new CreatePurchase($con, 'item_id', 'purchase_date', 'amount', 'price', 'payment', 'discount', 'place', 'pu_inp_submit');
$update_purchase = new UpdatePurchase($con, 'item_id1', 'purchase_date1', 'amount1', 'price1', 'payment1', 'discount1', 'place1', 'pu_upd_submit', 'id_purchase');
$pu_inp_info = new ShowInfo($con, 'pu_inp_info', 'pu_inp_info.txt'); 
$pu_upd_info = new ShowInfo($con, 'pu_upd_info', 'pu_upd_info.txt'); 
$it_inp_info = new ShowInfo($con, 'it_inp_info', 'it_inp_info.txt');
$it_upd_info = new ShowInfo($con, 'it_upd_info', 'it_upd_info.txt');  
?>
<div id="baner1">
<span style="padding:10px; font-size:130%; font-family:Cambria; color:#f0e1c2;">Aplikacja Wydatki</span><span style="font-family:Cambria; color:#f0e1c2">(c) M.Białowąs 2020</span></br>
<!--span style="padding:10px; color:#f0e1c2">Moduł wprowadzania i poprawiania v1.50</span--></br>
<span style="padding:10px;"><a href="exp_search_oopV3.php" target="_blank">Otwórz wyszukiwanie w nowej karcie</a></span><br>

<div id="inputp">

<!--------ADDING A NEW PURCHASE ENTRY------->
<p><?php $create_purchase->addPurchase();?></p>

</div>
</div>
<div id="baner2">
<form method="post" action="exp_input_update_oopV2.php">
<span style="color:#006666;">WPROWADZANIE TOWARU</span>
<table id="tabl2">
<tr><td>Nazwa:</td>
<td><input type="text" name="item_name1"/></td>
<td><input type="submit" value="Dodaj" name="it_inp_submit" id="button1"/></td></tr>
<tr><td>ID kategorii:</td>
<td><input type="number" name="id_cat1" size="3" min="1" max="999"/></td>
<td><input type="submit" name="it_inp_info" value="  ?  " id="button1"/></td></tr>
</table>
</form>
</div>
<div id="baner3">
<form method="post">
<span style="color:#993300;">POPRAWIANIE TOWARU</span>
<table id="tabl2">
<tr><td>ID towaru:</td><td colspan="2"><input type="number" name="id_item4" size="4" min="1" max="9999"/></td></tr>
<tr><td>Nazwa:</td><td colspan="2"><input type="text" name="item_name4"/></td>
<td style="text-align:right"><input type="submit" name="it_upd_submit" value="Zastąp" id="button2"/></td></tr>
<tr><td>ID kategorii:</td><td colspan="2"><input type="number" name="id_cat4" size="3" min="1" max="999"/></td>
<td><input type="submit" name="it_upd_info" value="  ?  " id="button2"/></td></tr>
</table>
</form>
</div>
<div id="formwpr">
<form method="post" action="exp_input_update_oopV2.php">
<span style="color:#006666;">WPROWADZANIE ZAKUPU</span>
<table id="tabl2">

<tr><td>ID towaru:</td><td colspan="1"><input type="number" name="item_id" size="5" min="1" max="9999" step="1"/></td>
<!--td><input type="submit" name="pu_inp_info" value="  ?  " id="button1"/></td--></tr>
<tr><td>Ilość:</td>
<td colspan="1"><input type="number" name="amount" size="5" min="0.001" max="10000" step="0.001"/></td></tr>
<tr><td>Cena:</td>
<td colspan="1"><input type="number" name="price" size="10" min="0.00" max="100000" step="0.01"/></td></tr>
<tr><td>Zapłata:</td>
<td colspan="1"><input type="number" name="payment" size="10" min="0.00" max="100000" step="0.01"/></td></tr>
<tr><td>Rabat:</td>
<td colspan="1"><input type="number" name="discount" size="10" min="0.00" max="100000" step="0.01"/></td></tr>
<tr><td>Data:</td>
<td colspan="1"><input type="text" name="purchase_date" size="8" value="<?php keepData($create_purchase->purchase_date, 'clr_form1'); ?>"/></td></tr>
<tr><td>Miejsce:</td>
<td colspan="1"><input type="text" name="place" value="<?php keepData($create_purchase->place, 'clr_form1'); ?>"/></td></tr>
<tr><td colspan="2" style="text-align:left"><input type="checkbox" name="clr_form1"/> Nie czyść daty i miejsca
 <input type="submit" name="pu_inp_info" value="  ?  " id="button1"/>
 <input type="submit" name="pu_inp_submit" value="Dodaj" id="button1"/></td></tr>
</table>
</form>
<form method="post">
<span style="color:#993300;">POPRAWIANIE ZAKUPU</span>
<table id="tabl2">
<tr><td>ID zakupu:</td><td colspan="2"><input type="number" name="id_purchase" size="5" min="1" max="99999" step="1"/></td></tr>
<tr><td>ID towaru:</td><td colspan="2"><input type="number" name="item_id1" size="5" min="1" max="9999" step="1"/></td></tr>
<tr><td>Ilość:</td><td colspan="2"><input type="number" name="amount1" size="5" min="0.001" max="10000" step="0.001"/></td></tr>
<tr><td>Cena:</td><td colspan="2"><input type="number" name="price1" size="10" min="0.00" max="100000" step="0.01"/></td></tr>
<tr><td>Zapłata:</td><td colspan="2"><input type="number" name="payment1" size="10" min="0.00" max="100000" step="0.01"/></td></tr>
<tr><td>Rabat:</td><td colspan="2"><input type="number" name="discount1" size="10" min="0.00" max="100000" step="0.01"/></td></tr>
<tr><td>Data:</td><td colspan="2"><input type="text" name="purchase_date1" size="8"/></td></tr>
<tr><td>Miejsce:</td><td colspan="2"><input type="text" name="place1"/></td></tr>
<tr><td></td><td style="text-align:right"><input type="submit" name="pu_upd_info" value="  ?  " id="button2"/></td>
<td style="text-align:right"><input type="submit" name="pu_upd_submit" value="Zastąp" id="button2"/></td></tr>
</table>
</form>
<div id="update">
<?php
//------PURCHASE CORRECTION-----
$update_purchase->updatePurchase();
?>
</div>
</div>

<div id="space">
<?php
//-------LIST PLACES OF PURCHASE PRESENT IN BASE-----
$pu_inp_info->listPlaces();
?>
</div>
<div id="result2">

<!------------ADD NEW GOODS TO BASE------>
<span style='color:#006666; font-weight:bold'><?php $create_item->addItem();?></span></br>

<!--------LIST CATEGORIES --------->
<?php $pu_inp_info->listCategories();?>

</div>
<div id="result3">
<!------------ITEM UPDATE------------>
<span style='color:#993300; font-weight:bold'><?php $update_item->updateItem();?></span>
<?php 
$pu_inp_info->PrintHelp(); 
$pu_upd_info->PrintHelp(); 
$it_inp_info->printHelp();
$it_upd_info->printHelp();
?>

</div>

</body>
</html>

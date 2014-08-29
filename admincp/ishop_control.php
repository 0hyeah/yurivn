<?php
  /*======================================================================*\
   || #################################################################### ||
   || # IShop                                                            # ||
   || # Copyright Blaine0002(C) 2005 All rights reserved.                # ||
   || # ---------------------------------------------------------------- # ||
   || # For use with vBulletin Version 3.5.4                             # ||
   || # http://www.vbulletin.com | http://www.vbulletin.com/license.html # ||
   || # Discussion and support available at                              # ||
   || # http://www.vbulletin.org/forum/showthread.php?t=100344           # ||
   || #################################################################### ||
   \*======================================================================*/
error_reporting(E_ALL & ~E_NOTICE);
define('THIS_SCRIPT', 'icashadmin');
require_once('./global.php');   
   
function ParseInputs($val){
	if(is_int($val))
	{
	$val = intval($val);
	return $val;
	} else {
	$val = htmlspecialchars_uni(trim(addslashes($val)));
	return $val;
	}
}

if ($_GET['act'] == "main") {
print_cp_header("IShop Control");

print_form_header('', '');
print_table_header("Current Items In IShop", 18);


$all_cats = $db->query("select * from " . TABLE_PREFIX . "itemshop_cat order by cname");
while($all_c = $db->fetch_array($all_cats)){
$AllCats[$all_c['cid']]=$all_c;
}

$all_items = $db->query("select * from " . TABLE_PREFIX . "itemshop_items order by `cost`");
while($all_i = $db->fetch_array($all_items)){
$AllItems[]=$all_i;
}





if(is_array($AllCats)){
foreach($AllCats as $TheCat){


if($TheCat['cname']!=""){
print_table_header("<b>{$TheCat['cname']}</b>",18);
print_cells_row(array(
"<b>Icon/Name</b>",
"<b>Stock</b>",
"<b>Cost</b>",
"<b>Stat Fix</b>",
"<b>Edit</b>",
"<b>Delete</b>",),'thead','','',1);
print_cells_row("<b>{$TheCat['cname']}</b>", 10, '', -1);


if(is_array($AllItems)){
foreach($AllItems as $Data){
if($Data['type']==$TheCat['cid']){

if($Data['stock'] < 1){
$Data['stock_x']="<br /><b><span style='color:red;'><a href='?act=restock&id={$Data['id']}' style='color:red;' title='Auto Adds 50 To Stock'>Restock!</a></b></span>";}else{$Data['stock_x']="";}






print_cells_row(array("<img src='{$vbulletin->options['bburl']}/ishop/items/{$Data['img']}' alt='{$Data['name']}'><br/><b>{$Data['name']}</b>",
"{$Data['stock']}{$Data['stock_x']}",
"{$Data['cost']}",
"<a href='?act=Recitems&id={$Data['id']}' title='Will fix the sold stat incase people have sold it once bought'>Stat Fix</a>",
"<a href='?act=edit&id={$Data['id']}'>Edit</a>",
"<a href='?act=delete&id={$Data['id']}'>Delete</a>"),
 '','','',1);
}}}
print_table_header("<a href='?act=add&cat={$TheCat['cid']}'>Add New Item To {$TheCat['cname']} Category</a>",18);

}}}

print_table_footer();
	print_cp_footer();
	exit;
}


// ###################### Add New Item ########################
if ($_GET['act'] == "add") {
print_cp_header("IShop Control");
$gcat=$db->query("select * from " . TABLE_PREFIX . "itemshop_cat");
$holdb="";
while($cat=$db->fetch_array($gcat)){
if($cat['cid']==$_GET['cat']){
$holdb.="<option value='".$cat['cid']."' selected>".$cat['cname']."</option>";
} else {
$holdb.="<option value='".$cat['cid']."'>".$cat['cname']."</option>";
}}
// Images
$handle = opendir("ishop/items");
$icons = "<option value=''>Select An Item Image</option>";
while ($icon = readdir($handle)) {
if(preg_match("/(.jpg|.gif|.png|.bmp)/",$icon)) {
if($icon != '.' || $icon  != '..') {
$icons .= "<option value='".$icon."'>".$icon."</option>";
}}}
echo "<script>
function PreviewImage(jZk){
if(jZk==''){
document.images['preview'].style.display='none'
} else {
jSrc='{$vboptions[bburl]}/ishop/items/'+jZk
document.images['preview'].src=jSrc
document.images['preview'].style.display=''
}
}
</script>";
	print_form_header('ishop_control', 'do_add_item');
	print_table_header("Add New IShop Item");
	print_input_row("Item Name", 'name','');
	print_input_row("Item Description", 'desc','');
	print_input_row("Item Cost", 'cost','');
	print_label_row("Item Type <dfn>These are the created shop categories you have made</dfn>", '
		<select name="type" class="bginput">'.$holdb.'</select>');
	print_label_row("Item Image <dfn><img src='{$vboptions[bburl]}/ishop/items/blue crosstone.gif' style='display:none;' name='preview'></dfn>", '
		<select name="img" class="bginput" onchange="PreviewImage(this.options[this.options.selectedIndex].value)">'.$icons.'</select>');
	print_input_row("Current Stock <dfn>If at 0, Unable to buy<dfn>", 'stock','50');
	print_submit_row("Add New IShop Item", 0);
	print_cp_footer();
	exit;
}

// ###################### Edit Item ########################
if ($_GET['act'] == "edit") {
print_cp_header("IShop Control");
// Check item exists
$itemx=$db->query("select * from " . TABLE_PREFIX . "itemshop_items where id='".$_GET['id']."'");
$item=$db->fetch_array($itemx);
if($item['id']==""){
echo "Unable to find item inside database. [ <a href='javascript:history.go(-1)'>Back</a> ]";
exit;
}
$gcat=$db->query("select * from " . TABLE_PREFIX . "itemshop_cat");
$holdb="";
while($cat=$db->fetch_array($gcat)){
if($cat['cid']==$item['type']){
$holdb.="<option value='".$cat['cid']."' selected>".$cat['cname']."</option>";
} else {
$holdb.="<option value='".$cat['cid']."'>".$cat['cname']."</option>";
}}
// Images
$handle = opendir("ishop/items");
$icons = "<option value=''>Select An Item Image</option>";
while ($icon = readdir($handle)) {
if(preg_match("/(.jpg|.gif|.png|.bmp)/",$icon)) {
if($icon != '.' || $icon  != '..') {
if($icon==$item['img']){
$icons .= "<option value='".$icon."' selected>".$icon."</option>";
} else {
$icons .= "<option value='".$icon."'>".$icon."</option>";
}}}}
echo "<script>
function PreviewImage(jZk){
if(jZk==''){
document.images['preview'].style.display='none'
} else {
jSrc='{$vboptions[bburl]}/ishop/items/'+jZk
document.images['preview'].src=jSrc
document.images['preview'].style.display=''
}
}
</script>"; 
	print_form_header('ishop_control', 'do_edit_item');
	print_table_header("Edit IShop Item ".$item['name']);

echo "<input type='hidden' name='id' value='".$item['id']."'>";
	print_input_row("Item Name", 'name',$item['name']);
	print_input_row("Item Description", 'desc',$item['desc']);
	print_input_row("Item Cost", 'cost',$item['cost']);
	print_label_row("Item Type <dfn>These are the created shop categories you have made</dfn>", '
		<select name="type" class="bginput">'.$holdb.'</select>');
	print_label_row("Item Image <dfn><img src='{$vboptions[bburl]}/ishop/items/blue crosstone.gif' style='display:none;' name='preview'></dfn>", '
		<select name="img" class="bginput" onchange="PreviewImage(this.options[this.options.selectedIndex].value)">'.$icons.'</select>');
	print_input_row("Current Stock <dfn>If at 0, Unable to buy<dfn>", 'stock',$item['stock']);
	print_submit_row("Save Edits", 0);
echo "<script>
// Edit form - run picture
jSrc='{$vboptions[bburl]}/ishop/items/{$item['img']}'
document.images['preview'].src=jSrc
document.images['preview'].style.display=''
</script>";
	print_cp_footer();
	exit;

}

// ###################### Delete Item #######################
if($_GET['act']=="delete"){
print_cp_header("IShop Control");
// Check item exists
$itemx=$db->query("select * from " . TABLE_PREFIX . "itemshop_items where id='".$_GET['id']."'");
$item=$db->fetch_array($itemx);
if($item['id']==""){
print_cp_header("IShop Control");
echo "Unable to find item inside database. [ <a href='javascript:history.go(-1)'>Back</a> ]";
exit;
}
// Item found - verify delete

	print_form_header('ishop_control', 'do_delete_item');
	print_table_header("Delete IShop Item ".$item['name']);
// hidden id
echo "<input type='hidden' name='id' value='".$item['id']."'>";
	print_yes_no_row("Are you sure you want to delete this item? ({$item['name']})<dfn>It cannot be undone</dfn>", 'verify','');
	print_submit_row("Delete Item", 0);
	print_cp_footer();
	exit;
}

// ##################### Do Delete Item ######################
if ($_POST['do'] == "do_delete_item") {
print_cp_header("IShop Control");
$itemx=$db->query("select * from " . TABLE_PREFIX . "itemshop_items where id='".$_POST['id']."'");
$item=$db->fetch_array($itemx);
if($item['id']==""){
echo "Unable to find item inside database. [ <a href='javascript:history.go(-1)'>Back</a> ]";
exit;
}
if($_POST['verify']!=1){
	define('CP_REDIRECT', 'ishop_control.php?act=main');
	print_stop_message('ishop_item_ndeleted');
} else {

$cache_upgrade = array();



$db->query("delete from " . TABLE_PREFIX . "itemshop_stock where item_id='{$_POST['id']}'");
$db->query("delete from " . TABLE_PREFIX . "itemshop_items where id='".$_POST['id']."'");

	define('CP_REDIRECT', 'ishop_control.php?act=cStats&r=main');
	print_stop_message('ishop_item_deleted');
}
}

// ###################### Do Edit Item #######################
if ($_POST['do'] == "do_edit_item") {
print_cp_header("IShop Control");
// Check item exists
$itemx=$db->query("select * from " . TABLE_PREFIX . "itemshop_items where id='".$_POST['id']."'");
$item=$db->fetch_array($itemx);
if($item['id']==""){
echo "Unable to find item inside database. [ <a href='javascript:history.go(-1)'>Back</a> ]";
exit;
}
// +++++++++++++++++
// Parse Vars
// +++++++++++++++++
$_POST['name']=ParseInputs($_POST['name']);
$_POST['desc']=ParseInputs($_POST['desc']);
$_POST['img']=ParseInputs($_POST['img']);





$db->query("update " . TABLE_PREFIX . "itemshop_items set name='".$_POST['name']."',`desc`='".$_POST['desc']."',img='".$_POST['img']."',stock='".$_POST['stock']."',type='".$_POST['type']."',cost='".$_POST['cost']."'
where id='".$item['id']."'");
	define('CP_REDIRECT', 'ishop_control.php?act=cStats&r=main');
	print_stop_message('ishop_item_edited');
}

// ###################### Do Add Item ########################
if ($_POST['do'] == "do_add_item") {
print_cp_header("IShop Control");
// +++++++++++++++++
// Parse Vars
// +++++++++++++++++
$_POST['name']=ParseInputs($_POST['name']);
$_POST['desc']=ParseInputs($_POST['desc']);
$_POST['img']=ParseInputs($_POST['img']);

$db->query("update " . TABLE_PREFIX . "itemshop_cat set citems=citems+'1' where cid='{$_POST['type']}'");
$db->query("insert into " . TABLE_PREFIX . "itemshop_items values('','".$_POST['img']."','".$_POST['name']."','".$_POST['desc']."','".$_POST['cost']."','".$_POST['type']."','0','".$_POST['stock']."')");

	define('CP_REDIRECT', 'ishop_control.php?act=main');
	print_stop_message('ishop_item_added');
}

// ################### Restock Item #####################
if($_GET['act']=="restock"){
print_cp_header("IShop Control");
// Check item exists
$itemx=$db->query("select * from " . TABLE_PREFIX . "itemshop_items where id='".$_GET['id']."'");
$item=$db->fetch_array($itemx);
if($item['id']==""){
echo "Unable to find item inside database. [ <a href='javascript:history.go(-1)'>Back</a> ]";
exit;
}

$db->query("update " . TABLE_PREFIX . "itemshop_items set stock=stock+'50' where id='{$item['id']}'");
	define('CP_REDIRECT', 'ishop_control.php?act=main');
	print_stop_message('ishop_item_restock');
}


// ################### Recount Cat Stats #####################
if($_GET['act']=="cStats"){
print_cp_header("IShop Control");

$call=$db->query("SELECT MAX(cid) as max_cats FROM " . TABLE_PREFIX . "itemshop_cat");
$r = $db->fetch_array($call);
for($i=1;$i<=$r['max_cats'];$i++){
$gcatx=$db->query("select * from " . TABLE_PREFIX . "itemshop_cat where cid='{$i}'");
$TheCat=$db->fetch_array($gcatx);
if($TheCat['cname']!=""){
$gitemx=$db->query("select * from " . TABLE_PREFIX . "itemshop_items where type='{$i}' order by `cost`");
$db->query("update " . TABLE_PREFIX . "itemshop_cat set citems='0' where cid='{$i}'"); // first set to 0 [all]
while($Data=$db->fetch_array($gitemx)){
$db->query("update " . TABLE_PREFIX . "itemshop_cat set citems=citems+'1' where cid='{$i}'");
}}}

$db->query("update " . TABLE_PREFIX . "itemshop_cat set cprofit='0',csold='0'");
$callx=$db->query("SELECT MAX(uid) as max_own FROM " . TABLE_PREFIX . "itemshop_stock");
$rx = $db->fetch_array($callx);
for($ix=1;$ix<=$rx['max_own'];$ix++){
$gcatxx=$db->query("select * from " . TABLE_PREFIX . "itemshop_stock where uid='{$ix}'");
$TheOwn=$db->fetch_array($gcatxx);
$gti=$db->query("select * from " . TABLE_PREFIX . "itemshop_items where id='{$TheOwn['item_id']}'");
$boughtitem=$db->fetch_array($gti);
$db->query("update " . TABLE_PREFIX . "itemshop_cat set cprofit=cprofit+'{$boughtitem['cost']}',csold=csold+'1' where cid='{$boughtitem['type']}'");
}

if($_GET['r']=="" || !$_GET['r'])
	{
	$rd='ishop_control.php?act=cat';
	} else {
	$rd='ishop_control.php?act='.$_GET['r'];
	}



	define('CP_REDIRECT', $rd);
	print_stop_message('ishop_cat_stats');
}

// ################### Recount Item Sold Stats #####################
if($_GET['act']=="Recitems"){
print_cp_header("IShop Control");
$itemx=$db->query("select * from " . TABLE_PREFIX . "itemshop_items where id='".$_GET['id']."'");
$item=$db->fetch_array($itemx);
if($item['id']==""){
echo "Unable to find item inside database. [ <a href='javascript:history.go(-1)'>Back</a> ]";
exit;
}


$i=0;
$gbought=$db->query("select * from " . TABLE_PREFIX . "itemshop_stock where item_id='{$item['id']}'");
while($bought=$db->fetch_array($gbought)){
$i++;
}
$db->query("update " . TABLE_PREFIX . "itemshop_items set sold='{$i}' where id='{$item['id']}'");

	define('CP_REDIRECT', "ishop_control.php?act=main");
	print_stop_message('ishop_item_stats');
}

// ################### Category Control #####################
if($_GET['act']=="cat"){
print_cp_header("IShop Control");

print_form_header('', '');
print_table_header("Current Categories", 7);
print_cells_row(array(
"<b>Name</b>",
"<b>Description</b>",
"<b>Total Items</b>",
"<b>Total Sold</b>",
"<b>Total Profit</b>",
"<b>Edit</b>",
"<b>Delete</b>",),'thead','','',1);
$get=$db->query("select * from " . TABLE_PREFIX . "itemshop_cat order by `cid`");
while($Data=$db->fetch_array($get)){
print_cells_row(array(
"<b>{$Data['cname']}</b>",
"{$Data['cdescr']}",
"{$Data['citems']}",
"{$Data['csold']}",
"{$Data['cprofit']}",
"<a href='?act=editcat&id={$Data['cid']}'>Edit</a>",
"<a href='?act=deletecat&id={$Data['cid']}'>Delete</a>"),
 '','','',1);
}
print_table_header("<a href='?act=addcat'>Add New Category</a>",7);


print_table_footer();
	print_cp_footer();
	exit;
}

// ################### Category Add #####################
if($_GET['act']=="addcat"){
print_cp_header("IShop Control");
	print_form_header('ishop_control', 'do_add_cat');
	print_table_header("Add New Category");
	print_input_row("Cat Name", 'cname','');
	print_input_row("Cat Description", 'cdescr','');
	print_submit_row("Add Category", 0);
	print_cp_footer();
	exit;
}

// ################### Category Edit ####################
if($_GET['act']=="editcat"){
print_cp_header("IShop Control");
$catx=$db->query("select * from " . TABLE_PREFIX . "itemshop_cat where cid='".$_GET['id']."'");
$cat=$db->fetch_array($catx);
if($cat['cid']==""){
echo "Unable to find category inside database. [ <a href='javascript:history.go(-1)'>Back</a> ]";
exit;
}
// Cat exists - edit form
	print_form_header('ishop_control', 'do_edit_cat');
// hidden id
echo "<input type='hidden' name='id' value='".$cat['cid']."'>";
	print_table_header("Edit Category ".$cat['cname']);
	print_input_row("Cat Name", 'cname',$cat['cname']);
	print_input_row("Cat Description", 'cdescr',$cat['cdescr']);
	print_submit_row("Save Edits", 0);
	print_cp_footer();
	exit;
}

// ################# Do Category Add ####################
if($_POST['do']=="do_add_cat"){
print_cp_header("IShop Control");
// +++++++++++++++++
// Parse Vars
// +++++++++++++++++
$_POST['cname']=ParseInputs($_POST['cname']);
$_POST['cdescr']=ParseInputs($_POST['cdescr']);
$db->query("insert into " . TABLE_PREFIX . "itemshop_cat values('','{$_POST['cname']}','{$_POST['cdescr']}','0','0','0')");
	define('CP_REDIRECT', 'ishop_control.php?act=cat');
	print_stop_message('ishop_cat_added');

}

// ################# Do Category Edit ####################
if($_POST['do']=="do_edit_cat"){
print_cp_header("IShop Control");
$catx=$db->query("select * from " . TABLE_PREFIX . "itemshop_cat where cid='".$_POST['id']."'");
$cat=$db->fetch_array($catx);
if($cat['cid']==""){
echo "Unable to find category inside database. [ <a href='javascript:history.go(-1)'>Back</a> ]";
exit;
}
// +++++++++++++++++
// Parse Vars
// +++++++++++++++++
$_POST['cname']=ParseInputs($_POST['cname']);
$_POST['cdescr']=ParseInputs($_POST['cdescr']);
$db->query("update " . TABLE_PREFIX . "itemshop_cat set cname='".$_POST['cname']."',cdescr='".$_POST['cdescr']."' where cid='".$_POST['id']."'");
	define('CP_REDIRECT', 'ishop_control.php?act=cat');
	print_stop_message('ishop_cat_edit');

}

// ###################### Delete Cat #######################
if($_GET['act']=="deletecat"){
print_cp_header("IShop Control");
$catx=$db->query("select * from " . TABLE_PREFIX . "itemshop_cat where cid='".$_GET['id']."'");
$cat=$db->fetch_array($catx);
if($cat['cid']==""){
echo "Unable to find category inside database. [ <a href='javascript:history.go(-1)'>Back</a> ]";
exit;
}
$options="<option value='delete'>Delete all items inside category</option>";
$options.="<option value='move'>Move items to selected category below</option>";
$gcat=$db->query("select * from " . TABLE_PREFIX . "itemshop_cat");
$thecats="";
while($caty=$db->fetch_array($gcat)){
if($caty['cid']==$cat['cid']){
} else {
$thecats.="<option value='".$caty['cid']."'>".$caty['cname']."</option>";
}}


	print_form_header('ishop_control', 'do_delete_cat');
	print_table_header("Delete Category ".$cat['cname']);
echo "<input type='hidden' name='id' value='".$cat['cid']."'>";
	print_yes_no_row("Are you sure you want to delete this category? ({$cat['cname']})<dfn>It cannot be undone</dfn>", 'verify','');
	print_label_row("Action For Items Inside This Cat ", '
		<select name="choice" class="bginput" onchange="javascript:Choice(this.options[this.options.selectedIndex].value)">'.$options.'</select>');
print_label_row("<div id='mcatx'>Category To Move Items Into</div>", '
		<div id="mcatx2"><select name="mcat" class="bginput">'.$thecats.'</select></div>');
	print_submit_row("Delete Category", 0);

echo "<script>
function Choice(e){
	if(e=='delete'){
	mcatx.style.display='none'
	mcatx2.style.display='none'
	} else {
	mcatx.style.display=''
	mcatx2.style.display=''
	}
}

mcatx.style.display='none'
mcatx2.style.display='none'
</script>";

	print_cp_footer();
	exit;
}


// ##################### Do Delete Cat ######################
if ($_POST['do'] == "do_delete_cat") {
print_cp_header("IShop Control");
$catx=$db->query("select * from " . TABLE_PREFIX . "itemshop_cat where cid='".$_POST['id']."'");
$cat=$db->fetch_array($catx);
if($cat['cid']==""){
echo "Unable to find category inside database. [ <a href='javascript:history.go(-1)'>Back</a> ]";
exit;
}

$cache_upgrade = array(); 

if($_POST['verify']!=1){
	define('CP_REDIRECT', 'ishop_control.php?act=cStats');
	print_stop_message('ishop_cat_ndeleted');
} else {
	if($_POST['choice']=="delete"){
		$get_these_i = $db->query("select * from " . TABLE_PREFIX . "itemshop_items where type='{$cat['cid']}'");
		while($thisI = $db->fetch_array($get_these_i)){
		$theseItems[] = $thisI;
		}
		
		foreach($theseItems as $current_item){
		$db->query("delete from " . TABLE_PREFIX . "itemshop_stock where item_id='{$current_item['id']}'");
		}


	$db->query("delete from " . TABLE_PREFIX . "itemshop_items where type='".$cat['cid']."'");
	} else {

		$get_these_i = $db->query("select * from " . TABLE_PREFIX . "itemshop_items where type='{$cat['cid']}'");
		while($thisI = $db->fetch_array($get_these_i)){
		$theseItems[] = $thisI;
		}
		
	$db->query("update " . TABLE_PREFIX . "itemshop_items set type='".$_POST['mcat']."' where type='".$cat['cid']."'");
	}


$db->query("delete from " . TABLE_PREFIX . "itemshop_cat where cid='".$cat['cid']."'");
	define('CP_REDIRECT', 'ishop_control.php?act=cStats');
	print_stop_message('ishop_cat_deleted');
}
}


// ################### Mass Restock ######################
if ($_GET['act'] == "mass_restock") {
print_cp_header("IShop Control");
print_form_header('ishop_control', 'do_mass_restock');
print_table_header("Current Items In IShop Out Of Stock", 18);

$all_cats = $db->query("select * from " . TABLE_PREFIX . "itemshop_cat order by cname");
while($all_c = $db->fetch_array($all_cats)){
$AllCats[$all_c['cid']]=$all_c;
}

$all_items = $db->query("select * from " . TABLE_PREFIX . "itemshop_items where stock='0'");
while($all_i = $db->fetch_array($all_items)){
$AllItems[$all_i['id']]=$all_i;
}





print_table_header("<b>{$TheCat['cname']}</b>",18);
print_cells_row(array(
"<b>Icon/Name</b>",
"<b>Item Type (Category)</b>",
"<b>New Stock</b>",),'thead','','',1);


if(is_array($AllItems)){
	foreach($AllItems as $Data){
	print_cells_row(array("<img src='{$vboptions[bburl]}/ishop/items/{$Data['img']}' alt='{$Data['name']}'><br/><b>{$Data['name']}</b>",
	"{$AllCats[$Data['type']]['cname']}",
	"<input type='text' name='restock[{$Data['id']}]' value='30' class='form'>",),
 	'','','',1);
	}
}
print_submit_row("Update Item(s) Stock", 0,3);




print_table_footer();
	print_cp_footer();
	exit;
}

// ################# Do Mass Restock ####################
if($_POST['do']=="do_mass_restock"){
print_cp_header("IShop Control");

	if(is_array($_POST['restock'])){
		foreach($_POST['restock'] as $iKey => $restock){
		$restock = intval($restock);
		$db->query("update " . TABLE_PREFIX . "itemshop_items set stock='{$restock}' where id='{$iKey}'");
		}
	} else {
	echo "There were no items to restock. [<a href='javascript:history.go(-1);'>Back</a>]";
	exit;
	}

	define('CP_REDIRECT', 'ishop_control.php?act=mass_restock');
	print_stop_message('ishop_mass_restock');
}

// ################### Mass Recount Item Sold Stats #####################
if($_GET['act']=="mass_item_fix"){
print_cp_header("IShop Control");

$counter = 1;
$get_all_i = $db->query("select * from " . TABLE_PREFIX . "itemshop_items");
	while($thisI = $db->fetch_array($get_all_i)){
	$AllItems[$counter] = $thisI;
	$counter++;
	}

$endat = $counter;

	if($_GET['startat']){
	$newstart  = ($_GET['startat']+10);
	} else {
	$newstart  = 10;
	}

	$thisCount = 1;
	if(is_array($AllItems)){
		foreach($AllItems as $current_item){
			if(($thisCount >= ($newstart - 10) && $thisCount < $newstart)){
				$i=0;
				$gbought=$db->query("select * from " . TABLE_PREFIX . "itemshop_stock where item_id='{$current_item['id']}'");
				while($bought=$db->fetch_array($gbought)){
				$i++;
				}
			$db->query("update " . TABLE_PREFIX . "itemshop_items set sold='{$i}' where id='{$current_item['id']}'");
			echo "{$thisCount}. Sold Stat Fixed For Item {$current_item['name']}<br />";
			unset($i);
			}
		$thisCount++;
		}
	}



	if($_GET['startat']>$endat){
	echo "All Items Sold Stat's Recounted<script>\nlocation.href='ishop_control.php?act=main';\n</script>";
	} else {
	echo "<script>
	<!--
	function move(){
	location.href='ishop_control.php?act=mass_item_fix&startat={$newstart}'
	}
	setTimeout('move()',2000)
	-->
	</script><br/><a href='ishop_control.php?act=mass_item_fix&startat={$newstart}'>Click here if you are not redirected..</a>";
	}
}

?>

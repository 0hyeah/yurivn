<?php
  /*======================================================================*\
   || #################################################################### ||
   || # Ultra Imageshop                                                  # ||
   || # Copyright Blaine0002(C) 2005 All rights reserved.                # ||
   || # ---------------------------------------------------------------- # ||
   || # For use with vBulletin Version 3.5.0                             # ||
   || # http://www.vbulletin.com | http://www.vbulletin.com/license.html # ||
   || # Discussion and support available at                              # ||
   || # http://www.vbulletin.org/forum/showthread.php?t=100344           # ||
   || #################################################################### ||
   \*======================================================================*/
  error_reporting(E_ALL & ~E_NOTICE);
  define('NO_REGISTER_GLOBALS', 1);
  define('THIS_SCRIPT', 'ishop');
  $phrasegroups = array();
  $specialtemplates = array();
  $actiontemplates = array('shop' => array('ishop_inv_end', 'ishop_inv_row', 'ishop_inv_top', 'ishop_shop_end', 'ishop_shop_row', 'ishop_shop_top', 'ishop_cat_end', 'ishop_cat_row', 'ishop_cat_top', ), 'donate' => array('ishop_donate_item', ), 'ViewMember' => array('ishop_viewm_end', 'ishop_viewm_invrow', 'ishop_viewm_mini1', 'ishop_viewm_miniprof', 'ishop_viewm_statsend', 'ishop_viewm_top', ), );
  $globaltemplates = array('ishop', 'ishop_inv_end', 'ishop_inv_row', 'ishop_inv_top', 'ishop_shop_end', 'ishop_shop_row', 'ishop_shop_top', 'ishop_cat_end', 'ishop_cat_row', 'ishop_cat_top', 'navbar', );
  // #####################################################################
  // INCLUDES
  // #####################################################################
  require_once('./global.php');
  require_once('./includes/Sajax.php');
  require_once('./includes/functions_ishop.php');

session_start();

function sajax_build($data1, $data2)
{
    global $vbulletin;
    $_SESSION['case'] .= " WHEN uid = '$data1' AND member_id = '{$vbulletin->userinfo[userid]}' THEN $data2 ";
}

function sajax_update()
{
        global $db, $vbulletin;
        $db->query("update " . TABLE_PREFIX . "itemshop_stock set iorder = CASE " . $_SESSION['case'] . " END");
        session_destroy();
}


 //   $sajax_debug_mode = 1;
  	sajax_init();
	sajax_export("sajax_build");
	sajax_export("sajax_update");
	sajax_handle_client_request();
?>
	<script>
<?	sajax_show_javascript(); ?>
</script>
<script src="./includes/tableDnD.js" type="text/javascript"></script>
<?php
  // #####################################################################
  // SET UP CODE REQUIRED BY SCRIPT
  // #####################################################################
  if (!$vbulletin->options['ishop_active']) {
      print_no_permission();
  }

   $all = $db->query("select * from " . TABLE_PREFIX . "itemshop_cat order by cid");
   eval('$ishopleft .= "' . fetch_template('ishop_cat_top') . '";');
      while ($cat = $db->fetch_array($all)) {
	    eval('$ishopleft .= "' . fetch_template('ishop_cat_row') . '";');
	  }
	eval('$ishopleft .= "' . fetch_template('ishop_cat_end') . '";');


  // #####################################################################
  // IF NO ACTION GO TO SHOP
  // #####################################################################
  if (empty($_REQUEST['do'])) {
      $_REQUEST['do'] = 'shop';
  }
  // #####################################################################
  // MAIN ISHOP
  // ######################################################################
  if ($_REQUEST['do'] == 'shop') {
      $navbits = array("ishop.php?$session[sessionurl]&do=" . $Action => "Buy Items");
      $navbits[""] = "Ishop";


	  if (!$_REQUEST['showshop']) {
		  eval('$ishopmiddle .= "' . fetch_template('ishop_inv_top') . '";');
		  $inv_row = $db->query("
		  select s.*,i.* from
		  " . TABLE_PREFIX . "itemshop_stock s left join
		  " . TABLE_PREFIX . "itemshop_items i on(i.id=s.item_id)
		  where s.member_id='{$vbulletin->userinfo['userid']}'
		  order by s.iorder");
		while ($items = $db->fetch_array($inv_row)) {
				  $items['Sell'] = (int)$items['cost'] / $vbulletin->options['ishop_selldivide'];
				  $items['Sell'] = floor($items['Sell']);
				  $items['Sell'] = number_format($items['Sell']);
				  $id = $items['uid'];
				  eval('$ishopmiddle .= "' . fetch_template('ishop_inv_row') . '";');
	  	}
	    eval('$ishopmiddle .= "' . fetch_template('ishop_inv_end') . '";');
	  }

      if ($_REQUEST['showshop']) {

          eval('$ishopmiddle .= "' . fetch_template('ishop_shop_top') . '";');
          $get_all_items = $db->query("select * from " . TABLE_PREFIX . "itemshop_items order by name");
          $cats = $CatAll[$_REQUEST['showshop']];
          while ($items = $db->fetch_array($get_all_items)) {
        	  if ($items['type'] == $_REQUEST['showshop']) {
	              if ($vbulletin->userinfo[$vbulletin->options['ishop_pointfield']] >= $items['cost']) {
			          $items['BuyLink'] = "<a href='?$session[sessionurl]do=BuyItem&id={$items['id']}'>Buy</a>";
		          } else {
		              $items['BuyLink'] = "<a disabled=true href='javascript:;' title='Not Enough {$vbulletin->options['ishop_moneyname']} To Buy This Item'>Buy</a>";
		          }
		      $items['cost'] = number_format($items['cost']);
		      eval('$ishopmiddle .= "' . fetch_template('ishop_shop_row') . '";');
       		  }
          }
          eval('$ishopmiddle .= "' . fetch_template('ishop_shop_end') . '";');
      }
   }

  // #####################################################################
  // BUY ITEM
  // #####################################################################
  if ($_REQUEST['do'] == "BuyItem") {
      $vbulletin->input->clean_array_gpc('r', array(
	          'id' => TYPE_INT
      ));

      $gitem = $db->query("select * from " . TABLE_PREFIX . "itemshop_items where id='" . $vbulletin->GPC['id'] . "'");
      $item = $db->fetch_array($gitem);
      if ($item['id'] == "") {
          eval(standard_error(fetch_error('error_shop_invid')));
      }
      if (($vbulletin->userinfo[$vbulletin->options['ishop_pointfield']] - $item['cost']) < 0) {
          eval(standard_error(fetch_error('error_shop_nomoney')));
      }
      if ($item['stock'] < 1) {
          eval(standard_error(fetch_error('error_shop_nostock')));
      }

	  if ($vbulletin->options['ishop_adminnotify']) {
	  	  $title = 'I have just bought the '.$item[name].' item.';
	  	  $message = 'I have just bought the '.$item[name].' item.';
	  	  send_pm($vbulletin->userinfo['username'],$vbulletin->userinfo['userid'],$vbulletin->options['ishop_notifywho'],$title,$message);
	  }

      $db->query("update " . TABLE_PREFIX . "user set {$vbulletin->options['ishop_pointfield']}={$vbulletin->options['ishop_pointfield']}-'{$item['cost']}' where userid='{$vbulletin->userinfo['userid']}'");
      $db->query("insert into " . TABLE_PREFIX . "itemshop_stock values('','{$item['id']}','{$vbulletin->userinfo['userid']}','0','')");
      $db->query("update " . TABLE_PREFIX . "itemshop_cat set csold=csold+'1',cprofit=cprofit+'{$item['cost']}' where cid='{$item['type']}'");
      $db->query("update " . TABLE_PREFIX . "itemshop_items set sold=sold+'1',stock=stock-'1' where id='{$item['id']}'");

      $vbulletin->url = "ishop.php?showshop={$item['type']}" . $vbulletin->session->vars['sessionurl'] . "";
      eval(print_standard_redirect('shop_r_itempurchased', true, true));
  }
  // #####################################################################
  // SELL ITEM
  // #####################################################################
  if ($_REQUEST['do'] == "sellitem") {
      $vbulletin->input->clean_array_gpc('r', array(
	          'id' => TYPE_INT
      ));

      $gitem = $db->query("select * from " . TABLE_PREFIX . "itemshop_items where id='" . $vbulletin->GPC['id'] . "'");
      $item = $db->fetch_array($gitem);
      if ($item['id'] == "") {
          eval(standard_error(fetch_error('error_shop_invid')));
      }
      $runx = $db->query("select * from " . TABLE_PREFIX . "itemshop_stock where item_id='{$item['id']}' and member_id='{$vbulletin->userinfo['userid']}'");
      $your_items = $db->fetch_array($runx);
      if ($your_items['uid'] == "") {
          eval(standard_error(fetch_error('error_shop_noown')));
      }
      $sell = (int)$item['cost'] / $vbulletin->options['ishop_selldivide'];
      $sell = floor($sell);

      if ($vbulletin->options['ishop_adminnotify']) {
      		$title = 'I have just sold the '.$item[name].' item.';
	  		$message = 'I have just sold the '.$item[name].' item.';
	    	send_pm($vbulletin->userinfo['username'],$vbulletin->userinfo['userid'],$vbulletin->options['ishop_notifywho'],$title,$message);
      }

      $db->query("update " . TABLE_PREFIX . "user set {$vbulletin->options['ishop_pointfield']}={$vbulletin->options['ishop_pointfield']}+'" . $sell . "' where userid='{$vbulletin->userinfo['userid']}'");
      $db->query("delete from " . TABLE_PREFIX . "itemshop_stock where item_id='{$item['id']}' and member_id='{$vbulletin->userinfo['userid']}' LIMIT 1");
      $db->query("update " . TABLE_PREFIX . "itemshop_cat set csold=csold-'1',cprofit=cprofit-'{$item['cost']}' where cid='{$item['type']}'");
	  $db->query("update " . TABLE_PREFIX . "itemshop_items set sold=sold-'1',stock=stock+'1' where id='{$item['id']}'");

	  $vbulletin->url = "ishop.php" . $vbulletin->session->vars['sessionurl'] . "";
      eval(print_standard_redirect('shop_r_itemsold', true, true));
  }
  // #####################################################################
  // VIEW MEMBER
  // #####################################################################
  if ($_REQUEST['do'] == "ViewMember") {
      $navbits = array("ishop.php?$session[sessionurl]&do=" . $Action => "View Member");
      $navbits[""] = "Viewing Members Ishop Profile";

      $vbulletin->input->clean_array_gpc('r', array(
	          'id' => TYPE_INT
      ));

      $themember = $db->query_first("select * from " . TABLE_PREFIX . "user where userid='" . $vbulletin->GPC['id'] . "'");
      eval('$ishopmiddle .= "' . fetch_template('ishop_viewm_top') . '";');
      $cache_upgrade = array();
      $inv_row = $db->query("select s.*,i.*
  from " . TABLE_PREFIX . "itemshop_stock s
  left join " . TABLE_PREFIX . "itemshop_items i on(i.id=s.item_id)
  where s.member_id='{$themember['userid']}' order by s.iorder");
      while ($item = $db->fetch_array($inv_row)) {
          eval('$ishopmiddle .= "' . fetch_template('ishop_viewm_invrow') . '";');
      }

      eval('$ishopmiddle .= "' . fetch_template('ishop_viewm_end') . '";');
  }
  // #####################################################################
  // DONATE
  // #####################################################################
  if ($_REQUEST['do'] == "donate") {
      $navbits = array("ishop.php?$session[sessionurl]&do=" . $Action => "Donate");
      $navbits[""] = "Send Items To Members";

      $to = $_REQUEST['to'];
      $inv_row = $db->query("select s.*,i.*
      from `" . TABLE_PREFIX . "itemshop_stock` s
      left join `" . TABLE_PREFIX . "itemshop_items` i on(i.id=s.item_id)
      where s.member_id='" . $vbulletin->userinfo['userid'] . "'");
      $items = "<option value=''>Choose an item to send</option>";
      while ($item = $db->fetch_array($inv_row)) {
          $items .= "<option value='{$item['id']}'>{$item['name']}</option>";
      }
      eval('$ishopmiddle = "' . fetch_template('ishop_donate_item') . '";');
  }

  // #####################################################################
  // DONATE [Item]
  // #####################################################################
  if ($_REQUEST['do'] == "dodonateitem") {

      $vbulletin->input->clean_array_gpc('p', array(
	     'to' => TYPE_NOHTML,
	     'item' => TYPE_NOHTML,
	  ));

      if (!$user = $db->query_first("select * from " . TABLE_PREFIX . "user where username='" . $db->escape_string($vbulletin->GPC['to']) . "'")) {
          eval(standard_error(fetch_error('error_shop_senditonoexist')));
      }
      if (!$user_shop = $db->query_first("select * from " . TABLE_PREFIX . "user where userid='{$user['userid']}'")) {
          eval(standard_error(fetch_error('error_shop_senditonoprofile')));
      }
      if (!$theitem = $db->query_first("select * from " . TABLE_PREFIX . "itemshop_items where id='" . $db->escape_string($vbulletin->GPC['item']) . "'")) {
          eval(standard_error(fetch_error('error_shop_sendinoitem')));
      }
      if (!$your_item = $db->query_first("select s.*,i.* from " . TABLE_PREFIX . "itemshop_stock s left join " . TABLE_PREFIX . "itemshop_items i on(i.id=s.item_id) where s.member_id='{$vbulletin->userinfo['userid']}' and i.id='" . $db->escape_string($vbulletin->GPC['item']) . "'")) {
          eval(standard_error(fetch_error('error_shop_sendinoown')));
      }
      if ($user_shop['userid'] == $vbulletin->userinfo['userid']) {
          eval(standard_error(fetch_error('error_shop_senditonoself')));
      }

      if ($vbulletin->options['ishop_admindonatenotify']) {
		  $title = 'I have just donated '.$vbulletin->GPC[to].' the item '.$theitem[name].'.';
		  $message = 'I have just donated '.$vbulletin->GPC[to].' the item '.$theitem[name].'.';
		  send_pm($vbulletin->userinfo['username'],$vbulletin->userinfo['userid'],$vbulletin->options['ishop_notifywho'],$title,$message);
      }

      if ($vbulletin->options['ishop_donatenotify']) {
	  	  $title = 'I have just donated you the item '.$theitem[name].'.';
	  	  $message = 'I have just donated you the item '.$theitem[name].'.';
	  	  send_pm($vbulletin->userinfo['username'],$vbulletin->userinfo['userid'],$vbulletin->GPC['to'],$title,$message);
      }

      $db->query("update " . TABLE_PREFIX . "itemshop_stock set member_id='{$user_shop['userid']}' where member_id='{$vbulletin->userinfo['userid']}' and item_id='" . $db->escape_string($vbulletin->GPC['item']) . "' LIMIT 1");

      $vbulletin->url = "ishop.php?$session[sessionurl]do=Donate" . $vbulletin->session->vars['sessionurl'] . "";
      eval(print_standard_redirect('shop_r_itemdonatesuccess', true, true));
  }
  // #####################################################################
  // END, FINISH TEMPLATES
  // #####################################################################
  $navbits = construct_navbits($navbits);
  eval('$navbar = "' . fetch_template('navbar') . '";');
  eval('print_output("' . fetch_template('ishop') . '");');
?>
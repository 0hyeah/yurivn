/*======================================================================*\
|| #################################################################### ||
|| # vBulletin 4.2.2 Alpha 1
|| # ---------------------------------------------------------------- # ||
|| # Copyright �2000-2013 vBulletin Solutions Inc. All Rights Reserved. ||
|| # This file may not be redistributed in whole or significant part. # ||
|| # ---------------- VBULLETIN IS NOT FREE SOFTWARE ---------------- # ||
|| # http://www.vbulletin.com | http://www.vbulletin.com/license.html # ||
|| #################################################################### ||
\*======================================================================*/
vBulletin.events.systemInit.subscribe(function(){if(vBulletin.elements.vB_Notifications_NoPopups){vBulletin.vB_Notifications_NoPopups=new Object();for(var B=0;B<vBulletin.elements.vB_Notifications_NoPopups.length;B++){var A=vBulletin.elements.vB_Notifications_NoPopups[B][0];vBulletin.vB_Notifications_NoPopups[A]=new vB_Notifications_NoPopups(A)}vBulletin.elements.vB_Notifications_NoPopups=null}});function vB_Notifications_NoPopups(A){this.elementid=A;this.element=YAHOO.util.Dom.get(this.elementid);this.notifications_text=new Array();this.counter=0;this.timeout=null;this.timeout_time=2000;this.fetch_text();this.timeout=setTimeout("vBulletin.vB_Notifications_NoPopups['"+this.elementid+"'].cycle()",this.timeout_time)}vB_Notifications_NoPopups.prototype.fetch_text=function(){var C,B,A;C=YAHOO.util.Dom.get(this.element.id+"_menu").getElementsByTagName("tr");for(B=0;B<C.length;B++){A=C[B].getElementsByTagName("a");if(A.length){if(parseInt(A[1].firstChild.nodeValue)!=0){this.notifications_text.push('<a href="'+A[0].getAttribute("href")+'">'+A[0].firstChild.nodeValue+"</a> "+A[1].firstChild.nodeValue.bold())}}}};vB_Notifications_NoPopups.prototype.cycle=function(){if(this.counter>=this.notifications_text.length){this.counter=0}this.element.innerHTML=this.notifications_text[this.counter];this.counter++;this.timeout=setTimeout("vBulletin.vB_Notifications_NoPopups['"+this.elementid+"'].cycle()",this.timeout_time)};
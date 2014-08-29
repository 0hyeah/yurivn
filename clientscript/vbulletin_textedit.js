/*======================================================================*\
|| #################################################################### ||
|| # vBulletin 4.2.2 Alpha 1
|| # ---------------------------------------------------------------- # ||
|| # Copyright ©2000-2013 vBulletin Solutions Inc. All Rights Reserved. ||
|| # This file may not be redistributed in whole or significant part. # ||
|| # ---------------- VBULLETIN IS NOT FREE SOFTWARE ---------------- # ||
|| # http://www.vbulletin.com | http://www.vbulletin.com/license.html # ||
|| #################################################################### ||
\*======================================================================*/
function vB_Text_Editor(C,B){this.autosave_text="";this.autosave_title="";this.initialized=false;this.editorid=C;this.config=B;this.config.baseHref=getBaseUrl();this.initial_text_crc32=null;this.initial_title_crc32=null;this.lastautosave_text_crc32=null;this.lastautosave_title_crc32=null;this.autosave_ajax_req=null;this.editor=null;this.vBevents={editorready:new YAHOO.util.CustomEvent("editorready",this)};this.editorready=false;this.autosave_enabled=(this.config.vbulletin.contenttypeid!=""&&this.config.vbulletin.contenttypeid!=null&&this.config.vbulletin.userid!=0&&this.config.vbulletin.postautosave!=0);this.textarea=YAHOO.util.Dom.get(C+"_editor");this.textarea_backup=YAHOO.util.Dom.get(C+"_editor_backup");this.textarea.value=this.unescape_text(this.textarea.value);this.isSafari=CKEDITOR.env.webkit&&navigator.userAgent.toLowerCase().indexOf(" chrome/")==-1;this.disablewysiwyg=false;if(this.config._removePlugins){CKEDITOR.config.removePlugins=this.config._removePlugins}if(this.config._extraPlugins){CKEDITOR.config.extraPlugins=this.config._extraPlugins}var A=true;if(!CKEDITOR.vBulletin){CKEDITOR.vBulletin={};if(CKEDITOR.env.mobile&&!CKEDITOR.env.isCompatible){CKEDITOR.env.isCompatible=true;CKEDITOR.vBulletin.mobileSource=true}var D={failure:vBulletin_AJAX_Error_Handler,timeout:vB_Default_Timeout,success:this.load_language,scope:this};YAHOO.util.Connect.asyncRequest("GET",fetch_ajax_url("ckeditor.php?l="+this.config.language+"&t="+this.config.vbulletin.lastphraseupdate),D);A=false}if(CKEDITOR.vBulletin.mobileSource&&this.config.editorMode>0){this.config.startupMode="enhancedsource";this.disablewysiwyg=true}if(A){this.init()}}vB_Text_Editor.prototype.autosavetimer=function(D){if(!this.autosave_enabled){return }var A=1000*this.config.vbulletin.postautosave;var B=typeof (D)!="undefined"?D:A;var C=this;this.autoupdatetimer=setTimeout(function(){C.autosave()},B)};vB_Text_Editor.prototype.fade_autosave=function(A){if(!A){A=1}A-=0.05;if(A>0){YAHOO.util.Dom.setStyle(this.editor.editorid+"_autosaved","opacity",A);var B=this;setTimeout(function(){B.fade_autosave(A)},150)}else{this.hide_autosave_notice()}};vB_Text_Editor.prototype.hide_autosave_notice=function(){if(this.autosave_enabled){YAHOO.util.Dom.addClass(this.editorid+"_autosaved","hidden");YAHOO.util.Dom.setStyle(this.editorid+"_autosaved","opacity",1)}};vB_Text_Editor.prototype.autosave_notice=function(){YAHOO.util.Dom.removeClass(this.editor.editorid+"_autosaved","hidden");var A=this;setTimeout(function(){A.fade_autosave()},30)};vB_Text_Editor.prototype.autosave=function(){if(!this.autosave_enabled){return }if(!this.initialized){this.autosavetimer();return }var C=false;if(this.config.autoloadtitleid&&YAHOO.util.Dom.get(this.config.autoloadtitleid)){var I=YAHOO.util.Dom.get(this.config.autoloadtitleid).value;var F=crc32(I);if(this.lastautosave_title_crc32!=F){C=true}this.lastautosave_title_crc32=F}else{var I=""}var E=PHP.trim(this.getRawData());if(this.editor.document){var J=new RegExp("(<br>|<br />|\\s|&nbsp;)","gi");var D=(E.replace(J,"").length>0)?true:false}else{var D=(E.length>0)?true:false}var B=crc32(E);var C=(C||this.lastautosave_text_crc32!=B);this.lastautosave_text_crc32=B;var K=YAHOO.util.Connect.isCallInProgress(this.autosave_ajax_req);if(D&&C&&!K){if(this.editor.document){var M=this.editor.dataProcessor.getAutoSaveData()}else{var M=this.editor.getData()}var L={failure:vBulletin_AJAX_Error_Handler,timeout:vB_Default_Timeout,success:this.autosave_notice,scope:this};var H=vBulletin.attachinfo&&vBulletin.attachinfo.posthash?vBulletin.attachinfo.posthash:"";var A=vBulletin.attachinfo&&vBulletin.attachinfo.poststarttime?vBulletin.attachinfo.poststarttime:"";var G=SESSIONURL+"do=autosave&securitytoken="+SECURITYTOKEN+"&posthash="+PHP.urlencode(H)+"&poststarttime="+parseInt(A,10)+"&ajax=1&&pagetext="+PHP.urlencode(M)+"&title="+PHP.urlencode(I)+"&contenttypeid="+PHP.urlencode(this.config.vbulletin.contenttypeid)+"&contentid="+parseInt(this.config.vbulletin.contentid,10)+"&wysiwyg="+this.is_wysiwyg_mode()+"&parsetype="+PHP.htmlspecialchars(this.editor.config.parsetype)+"&parentcontentid="+parseInt(this.config.vbulletin.parentcontentid);this.autosave_ajax_req=YAHOO.util.Connect.asyncRequest("POST",fetch_ajax_url("ajax.php?do=autosave"),L,G)}if(K){this.autosavetimer(10000)}else{this.autosavetimer()}};vB_Text_Editor.prototype.is_wysiwyg_mode=function(){return(this.editor.mode=="wysiwyg"?1:0)};vB_Text_Editor.prototype.load_language=function(F){if(F.responseXML){CKEDITOR.vbphrase={};var C=F.responseXML.getElementsByTagName("phrase");if(C.length){for(var B=0;B<C.length;B++){var D=C[B].getAttribute("name");if(C[B].firstChild){var A=C[B].firstChild.nodeValue;var G=CKEDITOR.vbphrase;var E=D.split(".");for(var H=0;H<E.length;H++){if(H==(E.length-1)){G[E[H]]=A}else{if(typeof (G[E[H]])!="object"){G[E[H]]={}}G=G[E[H]]}}}}}CKEDITOR.vbphrase.dir=document.documentElement.dir;this.init()}};vB_Text_Editor.prototype.editor_ready=function(B){if(this.config.nobbcode){this.editor.getCommand("removeFormat").disable();this.hide_button("removeFormat");this.editor.getCommand("enhancedsource").disable();this.hide_button("enhancedsource")}if(this.disablewysiwyg){this.editor.getCommand("enhancedsource").disable()}if(!this.get_button("bold")){this.editor.addCommand("bold",{exec:function(){return }})}if(!this.get_button("italic")){this.editor.addCommand("italic",{exec:function(){return }})}if(!this.get_button("underline")){this.editor.addCommand("underline",{exec:function(){return }})}if(this.wysiwyg_mode==2&&CKEDITOR.env.gecko){var A=this;setTimeout(function(){A.force_caret_ff()},500);this.editor.document.on("mouseover",this.force_caret_ff,this);this.editor.document.on("click",this.force_caret_ff,this)}if(this.config.autofocus){this.check_focus()}YAHOO.util.Dom.addClass(this.editorid,this.config.bodyClass);this.set_autoload_crc32();this.editor.on("dataReady",this.setupEventHandling,this);if(CKEDITOR.env.ie&&CKEDITOR.env.version>=9&&this.editor.mode!="wysiwyg"&&THIS_SCRIPT=="member"){YAHOO.util.Dom.setStyle(this.editor.textarea.$,"width","100%");YAHOO.util.Dom.setStyle(this.editor.textarea.$,"height","100%")}};vB_Text_Editor.prototype.setupEventHandling=function(A){if(this.editor.mode=="wysiwyg"){if(!CKEDITOR.env.webkit){YAHOO.util.Event.on(this.editor.document.$,"mousedown",this.mousedown,this,true);YAHOO.util.Event.on(this.editor.document.$,"contextmenu",this.contextmenu,this,true)}YAHOO.util.Event.on(this.editor.document.$,"dblclick",this.img_dblclick,this,true);YAHOO.util.Event.on(this.editor.document.$,"dragend",this.img_dragend,this,true);if(this.isSafari){YAHOO.util.Event.on(this.editor.document.$,"mousedown",this.img_mousedown,this,true)}}};vB_Text_Editor.prototype.img_mousedown=function(B){var A=YAHOO.util.Event.getTarget(B);if(YAHOO.util.Dom.hasClass(A,"previewthumb")){B.preventDefault()}};vB_Text_Editor.prototype.mousedown=function(B){var A=YAHOO.util.Event.getTarget(B);if(A.tagName&&A.tagName.toLowerCase()=="html"&&this.which_button(B)==3){this.set_body_height()}};vB_Text_Editor.prototype.which_button=function(A){return A.which?A.which:(A.button==1?1:(A.button==2?3:(A.button==4?2:1)))};vB_Text_Editor.prototype.contextmenu=function(B){var A=YAHOO.util.Event.getTarget(B);if(A.tagName&&A.tagName.toLowerCase()=="body"&&this.which_button(B)==3){this.set_body_height("auto")}};vB_Text_Editor.prototype.pasteFromWordResize=function(){this.editor.on("dialogShow",function(C){if(C.data.getName()=="paste"){var A=C.data.getSize();var B=YAHOO.util.Dom.getElementsByClassName("cke_pasteframe","iframe",C.data.getElement().$);if(B.length){YAHOO.util.Dom.setStyle(B[0],"width","100%");YAHOO.util.Dom.setStyle(B[0],"height",A.height-115-95+"px")}}});if(!CKEDITOR.vbdialog_resize){CKEDITOR.dialog.on("resize",function(C){if(C.data.dialog.getName()=="paste"){var B=YAHOO.util.Dom.getElementsByClassName("cke_pasteframe","iframe",C.data.dialog.getElement().$);if(B.length){YAHOO.util.Dom.setStyle(B[0],"height",C.data.height-115+"px")}}if(C.data.dialog.getName()=="pastetext"){var A=YAHOO.util.Dom.getElementsByClassName("cke_dialog_ui_input_textarea","textarea",C.data.dialog.$);if(A.length){YAHOO.util.Dom.setStyle(A[0],"width",C.data.width+"px");YAHOO.util.Dom.setStyle(A[0],"height",C.data.height+"px")}}});CKEDITOR.vbdialog_resize=true}};vB_Text_Editor.prototype.set_body_height=function(B){var H=this.editor.window.$.frameElement.contentWindow.document.body;if(B){YAHOO.util.Dom.setStyle(H,"height",B)}else{var A=parseInt(YAHOO.util.Dom.getStyle(H,"margin-top"),10);var E=parseInt(YAHOO.util.Dom.getStyle(H,"margin-bottom"),10);var D=parseInt(YAHOO.util.Dom.getStyle(H,"padding-top"),10);var G=parseInt(YAHOO.util.Dom.getStyle(H,"padding-bottom"),10);var C=this.editor.window.$.frameElement.offsetHeight;var F=H.offsetHeight-A-E-D-G;if(F<C){YAHOO.util.Dom.setStyle(H,"height",(C-A-E-D-G)+"px")}}};vB_Text_Editor.prototype.img_dblclick=function(B){if(typeof (B.button)!="undefined"&&B.button!=0){return }this.check_focus();var A=YAHOO.util.Event.getTarget(B);if(YAHOO.util.Dom.hasClass(A,"previewthumb")){this.editor.current_attachmentid=A.getAttribute("attachmentid");this.editor.execCommand("openAttachmentConfig")}};vB_Text_Editor.prototype.img_dragend=function(B){var A=YAHOO.util.Dom.getElementsByClassName("previewthumb","img",this.editor.document.$);var C=false;for(i=0;i<A.length;i++){if(C=PHP.stripos(A[i].src,"attachment.php")){A[i].src=A[i].src.substr(C)}}};vB_Text_Editor.prototype.set_autoload_crc32=function(){this.initial_text_crc32=crc32(PHP.trim(this.getRawData()));this.lastautosave_text_crc32=this.initial_text_crc32;if(this.config.autoloadtitleid&&YAHOO.util.Dom.get(this.config.autoloadtitleid)){this.initial_title_crc32=crc32(PHP.trim(YAHOO.util.Dom.get(this.config.autoloadtitleid).value));this.lastautosave_title_crc32=this.initial_title_crc32}};vB_Text_Editor.prototype.force_caret_ff=function(B){if(B){B.removeListener()}if(B&&CKEDITOR.env.version>=80000&&CKEDITOR.env.version<110000&&B.name=="click"){this.editor.document.getBody().setAttribute("contentEditable",false);if(this.getRawData().length==0){var A=this.editor.dataProcessor;this.editor.dataProcessor=null;this.write_editor_contents("x");this.write_editor_contents("");this.editor.dataProcessor=A;this.check_focus()}}this.editor.document.getBody().setAttribute("contentEditable",true);if(this.config.autofocus){this.check_focus()}};vB_Text_Editor.prototype.init=function(){if(this.textarea_backup.value){this.textarea.value=this.textarea_backup.value;this.textarea_backup.value=""}this.editor=CKEDITOR.replace(this.editorid+"_editor",this.config);if(!this.editor){if(this.config.autofocus){this.check_focus()}this.editorready=true;return }this.editor.lang=CKEDITOR.vbphrase;this.editor.on("instanceReady",this.editor_ready,this);if(this.config.nobbcode){this.editor.config.startupMode="enhancedsource"}YAHOO.util.Dom.setAttribute(this.textarea,"autocomplete","off");YAHOO.util.Dom.setStyle(this.textarea,"visibility","hidden");YAHOO.util.Dom.setStyle(this.textarea,"width","0px");YAHOO.util.Dom.setStyle(this.textarea,"min-width","0px");this.editor.hitServer=true;var B=this.editor;this.editor.on("paste",function(D){var E=D.data;if(E.text||E.html){B.hitServer=false}},null,null,999);this.editor.on("afterPaste",function(D){if(!B.hitServer){B.hitServer=true}},null,null,1001);this.editor.editorid=this.editorid;if(this.config.vbulletin.attachinfo){vBulletin.attachinfo=this.config.vbulletin.attachinfo}if(this.editor!=null&&(typeof (require_click)=="undefined"||!require_click||this.editorid!="vB_Editor_QR")){this.initialize()}this.wysiwyg_mode=this.config.editorMode;this.init_footer_text();this.setup_unload();this.autosavetimer();this.vBevents.editorready.fire();this.editorready=true;var A=this.editorid;var C=this.config.moresmilies;CKEDITOR.on("dialogDefinition",function(F){var G=F.data.name;var H=F.data.definition;var E=F.data.definition.dialog;if(G=="smiley"&&C){var D=(function(){var I=function(K,J){J=J||{};return CKEDITOR.tools.extend({id:"more",type:"button",label:CKEDITOR.vbphrase.vbulletin.more,"class":"cke_dialog_ui_button_ok",onClick:function(L){var M=L.data.dialog;M.hide();vB_Editor[A].open_smilie_window();return false}},J,true)};I.type="button";I.override=function(J){return CKEDITOR.tools.extend(function(K){return I(K,J)},{type:"button"},true)};return I})();H.buttons=[D,CKEDITOR.dialog.cancelButton]}})};vB_Text_Editor.prototype.hide_autosave_button=function(){if(this.autosave_enabled){YAHOO.util.Dom.setStyle(this.editorid+"_restore_autosave","display","none")}};vB_Text_Editor.prototype.restore_autosave_text=function(){this.hide_autosave_button();this.write_editor_contents(this.autosave_text);if(this.config.autoloadtitleid&&this.autosave_title){YAHOO.util.Dom.get(this.config.autoloadtitleid).value=this.autosave_title}this.editor.focus()};vB_Text_Editor.prototype.init_footer_text=function(E){var C="";var A='<div class="as_ind_container"><div class="hidden" id="'+this.editor.editorid+'_autosaved">%1</div></div>';re=new RegExp("%1","gi");if(this.config.autoload){this.autosave_text=this.unescape_text(this.config.autoload);this.autosave_title=this.unescape_text(this.config.autoloadtitle);C='<div class="cke_bottom_restore_autosave" id="'+this.editor.editorid+'_restore_autosave"><span class="cke_toolgroup"><span class="cke_button"><a class="restoretext" role="button">%1</a></span></span></div>';YAHOO.util.Event.on(this.editor.editorid+"_restore_autosave","click",this.restore_autosave_text,this,true)}if(this.editor.getThemeSpace&&this.editor.getThemeSpace("bottom")){A=A.replace(re,this.editor.lang.autosave.autosaved);C=C.replace(re,this.editor.lang.autosave.restoreAutosaveContent);var B=this.editor.getThemeSpace("bottom");B.$.innerHTML=B.$.innerHTML+A+C}else{var D=this;this.editor.on("themeSpace",function(F){if(F.data.space=="bottom"){A=A.replace(re,D.editor.lang.autosave.autosaved);C=C.replace(re,D.editor.lang.autosave.restoreAutosaveContent);F.data.html+=A+C}})}};vB_Text_Editor.prototype.uninitialize=function(){this.initialized=false};vB_Text_Editor.prototype.initialize=function(){this.initialized=true};vB_Text_Editor.prototype.getRawData=function(){if(this.editor){if(this.editor.document){var A=this.editor.document.getBody().getHtml();if(CKEDITOR.env.gecko){A=A.replace(/<br>(?=\s*(:?$|<\/body>))/,"")}return A}else{return this.editor.textarea.$.value}}else{return this.textarea.value}};vB_Text_Editor.prototype.exit_prompt=function(C){var F=false;for(x in vB_Editor){var A=PHP.trim(vB_Editor[x].getRawData());var E=crc32(A);var B=null;if(this.config.autoloadtitleid&&YAHOO.util.Dom.get(this.config.autoloadtitleid)){B=crc32(PHP.trim(YAHOO.util.Dom.get(this.config.autoloadtitleid).value))}if(vB_Editor[x].lastautosave_text_crc32){var D=(vB_Editor[x].lastautosave_text_crc32!=E)}else{if(vB_Editor[x].lastautosave_title_crc32){var D=(vB_Editor[x].lastautosave_title_crc32!=B)}else{var D=(vB_Editor[x].lastautosave_text_crc32!=null&&vB_Editor[x].initial_text_crc32!=E)}}if(vB_Editor[x].initialized&&D){F=true}}if(F){if(C){C.returnValue=this.editor.lang.vbulletin.changes_will_be_lost}return this.editor.lang.vbulletin.changes_will_be_lost}};vB_Text_Editor.prototype.setup_unload=function(){var A=true;var C=YAHOO.util.Event.getListeners(window);if(C&&C.length){for(var B=0;B<C.length;B++){if(C[B].type=="beforeunload"&&C[B].fn==this.exit_prompt){A=false}}}if(A){YAHOO.util.Event.addListener(window,"beforeunload",this.exit_prompt,this,true)}};vB_Text_Editor.prototype.unescape_text=function(C){var B=C.match(/&#([0-9]+);/g);if(B){for(var A=0;typeof B[A]!="undefined";A++){if(submatch=B[A].match(/^&#([0-9]+);$/)){C=C.replace(submatch[0],String.fromCharCode(submatch[1]))}}}return C};vB_Text_Editor.prototype.check_focus=function(){if(this.editor){if(this.editor.focus){this.editor.focus()}}else{this.textarea.focus()}};vB_Text_Editor.prototype.destroy=function(){this.uninitialize();this.autosave_enabled=false;YAHOO.util.Connect.abort(this.autosave_ajax_req);if(this.editor){this.editor.focusManager.forceBlur();CKEDITOR.remove(this.editor)}};vB_Text_Editor.prototype.get_editor_contents=function(){if(this.editor){return this.editor.getData()}else{return this.textarea.value}};vB_Text_Editor.prototype.html_entity_decode=function(C){if(this.wysiwyg_mode==2||!C){return C}var D=document.createElement("div");YAHOO.util.Dom.setStyle(D,"display","none");C=C.replace(/</g,"&lt;").replace(/>/g,"&gt;");D.innerHTML='<textarea id="vb_entity_decoder">'+C+"</textarea>";var A=document.body.appendChild(D);var B=YAHOO.util.Dom.get("vb_entity_decoder").value;A.parentNode.removeChild(A);return B};vB_Text_Editor.prototype.write_editor_contents=function(C,B){var A=this;if(!B){C=PHP.trim(C)}C=this.html_entity_decode(C);if(this.editor){this.editor.setData(C,this.reset_autosave)}else{this.textarea.value=C}};vB_Text_Editor.prototype.reset_autosave=function(){vB_Editor[this.editorid].set_autoload_crc32()};vB_Text_Editor.prototype.enable_editor=function(B,A){this.initialize();this.write_editor_contents(B,A)};vB_Text_Editor.prototype.disable_editor=function(A){this.uninitialize()};vB_Text_Editor.prototype.prepare_submit=function(D,A){this.hide_autosave_notice();var B=this.getRawData();this.textarea_backup.value=this.get_editor_contents();var C=validatemessage(stripcode(B,true),D,A);if(C){this.textarea.value=B;this.uninitialize();return C}else{this.check_focus();return false}};vB_Text_Editor.prototype.open_smilie_window=function(B,A){if(typeof (B)=="undefined"){B=440}if(typeof (A)=="undefined"){A=480}smilie_window=openWindow(fetch_ajax_url("misc.php?"+SESSIONURL+"do=getsmilies&editorid="+this.editorid),B,A,"smilie_window");window.onunload=vB_Text_Editor.prototype.smiliewindow_onunload};vB_Text_Editor.prototype.insert_smilie=function(B,A){A.editor.insertHtml(YAHOO.util.Dom.getAttribute(this,"alt"))};vB_Text_Editor.prototype.init_smilies=function(C){if(C!=null){var B=fetch_tags(C,"img");for(var A=0;A<B.length;A++){if(B[A].id&&B[A].id.indexOf("_smilie_")!=false){B[A].style.cursor=pointer_cursor;B[A].unselectable="on";YAHOO.util.Event.addListener(B[A],"click",this.insert_smilie,this,false)}}}};vB_Text_Editor.prototype.smiliewindow_onunload=function(A){if(typeof smilie_window!="undefined"&&!smilie_window.closed){smilie_window.close()}};vB_Text_Editor.prototype.get_button=function(F,C){if(!C){C="button"}for(var B=0;B<this.editor.toolbox.toolbars.length;B++){var E=this.editor.toolbox.toolbars[B];for(var A=0;A<E.items.length;A++){var D=E.items[A];if((C=="button"&&D.button&&D.button.command==F)||(C=="combo"&&D.combo&&D.combo.command==F)){return D}}}};vB_Text_Editor.prototype.hide_button=function(B,G){if(!G){G="button"}var E=this.get_button(B,G);if(E){if(G=="button"){if(YAHOO.util.Dom.getStyle(E.id,"display")=="none"){return }var C=YAHOO.util.Dom.getAncestorByClassName(E.id,"cke_toolgroup");if(C){if(YAHOO.util.Dom.getStyle(C,"display")=="none"){return }var F=YAHOO.util.Dom.getElementsByClassName("cke_button","span",C);var D=0;for(var H=0;H<F.length;H++){var I=F[H].getElementsByTagName("a");if(I.length){if(I[0].id==E.id){continue}if(YAHOO.util.Dom.getStyle(I[0].id,"display")!="none"){D++}}}if(D==0){YAHOO.util.Dom.setStyle(C,"display","none")}YAHOO.util.Dom.setStyle(E.id,"display","none")}}else{var A=YAHOO.util.Dom.getAncestorByClassName(E.id,"cke_rcombo");if(A){YAHOO.util.Dom.setStyle(A,"display","none")}}}};vB_Text_Editor.prototype.remove_attachment=function(C){if(this.is_wysiwyg_mode()){var A=YAHOO.util.Dom.getElementsByClassName("previewthumb","img",this.editor.document.$);var D=A.length;for(var B=0;B<D;B++){if(C==YAHOO.util.Dom.getAttribute(A[B],"attachmentid")){A[B].parentNode.removeChild(A[B])}}}};
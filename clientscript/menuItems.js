/***********************************************
* Omni Slide Menu script - © John Davenport Scheuer
* very freely adapted from Dynamic-FX Slide-In Menu (v 6.5) script- by maXimus
* This notice MUST stay intact for legal use
* Visit Dynamic Drive at http://www.dynamicdrive.com/ for full original source code
* as first mentioned in http://www.dynamicdrive.com/forums
* username:jscheuer1
***********************************************/

//One global variable to set, use true if you want the menus to reinit when the user changes text size (recommended):
resizereinit=true;

menu[3] = {
id:'kbmenu3', //use unique quoted id (quoted) REQUIRED!!
bartext:'YuriVN Forum Menu',
menupos:'right',
kviewtype:'fixed', 
menuItems:[ // REQUIRED!!
//[name, link, target, colspan, endrow?] - leave 'link' and 'target' blank to make a header
["*** ~YuriVN Forum Zone~ ***"], //  create header of Forum Zone
["Thông báo", "http://yurivn.net/forumdisplay.php?f=2", "", 1, "no"],
["Hỗ trợ thành viên", "http://yurivn.net/forumdisplay.php?f=3", "", 1, "no"],
["Shop - Award - Item", "http://yurivn.net/forumdisplay.php?f=56", "", 1], // no means same line
["Award Requests", "http://yurivn.net/forumdisplay.php?f=53", "", 1],

["Nhóm dịch YuriVN"], //create header
["Nhóm dịch truyện", "http://yurivn.net/forumdisplay.php?f=30", "", 1, "no"],
["Silver Moon", "http://yurivn.net/forumdisplay.php?f=56", "", 1],

["*** ~Yuri Zone~ ***"], //create header
["Shoujo-ai tiếng Việt"], //create header
["Truyện dịch", "http://yurivn.net/forumdisplay.php?f=26", "", 1, "no"],
["Anime Vietsub", "http://yurivn.net/forumdisplay.php?f=51", "", 1],

["Manga Shoujo-ai"], //create header
["Download Manga", "http://yurivn.net/forumdisplay.php?f=22", "", 1, "no"],
["Manga Online", "http://yurivn.net/forumdisplay.php?f=47", "", 1],

["Anime Shoujo-ai"], //create header
["Download Anime", "http://yurivn.net/forumdisplay.php?f=23", "", 1, "no"],
["Anime Online", "http://yurivn.net/forumdisplay.php?f=24", "", 1],

["Gallery"], //create header
["Gallery", "http://yurivn.net/forumdisplay.php?f=6", "", 1, "no"],
["Artbook", "http://yurivn.net/forumdisplay.php?f=72", "", 1, "no"],
["Wallpaper", "http://yurivn.net/forumdisplay.php?f=73", "", 1],
["Cosplay", "http://yurivn.net/forumdisplay.php?f=74", "", 1, "no"],
["Avatar & E-Card", "http://yurivn.net/forumdisplay.php?f=75", "", 1, "no"],
["Random Images", "http://yurivn.net/forumdisplay.php?f=78", "", 1],

["Discussion"], //create header
["Anime/Manga Review", "http://yurivn.net/forumdisplay.php?f=48", "", 1, "no"],
["Thăm dò", "http://yurivn.net/forumdisplay.php?f=77", "", 1],

["Fan Creativity"], //create header
["Fictions", "http://yurivn.net/forumdisplay.php?f=10", "", 1, "no"],
["Fan Art", "http://yurivn.net/forumdisplay.php?f=11", "", 1],

["*** ~Mem Zone~ ***"], //create header
["Yurivn Events"], //create header
["King&Queen Yurivn", "http://yurivn.net/forumdisplay.php?f=34", "", 1, "no"],
["Thi viết fic Yuri/Shoujo-ai", "http://yurivn.net/forumdisplay.php?f=39", "", 1],

["Giao lưu - Kết bạn"], //create header
["Yurier's Gallery", "http://yurivn.net/forumdisplay.php?f=28", "", 1, "no"],
["Góc tâm sự", "http://yurivn.net/forumdisplay.php?f=45", "", 1, "no"],
["Funny", "http://yurivn.net/forumdisplay.php?f=15", "", 1],

["Game Center"], //create header
["Game Online", "http://yurivn.net/forumdisplay.php?f=36", "", 1, "no"],
["Game Offline", "http://yurivn.net/forumdisplay.php?f=49", "", 1, "no"],
["Yuri Games", "http://yurivn.net/forumdisplay.php?f=35", "", 1],

["Music Zone"], //create header
["Vietnamese", "Vietnamese", "", 1, "no"],
["International", "http://yurivn.net/forumdisplay.php?f=18", "", 1, "no"],
["Anime/Game OST", "http://yurivn.net/forumdisplay.php?f=19", "", 1],

["Movie"], //create header
["Movie tổng hợp", "http://yurivn.net/forumdisplay.php?f=20", "", 1, "no"],
["The L films", "http://yurivn.net/forumdisplay.php?f=44", "", 1],

["IT Box"],
["IT box", "http://yurivn.net/forumdisplay.php?f=43", "", 1],
["Recycle Bin", "http://yurivn.net/forumdisplay.php?f=21", "", 1],

["Nhấn đúp chuột trái để ẩn menu này."],
["Trang chủ Forum", "http://yurivn.net/", "", 1],

]}; // REQUIRED!! do not edit or remove

////////////////////Stop Editing/////////////////

make_menus();
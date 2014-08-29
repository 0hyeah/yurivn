<?php

//=============================== oO Manga Oo ===============================//
function Manga_add_validate($userid, $username, $date, $image, $name, $type, $author, $genre, $summary, $summary, $source, $hostnum, $host1name, $host2name, $host1link, $host2link){
    $error = '';
    if (strpos(strtoupper($image),"TTP")!=1) $error = $error.'- Hình ảnh minh họa<br>';
    if ($name=='') $error = $error.'- Tên manga<br>';
    if ($author=='') $error = $error.'- Tên tác giả<br>';
    if ($genre=='') $error = $error.'- Thể loại<br>';
    if ($source=='' || $source=='Nguồn nhóm dịch bản Eng') $error = $error.'- Nguồn (Nếu không rõ nguồn, bạn có thể điền "Không biết")<br>';
    if ($hostnum == '1'){
        if(strpos(strtoupper($host1link),"TTP")!=1)$error = $error.'- Link download <br>';
    }
    if ($hostnum == '2'){
        if($host1name=='Bỏ trống nếu chọn duy nhất 1 host' || $host1name=='')$error = $error.'- Tên host chính <br>';
        if(strpos(strtoupper($host1link),"TTP")!=1)$error = $error.'- Link download chính <br>';
        if($host2name=='Bỏ trống nếu chọn duy nhất 1 host' || $host2name=='')$error = $error.'- Tên host mirror <br>';
        if(strpos(strtoupper($host2link),"TTP")!=1)$error = $error.'- Link download mirror<br>';
    }
    if ($error!='') 
        $error = 'Manga của bạn chưa được post vào forum do điền thiếu hoặc điền không đúng các thông tin dưới đây:<br>'.$error;
    return $error;
}

function Manga_add_thread($userid, $username, $date, $image, $name, $type, $author, $genre, $summary, $summary, $source, $hostnum, $host1name, $host2name, $host1link, $host2link){
            
            //Build post content
            if ($type=="Series"){
                $M4rumid = 149;
                $status = "Chưa đủ bộ";
                if ($hostnum==1)
                    $link="[B][Center][URL=\"$host1link\"]Chapter 001[/URL][/B][/Center]";
                else 
                    $link="[B][Center]Chapter 001:    [URL=\"$host1link\"]$host1name [/URL]| [URL=\"$host2link\"]$host2name [/URL][/B][/Center]";
            
            }
            else if ($type=="One-shot"){
                $M4rumid = 150;
                $status = "Đủ bộ";
                if ($hostnum==1)
                    $link="[B][Center][URL=\"$host1link\"]Download[/URL][/B][/Center]";
                else 
                    $link="[B][Center][URL=\"$host1link\"]$host1name [/URL]| [URL=\"$host2link\"]$host2name [/URL][/B][/Center]";
            } 
                
            else if ($type=="Doujinshi"){ 
                $M4rumid = 151;
                $status = "Đủ bộ";
                if ($hostnum==1)
                    $link="[B][Center][URL=\"$host1link\"]Download[/URL][/B][/Center]";
                else 
                    $link="[B][Center][URL=\"$host1link\"]$host1name [/URL]| [URL=\"$host2link\"]$host2name [/URL][/B][/Center]";
            }
            
            $postcontent= <<<HERE
                [Center][IMG] $image [/IMG][/Center]
            
		[B][color="red"]Tựa đề:[/color][/B] $name
                [B][color="red"]Tác giả:[/color][/B] $author
                [B][color="red"]Thể loại:[/color][/B] $genre
                [B][color="red"]Sơ lược nội dung:[/color][/B] $summary
                [B][color="red"]Tình trạng:[/color][/B] $status
                [B][color="red"]Nguồn:[/color][/B] $source

                [CENTER][SIZE=3][B][COLOR="red"]Download[/COLOR][/B][/SIZE][/CENTER]
                $link
HERE;
            
            //post to forum
            mysql_query("INSERT INTO `yurivn_GIN`.`thread` (`threadid`,"
                                                            ." `title`,"
                                                            ." `prefixid`,"
                                                            ." `firstpostid`,"
                                                            ." `lastpostid`,"
                                                            ." `lastpost`,"
                                                            ." `forumid`,"
                                                            ." `pollid`,"
                                                            ." `open`,"
                                                            ." `replycount`,"
                                                            ." `hiddencount`,"
                                                            ." `deletedcount`,"
                                                            ." `postusername`,"
                                                            ." `postuserid`,"
                                                            ." `lastposter`,"
                                                            ." `dateline`,"
                                                            ." `views`,"
                                                            ." `iconid`,"
                                                            ." `notes`,"
                                                            ." `visible`,"
                                                            ." `sticky`,"
                                                            ." `votenum`,"
                                                            ." `votetotal`,"
                                                            ." `attach`,"
                                                            ." `similar`,"
                                                            ." `taglist`,"
                                                            ." `awardedcredits`,"
                                                            ." `threaddesc`)"
                        ." VALUES                           (NULL,"
                                                            ." '$name',"
                                                            ." '',"
                                                            ." '0',"
                                                            ." '0',"
                                                            ." '0',"
                                                            ." '$M4rumid',"
                                                            ." '0',"
                                                            ." '1',"
                                                            ." '0',"
                                                            ." '0',"
                                                            ." '0',"
                                                            ." '$username',"
                                                            ." '$userid',"
                                                            ." '$username',"
                                                            ." '$date',"
                                                            ." '0',"
                                                            ." '0',"
                                                            ." '',"
                                                            ." '1',"
                                                            ." '0',"
                                                            ." '0',"
                                                            ." '0',"
                                                            ." '0',"
                                                            ." '',"
                                                            ." NULL,"
                                                            ." '0',"
                                                            ." 'awarded');");
            $result =   mysql_query("SELECT `threadid` 
                                    FROM `yurivn_GIN`.`thread`
                                    WHERE `dateline` = '$date' AND `title`='$name'");
            $row = mysql_fetch_array($result);
            $thisnewthreadid = $row['threadid'];
            mysql_query("INSERT INTO `yurivn_GIN`.`post` (`postid`,"
                                                        ." `threadid`,"
                                                        ." `parentid`,"
                                                        ." `username`,"
                                                        ." `userid`,"
                                                        ." `title`,"
                                                        ." `dateline`,"
                                                        ." `pagetext`,"
                                                        ." `allowsmilie`,"
                                                        ." `showsignature`,"
                                                        ." `ipaddress`,"
                                                        ." `iconid`,"
                                                        ." `visible`,"
                                                        ." `attach`,"
                                                        ." `infraction`,"
                                                        ." `reportthreadid`,"
                                                        ." `kbank`,"
                                                        ." `post_thanks_amount`)
                        VALUES                          (NULL,"
                                                        ." '$thisnewthreadid',"
                                                        ." '0',"
                                                        ."'$username',"
                                                        ." '$userid',"
                                                        ." '',"
                                                        ." '$date',"
                                                        ." '$postcontent',"
                                                        ." '1',"
                                                        ." '1',"
                                                        ." '',"
                                                        ." '0',"
                                                        ." '1',"
                                                        ." '0',"
                                                        ." '0',"
                                                        ." '0',"
                                                        ." '0.00',"
                                                        ." '0');");            
            $result =   mysql_query("SELECT `postid` 
                                    FROM `yurivn_GIN`.`post`
                                    WHERE `dateline` = '$date' AND `threadid`='$thisnewthreadid'");
            $row = mysql_fetch_array($result);
            $thisnewpostid = $row['postid'];
            mysql_query("UPDATE `yurivn_GIN`.`thread` 
                        SET `firstpostid` = '$thisnewpostid',
                            `lastpostid` = '$thisnewpostid',
                            `lastpost` = '$date'
                        WHERE `thread`.`threadid` =$thisnewthreadid");
            
            //Update post count
            $result = mysql_query("SELECT `posts` 
                                   FROM `yurivn_GIN`.`user`
                                   WHERE `userid` = '$userid'");
            $row = mysql_fetch_array($result);
            $postcount = $row['posts'];
            $postcount = $postcount + 1;
            mysql_query("UPDATE `yurivn_GIN`.`user` 
                        SET `posts` = '$postcount'
                        WHERE `userid` = '$userid'");
            }   
            
function Manga_add_award($username,$hostnum){
    $YunNewManga = 5;
    $YunMainLink = 15;
    $YunMirrorLink = 5;   
    
    $result = mysql_query("SELECT `reputation` 
                           FROM `yurivn_GIN`.`user`
                           WHERE `username` = '$username'");
    $row = mysql_fetch_array($result);
    $Yun = $row['reputation'];
    $award_info = "";
    
    if($hostnum==1){
        $Yun = $Yun + $YunNewManga + $YunMainLink;
        $award_info="Số Yun nhận được:<br>
                    - Post manga mới: $YunNewManga <br>
                    - Upload: $YunMainLink <br>";
    }
        
    if($hostnum==2){
        $Yun = $Yun + $YunNewManga + $YunMainLink + $YunMirrorLink;
        $award_info="Số Yun nhận được:<br>
                    - Post manga mới: $YunNewManga <br>
                    - Upload link chính: $YunMainLink <br>
                    - Upload link mirror: $YunMirrorLink <br>";
    }
        
    mysql_query("UPDATE `yurivn_GIN`.`user` 
                 SET `reputation` = '$Yun'
                 WHERE `username` = '$username'");
    
    
    return $award_info;
}
?>
<?php
class Manga
{
    protected $_mangaId;
    protected $_awardId;
    protected $_oldPostId;
    protected $_active = 1;
    protected $_illustration;
    protected $_mangaTitle;
    protected $_otherTitle;
    protected $_author;
    protected $_type;
    protected $_numberOfChapter = 0;
    protected $_finishedChapter = 0;
    protected $_originalComposition;
    protected $_genre;
    protected $_summary;
    protected $_fansubName;
    protected $_fansubMember;
    protected $_fansubSite;
    protected $_fansubNote;
    protected $_status = 2;
    protected $_numberOfHost;
    protected $_hostName = array();
    protected $_readOnlineStatus;
    protected $_action;

    protected $_forumId;
    protected $_onlineForumId;
    protected $_threadId;
    protected $_postId;
    protected $_posterId;

    protected $_vbphrase;
    protected $_db;
    protected $_table;
    protected $_limit = 0;
    protected $_filter = 'mangatitle';
    protected $_keyword = '';
    protected $_page = 1;

    const RESOURCE_TYPE = 'vietsubmanga_manga';

    function __construct() {
        global $vbphrase, $vbulletin;
        $this->_db = new Database();
        $this->_table = TABLE_PREFIX.'yrms_vietsubmanga_manga';
        $this->_db->setTable($this->_table);
        $this->_vbphrase = $vbphrase;
        $this->_vbulletin = $vbulletin;

        $this->_forumId = $this->_vbulletin->options['yrms_vietsubmanga_id_truyendich'];
        $this->_onlineForumId = $this->_vbulletin->options['yrms_vietsubmanga_id_doconline'];

        define('CHAPTER_LIST', serialize(array('url' => 'vietsubmanga_chapterlist.php&mangaid=', 'name' => $this->_vbphrase['yrms_chaptermanage'])));
        define('MANGA_EDIT', serialize(array('url' => 'vietsubmanga_mangaedit.php&mangaid=', 'name' => $this->_vbphrase['yrms_edit'])));
        define('MANGA_REPORT', serialize(array('url' => 'vietsubmanga_mangareport.php&mangaid=', 'name' => $this->_vbphrase['yrms_brokenlink_report'])));
        define('MANGA_DELETE', serialize(array('url' => 'vietsubmanga_mangadelete.php&mangaid=', 'name' => $this->_vbphrase['yrms_delete'])));
    }

    public function getMangaId()
    {
        return $this->_mangaId;
    }

    public function getMangaTitle()
    {
        return $this->_mangaTitle;
    }

    public function getAuthor()
    {
        return $this->_author;
    }

    public function getFinishedChapter()
    {
        return $this->_finishedChapter;
    }

    public function getFansubName()
    {
        return $this->_fansubName;
    }

    public function getNumberOfChapter()
    {
        return $this->_numberOfChapter;
    }

    public function getStatus()
    {
        return $this->_status;
    }

    public function getForumId()
    {
        return $this->_forumId;
    }

    public function getOnlineForumId()
    {
        return $this->_onlineForumId;
    }

    public function getThreadId()
    {
        return $this->_threadId;
    }

    public function getTotal()
    {
        return $this->_db->getTotal("WHERE active = '1'");
    }

    public function getPosterId()
    {
        return $this->_posterId;
    }

    public function getPostId()
    {
        return $this->_postId;
    }

    public function setThreadId($threadId)
    {
        $this->_threadId = $threadId;
        return $this;
    }

    public function setPostId($postId)
    {
        $this->_postId = $postId;
        return $this;
    }

    public function setPosterId($posterId)
    {
        $this->_posterId = $posterId;
        return $this;
    }

    public function setKeyword($keyword)
    {
        $this->_keyword = $keyword;
        return $this;
    }

    public function setLimit($limit)
    {
        $this->_limit = $limit;
        return $this;
    }

    public function setFilter($filter)
    {
        $this->_filter = $filter;
        return $this;
    }

    public function setPage($page)
    {
        $this->_page = $page;
        return $this;
    }

    public function getData()
    {
        foreach($this as $key => $value) {
            $dataKey = strtolower(str_replace('_', '', $key));
            if (isset($this->$key)) {
                $mangaData[$dataKey] = $this->$key;
            }
        }
        return $mangaData;
    }

    public function setData($mangaData)
    {
        if (isset($mangaData['fansubmember']) && unserialize($mangaData['fansubmember']) !== FALSE) {
            $this->_fansubMember = unserialize($mangaData['fansubmember']);
            unset($mangaData['fansubmember']);
        }
        if (isset($mangaData['hostname']) && unserialize($mangaData['hostname']) !== FALSE) {
            $this->_hostName = unserialize($mangaData['hostname']);
            unset($mangaData['hostname']);
        }

        foreach($this as $key => $value) {
            $dataKey = strtolower(str_replace('_', '', $key));
            if (isset($mangaData[$dataKey])) {
                $this->$key = $mangaData[$dataKey];
            }
        }

        if (empty($this->_author)) {
            $this->_author = $this->_vbphrase['yrms_unknown'];
        }
        if (empty($this->_fansubName)) {
            $this->_fansubName = $this->_vbphrase['yrms_unknown'];
        }
        if (empty($this->_fansubSite)) {
            $this->_fansubSite = $this->_vbphrase['yrms_unknown'];
        }
        if ($this->_type == 2) {
            $this->_numberOfChapter = 1;
        }
        if (empty($this->_posterId)) {
            $this->_posterId = $this->_vbulletin->userinfo['userid'];
        }

        $this->_action = array(
            unserialize(CHAPTER_LIST)
        );
        //    $action = "<a href='vietsubmanga.php?do=chapterlist&mangaid=".$manga->getMangaId()."'>{$vbphrase['yrms_chaptermanage']}</a><br/>
        //               <a href='yrms/vietsubmanga.php?do=mangareport&mangaid=".$manga->getMangaId()."'>{$vbphrase['yrms_brokenlink_report']}</a><br/>";
        //    if($manga->checkOwner()){
        //        $action = "<a href='yrms/vietsubmanga.php?do=mangaedit&mangaid=".$manga->getMangaId()."'>{$vbphrase['yrms_edit']}</a><br/>$action";
        //    }
        //    if(can_moderate($vbulletin->options['yrms_vietsubmanga_id_truyendich']))
        //        $action.= "<a href='yrms/vietsubmanga.php?do=delete&mangaid=".$manga->getMangaId()."'>{$vbphrase['yrms_delete']}</a><br/>";

        return $this;
    }

    public function load($mangaId)
    {
        $award = new Award();
        $awardTable = $award->getTable();
        $mangaData = $this->_db->fetchOnce("SELECT $this->_table.*, $awardTable.awardid
                                            FROM $this->_table, $awardTable
                                            WHERE `$this->_table`.`mangaid` = `$awardTable`.`resourceid`
                                            AND `$this->_table`.`mangaid` = '$mangaId'
                                            AND `$awardTable`.`resourcetype` = '".self::RESOURCE_TYPE."'");

        if (!empty($mangaData)) {
            $this->setData($mangaData);
            return $this;
        } else {
            return FALSE;
        }

    }

    public function save() {
        $this->threadSave();

        $mangaData = array(
            'threadid' => $this->_threadId,
            'postid' => $this->_postId,
            'illustration' => $this->_illustration,
            'mangatitle' => $this->_mangaTitle,
            'othertitle' => $this->_otherTitle,
            'author' => $this->_author,
            'type' => $this->_type,
            'numberofchapter' => $this->_numberOfChapter,
            'originalcomposition' => $this->_originalComposition,
            'genre' => $this->_genre,
            'summary' => $this->_summary,
            'fansubname' => $this->_fansubName,
            'fansubmember' => serialize($this->fansubmember),
            'fansubsite' => $this->_fansubSite,
            'fansubnote' => $this->_fansubNote,
            'status' => $this->_status,
            'numberofhost' => $this->numberOfHost,
            'hostname' => serialize($this->_hostName)
        );

        if($this->_mangaId) {
            $this->_db->setTable($this->_table)->update($mangaData, "`mangaid` = '$this->_mangaId'");
        } else {
            $this->_db->setTable($this->_table)->insert($mangaData);
            $this->_mangaId=$this->_vbulletin->db->insert_id();
        }
    }

    public function threadSave()
    {
        //region Set_thread_main_Info

        $postInfo['prefixid'] = $this->_vbulletin->options['yrms_vietsubmanga_prefixid_type'.$this->_type];

        if(!empty($this->_fansubName)) {
            $postInfo['title'] .= "[{$this->_fansubName}] ";
        }
        if($this->_type == 3 && !empty($this->_originalComposition)) {
            $postInfo['title'] .= "[{$this->_originalComposition}] ";
        }
        $postInfo['title'] .= $this->_mangaTitle;

        $postInfo['allowsmilie'] = 1;
        $postInfo['visible'] = 1;
        $postInfo['parseurl'] = 1;
        //endregion

        //region Manga_info
        if ($this->_numberOfChapter == 0)
            $numberOfChapter = '??';
        else
            $numberOfChapter = $this->_numberOfChapter;

        $mangaInfo = construct_phrase($this->_vbphrase['yrms_postformat_highlightvalue'], $this->_vbphrase['yrms_mangatitle'], $this->_mangaTitle)."\n";
        if ($this->_otherTitle) {
            $mangaInfo .= construct_phrase($this->_vbphrase['yrms_postformat_highlightvalue'], $this->_vbphrase['yrms_othertitle'], $this->_otherTitle)."\n";
        }
        $mangaInfo .= construct_phrase($this->_vbphrase['yrms_postformat_highlightvalue'], $this->_vbphrase['yrms_author'], $this->_author)."\n"
                     .construct_phrase($this->_vbphrase['yrms_postformat_highlightvalue'], $this->_vbphrase['yrms_genre'], $this->_genre)."\n"
                     .construct_phrase($this->_vbphrase['yrms_postformat_highlightvalue'], $this->_vbphrase['yrms_numberofchapter'], $numberOfChapter)."\n"
                     .construct_phrase($this->_vbphrase['yrms_postformat_highlightvalue'], $this->_vbphrase['yrms_projectstatus'], $this->_vbphrase["yrms_projectstatus{$this->_status}"])."\n";

        $fansubInfo = '';
        if(!empty($this->_fansubMember['translator'])) {
            $fansubInfo .= construct_phrase($this->_vbphrase['yrms_postformat_highlightvalue'], $this->_vbphrase['yrms_translator'], get_username_massively($this->_fansubMember['translator']))."\n";
        }
        if(!empty($this->_fansubMember['proofreader'])) {
            $fansubInfo .= construct_phrase($this->_vbphrase['yrms_postformat_highlightvalue'], $this->_vbphrase['yrms_proofreader'], get_username_massively($this->_fansubMember['proofreader']))."\n";
        }
        if(!empty($this->_fansubMember['editor'])) {
            $fansubInfo .= construct_phrase($this->_vbphrase['yrms_postformat_highlightvalue'], $this->_vbphrase['yrms_mangaeditor'], get_username_massively($this->_fansubMember['editor']))."\n";
        }
        if(!empty($this->_fansubMember['qualitychecker'])) {
            $fansubInfo .= construct_phrase($this->_vbphrase['yrms_postformat_highlightvalue'], $this->_vbphrase['yrms_qualitychecker'], get_username_massively($this->_fansubMember['qualitychecker']))."\n";
        }
        if(!empty($this->_fansubMember['uploader'])) {
            $fansubInfo .= construct_phrase($this->_vbphrase['yrms_postformat_highlightvalue'], $this->_vbphrase['yrms_uploader'], get_username_massively($this->_fansubMember['uploader']))."\n";
        }
        $fansubInfo .= construct_phrase($this->_vbphrase['yrms_postformat_highlightvalue'], $this->_vbphrase['yrms_fansub_website'], $this->_fansubSite);
        //endregion

        $linkformat = false;
        if($linkformat === false){
            $linkformat = $this->_vbphrase['yrms_tobeupdated'];
        }

        //build post
        $postInfo['pagetext']= construct_phrase($this->_vbphrase['yrms_postformat_vietsubmanga'],
                                                $this->_vbulletin->options['yrms_main_illustrationwidth'],
                                                $this->_illustration,
                                                $mangaInfo,
                                                $this->_summary,
                                                $this->_fansubName,
                                                $fansubInfo,
                                                $this->_fansubNote,
                                                $linkformat);

        $thread = new ForumPost('thread');
        $thread->setForumId($this->_forumId)
               ->setThreadId($this->_threadId)
               ->setPostInfo($postInfo)
               ->setYrmspost(1)
               ->setPosterId($this->_posterId)
               ->save();
        $this->_threadId = $thread->getThreadId();
        $this->_postId = $thread->getPostId();
    }
    
    public function build_linkformat(){
        global $vbulletin, $vbphrase;
        if($this->readonlinestatus==1){
            $head = "{$vbphrase['yrms_readonlinelink']}";
        } else{
            $head = "{$vbphrase['yrms_chaptername']}";
        }
        if(!empty($this->numberofhost)){
            $head .="|{colsp=$this->numberofhost}Download\n";
        } else{
            $head .="\n";
        }
        $body = array();
        $allchapters = array();
        $allchaptersid=$vbulletin->db->query_read("SELECT `chapterid` "
                                                . "FROM `".TABLE_PREFIX."yrms_vietsubmanga_chapter` "
                                                . "WHERE `mangaid` = '$this->_mangaId' "
                                                . "ORDER BY `type`,`chapternumber` ASC ");                                       
        if($vbulletin->db->num_rows($allchaptersid)!=0){
            while ($chapteridpack = $vbulletin->db->fetch_array($allchaptersid)) {
                $allchapters[] = new Chapter($this, $chapteridpack['chapterid']);
            }
            
            foreach($allchapters as $chapter){
                $rows = array("readonline"=>"");
                if (!empty($chapter->numberofhost)) {
                    $rows += array_fill_keys(array_keys(array_flip($this->hostname)), "");
                }
                if($chapter->rate==1){
                    $rows = array("readonline"=>"");
                    if (!empty($chapter->numberofhost)) {
                        $rows[] = '{colsp=' . $this->numberofhost . '}[CENTER][URL="' . $vbulletin->options['bburl'] . "/" . fetch_seo_url('post', fetch_postinfo($chapter->postid)) . '"]Download[/URL][/CENTER] ';
                    }
                }
                else if(!empty($chapter->numberofhost)){
                    foreach($chapter->downloadlink as $hostname => $hostlinks){
                        if(count($hostlinks)>1){
                            $thishost=array();
                            $partnumber=1;
                            foreach($hostlinks as $hostlink){
                                $thishost[] = '[URL="'.$hostlink.'"]'.$hostname.' '.$partnumber.'[/URL]';
                                $partnumber++;
                            }
                            $thishost = '[CENTER]'.implode(" - ", $thishost).'[/CENTER] ';
                        } else{
                            $thishost = '[CENTER][URL="'.$hostlinks[0].'"]'.$hostname.'[/URL][/CENTER] ';
                        }
                        $rows[$hostname]=$thishost;
                    }
                }
                if (!empty($chapter->readonlinepostid)) {
                    $rows["readonline"] = '[CENTER][URL="' . $vbulletin->options['bburl']."/".fetch_seo_url('post', fetch_postinfo($chapter->readonlinepostid)) . '"]' . $vbphrase["yrms_chaptertype{$chapter->type}"] . " " . $chapter->chapternumber . '[/URL][/CENTER] ';
                } else{
                    $rows["readonline"] = '[CENTER]'.$vbphrase["yrms_chaptertype{$chapter->type}"] . ' ' . $chapter->chapternumber.'[/CENTER] ';
                }
                
                $rows = implode("|", $rows);
                $body[] = $rows;                 
            }
            //$head=rtrim($head, "|");
            $body = implode("\n", $body);
            $linkformat = '[TABLE="head"]'.$head."\n".$body.'[/TABLE]';
            return $linkformat;
        } else{
            return false;
        }
    }    
    
    public function update(){
        global $vbulletin,$vbphrase;
        if(empty($this->_author))
                $this->_author = $vbphrase['yrms_unknown'];
        if(empty($this->_fansubName))
                $this->_fansubName = $vbphrase['yrms_unknown'];
        if(empty($this->_fansubSite))
                $this->_fansubSite = $vbphrase['yrms_unknown'];
        if($this->_type == 2)
            $this->_numberOfChapter = 1;
        

        //if postid was changed, update some information
        if($this->_oldPostId!=$this->_postId && !empty($this->_postId)){
            $this->_threadId=$vbulletin->db->fetch_array($vbulletin->db->query_read("SELECT `threadid` FROM `".TABLE_PREFIX."thread` WHERE `firstpostid`='$this->_postId'"));
            $this->_threadId = $this->_threadId['threadid'];
            $vbulletin->db->query_write("UPDATE `".TABLE_PREFIX."post` 
                                         SET    `yrmspost`='0'
                                         WHERE  `postid`='$this->_oldPostId'"); 
            $vbulletin->db->query_write("UPDATE `".TABLE_PREFIX."post` 
                                         SET    `yrmspost`='1'
                                         WHERE  `postid`='$this->_postId'");
            $vbulletin->db->query_write("UPDATE `".TABLE_PREFIX."yrms_award` 
                                         SET    `postid`='$this->_postId'
                                         WHERE  `postid`='$this->_oldPostId'"); 
        }
        //update new information to database
        $vbulletin->db->query_write("UPDATE `".TABLE_PREFIX."yrms_vietsubmanga_manga` "
                                  . "SET     `awardid`='$this->_awardId',"
                                          . "`threadid`='$this->_threadId',"
                                          . "`postid`='$this->_postId',"
                                          . "`active`='$this->_active',"
                                          . "`illustration`='$this->_illustration',"
                                          . "`mangatitle`='$this->_mangaTitle',"
                                          . "`othertitle`='$this->_otherTitle',"
                                          . "`author`='$this->_author',"
                                          . "`type`='$this->_type',"
                                          . "`finishedchapter`='$this->_finishedChapter',"
                                          . "`numberofchapter`='$this->_numberOfChapter',"
                                          . "`originalcomposition`='$this->_originalComposition',"
                                          . "`genre`='$this->_genre',"
                                          . "`summary`='$this->_summary',"
                                          . "`fansubname`='$this->_fansubName',"
                                          . "`fansubmember`='".serialize($this->fansubmember)."',"
                                          . "`fansubsite`='$this->_fansubSite',"
                                          . "`fansubnote`='$this->_fansubNote',"
                                          . "`status`='$this->status',"
                                          . "`numberofhost`='$this->numberofhost',"
                                          . "`hostname`='".serialize($this->hostname)."',"
                                          . "`readonlinestatus`='$this->readonlinestatus'"
                                  . "WHERE   `mangaid`=$this->_mangaId");
        
        //and update the post in forum
        $edit = $this->build_headpost();
        editPost($edit);
         
    }
    
    public function get($mangaId){
        global $vbulletin;
        $mangainfo=$vbulletin->db->fetch_array($vbulletin->db->query_read("SELECT * FROM `".TABLE_PREFIX."yrms_vietsubmanga_manga` WHERE `mangaid`='$mangaId'"));
        $this->set($mangainfo);
        return $mangainfo;
    }

    public function checkOwner($userId=''){
        if(empty($userId)) {
            $userId = $this->_vbulletin->userinfo['userid'];
        }

        if(can_moderate($this->_forumId)) {
            return true;
        }

        if (is_first_poster($this->_threadId)) {
            return true;
        }

        return false;
//        global $vbulletin;
//        if (empty($userId)) {
//            $userId = $vbulletin->userinfo['userId'];
//        }
//        if (!empty($mangaId)) {
//            $this->get($mangaId);
//        }
//
//        if (!empty($this->fansubmember)) {
//            foreach ($this->fansubmember as $fansubmembers) {
//                if (in_array($userId, explode(',', $fansubmembers))) {
//                    return true;
//                }
//            }
//            return false;
//        } else {
//            return false;
//        }
    }
    
    public function getCollection(){
        if($this->_keyword){
            if($this->_filter == 'mangatitle') {
                $searchQuery = "AND $this->_table.mangatitle LIKE '$this->_keyword' OR `othertitle` LIKE '$this->_keyword'";
            } else {
                $searchQuery = "AND $this->_table.$this->_filter LIKE '$this->_keyword'";
            }
        }

        if ($this->_limit) {
            $totalManga= $this->getTotal();

            $totalPage = ceil($totalManga/$this->_limit);
            if($this->_page > $totalPage) {
                $this->_page = $totalPage;
            }

            $limitQuery = "LIMIT ".($this->_limit*($this->_page-1)).",$this->_limit";
        }

        $chapterObject = new Chapter;
        $chapterTable = $chapterObject->getTable();
        $query = "SELECT $this->_table.*, IFNULL(chapter.count_chapter, 0) AS `finishedchapter` FROM `$this->_table` LEFT JOIN
                  (
                    SELECT COUNT(*) AS count_chapter, mangaid
                    FROM $chapterTable
                    WHERE `active` = '1'
                    AND `type` = '1'
                    GROUP BY mangaid
                  ) chapter
                  ON $this->_table.mangaid = chapter.mangaid
                  WHERE $this->_table.`active` = '1'
                  $searchQuery
                  $limitQuery";
        $mangaDatas = $this->_db->fetchAll($query);

        if (!empty($mangaDatas)) {
            $mangaCollection = array();
            foreach ($mangaDatas as $mangaData) {
                $this->setData($mangaData);
                $mangaCollection[] = clone $this;
            }
            return $mangaCollection;
        } else {
            return NULL;
        }

    }
    
    public function reward(){
        global $vbulletin,$vbphrase;
        //analysis information
        if(empty($this->_author))
                $this->_author = $vbphrase['yrms_unknown'];
        if(empty($this->_fansubName))
                $this->_fansubName = $vbphrase['yrms_unknown'];
        if(empty($this->_fansubSite))
                $this->_fansubSite = $vbphrase['yrms_unknown'];
        if($this->_type == 2)
            $this->_numberOfChapter = 1;
        
        //get some misc information
        $this->_threadId=$vbulletin->db->fetch_array($vbulletin->db->query_read("SELECT `threadid` FROM `".TABLE_PREFIX."thread` WHERE `firstpostid`='$this->_postId'"));
        $this->_threadId=$this->_threadId['threadid'];
        
        $userid=$vbulletin->db->fetch_array($vbulletin->db->query_read("SELECT `userid` FROM `".TABLE_PREFIX."post` WHERE `postid`='$this->_postId'"));
        $userid=$userid['userid'];
        $this->fansubmember=array(
            $vbphrase['yrms_uploader'] => $userid
        );
        
        //make the post became yrmspost
        $vbulletin->db->query_write("UPDATE `".TABLE_PREFIX."post` SET `yrmspost`=1 WHERE `postid`=$this->_postId");
        
        //reward the poster
        $award = new Award;
        $award->postid = $this->_postId;
        $award->awardcontent = array(
            "{$userid}" => $vbulletin->options['yrms_vietsubmanga_yun_newproject'],
        );
        $award->awardtype = 'vietsubmanga_newproject';
        $award->add();
        
        //and add the manga to database     
        $vbulletin->db->query_write("INSERT INTO `".TABLE_PREFIX."yrms_vietsubmanga_manga`(`awardid`, `threadid`, `postid`, `illustration`, `mangatitle`, `othertitle`, `author`, `type`, `numberofchapter`, `originalcomposition`, `genre`, `summary`, `fansubname`, `fansubmember`, `fansubsite`, `fansubnote`, `status`, `numberofhost`) 
                                    VALUES ('$award->awardid','$this->_threadId','$this->_postId','$this->_illustration','$this->_mangaTitle','$this->_otherTitle','$this->_author',$this->_type,'$this->_numberOfChapter','$this->_originalComposition','$this->_genre','$this->_summary','$this->_fansubName','".serialize($this->fansubmember)."','$this->_fansubSite','$this->_fansubNote','$this->status','$this->numberofhost')");         
    }
}

class Chapter{
    public $manga;
    
    public $chapterid;
    public $postid;
    public $readonlinepostid;
    public $active;
    public $status;
    public $type;
    public $chapternumber;
    public $chaptertitle;
    public $rate;
    public $numberofhost;
    public $downloadlink;
    public $onlinelink;
    public $fansubmember;
    public $fansubnote;
    
    public $poster;
    public $readonlineposter;

    protected $_db;
    protected $_table;
    function __construct() {
        $this->_db = new Database();
        $this->_table = TABLE_PREFIX.'yrms_vietsubmanga_chapter';
    }

    public function getTable()
    {
        return $this->_table;
    }

    public function getAll($perPage = ''){
        global $vbulletin, $vbphrase;
        $totalchapters=$vbulletin->db->query_read("SELECT * FROM `".TABLE_PREFIX."yrms_vietsubmanga_chapter` WHERE `mangaid`='{$this->manga->mangaid}'");
        if (empty($_GET['page'])) {
            $page = 1;
        } else {
            $page = $_GET['page'];
        }

        if(!empty($perPage))
        if ($page > ceil($vbulletin->db->num_rows($totalchapters) / $perPage)) {
            $page = ceil($vbulletin->db->num_rows($totalchapters) / $perPage);
        }
        if($page<1){
            $page=1;
        }

        $query = "SELECT * FROM `".TABLE_PREFIX."yrms_vietsubmanga_chapter` WHERE `mangaid`='{$this->manga->mangaid}' LIMIT ".(20*($page-1)).",20";

        $result = $vbulletin->db->query_read($query);

        $chapters = array();
        while($row = $vbulletin->db->fetch_array($result)){
            $chapters[] = $row;
        }

        return $chapters;
    }
    
    public function add(){
        global $vbulletin,$vbphrase;
        // <editor-fold defaultstate="collapsed" desc="analysis information">
        if ($this->_type == 2) {
            $this->chapternumber = "";
        }
        $this->fansubmember = str_replace('false', '', $this->fansubmember);
        $this->fansubmember = str_replace(',,', '', $this->fansubmember);
        // </editor-fold>
        
        // <editor-fold defaultstate="collapsed" desc="make new update post, and new read online thread">
        $downloadpost = $this->buildpost('download');
        $this->_postId = newPost($downloadpost);

        if (!empty($this->onlinelink)) {
            $onlinepost = $this->buildpost('online');
            $idpack = newThread($onlinepost);
            $this->readonlinepostid = $idpack['postid'];
        }
        //set yrms type for new posts
        $vbulletin->db->query_write("UPDATE `" . TABLE_PREFIX . "post` "
                . "SET `yrmspost`=1 "
                . "WHERE `postid` = '$this->_postId'");
        $vbulletin->db->query_write("UPDATE `" . TABLE_PREFIX . "post` "
                . "SET `yrmspost`=1 "
                . "WHERE `postid` = '$this->readonlinepostid'");
        // </editor-fold>          
          
        // <editor-fold defaultstate="collapsed" desc="add chapter to the database ">
        $vbulletin->db->query_write("INSERT INTO `" .TABLE_PREFIX. "yrms_vietsubmanga_chapter`"
                . "(`mangaid`,"
                . "`postid`, "
                . "`readonlinepostid`, "
                . "`active`, "
                . "`status`, "
                . "`type`, "
                . "`chapternumber`, "
                . "`chaptertitle`, "
                . "`rate`, "
                . "`numberofhost`, "
                . "`downloadlink`, "
                . "`onlinelink`, "
                . "`fansubmember`, "
                . "`fansubnote`) "
        . "VALUES ('{$this->manga->mangaid}',"
                . "'$this->_postId',"
                . "'$this->readonlinepostid',"
                . "'1',"
                . "'$this->status',"
                . "'$this->_type',"
                . "'$this->chapternumber',"
                . "'$this->chaptertitle',"
                . "'$this->rate',"
                . "'$this->numberofhost',"
                . "'".serialize($this->downloadlink)."',"
                . "'$this->onlinelink',"
                . "'".serialize($this->fansubmember)."',"
                . "'$this->_fansubNote')");
        $this->chapterid = $vbulletin->db->insert_id();
// </editor-fold>

        // <editor-fold defaultstate="collapsed" desc="update manga">
        $this->manga->fansubmember = update_array_to_array($this->fansubmember, $this->manga->fansubmember);
        $this->manga->fansubmember = reindex_array($this->manga->fansubmember, array("translator","proofreader","editor","qualitychecker","uploader"));
        if ($this->_type == 1 || $this->_type ==2) {
            $this->manga->finishedchapter++;
        }
        if(!empty($this->onlinelink)){
            $this->manga->readonlinestatus = 1;
        }
        
        $chapterhostname=array();
        foreach($this->downloadlink as $hostname => $hostlink){
            $chapterhostname[] = $hostname;
        }
        $this->manga->hostname = update_array_to_array($chapterhostname, $this->manga->hostname);
        
        $this->manga->numberofhost = count($this->manga->hostname);
        $this->manga->update();
        
        // </editor-fold>
        
        // <editor-fold defaultstate="collapsed" desc=" reward ">
        //For download post
        $award_download = new Award;
        $award_download->postid = $this->_postId;
        if (strpos(strtolower($this->manga->fansubsite),'yurivn')) {
            $award_download->awardcontent = $this->build_awardcontent_fansubmember();
        } else{
            if (array_key_exists($vbulletin->userinfo['userid'], $award_download->awardcontent)) {
                $award_download->awardcontent[$vbulletin->userinfo['userid']] += $vbulletin->options['yrms_vietsubmanga_yun_uploader'];
            } else {
                $award_download->awardcontent += array($vbulletin->userinfo['userid'] => $vbulletin->options['yrms_vietsubmanga_yun_uploader']);
            }
        }
        
        if ($this->numberofhost>=2){
            if (array_key_exists($vbulletin->userinfo['userid'], $award_download->awardcontent)) {
                $award_download->awardcontent[$vbulletin->userinfo['userid']] += $vbulletin->options['yrms_vietsubmanga_yun_mirror'];
            } else {
                $award_download->awardcontent += array($vbulletin->userinfo['userid'] => $vbulletin->options['yrms_vietsubmanga_yun_mirror']);
            }
        }
        var_dump($award_download->awardcontent);
        $award_download->resourcetype = 'vietsubmanga';
        $award_download->resourceid = $this->chapterid;
        $award_download->resourceheadid = $this->manga->mangaid;
        $award_download->add();

        //For readonline post
        if(!empty($this->readonlinepostid)){
            $award_online = new Award;
            $award_online->postid = $this->readonlinepostid;
            $award_online->awardcontent = array($vbulletin->userinfo['userid'] => $vbulletin->options['yrms_vietsubmanga_yun_online']);
            $award_online->resourcetype = 'vietsubmanga';
            $award_online->resourceid = $this->chapterid;
            $award_online->resourceheadid = $this->manga->mangaid;
            $award_online->add();
        }
        // </editor-fold>
        
        // <editor-fold defaultstate="collapsed" desc=" return the success message ">
        if (isset($award_online)) {
            $awardcontent = update_array_to_array($award_online->awardcontent, $award_download->awardcontent);
        } 
        else {
            $awardcontent = $award_download->awardcontent;
        }

        $awardinfo = array();
        foreach ($awardcontent as $userid => $amount) {
            $userinfo = fetch_userinfo($userid);
            $username = $userinfo["username"];
            $awardinfo[] = "{$username}: {$amount} {$vbulletin->options['yrms_main_moneyname']}";
        }
        $awardinfo = implode("\n", $awardinfo);


        $return_message = construct_phrase($vbphrase['yrms_msg_success_newchapter'],                                            $vbulletin->userinfo['username'], 
                                           $vbphrase["yrms_chaptertype{$this->_type}"] . " " . $this->chapternumber,                                            $this->manga->mangatitle,                                            nl2br($awardinfo));


        return $return_message;


// </editor-fold>                                         
    }
    
    public function reward(){
        global $vbulletin,$vbphrase;
        // <editor-fold defaultstate="collapsed" desc="analysis information">
        if ($this->_type == 2) {
            $this->chapternumber = "";
        }
        $this->fansubmember = str_replace('false', '', $this->fansubmember);
        $this->fansubmember = str_replace(',,', '', $this->fansubmember);
        if ($this->_postId == 0){
            $this->_postId = $this->manga->postid;
        }
        // </editor-fold>
        
        // <editor-fold defaultstate="collapsed" desc="create new update post, and new read online thread">
        // <editor-fold defaultstate="collapsed" desc=" create new update post ">
        //normal case: there is update post for the chapter, or there is not but no 18+ content.
        //reward and add chapter to the database only, no need to make any new post. Only set the post to yrmspost.
        if ($this->_postId != $this->manga->postid){
            $vbulletin->db->query_write("UPDATE `" . TABLE_PREFIX . "post` "
                                      . "SET `yrmspost`=1 "
                                      . "WHERE `postid` = '$this->_postId'");
            if($this->rate==1 && !empty($this->numberofhost)){
                $downloadpost = $this->buildpost('download');
                editPost($downloadpost);
            }
        }
        
        //abnormal case 1: the dumb poster didn't make update post and this chapter is 18+ content
        //we will create a new update post for her
        else if ($this->_postId == $this->manga->postid && $this->rate == 1) {
            $downloadpost = $this->buildpost('download');
            $this->_postId = newPost($downloadpost, $this->poster);
            $vbulletin->db->query_write("UPDATE `" . TABLE_PREFIX . "post` "
                                      . "SET `yrmspost`=1 "
                                      . "WHERE `postid` = '$this->_postId'");
        }


        //abnormal case 2: no download link, only readonline link.
        //Skip this step
        // </editor-fold>

        // <editor-fold defaultstate="collapsed" desc=" create new read online thread ">
        //normal case: readonline post is a separate topic
        //do nothing but set the post to yrms post, and reformat it if the chapter is 18+ content
        if (!empty($this->onlinelink)) {
            $vbulletin->db->query_write("UPDATE `" . TABLE_PREFIX . "post` "
                                      . "SET `yrmspost`=1 "
                                      . "WHERE `postid` = '$this->readonlinepostid'");
            if($this->rate==1){
                $readonlinepost = $this->buildpost('online');
                editPost($readonlinepost);
            }
        }
        
        //abnormal case: readonline post is the same as update post, or manga post
        //turn it into normal case and treat as normal case
        if (!empty($this->onlinelink) && ($this->readonlinepostid == $this->_postId || $this->readonlinepostid == $this->manga->postid || $this->readonlinepostid == 0)) {
            $readonlinepost = $this->buildpost('online');
            $idpack = newThread($readonlinepost,  $this->readonlineposter);
            $this->readonlinepostid = $idpack['postid'];
            $vbulletin->db->query_write("UPDATE `" . TABLE_PREFIX . "post` "
                                      . "SET `yrmspost`=1 "
                                      . "WHERE `postid` = '$this->readonlinepostid'");
        }
        // </editor-fold>
        // </editor-fold>          
            
        // <editor-fold defaultstate="collapsed" desc="add chapter to the database ">
        $vbulletin->db->query_write("INSERT INTO `" .TABLE_PREFIX. "yrms_vietsubmanga_chapter`"
                . "(`mangaid`,"
                . "`postid`, "
                . "`readonlinepostid`, "
                . "`active`, "
                . "`status`, "
                . "`type`, "
                . "`chapternumber`, "
                . "`chaptertitle`, "
                . "`rate`, "
                . "`numberofhost`, "
                . "`downloadlink`, "
                . "`onlinelink`, "
                . "`fansubmember`, "
                . "`fansubnote`) "
        . "VALUES ('{$this->manga->mangaid}',"
                . "'$this->_postId',"
                . "'$this->readonlinepostid',"
                . "'1',"
                . "'$this->status',"
                . "'$this->_type',"
                . "'$this->chapternumber',"
                . "'$this->chaptertitle',"
                . "'$this->rate',"
                . "'$this->numberofhost',"
                . "'".serialize($this->downloadlink)."',"
                . "'$this->onlinelink',"
                . "'".serialize($this->fansubmember)."',"
                . "'$this->_fansubNote')");
        $this->chapterid = $vbulletin->db->insert_id();
// </editor-fold>

        // <editor-fold defaultstate="collapsed" desc="update manga">
        $this->manga->fansubmember = update_array_to_array($this->fansubmember, $this->manga->fansubmember);
        $this->manga->fansubmember = reindex_array($this->manga->fansubmember, array("translator","proofreader","editor","qualitychecker","uploader"));
        if ($this->_type == 1 || $this->_type ==2) {
            $this->manga->finishedchapter++;
        }
        if(!empty($this->onlinelink)){
            $this->manga->readonlinestatus = 1;
        }
        
        $chapterhostname=array();
        if(!empty($this->numberofhost)){
            foreach($this->downloadlink as $hostname => $hostlink){
                $chapterhostname[] = $hostname;
            }
            $this->manga->hostname = update_array_to_array($chapterhostname, $this->manga->hostname);

            $this->manga->numberofhost = count($this->manga->hostname);
        }
        $this->manga->update();
        
        // </editor-fold>
        
        // <editor-fold defaultstate="collapsed" desc=" reward ">
        //For download post
        $award_download = new Award;
        $award_download->postid = $this->_postId;
        if (strpos(strtolower($this->manga->fansubsite),'yurivn')) {
            $award_download->awardcontent = $this->build_awardcontent_fansubmember();
        }
        if(!empty($this->numberofhost)){
            if (array_key_exists($this->poster, $award_download->awardcontent)) {
                $award_download->awardcontent[$this->poster] += $vbulletin->options['yrms_vietsubmanga_yun_uploader'];
            } else {
                $award_download->awardcontent += array($this->poster => $vbulletin->options['yrms_vietsubmanga_yun_uploader']);
            }
            if ($this->numberofhost >=2){
                if (array_key_exists($this->poster, $award_download->awardcontent)) {
                    $award_download->awardcontent[$this->poster] += $vbulletin->options['yrms_vietsubmanga_yun_mirror'];
                } else {
                    $award_download->awardcontent += array($this->poster => $vbulletin->options['yrms_vietsubmanga_yun_mirror']);
                }
            }    
        }
        
        $award_download->resourcetype = 'vietsubmanga';
        $award_download->resourceid = $this->chapterid;
        $award_download->resourceheadid = $this->manga->mangaid;
        $award_download->add();

        //For readonline post
        if(!empty($this->readonlinepostid)){
            $award_online = new Award;
            $award_online->postid = $this->readonlinepostid;
            $award_online->awardcontent = array($this->readonlineposter => $vbulletin->options['yrms_vietsubmanga_yun_online']);
            $award_online->resourcetype = 'vietsubmanga';
            $award_online->resourceid = $this->chapterid;
            $award_online->resourceheadid = $this->manga->mangaid;
            $award_online->add();
        }
        // </editor-fold>
        
        // <editor-fold defaultstate="collapsed" desc=" return the success message ">
        if (isset($award_online)) {
            $awardcontent = $award_download->awardcontent + $award_online->awardcontent;
        } 
        else {
            $awardcontent = $award_download->awardcontent;
        }

        $awardinfo = array();
        foreach ($awardcontent as $userid => $amount) {
            $userinfo = fetch_userinfo($userid);
            $username = $userinfo["username"];
            $awardinfo[] = "{$username}: {$amount} {$vbulletin->options['yrms_main_moneyname']}";
        }
        $awardinfo = implode("\n", $awardinfo);


        $return_message = construct_phrase($vbphrase['yrms_msg_success_rewardchapter'],
                                           $vbulletin->userinfo['username'], 
                                           $vbphrase["yrms_chaptertype{$this->_type}"] . " " . $this->chapternumber, 
                                           $this->manga->mangatitle,                                            
                                           nl2br($awardinfo));


        return $return_message;


// </editor-fold>                                         
    }
    
    public function get(){
        global $vbulletin;
        $chapterinfo=$vbulletin->db->fetch_array($vbulletin->db->query_read("SELECT * FROM `".TABLE_PREFIX."yrms_vietsubmanga_chapter` WHERE `chapterid`='$this->chapterid'"));
        $this->set($chapterinfo);  
    }
    
    public function set($chapterinfo){
        $this->chapterid = $chapterinfo['chapterid'];
        $this->_postId = $chapterinfo['postid'];
        $this->readonlinepostid = $chapterinfo['readonlinepostid'];
        $this->_active = $chapterinfo['active'];
        $this->status = $chapterinfo['status'];
        $this->_type = $chapterinfo['type'];
        if ($chapterinfo['chapternumber'] == 0) {
            $this->chapternumber = "";
        } else{
            $this->chapternumber = $chapterinfo['chapternumber'];
        }
        $this->chaptertitle = $chapterinfo['chaptertitle'];
        $this->rate = $chapterinfo['rate'];
        $this->numberofhost = $chapterinfo['numberofhost'];
        $this->downloadlink = unserialize($chapterinfo['downloadlink']);
        $this->onlinelink = $chapterinfo['onlinelink'];
        $this->fansubmember = unserialize($chapterinfo['fansubmember']);
        $this->_fansubNote = $chapterinfo['fansubnote'];
    }
    
    public function chapterlist(){
        global $vbulletin, $vbphrase;
        $totalchapters=$vbulletin->db->query_read("SELECT * FROM `".TABLE_PREFIX."yrms_vietsubmanga_chapter` WHERE `mangaid`='{$this->manga->mangaid}'");
        if (empty($_GET['page'])) {
            $page = 1;
        } else {
            $page = $_GET['page'];
        }
        if ($page > ceil($vbulletin->db->num_rows($totalchapters) / 20)) {
            $page = ceil($vbulletin->db->num_rows($totalchapters) / 20);
        }
        if($page<1){
            $page=1;
        }
        
        $query = "SELECT * FROM `".TABLE_PREFIX."yrms_vietsubmanga_chapter` WHERE `mangaid`='{$this->manga->mangaid}' LIMIT ".(20*($page-1)).",20";
        $chapterlist = '';    
        $chapters = $vbulletin->db->query_read($query);
        
        $tableorder = 1;
        while($chapter = $vbulletin->db->fetch_array($chapters)){
            $this->set($chapter);
            $action = '';
            
            // <editor-fold defaultstate="collapsed" desc=" Analyze information ">
            if ($this->chapternumber==0) {
                $chapternumber = '';
            } else {
                $chapternumber = $this->chapternumber;
            }
            
            if(empty($this->chaptertitle)){
                $chaptertitle='';
            } else{
                $chaptertitle = " - ".$this->chaptertitle;
            }
            
            $linkstatus = 1;
            if ($this->numberofhost > 1) {
                $mirrorstatus = 1;
            } else {
                $mirrorstatus = 0;
                $action.='<a href="yrms/vietsubmanga.php?do=chaptermirrorlinkadd&chapterid='.$this->chapterid.'">'.$vbphrase['yrms_mirrorlinkadd'].'</a><br/>';
                if (!empty($this->numberofhost)) {
                    $linkstatus = 1;
                    $action .= '<a href="yrms/vietsubmanga.php?do=chapterreport&chapterid='.$this->chapterid.'">'.$vbphrase['yrms_brokenlink_report'].'</a><br/>';
                } else{
                    $linkstatus = 0;
                }
            }
            if (!empty($this->onlinelink)){
                $onlinestatus = 1;
            } else {
                $onlinestatus = 0;
                $action.='<a href="yrms/vietsubmanga.php?do=chaptermirrorlinkadd&chapterid='.$this->chapterid.'">'.$vbphrase['yrms_readonlinelinkadd'].'</a><br/>';
            }
            // </editor-fold>
            
            if($this->manga->isfansubmember()){
                $action = '<a href="yrms/vietsubmanga.php?do=chapteredit&chapterid='.$this->chapterid.'">'.$vbphrase['yrms_edit'].'</a><br/>'.$action;
            }
            if(can_moderate($vbulletin->options['yrms_vietsubmanga_id_truyendich'])){
                
            }
                    $action.= '<a href="yrms/vietsubmanga.php?do=delete&chapterid='.$this->chapterid.'">'.$vbphrase['yrms_delete'].'</a><br/>';                               
            
            

            $chapterdata = "<td style=\"text-align:left;\">
                          {$vbphrase["yrms_chaptertype{$this->_type}"]} $chapternumber $chaptertitle <img id=\"{$this->chapterid}toggle\" style=\"vertical-align: middle;margin-bottom:2px;\" height=\"18\" src=\"yrms/images/expand.png\" onclick=\"toggleOption({$this->chapterid})\"/>
                          <div id=\"{$this->chapterid}content\" style=\"display:none;\">$action</div>
                          </td>
                          <td>{$vbphrase["yrms_availablestatus{$mirrorstatus}"]}</td>
                          <td>{$vbphrase["yrms_availablestatus{$onlinestatus}"]}</td>
                          <td>{$vbphrase["yrms_linkstatus{$linkstatus}"]}";
            if($tableorder%2==1)
                $chapterdata = "<tr>$chapterdata</tr>";
            else
                $chapterdata = "<tr class=\"alt\">$chapterdata</tr>";
            $chapterlist .= $chapterdata;
            $tableorder++;
        }
        if(!empty($chapterlist))
            $chapterlist='<div class="datagrid">
                    <table>
                     <thead>
                         <tr>
                             <th>'.$vbphrase['yrms_chaptername'].'</th>
                             <th>'.$vbphrase['yrms_mirrorlink'].'</th>
                             <th>'.$vbphrase['yrms_readonlinelink'].'</th>
                             <th>'.$vbphrase['yrms_linkstatus'].'</th>
                         </tr>
                     </thead>
                     <tbody>
                     '.$chapterlist.'
                     </tbody>
                     <tfoot>
                        <tr>
                            <td colspan="6">
                                <div id="paging" style="padding-right:10px;">
                                    '.  construct_pagenavigation($page, 20, $vbulletin->db->num_rows($totalchapters), removeqsvar($_SERVER['REQUEST_URI'],page)).'
                                </div>
                            </td>  
                        </tr>
                     </tfoot>
                     </table>
                     </div>';
        else{
            $messagetype = "error";
            $message= construct_phrase($vbphrase['yrms_msg_error_emptylist'],$vbphrase['yrms_chaptername'],$vbphrase['yrms_chapteradd']);
            $messagebox = vB_Template::create('yrms_messagebox');
            $messagebox->register('messagetype', $messagetype);
            $messagebox->register('message', $message);
            $chapterlist=$messagebox->render();
        }
        return $chapterlist;
        
    }
    
    public function build_awardcontent_fansubmember(){
        global $vbulletin;
        $awardcontent = array();
        foreach($this->fansubmember as $position => $members){
            ${$position."s"} = explode(",", $members);
            foreach(${$position."s"} as $$position){
                $member = $$position;
                $amount = ceil($vbulletin->options['yrms_vietsubmanga_yun_'.$position]/count(${$position."s"}));
                if (array_key_exists($member, $awardcontent)) {
                    $awardcontent[$member] += $amount;
                } else {
                    $awardcontent += array($member => $amount);
                }
            }
        }
        return $awardcontent;
    }
    
    public function buildpost($type){
        global $vbulletin, $vbphrase;
        //set id
        if($type=='download'){
            $post['forumid'] = $vbulletin->options['yrms_vietsubmanga_id_truyendich'];
            $post['parentid'] = $this->manga->postid;
            $post['threadid'] = $this->manga->threadid;
            $post['postid'] = $this->_postId;
        }
        else if($type=='online'){
            $post['forumid'] = $vbulletin->options['yrms_vietsubmanga_id_doconline'];
            $post['threadid'] = extract_threadid_from_url(fetch_seo_url('post', fetch_postinfo($this->readonlinepostid)));
            $post['postid'] = $this->readonlinepostid;
        }
        
        //title
        if($this->manga->type==2 && $this->_type==1)
            $post['title']="{$this->manga->mangatitle}";
        else    
            $post['title']="{$this->manga->mangatitle} {$vbphrase["yrms_chaptertype{$this->_type}"]} {$this->chapternumber}";
        if(!empty($this->chaptertitle))
            $post['title'].="- {$this->chaptertitle}";        
        
        //other setting
        $post['allowsmilie'] = 1;
        $post['visible'] = 1;
        $post['parseurl'] = 1;
        
        if($type=='download'){       
            if ($this->rate == 1) {
                $post['pagetext'] = construct_phrase($vbphrase['yrms_postformat_updatehide'], $post['title'], $vbphrase['yrms_fansub_note'].": ".$this->_fansubNote, $this->build_linkformat());
            } else {
                $post['pagetext'] = construct_phrase($vbphrase['yrms_postformat_update'], $post['title'], $vbphrase['yrms_fansub_note'].": ".$this->_fansubNote);
            }
        }
        else if ($type == 'online') {
            if ($this->rate == 1) {
                $post['pagetext'] = construct_phrase($vbphrase['yrms_postformat_readonlinehide'], $post['title'], $vbphrase['yrms_fansub_note'].": ".$this->_fansubNote, $this->onlinelink);
            } else{
                $post['pagetext'] = construct_phrase($vbphrase['yrms_postformat_readonline'], $post['title'], $vbphrase['yrms_fansub_note'].": ".$this->_fansubNote, $this->onlinelink);
            }
        }

        return $post;      
    }
    
    public function build_linkformat(){
        $head = "{colsp=$this->numberofhost}Download\n";
        $body = array();
        foreach($this->downloadlink as $hostname => $hostlinks){
            if(count($hostlinks)>1){
                $thishost=array();
                $partnumber=1;
                foreach($hostlinks as $hostlink){
                    $thishost[] = '[URL="'.$hostlink.'"]'.$hostname.' Part'.$partnumber.'[/URL]';
                    $partnumber++;
                }
                $thishost = implode(" - ", $thishost);
            }
            else{
                $thishost = '[URL="'.$hostlinks[1].'"]'.$hostname.'[/URL]';
            }
            $body[]=$thishost;
        }
        //$head=rtrim($head, "|");
        $body = implode("|", $body);
        $linkformat = '[TABLE="head"]'.$head."\n".$body.'[/TABLE]';
        return $linkformat;
    }
    
    public function validate(){
        global $vbulletin, $vbphrase;
        $error = "";
        
        // <editor-fold defaultstate="collapsed" desc=" for reward only ">
        if ($_POST['submitted'] == $vbphrase['yrms_newchapter_award']) {
            //check postid
            if (isset($this->_postId)) {
                if ($vbulletin->db->num_rows($vbulletin->db->query_read("SELECT* FROM `" . TABLE_PREFIX . "post` WHERE `postid`='$this->_postId'")) == 0) {
                    $error.= construct_phrase($vbphrase['yrms_msg_error_postid'], $vbphrase['yrms_posturl']) . "\n";
                }
            }
        }

        // </editor-fold>
        
        // <editor-fold defaultstate="collapsed" desc=" for the rests">
        else{
            //check download link
            if (empty($this->numberofhost)) {
                $error.= construct_phrase($vbphrase['yrms_msg_error_blankfield'], $vbphrase['yrms_numberofhost']) . "\n";
            }         else {
                $number = 1;
                foreach ($this->downloadlink as $hostname => $hostlink) {
                    if (empty($hostname))
                        $error.= construct_phrase($vbphrase['yrms_msg_error_blankfield'], $vbphrase['yrms_hostname'] . " " . $number) . "\n";
                    if (empty($hostlink))
                        $error.= construct_phrase($vbphrase['yrms_msg_error_blankfield'], $vbphrase['yrms_hostlink'] . " " . $number) . "\n";
                    $number++;
                }
            }
        }
        // </editor-fold>
        
        // <editor-fold defaultstate="collapsed" desc=" for both ">
        //check chapter type
        if ($this->_type == 2 && ($this->manga->finishedchapter > 0 || $this->manga->type == 1)) {
            $error.= construct_phrase($vbphrase['yrms_msg_error_invalidchaptertype'], $vbphrase['yrms_type'], $vbphrase['yrms_chaptertype2']) . "\n";
        }


        //check chapternumber
        if (empty($this->chapternumber) && $this->_type != 2) {
            $error.= construct_phrase($vbphrase['yrms_msg_error_blankfield'], $vbphrase['yrms_chapternumber']) . "\n";
        }

        //check mangastatus
        if (empty($this->manga->status)) {
            $error.= construct_phrase($vbphrase['yrms_msg_error_blankselect'], $vbphrase['yrms_projectstatus']) . "\n";
        }


        //check rate
        if (!isset($this->rate)) {
            $error.= construct_phrase($vbphrase['yrms_msg_error_blankselect'], $vbphrase['yrms_rate']) . "\n";
        }


        //check translator
        if (empty($this->fansubmember["translator"])) {
            $error.= construct_phrase($vbphrase['yrms_msg_error_blankfield'], $vbphrase['yrms_translator']) . "\n";
        } else if (strpos($this->fansubmember["translator"], "false") !== false && strpos(strtolower($this->manga->fansubsite), 'yurivn')) {
            $error.= construct_phrase($vbphrase['yrms_msg_error_notvaliduser'], $vbphrase['yrms_translator']) . "\n";
        }

        //check proofreader
        if (!empty($this->fansubmember["proofreader"]) && strpos($this->fansubmember["proofreader"], "false") !== false && strpos(strtolower($this->manga->fansubsite), 'yurivn')) {
            $error.= construct_phrase($vbphrase['yrms_msg_error_notvaliduser'], $vbphrase['yrms_proofreader']) . "\n";
        }

        //check editor
        if (empty($this->fansubmember["editor"])) {
            $error.= construct_phrase($vbphrase['yrms_msg_error_blankfield'], $vbphrase['yrms_mangaeditor']) . "\n";
        } else if (strpos($this->fansubmember["editor"], "false") !== false && strpos(strtolower($this->manga->fansubsite), 'yurivn')) {
            $error.= construct_phrase($vbphrase['yrms_msg_error_notvaliduser'], $vbphrase['yrms_mangaeditor']) . "\n";
        }

        //check qualitychecker
        if (!empty($this->fansubmember["qualitychecker"]) && strpos($this->fansubmember["qualitychecker"], "false") !== false && strpos(strtolower($this->manga->fansubsite), 'yurivn')) {
            $error.= construct_phrase($vbphrase['yrms_msg_error_notvaliduser'], $vbphrase['yrms_qualitychecker']) . "\n";
        }

// </editor-fold>
        return $error;
        
    }
}

?>

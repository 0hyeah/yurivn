<?php
class Award{
    public $awardid;
    public $postid;
    public $awardcontent;
    public $resourcetype;
    public $resourceid;
     
    function add(){
        global $vbulletin;
        $vbulletin->db->query_write("INSERT INTO `".TABLE_PREFIX."yrms_award`(`postid`, `awardcontent`, `resourcetype`, `resourceid`, `resourceheadid`) 
                                    VALUES ($this->postid,'".serialize($this->awardcontent)."','$this->resourcetype','$this->resourceid','$this->resourceheadid')");
        $this->awardid = $vbulletin->db->insert_id();
        
        foreach($this->awardcontent as $userid => $amount ){
            $vbulletin->db->query_write("UPDATE `".TABLE_PREFIX."user` SET `{$vbulletin->options['yrms_main_moneycolumn']}`=`{$vbulletin->options['yrms_main_moneycolumn']}` + $amount WHERE `userid`=$userid");
        }
    }
}
?>

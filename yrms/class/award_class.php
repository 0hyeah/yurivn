<?php
class Award{
    protected $_awardId;
    protected $_postId;
    protected $_awardContent;
    protected $_resourceType;
    protected $_resourceId;

    protected $_vbulletin;
    protected $_db;
    protected $_table;

    function __construct()
    {
        global $vbulletin;
        $this->_vbulletin = $vbulletin;
        $this->_db = new Database();
        $this->_table = TABLE_PREFIX.'yrms_award';
    }

    public function getTable()
    {
        return $this->_table;
    }

    public function setAwardContent($awardContent)
    {
        $this->_awardContent = $awardContent;
        return $this;
    }

    public function setAwardId($awardId)
    {
        $this->_awardId = $awardId;
        return $this;
    }

    public function setPostId($postId)
    {
        $this->_postId = $postId;
        return $this;
    }

    public function setResourceId($resourceId)
    {
        $this->_resourceId = $resourceId;
        return $this;
    }

    public function setResourceType($resourceType)
    {
        $this->_resourceType = $resourceType;
        return $this;
    }

    public function save(){
        $awardData = array(
            'postid' => $this->_postId,
            'awardcontent' => serialize($this->_awardContent),
            'resourcetype' => $this->_resourceType,
            'resourceid' => $this->_resourceId
        );

        if (!$this->_awardId) {
            $this->_db->setTable($this->_table)->insert($awardData);
            $this->_awardId = $this->_db->insert_id();

            foreach($this->_awardContent as $userId => $amount ){
                $this->_db->query("UPDATE `".TABLE_PREFIX."user`
                                   SET `{$this->_vbulletin->options['yrms_main_moneycolumn']}`=`{$this->_vbulletin->options['yrms_main_moneycolumn']}` + $amount
                                   WHERE `userid`='$userId'");
            }
        } else {
            $this->_db->setTable($this->_table)->update($awardData, "awardid = '$this->_awardId'");
        }
    }
}
?>

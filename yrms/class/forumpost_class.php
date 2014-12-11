<?php

class ForumPost
{
    protected $_type;
    protected $_forumId;
    protected $_threadId;
    protected $_postId;
    protected $_posterId;
    protected $_postInfo;

    protected $_yrmspost = 0;

    protected $_db;
    protected $_postTable;
    protected $_threadTable;
    protected $_vbulletin;

    function __construct($type)
    {
        global $vbulletin;
        $this->_db = new Database();
        $this->_postTable = TABLE_PREFIX.'post';
        $this->_threadTable = TABLE_PREFIX.'thread';
        $this->_vbulletin = $vbulletin;

        $this->_type = $type;
    }

    public function getPostId()
    {
        return $this->_postId;
    }

    public function getThreadId()
    {
        return $this->_threadId;
    }

    public function setPostId($postId)
    {
        $this->_postId = $postId;
        return $this;
    }

    public function setPostInfo($postInfo)
    {
        $this->_postInfo = $postInfo;
        return $this;
    }

    public function setThreadId($threadId)
    {
        $this->_threadId = $threadId;
        return $this;
    }

    public function setYrmspost($yrmspost)
    {
        $this->_yrmspost = $yrmspost;
        return $this;
    }

    public function setForumId($forumId)
    {
        $this->_forumId = $forumId;
        return $this;
    }

    public function setPosterId($posterId)
    {
        $this->_posterId = $posterId;
        return $this;
    }

    public function save()
    {
        switch ($this->_type) {
            case 'thread':
                $this->threadSave();
                break;
            case 'post':
                break;
        }
    }

    public function threadSave()
    {
        $threadman =& datamanager_init('Thread_FirstPost', $this->_vbulletin, ERRTYPE_ARRAY, 'threadpost');
        $foruminfo = fetch_foruminfo($this->_forumId);

        $threadinfo = fetch_threadinfo($this->_threadId);
        if($threadinfo) {
            $threadman->set_existing($threadinfo);
        }

        $threadman->set_info('forum', $foruminfo);
        $threadman->set_info('thread', $threadinfo);
        $threadman->setr('forumid', $this->_forumId);
        $threadman->setr('userid', $this->_posterId);
        $threadman->setr('title', $this->_postInfo['title']);
        $threadman->setr('pagetext', $this->_postInfo['pagetext']);

        $threadman->setr('showsignature', $signature);
        $threadman->set('allowsmilie', $this->_postInfo['allowsmilie']);
        $threadman->set('visible', $this->_postInfo['visible']);
        $threadman->set_info('parseurl', $this->_postInfo['parseurl']);
        $threadman->set('prefixid', $this->_postInfo['prefixid']);

        if (!$this->_threadId) {
            $this->_threadId = $threadman->save();
        } else {
            $threadman->save();
        }

        $this->_postId = $this->findFirstPost($this->_threadId);

        if ($this->_yrmspost && $this->_postId) {
            $this->_db->query("UPDATE $this->_postTable SET `yrmspost`= '$this->_yrmspost' WHERE `postid`='$this->_postId'");
        }

        rebuildForum($this->_forumId);
    }

    protected function findThread($postId)
    {
        $result = $this->_db->fetchOnce("SELECT `threadid` FROM $this->_postTable WHERE `postid`='$postId'");
        return $result['threadid'];
    }

    protected function findFirstPost($threadId)
    {
        $result = $this->_db->fetchOnce("SELECT `firstpostid` FROM $this->_threadTable WHERE `threadid`='$threadId'");
        return $result['firstpostid'];
    }

    public function findPoster()
    {
        switch ($this->_type) {
            case 'thread':
                $data = $this->_db->fetchOnce("SELECT `postuserid` FROM $this->_threadTable WHERE `threadid` = '$this->_threadId'");
                break;
        }
        return $data['postuserid'];
    }

    function editPost($edit){
        global $vbulletin;
        $postinfo = fetch_postinfo($edit['postid']);
        $foruminfo = fetch_foruminfo($edit['forumid']);
        $threadinfo = fetch_threadinfo($edit['threadid']);

        $threadman =& datamanager_init('Thread', $vbulletin, ERRTYPE_SILENT, 'threadpost');
        $threadman->set_existing($threadinfo);
        $threadman->set_info('forum', $foruminfo);
        $threadman->set_info('thread', $threadinfo);
        $threadman->set('title', $edit['title']);
        $threadman->set('prefixid', $edit['prefixid']);
        $threadman->save();

        $postman =& datamanager_init('Post', $vbulletin, ERRTYPE_ARRAY, 'threadpost');
        $postman->set_existing($postinfo);
        $postman->setr('title', $edit['title']);
        $postman->setr('pagetext', $edit['pagetext']);
        $postman->save();
    }
}
 
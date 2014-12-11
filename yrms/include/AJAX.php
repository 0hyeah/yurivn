<?php
chdir('../../');
require_once('./global.php');
require_once(DIR . '/includes/functions_user.php');
require_once('yrms/class/vietsubmanga_class.php');
require_once('yrms/class/database_class.php');
require_once('yrms/include/function.php');

switch (getPost('do')){
    case 'checkVietSubManga':
        $filter = 'mangatitle';
        $rawKeywords = array_map('trim', explode(',', getPost('mangaNames')));

        $mangaObject = new Manga();
        $existedMangas = array();

        $keywords = array();
        foreach ($rawKeywords as $rawKeyword) {
            if($rawKeyword && !in_array($rawKeyword, $keywords)) {
                $keywords[] = $rawKeyword;
            }
        }

        if($keywords) {
            foreach ($keywords as $key => $keyword) {
                $result = $mangaObject->setFilter($filter)->setKeyword($keyword)->getCollection();
                if($result) {
                    $existedMangas = array_merge($result,$existedMangas);
                }

            }
        }

        if($existedMangas) {
            $existedList = array();
            foreach ($existedMangas as $existedManga) {
                $threadId = $existedManga->getThreadId();
                $existedList[] = construct_phrase($vbphrase['yrms_ajax_dupplicatewarning_element'], fetch_seo_url('thread', fetch_threadinfo($threadId)), $existedManga->getMangaTitle());
            }

            $existedList = implode('', $existedList);

            $warning = construct_phrase($vbphrase['yrms_ajax_dupplicatewarning'], $vbphrase['yrms_manga'], $existedList);
            echo $warning;
        }

        break;
}

<?php
if($post['yrmspost']==1){
    global $vbulletin;
    $awards = $vbulletin->db->query_read("SELECT `awardcontent` FROM `".TABLE_PREFIX."yrms_award` WHERE `postid`={$post['postid']}");
    //if($awards==false)
        //break;
    $apcode = 1;
    while($award = $vbulletin->db->fetch_array($awards)){
        //$awardcontent 
        $awardcontent = unserialize($award['awardcontent']);
        
        
        foreach($awardcontent as $userinfo[$apcode]['userid'] => $amounts[$apcode]){
            $userinfo[$apcode] = fetch_userinfo($userinfo[$apcode]['userid']);
            
            $apcodecheck=1;
            while($apcodecheck<$apcode){
                if($userinfo[$apcodecheck]['userid']==$userinfo[$apcode]['userid']){        
                    $checkresult = true;
                    break;
                }
                else
                    $checkresult = false;
                
                $apcodecheck++;
            }
            if($checkresult==true){
                $amounts[$apcodecheck] += $amounts[$apcode];
                $items[$apcodecheck] = "{$userinfo[$apcodecheck]['musername']}: $amounts[$apcodecheck]<br/>";
            }
            else{
                $items[$apcode] = "{$userinfo[$apcode]['musername']}: $amounts[$apcode]<br />";
                $apcode++;
                
            }
        }
        
        
    }
    unset($amounts[$apcode]);
    foreach($amounts as $amount)
        $totalamount += $amount;
    $apcode = 1;
    foreach($items as $item){
        if($apcode%2==0)
            $awardcontentright .= $item;
        else 
            $awardcontentleft .= $item;
        $apcode++;
    }
        
    $templater = vB_Template::create('yrms_awardbox');
    $templater->register('totalamount', $totalamount);
    $templater->register('awardcontentleft', $awardcontentleft);
    $templater->register('awardcontentright', $awardcontentright);
    $template_hook['postbit_signature_start'] .= $templater->render();
}
//$template_hook['postbit_controls'] .= $kbank_award_button;
?>

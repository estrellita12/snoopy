<?php


function get_youtube_video($boardid,$pt_id,$pt_url){

    include_once 'Snoopy.class.php';
    $snoopy=new snoopy;
    $snoopy->fetch("https://www.youtube.com/channel/$pt_url/videos?view=0&sort=dd");

    $html_txt = $snoopy->results;
    preg_match_all("|\"title\":{\"runs\":\[{\"text\":\"(.*)\"}\],|U", $html_txt, $title, PREG_SET_ORDER);
    preg_match_all("/\"gridVideoRenderer\":{\"videoId\":\"([A-z0-9_-]*)\"/", $html_txt, $v_code, PREG_SET_ORDER);

    for($i=count($title)-1; $i>=0;$i--){
        $subject = addslashes($title[$i][1]);

        $row = sql_fetch("select count(*) as cnt from shop_board_{$boardid} where fileurl1='{$v_code[$i][1]}'  ");
        $cnt = $row['cnt'];
        if($cnt == 0){
            $fid = get_next_num("shop_board_{$boardid}");
            $sql_commend = " , btype = '2' , ca_name  = ' ' , issecret = 'N' , havehtml = 'N' , writer = '1' , writer_s = '관리자' , subject = '{$subject}' , memo = '<iframe width=\"1200\" height=\"720\" src=\"https://www.youtube.com/embed/{$v_code[$i][1]}\" frameborder=\"0\" allow=\"accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture\" allowfullscreen=\"\"></iframe>' , passwd   = ' ' , average  = ' ' , main_use = '1' , main_order = '5' , product  = ' ' , pt_id    = '$pt_id' , fileurl1='{$v_code[$i][1]}' ";
            $sql = " insert into shop_board_{$boardid} set fid='{$fid}', wdate   = '".TB_SERVER_TIME."' , wip = '127.0.0.1' , thread  = 'A' {$sql_commend} ";
            sql_query($sql);
        }else{
            $row1 = sql_fetch("select count(*) as cnt from shop_board_{$boardid} where fileurl1='{$v_code[$i][1]}' and subject = '{$subject}' ");
            $cnt1 = $row1['cnt'];
            if($cnt1==0){
                $sql = " update shop_board_{$boardid} set  memo = '<iframe width=\"1200\" height=\"720\" src=\"https://www.youtube.com/embed/{$v_code[$i][1]}\" frameborder=\"0\" allow=\"accelerometer; autoplay; encrypted-media; gyroscope;picture-in-picture\" allowfullscreen=\"\"></iframe>', subject = '{$subject}' where fileurl1='{$v_code[$i][1]}' ";
                sql_query($sql);
            }
        }
    }
}

?>

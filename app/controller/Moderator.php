<?php
namespace app\controller;
use think\facade\View;
use think\facade\Request;

use app\model\Contests;
use app\model\ContestJudgers;
use app\model\ContestPlayers;
use app\model\ContestPoints;
use app\model\SessionToMsg;

class Moderator
{
    public function index($cid)
    {
        $dbC=new Contests();
        $visitable=$dbC->enable($cid);
        if(!$visitable)return redirect('/visitwrong');
        View::assign('page_title','主持人界面');
        
        $dbCJ=new ContestJudgers();
        $dbCP=new ContestPlayers();
        $dbCPs=new ContestPoints();
        $judger_page=$dbC->getJudgerPage($cid);
        $judger_page_content=$dbC->getJudgerPageContentPre($cid);
        $judger_page_contents=$dbC->getJudgerPageContent($cid);
        $judger_next_page=$dbC->getJudgerNextPage($cid);
        $judger_next_page_content=$dbC->getJudgerNextPageContentPre($cid);
        if($judger_page=='score')
        {
            $retj=[];
            $judgers=$dbCJ->getEnableJudgers($cid);
            for($i=0;$i<count($judger_page_contents);$i++)
            {
                $retj[$i]['pid']=$judger_page_contents[$i];
                $point_judgers=$dbCPs->getPlayerPointEnableJudgers($cid,$judger_page_contents[$i]);
                for($j=0;$j<count($judgers);$j++)
                {
                    $retj[$i]['judgers'][$j]['jid']=$judgers[$j]['jid'];
                    $retj[$i]['judgers'][$j]['scored']=in_array($judgers[$j]['jid'],$point_judgers);
                }
            }
            View::assign('score_status',$retj);
        }
        $screen_page=$dbC->getScreenPage($cid);
        $screen_page_content=$dbC->getScreenPageContentPre($cid);
        $screen_page_contents=$dbC->getScreenPageContent($cid);
        if($screen_page=='view')
        {
            $rets=[];
            $rets['pid']=$screen_page_contents[0];
            $viewplayer=$dbCP->getPlayer($cid,$screen_page_contents[0]);
            $points=$dbCPs->getPlayerEnablePoints($cid,$screen_page_contents[0]);
            $rets['pname']=$viewplayer['pname'];
            $rets['point']=$viewplayer['point'];
            $rets['points_length']=count($points);
            $rets['points']=$points;
            View::assign('view_content',$rets);
        }
        if($screen_page=='group')
        {
            $rets=[];
            for($i=0;$i<count($screen_page_contents);$i++)
            {
                $rets[$i]['pid']=$screen_page_contents[$i];
                $groupplayer=$dbCP->getPlayer($cid,$screen_page_contents[$i]);
                $rets[$i]['pname']=$groupplayer['pname'];
                $rets[$i]['sequence']=$groupplayer['sequence'];
                $rets[$i]['point']=$groupplayer['point'];
            }
            foreach($rets as $v)
            {
                $key_arrays[]=$v['sequence'];
            }
            array_multisort($key_arrays,SORT_ASC,SORT_NUMERIC,$rets);
            View::assign('group_content',$rets);
        }
        if($screen_page=='rank')
        {
            $rets=[];
            for($i=0;$i<count($screen_page_contents);$i++)
            {
                $res=$dbCP->getRankPlayers($cid,$screen_page_contents[$i]);
                $j=count($rets);
                for($k=0;$k<count($res);$k++)
                {
                    $rets[$j]['pid']=$res[$k]['pid'];
                    $rets[$j]['pname']=$res[$k]['pname'];
                    $rets[$j]['sequence']=$res[$k]['sequence'];
                    $rets[$j]['point']=$res[$k]['point'];
                    $rets[$j]['rank']=$res[$k]['rank'];
                    $j++;
                }
            }
            foreach($rets as $v)
            {
                $key_arrays[]=$v['rank'];
            }
            array_multisort($key_arrays,SORT_ASC,SORT_NUMERIC,$rets);
            View::assign('rank_content',$rets);
        }
        
        View::assign([
            'judger_page'=>$judger_page,
            'judger_page_content'=>$judger_page_content,
            'judger_next_page'=>$judger_next_page,
            'judger_next_page_content'=>$judger_next_page_content,
            'screen_page'=>$screen_page,
            'screen_page_content'=>$screen_page_content
        ]);
        
        View::assign([
            'cid'=>$cid,
            'contest_name'=>$dbC->getSetting($cid,'name'),
            'contest_favicon'=>$dbC->getSetting($cid,'favicon'),
            'contest_logo'=>$dbC->getSetting($cid,'logo'),
            'contest_color'=>$dbC->getSetting($cid,'color'),
            'contest_accent_color'=>$dbC->getSetting($cid,'accent_color')
        ]);
        return View::fetch();
    }
    public function indexapi($cid,$action)
    {
        $dbC=new Contests();
        $visitable=$dbC->enable($cid);
        if(!$visitable)return redirect('/visitwrong');
        $all = Request::param();
        $dbSTM = new SessionToMsg();
        if(Request::method() == 'POST')
        {
            header('Content-Type:application/json');
            if($action=='getreload')
            {
                $judger_page=$dbC->getJudgerPage($cid);
                $judger_page_content=$dbC->getJudgerPageContentPre($cid);
                $screen_page=$dbC->getScreenPage($cid);
                $screen_page_content=$dbC->getScreenPageContentPre($cid);
                if($judger_page!=$all['judger_page']||$judger_page_content!=$all['judger_page_content'])
                {
                    $msg=$dbSTM->setMsg('moderatorindex',1,'需要切换','主持人页面已更新');
                    // return redirect('/contest'.$cid.'/moderator');
                    return $dbSTM->retMsg(1,'需要切换','主持人页面已更新');
                }
                if($screen_page!=$all['screen_page']||$screen_page_content!=$all['screen_page_content'])
                {
                    $msg=$dbSTM->setMsg('moderatorindex',1,'需要切换','主持人页面已更新');
                    // return redirect('/contest'.$cid.'/moderator');
                    return $dbSTM->retMsg(1,'需要切换','主持人页面已更新');
                }
                $msg=$dbSTM->setMsg('moderatorindex',0,'无需切换','主持人页面无需更新');
                // return redirect('/contest'.$cid.'/moderator');
                return $dbSTM->retMsg(0,'无需切换','主持人页面无需更新');
            }
            if($action=='getstatus')
            {
                $dbCJ=new ContestJudgers();
                $dbCPs=new ContestPoints();
                $judgers=$dbCJ->getEnableJudgers($cid);
                $players=$dbC->getJudgerPageContent($cid);
                $ret=[];
                $flag=true;
                for($i=0;$i<count($players);$i++)
                {
                    $ret[$i]['pid']=$players[$i];
                    $point_judgers=$dbCPs->getPlayerPointEnableJudgers($cid,$players[$i]);
                    for($j=0;$j<count($judgers);$j++)
                    {
                        $ret[$i]['judgers'][$j]['jid']=$judgers[$j]['jid'];
                        $ret[$i]['judgers'][$j]['scored']=in_array($judgers[$j]['jid'],$point_judgers);
                        if(!in_array($judgers[$j]['jid'],$point_judgers))
                            $flag=false;
                    }
                }
                if($flag)
                {
                    for($i=0;$i<count($players);$i++)
                    {
                        $ret=$dbCP->calcPoint($cid,$players[$i]);
                    }
                    $ret=$dbC->autoNextPage($cid);
                    return $dbSTM->retMsg(1,'需要切换','主持人页面已更新');
                }
                return $dbSTM->retMsg(0,'更新数据',json_encode($ret));
            }
            if($action=='getmsg')
            {
                return $dbSTM->popMsg('moderatorindex');
            }
        }
    }
}

<?php
namespace app\controller;
use think\facade\View;
use think\facade\Request;

use app\model\Contests;
use app\model\ContestJudgers;
use app\model\ContestPlayers;
use app\model\ContestPoints;
use app\model\SessionToMsg;

class Show
{
    public function index($cid)
    {
        $dbC=new Contests();
        $visitable=$dbC->enable($cid);
        if(!$visitable)return redirect('/visitwrong');
        $screen_page=$dbC->getScreenPage($cid);
        return redirect('/contest'.$cid.'/show/'.$screen_page);
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
                $screen_page=$dbC->getScreenPage($cid);
                $screen_page_content=$dbC->getScreenPageContentPre($cid);
                if($screen_page!=$all['screen_page']||$screen_page_content!=$all['screen_page_content'])
                {
                    $msg=$dbSTM->setMsg('showindex',1,'需要切换','屏幕页面已更新');
                    // return redirect('/contest'.$cid.'/show');
                    return $dbSTM->retMsg(1,'需要切换','屏幕页面已更新');
                }
                $msg=$dbSTM->setMsg('showindex',0,'无需切换','屏幕页面无需更新');
                // return redirect('/contest'.$cid.'/show');
                return $dbSTM->retMsg(0,'无需切换','屏幕页面无需更新');
            }
            if($action=='getmsg')
            {
                return $dbSTM->popMsg('showindex');
            }
        }
    }
    
    public function background($cid)
    {
        $dbC=new Contests();
        $visitable=$dbC->enable($cid);
        if(!$visitable)return redirect('/visitwrong');
        $screen_page=$dbC->getScreenPage($cid);
        $screen_page_content=$dbC->getScreenPageContentPre($cid);
        $screen_page_contents=$dbC->getScreenPageContent($cid);
        if($screen_page!='background')
            return redirect('/contest'.$cid.'/show/'.$screen_page);
        View::assign('page_title','背景页面');
        
        View::assign('page_pic',$dbC->getScreenBackgroundPagePic($cid));
        View::assign([
            'screen_page'=>$screen_page,
            'screen_page_content'=>$screen_page_content
        ]);
        View::assign([
            'cid'=>$cid,
            'contest_name'=>$dbC->getSetting($cid,'name'),
            'contest_favicon'=>$dbC->getSetting($cid,'favicon'),
            'contest_logo'=>$dbC->getSetting($cid,'logo'),
        ]);
        return View::fetch();
    }
    
    public function view($cid)
    {
        $dbC=new Contests();
        $visitable=$dbC->enable($cid);
        if(!$visitable)return redirect('/visitwrong');
        $screen_page=$dbC->getScreenPage($cid);
        $screen_page_content=$dbC->getScreenPageContentPre($cid);
        $screen_page_contents=$dbC->getScreenPageContent($cid);
        if($screen_page!='view')
            return redirect('/contest'.$cid.'/show/'.$screen_page);
        View::assign('page_title','详细页面');
        
        $dbCP=new ContestPlayers();
        $dbCPs=new ContestPoints();
        $rets=[];
        $rets['pid']=$screen_page_contents[0];
        $viewplayer=$dbCP->getPlayer($cid,$screen_page_contents[0]);
        $points=$dbCPs->getPlayerEnablePoints($cid,$screen_page_contents[0]);
        $rets['pname']=$viewplayer['pname'];
        $rets['point']=$viewplayer['point'];
        $rets['points_length']=count($points);
        $rets['points']=$points;
        View::assign('view_content',$rets);
        
        View::assign('page_pic',$dbC->getScreenPagePic($cid));
        View::assign([
            'screen_page'=>$screen_page,
            'screen_page_content'=>$screen_page_content
        ]);
        View::assign([
            'cid'=>$cid,
            'contest_name'=>$dbC->getSetting($cid,'name'),
            'contest_favicon'=>$dbC->getSetting($cid,'favicon'),
            'contest_logo'=>$dbC->getSetting($cid,'logo'),
        ]);
        return View::fetch();
    }
    
    public function group($cid)
    {
        $dbC=new Contests();
        $visitable=$dbC->enable($cid);
        if(!$visitable)return redirect('/visitwrong');
        $screen_page=$dbC->getScreenPage($cid);
        $screen_page_content=$dbC->getScreenPageContentPre($cid);
        $screen_page_contents=$dbC->getScreenPageContent($cid);
        if($screen_page!='group')
            return redirect('/contest'.$cid.'/show/'.$screen_page);
        View::assign('page_title','分组页面');
        
        $dbCP=new ContestPlayers();
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
        
        View::assign('page_pic',$dbC->getScreenPagePic($cid));
        View::assign([
            'screen_page'=>$screen_page,
            'screen_page_content'=>$screen_page_content
        ]);
        View::assign([
            'cid'=>$cid,
            'contest_name'=>$dbC->getSetting($cid,'name'),
            'contest_favicon'=>$dbC->getSetting($cid,'favicon'),
            'contest_logo'=>$dbC->getSetting($cid,'logo'),
        ]);
        return View::fetch();
    }
    
    public function rank($cid)
    {
        $dbC=new Contests();
        $visitable=$dbC->enable($cid);
        if(!$visitable)return redirect('/visitwrong');
        $screen_page=$dbC->getScreenPage($cid);
        $screen_page_content=$dbC->getScreenPageContentPre($cid);
        $screen_page_contents=$dbC->getScreenPageContent($cid);
        if($screen_page!='rank')
            return redirect('/contest'.$cid.'/show/'.$screen_page);
        View::assign('page_title','排名页面');
        
        $dbCP=new ContestPlayers();
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
        
        View::assign('page_pic',$dbC->getScreenPagePic($cid));
        View::assign([
            'screen_page'=>$screen_page,
            'screen_page_content'=>$screen_page_content
        ]);
        View::assign([
            'cid'=>$cid,
            'contest_name'=>$dbC->getSetting($cid,'name'),
            'contest_favicon'=>$dbC->getSetting($cid,'favicon'),
            'contest_logo'=>$dbC->getSetting($cid,'logo'),
        ]);
        return View::fetch();
    }
}

<?php
namespace app\controller;
use think\facade\View;
use think\facade\Request;

use app\model\Contests;
use app\model\ContestJudgers;
use app\model\ContestPlayers;
use app\model\ContestPoints;
use app\model\CookieToSession;
use app\model\SessionToMsg;

class Judger
{
    public function index($cid)
    {
        $dbC=new Contests();
        $visitable=$dbC->enable($cid);
        if(!$visitable)return redirect('/visitwrong');
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isCJ = $CTS->isContestJudger($cid);
        if($isA)
            return redirect('/contest'.$cid.'/judger/error');
        if($isCJ)
        {
            $jid=$CTS->getJid($cid);
            $dbCJ = new ContestJudgers();
            $login_page = $dbCJ->getPage($cid,$jid);
            return redirect('/contest'.$cid.'/judger/'.$login_page);
        }
        return redirect('/contest'.$cid.'/judger/login');
    }
    
    public function error($cid)
    {
        $dbC=new Contests();
        $visitable=$dbC->enable($cid);
        if(!$visitable)return redirect('/visitwrong');
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isCJ = $CTS->isContestJudger($cid);
        if($isCJ)
        {
            $jid=$CTS->getJid($cid);
            $dbCJ = new ContestJudgers();
            $login_page = $dbCJ->getPage($cid,$jid);
            return redirect('/contest'.$cid.'/judger/'.$login_page);
        }
        if(!$isA)
            return redirect('/contest'.$cid.'/judger/login');
        View::assign('page_title','评委错误');
        
        if($isA)
        {
            $aid = $CTS->getAid();
            $isCA = $CTS->isContestAdmin($cid);
            View::assign([
                'usertype'=>'admin',
                'aid'=>$aid,
                'permission'=>$isCA
            ]);
        }
        
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
    public function errorapi($cid,$action)
    {
        $dbC=new Contests();
        $visitable=$dbC->enable($cid);
        if(!$visitable)return redirect('/visitwrong');
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isCJ = $CTS->isContestJudger($cid);
        if($isCJ)
        {
            $jid=$CTS->getJid($cid);
            $dbCJ = new ContestJudgers();
            $login_page = $dbCJ->getPage($cid,$jid);
            return redirect('/contest'.$cid.'/judger/'.$login_page);
        }
        if(!$isA)
            return redirect('/contest'.$cid.'/judger/login');
        $all = Request::param();
        $dbSTM = new SessionToMsg();
        if(Request::method() == 'GET')
        {
            if($action=='logout')
            {
                $ret = $CTS->delAid();
                return redirect('/contest'.$cid.'/judger/login');
            }
            if($action=='admin')
                return redirect('/contest'.$cid.'/admin');
        }
    }
    
    public function login($cid)
    {
        $dbC=new Contests();
        $visitable=$dbC->enable($cid);
        if(!$visitable)return redirect('/visitwrong');
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isCJ = $CTS->isContestJudger($cid);
        if($isA)
            return redirect('/contest'.$cid.'/judger/error');
        if($isCJ)
        {
            $jid=$CTS->getJid($cid);
            $dbCJ = new ContestJudgers();
            $login_page = $dbCJ->getPage($cid,$jid);
            return redirect('/contest'.$cid.'/judger/'.$login_page);
        }
        View::assign('page_title','评委登录');
        
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
    public function loginapi($cid,$action)
    {
        $dbC=new Contests();
        $visitable=$dbC->enable($cid);
        if(!$visitable)return redirect('/visitwrong');
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isCJ = $CTS->isContestJudger($cid);
        if($isA)
            return redirect('/contest'.$cid.'/judger/error');
        if($isCJ)
        {
            $jid=$CTS->getJid($cid);
            $dbCJ = new ContestJudgers();
            $login_page = $dbCJ->getPage($cid,$jid);
            return redirect('/contest'.$cid.'/judger/'.$login_page);
        }
        $all = Request::param();
        $dbSTM = new SessionToMsg();
        if(Request::method() == 'POST')
        {
            header('Content-Type:application/json');
            if($action=='login')
            {
                $dbCJ = new ContestJudgers();
                $ret = $dbCJ->login($cid,$all['logincode']);
                if($ret)
                {
                    //$msg=$dbSTM->setMsg('judgerlogin',0,'登录成功','登录成功');
                    // return redirect('/contest'.$cid.'/judger');
                    return $dbSTM->retMsg(0,'登录成功','登录成功');
                }
                else
                {
                    $msg=$dbSTM->setMsg('judgerlogin',1,'登录错误','请输入正确的八位登录码');
                    // return redirect('/contest'.$cid.'/judger/login');
                    return $dbSTM->retMsg(1,'登录错误','请输入正确的八位登录码');
                }
            }
            if($action=='getmsg')
            {
                return $dbSTM->popMsg('judgerlogin');
            }
        }
        if(Request::method() == 'GET')
        {
            if($action=='login')
            {
                $dbCJ = new ContestJudgers();
                $ret = $dbCJ->login($cid,$all['logincode']);
                if($ret)
                {
                    //$msg=$dbSTM->setMsg('judgerlogin',0,'登录成功','登录成功');
                    return redirect('/contest'.$cid.'/judger');
                }
                else
                {
                    $msg=$dbSTM->setMsg('judgerlogin',1,'登录错误','请输入正确的八位登录码');
                    return redirect('/contest'.$cid.'/judger/login');
                }
            }
        }
    }
    
    public function logout($cid)
    {
        $dbC=new Contests();
        $visitable=$dbC->enable($cid);
        if(!$visitable)return redirect('/visitwrong');
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isCJ = $CTS->isContestJudger($cid);
        if($isA)
            return redirect('/contest'.$cid.'/judger/error');
        if(!$isCJ)
            return redirect('/contest'.$cid.'/judger/login');
        View::assign('page_title','评委退出登录');
        
        if($isCJ)
        {
            $jid = $CTS->getJid($cid);
            View::assign([
                'usertype'=>'judger',
                'jid'=>$jid
            ]);
        }
        
        View::assign([
            'cid'=>$cid,
            'logined'=>true,
            'contest_name'=>$dbC->getSetting($cid,'name'),
            'contest_favicon'=>$dbC->getSetting($cid,'favicon'),
            'contest_logo'=>$dbC->getSetting($cid,'logo'),
            'contest_color'=>$dbC->getSetting($cid,'color'),
            'contest_accent_color'=>$dbC->getSetting($cid,'accent_color')
        ]);
        return View::fetch();
    }
    public function logoutapi($cid,$action)
    {
        $dbC=new Contests();
        $visitable=$dbC->enable($cid);
        if(!$visitable)return redirect('/visitwrong');
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isCJ = $CTS->isContestJudger($cid);
        if($isA)
            return redirect('/contest'.$cid.'/judger/error');
        if(!$isCJ)
            return redirect('/contest'.$cid.'/judger/login');
        $all = Request::param();
        $dbSTM = new SessionToMsg();
        if(Request::method() == 'GET')
        {
            if($action=='logout')
            {
                $ret = $CTS->delJid($cid);
                return redirect('/contest'.$cid.'/judger/login');
            }
        }
    }
    
    public function wait($cid)
    {
        $dbC=new Contests();
        $visitable=$dbC->enable($cid);
        if(!$visitable)return redirect('/visitwrong');
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isCJ = $CTS->isContestJudger($cid);
        if($isA)
            return redirect('/contest'.$cid.'/judger/error');
        if(!$isCJ)
        {
            return redirect('/contest'.$cid.'/judger/login');
        }
        else
        {
            $jid=$CTS->getJid($cid);
            $dbCJ = new ContestJudgers();
            $login_page = $dbCJ->getPage($cid,$jid);
            if($login_page!='wait')
                return redirect('/contest'.$cid.'/judger/'.$login_page);
        }
        View::assign('page_title',''.$jid.'号评委等待评分');
        $login_page_content = $dbCJ->getPageContent($cid,$jid);
        $login_page_content_length=count($login_page_content);
        $dbCP = new ContestPlayers();
        foreach($login_page_content as &$v)
        {
            $v=$dbCP->getPlayer($cid,$v);
        }
        unset($v);
        
        View::assign([
            'players'=>$login_page_content,
            'player_length'=>$login_page_content_length
        ]);
        
        $page_content=$dbCJ->getPageContentPre($cid,$jid);
        View::assign([
            'page_content'=>$page_content
        ]);
        
        View::assign([
            'cid'=>$cid,
            'logined'=>true,
            'contest_name'=>$dbC->getSetting($cid,'name'),
            'contest_favicon'=>$dbC->getSetting($cid,'favicon'),
            'contest_logo'=>$dbC->getSetting($cid,'logo'),
            'contest_color'=>$dbC->getSetting($cid,'color'),
            'contest_accent_color'=>$dbC->getSetting($cid,'accent_color')
        ]);
        return View::fetch();
    }
    public function waitapi($cid,$action)
    {
        $dbC=new Contests();
        $visitable=$dbC->enable($cid);
        if(!$visitable)return redirect('/visitwrong');
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isCJ = $CTS->isContestJudger($cid);
        if($isA)
            return redirect('/contest'.$cid.'/judger/error');
        if(!$isCJ)
        {
            return redirect('/contest'.$cid.'/judger/login');
        }
        $all = Request::param();
        $dbSTM = new SessionToMsg();
        if(Request::method() == 'POST')
        {
            header('Content-Type:application/json');
            if($action=='getreload')
            {
                $judger_page=$dbC->getJudgerPage($cid);
                $judger_page_content=$dbC->getJudgerPageContentPre($cid);
                if($judger_page!=$all['judger_page']||$judger_page_content!=$all['judger_page_content'])
                {
                    $msg=$dbSTM->setMsg('judgerwait',1,'需要切换','评委页面已更新');
                    // return redirect('/contest'.$cid.'/judger/score');
                    return $dbSTM->retMsg(1,'需要切换','评委页面已更新');
                }
                $msg=$dbSTM->setMsg('judgerwait',0,'无需切换','评委页面无需更新');
                // return redirect('/contest'.$cid.'/judger/wait');
                return $dbSTM->retMsg(0,'无需切换','评委页面无需更新');
            }
            if($action=='getmsg')
            {
                return $dbSTM->popMsg('judgerwait');
            }
        }
    }
    
    public function score($cid)
    {
        $dbC=new Contests();
        $visitable=$dbC->enable($cid);
        if(!$visitable)return redirect('/visitwrong');
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isCJ = $CTS->isContestJudger($cid);
        if($isA)
            return redirect('/contest'.$cid.'/judger/error');
        if(!$isCJ)
        {
            return redirect('/contest'.$cid.'/judger/login');
        }
        else
        {
            $jid=$CTS->getJid($cid);
            $dbCJ = new ContestJudgers();
            $login_page = $dbCJ->getPage($cid,$jid);
            if($login_page!='score')
                return redirect('/contest'.$cid.'/judger/'.$login_page);
        }
        View::assign('page_title',''.$jid.'号评委正在评分');
        $login_page_content = $dbCJ->getPageContent($cid,$jid);
        $login_page_content_length=count($login_page_content);
        $dbCP = new ContestPlayers();
        $dbCPs = new ContestPoints();
        $flag=true;
        foreach($login_page_content as &$v)
        {
            $v=$dbCP->getPlayer($cid,$v);
            $tmp=$dbCPs->getPoint($cid,$v['pid'],$jid);
            if(!$tmp)
                $flag=false;
        }
        if($flag)
        {
            $ret = $dbCJ->toNextPage($cid,$jid);
            return redirect('/contest'.$cid.'/judger');
        }
        unset($v);
        
        View::assign([
            'players'=>$login_page_content,
            'player_length'=>$login_page_content_length
        ]);
        
        View::assign([
            'cid'=>$cid,
            'logined'=>true,
            'contest_name'=>$dbC->getSetting($cid,'name'),
            'contest_favicon'=>$dbC->getSetting($cid,'favicon'),
            'contest_logo'=>$dbC->getSetting($cid,'logo'),
            'contest_color'=>$dbC->getSetting($cid,'color'),
            'contest_accent_color'=>$dbC->getSetting($cid,'accent_color')
        ]);
        return View::fetch();
    }
    public function scoreapi($cid,$action)
    {
        $dbC=new Contests();
        $visitable=$dbC->enable($cid);
        if(!$visitable)return redirect('/visitwrong');
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isCJ = $CTS->isContestJudger($cid);
        if($isA)
            return redirect('/contest'.$cid.'/judger/error');
        if(!$isCJ)
        {
            return redirect('/contest'.$cid.'/judger/login');
        }
        $jid=$CTS->getJid($cid);
        $dbCJ = new ContestJudgers();
        $all = Request::param();
        $dbSTM = new SessionToMsg();
        if(Request::method() == 'POST')
        {
            header('Content-Type:application/json');
            if($action=='score')
            {
                $login_page_content = $dbCJ->getPageContent($cid,$jid);
                $login_page_content_length=count($login_page_content);
                $ret='';
                $res=[];
                $dbCPs = new ContestPoints();
                $dbCP = new ContestPlayers();
                foreach($login_page_content as $v)
                {
                    $v=$dbCP->getPlayer($cid,$v);
                    $tmp=$dbCPs->getPoint($cid,$v['pid'],$jid);
                    if($tmp)
                    {
                        $res['res'.$v['pid']]=false;
                        if($ret=='')
                            $ret=''.$v['pid'].'号选手';
                        else
                            $ret=''.$ret.'、'.$v['pid'].'号选手';
                    }
                    else
                    {
                        $res['res'.$v['pid']]=$dbCPs->setPoint($cid,$v['pid'],$jid,$all['point'.$v['pid']]);
                    }
                }
                if($ret=='')
                {
                    //$msg=$dbSTM->setMsg('judgerscore',0,'评分成功','评分成功');
                    $ret = $dbCJ->toNextPage($cid,$jid);
                    // return redirect('/contest'.$cid.'/judger');
                    return $dbSTM->retMsg(0,'评分成功','评分成功');
                }
                else
                {
                    $msg=$dbSTM->setMsg('judgerscore',1,'评分错误','已经给'.$ret.'打过分了');
                    // return redirect('/contest'.$cid.'/judger/score');
                    return $dbSTM->retMsg(1,'评分错误','已经给'.$ret.'打过分了');
                }
            }
            if($action=='getmsg')
            {
                return $dbSTM->popMsg('judgerscore');
            }
            if($action=='nextpage')
            {
                $ret = $dbCJ->toNextPage($cid,$jid);
                // return redirect('/contest'.$cid.'/judger');
                return $dbSTM->retMsg(0,'操作成功','操作成功');
            }
        }
        if(Request::method() == 'GET')
        {
            if($action=='nextpage')
            {
                $dbCJ = new ContestJudgers();
                $ret = $dbCJ->toNextPage($cid,$jid);
                return redirect('/contest'.$cid.'/judger');
            }
        }
    }
    
    public function end($cid)
    {
        $dbC=new Contests();
        $visitable=$dbC->enable($cid);
        if(!$visitable)return redirect('/visitwrong');
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isCJ = $CTS->isContestJudger($cid);
        if($isA)
            return redirect('/contest'.$cid.'/judger/error');
        if(!$isCJ)
        {
            return redirect('/contest'.$cid.'/judger/login');
        }
        else
        {
            $jid=$CTS->getJid($cid);
            $dbCJ = new ContestJudgers();
            $login_page = $dbCJ->getPage($cid,$jid);
            if($login_page!='end')
                return redirect('/contest'.$cid.'/judger/'.$login_page);
        }
        View::assign('page_title',''.$jid.'号评委评分结束');
        
        View::assign([
            'cid'=>$cid,
            'logined'=>true,
            'contest_name'=>$dbC->getSetting($cid,'name'),
            'contest_favicon'=>$dbC->getSetting($cid,'favicon'),
            'contest_logo'=>$dbC->getSetting($cid,'logo'),
            'contest_color'=>$dbC->getSetting($cid,'color'),
            'contest_accent_color'=>$dbC->getSetting($cid,'accent_color')
        ]);
        return View::fetch();
    }
}

<?php
namespace app\controller;
use think\facade\View;
use think\facade\Request;
use \think\facade\Filesystem;

use app\model\Admins;
use app\model\Contests;
use app\model\ContestJudgers;
use app\model\ContestPlayers;
use app\model\ContestPoints;
use app\model\GlobalSettings;
use app\model\CookieToSession;
use app\model\SessionToMsg;
use app\model\QuickResponseCode;

class Control
{
    public function index($cid)
    {
        $dbC=new Contests();
        $visitable=$dbC->enable($cid);
        if(!$visitable)return redirect('/visitwrong');
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isCA = $CTS->isContestAdmin($cid);
        $isCJ = $CTS->isContestJudger($cid);
        if($isCJ||($isA&&(!$isCA)))
            return redirect('/contest'.$cid.'/admin/error');
        if($isCA)
            return redirect('/contest'.$cid.'/admin/control');
        return redirect('/contest'.$cid.'/admin/login');
    }
    
    public function error($cid)
    {
        $dbC=new Contests();
        $visitable=$dbC->enable($cid);
        if(!$visitable)return redirect('/visitwrong');
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isCA = $CTS->isContestAdmin($cid);
        $isCJ = $CTS->isContestJudger($cid);
        if($isCA)
            return redirect('/contest'.$cid.'/admin/control');
        if((!$isA)&&(!$isCJ))
            return redirect('/contest'.$cid.'/admin/login');
        View::assign('page_title','管理错误');
        
        if($isA)
        {
            $aid = $CTS->getAid();
            View::assign([
                'usertype'=>'admin',
                'aid'=>$aid,
                'permission'=>false
            ]);
        }
        if($isCJ)
        {
            $jid = $CTS->getJid($cid);
            View::assign([
                'usertype'=>'judger',
                'jid'=>$jid,
                'permission'=>true
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
        $isCA = $CTS->isContestAdmin($cid);
        $isCJ = $CTS->isContestJudger($cid);
        if($isCA)
            return redirect('/contest'.$cid.'/admin/control');
        if((!$isA)&&(!$isCJ))
            return redirect('/contest'.$cid.'/admin/login');
        $all = Request::param();
        $dbSTM = new SessionToMsg();
        if(Request::method() == 'GET')
        {
            if($action=='logout')
            {
                if($isA)
                    $ret = $CTS->delAid();
                if($isCJ)
                    $ret = $CTS->delJid($cid);
                return redirect('/contest'.$cid.'/admin/login');
            }
            if($action=='judger')
                return redirect('/contest'.$cid.'/judger');
        }
    }
    
    public function login($cid)
    {
        $dbC=new Contests();
        $visitable=$dbC->enable($cid);
        if(!$visitable)return redirect('/visitwrong');
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isCA = $CTS->isContestAdmin($cid);
        $isCJ = $CTS->isContestJudger($cid);
        if($isCJ||($isA&&(!$isCA)))
            return redirect('/contest'.$cid.'/admin/error');
        if($isCA)
            return redirect('/contest'.$cid.'/admin/control');
        View::assign('page_title','管理登录');
        
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
        $isCA = $CTS->isContestAdmin($cid);
        $isCJ = $CTS->isContestJudger($cid);
        if($isCJ||($isA&&(!$isCA)))
            return redirect('/contest'.$cid.'/admin/error');
        if($isCA)
            return redirect('/contest'.$cid.'/admin/control');
        $all = Request::param();
        $dbSTM = new SessionToMsg();
        if(Request::method() == 'POST')
        {
            header('Content-Type:application/json');
            if($action=='login')
            {
                $dbA = new Admins();
                $ret = $dbA->login($all['loginname'],$all['password']);
                if($ret)
                {
                    //$msg=$dbSTM->setMsg('adminlogin',0,'登录成功','登录成功');
                    // return redirect('/contest'.$cid.'/admin');
                    return $dbSTM->retMsg(0,'登录成功','登录成功');
                }
                else
                {
                    $msg=$dbSTM->setMsg('adminlogin',1,'登录错误','请输入正确的登录名称和登录密码');
                    // return redirect('/contest'.$cid.'/admin/login');
                    return $dbSTM->retMsg(1,'登录错误','请输入正确的登录名称和登录密码');
                }
            }
            if($action=='getmsg')
            {
                return $dbSTM->popMsg('adminlogin');
            }
        }
        if(Request::method() == 'GET')
        {
            if($action=='login')
            {
                $dbA = new Admins();
                $ret = $dbA->login($all['loginname'],$all['password']);
                if($ret)
                {
                    //$msg=$dbSTM->setMsg('adminlogin',0,'登录成功','登录成功');
                    return redirect('/contest'.$cid.'/admin');
                }
                else
                {
                    $msg=$dbSTM->setMsg('adminlogin',1,'登录错误','请输入正确的登录名称和登录密码');
                    return redirect('/contest'.$cid.'/admin/login');
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
        $isCA = $CTS->isContestAdmin($cid);
        $isCJ = $CTS->isContestJudger($cid);
        if($isCJ||($isA&&(!$isCA)))
            return redirect('/contest'.$cid.'/admin/error');
        if(!$isCA)
            return redirect('/contest'.$cid.'/admin/login');
        View::assign('page_title','管理退出登录');
        
        if($isCA)
        {
            $aid = $CTS->getAid();
            View::assign([
                'usertype'=>'admin',
                'aid'=>$aid
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
        $isCA = $CTS->isContestAdmin($cid);
        $isCJ = $CTS->isContestJudger($cid);
        if($isCJ||($isA&&(!$isCA)))
            return redirect('/contest'.$cid.'/admin/error');
        if(!$isCA)
            return redirect('/contest'.$cid.'/admin/login');
        $all = Request::param();
        $dbSTM = new SessionToMsg();
        if(Request::method() == 'GET')
        {
            if($action=='logout')
            {
                $ret = $CTS->delAid();
                return redirect('/contest'.$cid.'/admin/login');
            }
        }
    }
    
    public function profile($cid)
    {
        $dbC=new Contests();
        $visitable=$dbC->enable($cid);
        if(!$visitable)return redirect('/visitwrong');
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isCA = $CTS->isContestAdmin($cid);
        $isCJ = $CTS->isContestJudger($cid);
        if($isCJ||($isA&&(!$isCA)))
            return redirect('/contest'.$cid.'/admin/error');
        if(!$isCA)
            return redirect('/contest'.$cid.'/admin/login');
        View::assign('page_title','管理员档案');
        
        $aid = $CTS->getAid();
        $dbA = new Admins();
        $admin = $dbA->getProfile($aid);
        View::assign([
            'aname'=>$admin['aname'],
            'loginname'=>$admin['loginname'],
            'permission'=>$admin['permission']
        ]);
        
        View::assign([
            'cid'=>$cid,
            'logined'=>true,
            'profile'=>true,
            'contest_name'=>$dbC->getSetting($cid,'name'),
            'contest_favicon'=>$dbC->getSetting($cid,'favicon'),
            'contest_logo'=>$dbC->getSetting($cid,'logo'),
            'contest_color'=>$dbC->getSetting($cid,'color'),
            'contest_accent_color'=>$dbC->getSetting($cid,'accent_color')
        ]);
        return View::fetch();
    }
    public function profileapi($cid,$action)
    {
        $dbC=new Contests();
        $visitable=$dbC->enable($cid);
        if(!$visitable)return redirect('/visitwrong');
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isCA = $CTS->isContestAdmin($cid);
        $isCJ = $CTS->isContestJudger($cid);
        if($isCJ||($isA&&(!$isCA)))
            return redirect('/contest'.$cid.'/admin/error');
        if(!$isCA)
            return redirect('/contest'.$cid.'/admin/login');
        $all = Request::param();
        $dbSTM = new SessionToMsg();
        if(Request::method() == 'POST')
        {
            header('Content-Type:application/json');
            if($action=='aname')
            {
                $dbA = new Admins();
                $ret = $dbA->modAname($aid,$all['aname']);
                if($ret)
                {
                    $msg=$dbSTM->setMsg('adminprofile',1,'修改成功','管理员名称已更新');
                    // return redirect('/contest'.$cid.'/admin/profile');
                    return $dbSTM->retMsg(1,'修改成功','管理员名称已更新');
                }
                else
                {
                    $msg=$dbSTM->setMsg('adminprofile',2,'修改错误','请输入正确的新管理员名称');
                    // return redirect('/contest'.$cid.'/admin/profile');
                    return $dbSTM->retMsg(2,'修改错误','请输入正确的新管理员名称');
                }
            }
            if($action=='loginname')
            {
                $dbA = new Admins();
                $ret = $dbA->tryLoginname($aid,$all['loginname']);
                if(!$ret)
                {
                    $msg=$dbSTM->setMsg('adminprofile',2,'修改错误','新的登录名称与其他管理员冲突');
                    // return redirect('/contest'.$cid.'/admin/profile');
                    return $dbSTM->retMsg(2,'修改错误','新的登录名称与其他管理员冲突');
                }
                $ret = $dbA->modLoginname($aid,$all['loginname']);
                if($ret)
                {
                    $msg=$dbSTM->setMsg('adminprofile',1,'修改成功','登录名称已更新');
                    // return redirect('/contest'.$cid.'/admin/profile');
                    return $dbSTM->retMsg(1,'修改成功','登录名称已更新');
                }
                else
                {
                    $msg=$dbSTM->setMsg('adminprofile',2,'修改错误','请输入由数字、大小写字母和下划线“_”组成的新的登录名称');
                    // return redirect('/contest'.$cid.'/admin/profile');
                    return $dbSTM->retMsg(2,'修改错误','请输入由数字、大小写字母和下划线“_”组成的新的登录名称');
                }
            }
            if($action=='password')
            {
                $dbA = new Admins();
                $ret = $dbA->tryPassword($aid,$all['passwordpre']);
                if(!$ret)
                {
                    $msg=$dbSTM->setMsg('adminprofile',2,'修改错误','请输入正确的旧登录密码');
                    // return redirect('/contest'.$cid.'/admin/profile');
                    return $dbSTM->retMsg(2,'修改错误','请输入正确的旧登录密码');
                }
                $ret = $dbA->modPassword($aid,$all['password']);
                if($ret)
                {
                    $msg=$dbSTM->setMsg('adminprofile',1,'修改成功','登录密码已更新');
                    // return redirect('/contest'.$cid.'/admin/profile');
                    return $dbSTM->retMsg(1,'修改成功','登录密码已更新');
                }
                else
                {
                    $msg=$dbSTM->setMsg('adminprofile',2,'修改错误','请输入正确的新登录密码');
                    // return redirect('/contest'.$cid.'/admin/profile');
                    return $dbSTM->retMsg(2,'修改错误','请输入正确的新登录密码');
                }
            }
            if($action=='getmsg')
            {
                return $dbSTM->popMsg('adminprofile');
            }
        }
    }
    
    public function players($cid)
    {
        $dbC=new Contests();
        $visitable=$dbC->enable($cid);
        if(!$visitable)return redirect('/visitwrong');
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isCA = $CTS->isContestAdmin($cid);
        $isCJ = $CTS->isContestJudger($cid);
        if($isCJ||($isA&&(!$isCA)))
            return redirect('/contest'.$cid.'/admin/error');
        if(!$isCA)
            return redirect('/contest'.$cid.'/admin/login');
        View::assign('page_title','选手列表');
        
        $players_order_key=$CTS->getPlayersOrderKey($cid);
        $players_order_by=$CTS->getPlayersOrderBy($cid);
        $dbCP=new ContestPlayers();
        $players=$dbCP->getPlayersOrdered($cid,$players_order_key,$players_order_by);
        View::assign([
            'players'=>$players,
            'players_order_key'=>$players_order_key,
            'players_order_by'=>$players_order_by
        ]);
        
        View::assign([
            'cid'=>$cid,
            'logined'=>true,
            'tabbar'=>'players',
            'contest_name'=>$dbC->getSetting($cid,'name'),
            'contest_favicon'=>$dbC->getSetting($cid,'favicon'),
            'contest_logo'=>$dbC->getSetting($cid,'logo'),
            'contest_color'=>$dbC->getSetting($cid,'color'),
            'contest_accent_color'=>$dbC->getSetting($cid,'accent_color')
        ]);
        return View::fetch();
    }
    public function playersapi($cid,$action)
    {
        $dbC=new Contests();
        $visitable=$dbC->enable($cid);
        if(!$visitable)return redirect('/visitwrong');
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isCA = $CTS->isContestAdmin($cid);
        $isCJ = $CTS->isContestJudger($cid);
        if($isCJ||($isA&&(!$isCA)))
            return redirect('/contest'.$cid.'/admin/error');
        if(!$isCA)
            return redirect('/contest'.$cid.'/admin/login');
        $all = Request::param();
        $dbSTM = new SessionToMsg();
        if(Request::method() == 'POST')
        {
            header('Content-Type:application/json');
            if($action=='calc')
            {
                $dbCP=new ContestPlayers();
                $ret = $dbCP->calcRank($cid);
                $msg=$dbSTM->setMsg('adminplayers',1,'计算成功','排名已更新');
                // return redirect('/contest'.$cid.'/admin/players');
                return $dbSTM->retMsg(1,'计算成功','排名已更新');
            }
            if($action=='score')
            {
                $dbCP=new ContestPlayers();
                $ret = $dbCP->calcPoint($cid,$all['pid']);
                $msg=$dbSTM->setMsg('adminplayers',1,'计算成功','得分已更新，请重新计算排名');
                // return redirect('/contest'.$cid.'/admin/players');
                return $dbSTM->retMsg(1,'计算成功','得分已更新，请重新计算排名');
            }
            if($action=='del')
            {
                $dbCP=new ContestPlayers();
                $dbCPs=new ContestPoints();
                $dellist=json_decode($all['dellist']);
                foreach($dellist as $v)
                {
                    $ret=$dbCPs->delPlayerPoints($cid,$v);
                    $ret=$dbCP->delPlayer($cid,$v);
                }
                $msg=$dbSTM->setMsg('adminplayers',1,'删除成功','列表已更新');
                // return redirect('/contest'.$cid.'/admin/players');
                return $dbSTM->retMsg(1,'删除成功','列表已更新');
            }
            if($action=='order')
            {
                $ret=$CTS->setPlayersOrder($cid,$all['players_order_key'],$all['players_order_by']);
                // return redirect('/contest'.$cid.'/admin/players');
                return $dbSTM->retMsg(1,'修改成功','排序方式已更新');
            }
            if($action=='getmsg')
            {
                return $dbSTM->popMsg('adminplayers');
            }
        }
        if(Request::method() == 'GET')
        {
            if($action=='export')
            {
                $dbCP=new ContestPlayers();
                return $dbCP->exportPlayers($cid);
            }
        }
    }
    public function playeradd($cid)
    {
        $dbC=new Contests();
        $visitable=$dbC->enable($cid);
        if(!$visitable)return redirect('/visitwrong');
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isCA = $CTS->isContestAdmin($cid);
        $isCJ = $CTS->isContestJudger($cid);
        if($isCJ||($isA&&(!$isCA)))
            return redirect('/contest'.$cid.'/admin/error');
        if(!$isCA)
            return redirect('/contest'.$cid.'/admin/login');
        View::assign('page_title','添加选手');
        
        View::assign([
            'cid'=>$cid,
            'logined'=>true,
            'tabbar'=>'players',
            'contest_name'=>$dbC->getSetting($cid,'name'),
            'contest_favicon'=>$dbC->getSetting($cid,'favicon'),
            'contest_logo'=>$dbC->getSetting($cid,'logo'),
            'contest_color'=>$dbC->getSetting($cid,'color'),
            'contest_accent_color'=>$dbC->getSetting($cid,'accent_color')
        ]);
        return View::fetch();
    }
    public function playeraddapi($cid,$action)
    {
        $dbC=new Contests();
        $visitable=$dbC->enable($cid);
        if(!$visitable)return redirect('/visitwrong');
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isCA = $CTS->isContestAdmin($cid);
        $isCJ = $CTS->isContestJudger($cid);
        if($isCJ||($isA&&(!$isCA)))
            return redirect('/contest'.$cid.'/admin/error');
        if(!$isCA)
            return redirect('/contest'.$cid.'/admin/login');
        $all = Request::param();
        $dbSTM = new SessionToMsg();
        if(Request::method() == 'POST')
        {
            header('Content-Type:application/json');
            if($action=='add')
            {
                $pid=$all['pid'];
                $sequence=$all['sequence'];
                $dbCP=new ContestPlayers();
                $players=$dbCP->getPlayersOrdered($cid,'pid','desc');
                if(is_null($pid)||strlen($pid)==0)
                {
                    if(count($players)==0)
                        $pid=1;
                    else
                        $pid=$players[0]['pid']+1;
                }
                else
                {
                    foreach($players as $v)
                    {
                        if($v['pid']==$pid)
                        {
                            $msg=$dbSTM->setMsg('adminplayeradd',2,'添加失败','选手序号已存在');
                            // return redirect('/contest'.$cid.'/admin/playeradd');
                            return $dbSTM->retMsg(2,'添加失败','选手序号已存在');
                        }
                    }
                }
                $players=$dbCP->getPlayersOrdered($cid,'sequence','desc');
                if(is_null($sequence)||strlen($sequence)==0)
                {
                    if(count($players)==0)
                        $sequence=1;
                    else
                        $sequence=$players[0]['sequence']+1;
                }
                else
                {
                    foreach($players as $v)
                    {
                        if($v['sequence']==$sequence)
                        {
                            $msg=$dbSTM->setMsg('adminplayeradd',2,'添加失败','选手顺序已存在');
                            // return redirect('/contest'.$cid.'/admin/playeradd');
                            return $dbSTM->retMsg(2,'添加失败','选手顺序已存在');
                        }
                    }
                }
                $ret=$dbCP->addPlayer($cid,$pid,$all['pname'],$sequence);
                $msg=$dbSTM->setMsg('adminplayers',1,'添加成功','选手列表已更新');
                // return redirect('/contest'.$cid.'/admin/players');
                return $dbSTM->retMsg(1,'添加成功','选手列表已更新');
            }
            if($action=='getmsg')
            {
                return $dbSTM->popMsg('adminplayeradd');
            }
        }
    }
    public function playereditre($cid)
    {
        $dbC=new Contests();
        $visitable=$dbC->enable($cid);
        if(!$visitable)return redirect('/visitwrong');
        return redirect('/contest'.$cid.'/admin/players');
    }
    public function playeredit($cid,$pid)
    {
        $dbC=new Contests();
        $visitable=$dbC->enable($cid);
        if(!$visitable)return redirect('/visitwrong');
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isCA = $CTS->isContestAdmin($cid);
        $isCJ = $CTS->isContestJudger($cid);
        if($isCJ||($isA&&(!$isCA)))
            return redirect('/contest'.$cid.'/admin/error');
        if(!$isCA)
            return redirect('/contest'.$cid.'/admin/login');
        View::assign('page_title','修改选手');
        
        $dbCP=new ContestPlayers();
        $player=$dbCP->getPlayer($cid,$pid);
        View::assign([
            'pid'=>$pid,
            'sequence'=>$player['sequence'],
            'pname'=>$player['pname'],
            'point'=>$player['point'],
            'rank'=>$player['rank']
        ]);
        
        View::assign([
            'cid'=>$cid,
            'logined'=>true,
            'tabbar'=>'players',
            'contest_name'=>$dbC->getSetting($cid,'name'),
            'contest_favicon'=>$dbC->getSetting($cid,'favicon'),
            'contest_logo'=>$dbC->getSetting($cid,'logo'),
            'contest_color'=>$dbC->getSetting($cid,'color'),
            'contest_accent_color'=>$dbC->getSetting($cid,'accent_color')
        ]);
        return View::fetch();
    }
    public function playereditapi($cid,$action,$pid)
    {
        $dbC=new Contests();
        $visitable=$dbC->enable($cid);
        if(!$visitable)return redirect('/visitwrong');
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isCA = $CTS->isContestAdmin($cid);
        $isCJ = $CTS->isContestJudger($cid);
        if($isCJ||($isA&&(!$isCA)))
            return redirect('/contest'.$cid.'/admin/error');
        if(!$isCA)
            return redirect('/contest'.$cid.'/admin/login');
        $all = Request::param();
        $dbSTM = new SessionToMsg();
        if(Request::method() == 'POST')
        {
            header('Content-Type:application/json');
            if($action=='edit')
            {
                $pid=(int)$pid;
                $sequence=(int)$all['sequence'];
                $dbCP=new ContestPlayers();
                $players=$dbCP->getPlayersOrdered($cid,'sequence','desc');
                if(is_null($sequence)||strlen($sequence)==0)
                {
                    if(count($players)==1)
                        $sequence=1;
                    else
                        $sequence=$players[0]['sequence']+1;
                }
                else
                {
                    foreach($players as $v)
                    {
                        if($v['sequence']==$sequence&&$v['pid']!=$pid)
                        {
                            $msg=$dbSTM->setMsg('adminplayeredit'.$pid,2,'修改失败','选手顺序已存在');
                            // return redirect('/contest'.$cid.'/admin/playeredit'.$pid);
                            return $dbSTM->retMsg(2,'修改失败','选手顺序已存在');
                        }
                    }
                }
                $ret=$dbCP->updatePlayer($cid,$pid,$all['pname'],$sequence);
                $msg=$dbSTM->setMsg('adminplayers',1,'修改成功','选手列表已更新');
                // return redirect('/contest'.$cid.'/admin/players');
                return $dbSTM->retMsg(1,'修改成功','选手列表已更新');
            }
            if($action=='getmsg')
            {
                return $dbSTM->popMsg('adminplayeredit'.$pid);
            }
        }
    }
    
    public function judgers($cid)
    {
        $dbC=new Contests();
        $visitable=$dbC->enable($cid);
        if(!$visitable)return redirect('/visitwrong');
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isCA = $CTS->isContestAdmin($cid);
        $isCJ = $CTS->isContestJudger($cid);
        if($isCJ||($isA&&(!$isCA)))
            return redirect('/contest'.$cid.'/admin/error');
        if(!$isCA)
            return redirect('/contest'.$cid.'/admin/login');
        View::assign('page_title','评委列表');
        
        $judgers_order_key=$CTS->getJudgersOrderKey($cid);
        $judgers_order_by=$CTS->getJudgersOrderBy($cid);
        $dbCJ=new ContestJudgers();
        if($judgers_order_key=='login_page')
        {
            $judgers_order_key2='login_page_content';
            $judgers_order_by2=$judgers_order_by;
            $judgers=$dbCJ->getJudgersOrdered2($cid,$judgers_order_key,$judgers_order_by,$judgers_order_key2,$judgers_order_by2);
        }
        else
            $judgers=$dbCJ->getJudgersOrdered($cid,$judgers_order_key,$judgers_order_by);
        View::assign([
            'judgers'=>$judgers,
            'judgers_order_key'=>$judgers_order_key,
            'judgers_order_by'=>$judgers_order_by
        ]);
        
        View::assign([
            'cid'=>$cid,
            'logined'=>true,
            'tabbar'=>'judgers',
            'contest_name'=>$dbC->getSetting($cid,'name'),
            'contest_favicon'=>$dbC->getSetting($cid,'favicon'),
            'contest_logo'=>$dbC->getSetting($cid,'logo'),
            'contest_color'=>$dbC->getSetting($cid,'color'),
            'contest_accent_color'=>$dbC->getSetting($cid,'accent_color')
        ]);
        return View::fetch();
    }
    public function judgersapi($cid,$action)
    {
        $dbC=new Contests();
        $visitable=$dbC->enable($cid);
        if(!$visitable)return redirect('/visitwrong');
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isCA = $CTS->isContestAdmin($cid);
        $isCJ = $CTS->isContestJudger($cid);
        if($isCJ||($isA&&(!$isCA)))
            return redirect('/contest'.$cid.'/admin/error');
        if(!$isCA)
            return redirect('/contest'.$cid.'/admin/login');
        $all = Request::param();
        $dbSTM = new SessionToMsg();
        if(Request::method() == 'POST')
        {
            header('Content-Type:application/json');
            if($action=='sync')
            {
                $dbCJ=new ContestJudgers();
                $synclist=json_decode($all['synclist']);
                foreach($synclist as $v)
                {
                    $ret=$dbCJ->syncPage($cid,$v);
                }
                $msg=$dbSTM->setMsg('adminjudgers',1,'同步成功','列表已更新');
                // return redirect('/contest'.$cid.'/admin/judgers');
                return $dbSTM->retMsg(1,'同步成功','列表已更新');
            }
            if($action=='del')
            {
                $dbCJ=new ContestJudgers();
                $dbCPs=new ContestPoints();
                $dellist=json_decode($all['dellist']);
                foreach($dellist as $v)
                {
                    $ret=$dbCPs->delJudgerPoints($cid,$v);
                    $ret=$dbCJ->delJudger($cid,$v);
                }
                $msg=$dbSTM->setMsg('adminjudgers',1,'删除成功','列表已更新');
                // return redirect('/contest'.$cid.'/admin/judgers');
                return $dbSTM->retMsg(1,'删除成功','列表已更新');
            }
            if($action=='order')
            {
                $ret=$CTS->setJudgersOrder($cid,$all['judgers_order_key'],$all['judgers_order_by']);
                // return redirect('/contest'.$cid.'/admin/judgers');
                return $dbSTM->retMsg(1,'修改成功','排序方式已更新');
            }
            if($action=='getmsg')
            {
                return $dbSTM->popMsg('adminjudgers');
            }
        }
    }
    public function judgeradd($cid)
    {
        $dbC=new Contests();
        $visitable=$dbC->enable($cid);
        if(!$visitable)return redirect('/visitwrong');
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isCA = $CTS->isContestAdmin($cid);
        $isCJ = $CTS->isContestJudger($cid);
        if($isCJ||($isA&&(!$isCA)))
            return redirect('/contest'.$cid.'/admin/error');
        if(!$isCA)
            return redirect('/contest'.$cid.'/admin/login');
        View::assign('page_title','添加评委');
        
        View::assign([
            'cid'=>$cid,
            'logined'=>true,
            'tabbar'=>'judgers',
            'contest_name'=>$dbC->getSetting($cid,'name'),
            'contest_favicon'=>$dbC->getSetting($cid,'favicon'),
            'contest_logo'=>$dbC->getSetting($cid,'logo'),
            'contest_color'=>$dbC->getSetting($cid,'color'),
            'contest_accent_color'=>$dbC->getSetting($cid,'accent_color')
        ]);
        return View::fetch();
    }
    public function judgeraddapi($cid,$action)
    {
        $dbC=new Contests();
        $visitable=$dbC->enable($cid);
        if(!$visitable)return redirect('/visitwrong');
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isCA = $CTS->isContestAdmin($cid);
        $isCJ = $CTS->isContestJudger($cid);
        if($isCJ||($isA&&(!$isCA)))
            return redirect('/contest'.$cid.'/admin/error');
        if(!$isCA)
            return redirect('/contest'.$cid.'/admin/login');
        $all = Request::param();
        $dbSTM = new SessionToMsg();
        if(Request::method() == 'POST')
        {
            header('Content-Type:application/json');
            if($action=='add')
            {
                $jid=$all['jid'];
                $logincode=$all['logincode'];
                $dbCJ=new ContestJudgers();
                $judgers=$dbCJ->getJudgersOrdered($cid,'jid','desc');
                if(is_null($jid)||strlen($jid)==0)
                {
                    if(count($judgers)==0)
                        $jid=1;
                    else
                        $jid=$judgers[0]['jid']+1;
                }
                else
                {
                    foreach($judgers as $v)
                    {
                        if($v['jid']==$jid)
                        {
                            $msg=$dbSTM->setMsg('adminjudgeradd',2,'添加失败','评委序号已存在');
                            // return redirect('/contest'.$cid.'/admin/judgeradd');
                            return $dbSTM->retMsg(2,'添加失败','评委序号已存在');
                        }
                    }
                }
                $judgers=$dbCJ->getJudgersOrdered($cid,'logincode','desc');
                if(is_null($logincode)||strlen($logincode)==0)
                {
                    $flag=true;
                    while($flag)
                    {
                        $logincode=$dbCJ->genLogincode();
                        $flag=false;
                        foreach($judgers as $v)
                        {
                            if($v['logincode']==$logincode)
                            {
                                $flag=true;
                                break;
                            }
                        }
                    }
                }
                else
                {
                    foreach($judgers as $v)
                    {
                        if($v['logincode']==$logincode)
                        {
                            $msg=$dbSTM->setMsg('adminjudgeradd',2,'添加失败','登录码已存在');
                            // return redirect('/contest'.$cid.'/admin/judgeradd');
                            return $dbSTM->retMsg(2,'添加失败','登录码已存在');
                        }
                    }
                }
                $ret=$dbCJ->addJudger($cid,$jid,$all['jname'],$logincode,$all['enable'],$all['login_page'],$all['login_page_content']);
                $msg=$dbSTM->setMsg('adminjudgers',1,'添加成功','评委列表已更新');
                // return redirect('/contest'.$cid.'/admin/judgers');
                return $dbSTM->retMsg(1,'添加成功','评委列表已更新');
            }
            if($action=='getmsg')
            {
                return $dbSTM->popMsg('adminjudgeradd');
            }
        }
    }
    public function judgereditre($cid)
    {
        $dbC=new Contests();
        $visitable=$dbC->enable($cid);
        if(!$visitable)return redirect('/visitwrong');
        return redirect('/contest'.$cid.'/admin/judgers');
    }
    public function judgeredit($cid,$jid)
    {
        $dbC=new Contests();
        $visitable=$dbC->enable($cid);
        if(!$visitable)return redirect('/visitwrong');
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isCA = $CTS->isContestAdmin($cid);
        $isCJ = $CTS->isContestJudger($cid);
        if($isCJ||($isA&&(!$isCA)))
            return redirect('/contest'.$cid.'/admin/error');
        if(!$isCA)
            return redirect('/contest'.$cid.'/admin/login');
        View::assign('page_title','修改评委');
        
        $dbCJ=new ContestJudgers();
        $judger=$dbCJ->getJudger($cid,$jid);
        View::assign([
            'jid'=>$jid,
            'logincode'=>$judger['logincode'],
            'jname'=>$judger['jname'],
            'enable'=>$judger['enable'],
            'login_page'=>$judger['login_page'],
            'login_page_content'=>$judger['login_page_content']
        ]);
        
        View::assign([
            'cid'=>$cid,
            'logined'=>true,
            'tabbar'=>'judgers',
            'contest_name'=>$dbC->getSetting($cid,'name'),
            'contest_favicon'=>$dbC->getSetting($cid,'favicon'),
            'contest_logo'=>$dbC->getSetting($cid,'logo'),
            'contest_color'=>$dbC->getSetting($cid,'color'),
            'contest_accent_color'=>$dbC->getSetting($cid,'accent_color')
        ]);
        return View::fetch();
    }
    public function judgereditapi($cid,$action,$jid)
    {
        $dbC=new Contests();
        $visitable=$dbC->enable($cid);
        if(!$visitable)return redirect('/visitwrong');
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isCA = $CTS->isContestAdmin($cid);
        $isCJ = $CTS->isContestJudger($cid);
        if($isCJ||($isA&&(!$isCA)))
            return redirect('/contest'.$cid.'/admin/error');
        if(!$isCA)
            return redirect('/contest'.$cid.'/admin/login');
        $all = Request::param();
        $dbSTM = new SessionToMsg();
        if(Request::method() == 'POST')
        {
            header('Content-Type:application/json');
            if($action=='edit')
            {
                $jid=(int)$jid;
                $logincode=$all['logincode'];
                $enable=$all['enable'];
                $dbCJ=new ContestJudgers();
                $dbCPs=new ContestPoints();
                $judgers=$dbCJ->getJudgersOrdered($cid,'logincode','desc');
                foreach($judgers as $v)
                {
                    if($v['logincode']==$logincode&&$v['jid']!=$jid)
                    {
                        $msg=$dbSTM->setMsg('adminjudgeredit'.$jid,2,'修改失败','登录码已存在');
                        // return redirect('/contest'.$cid.'/admin/judgeredit'.$jid);
                        return $dbSTM->retMsg(2,'修改失败','登录码已存在');
                    }
                }
                $ret=$dbCJ->updateJudger($cid,$jid,$all['jname'],$logincode,$enable,$all['login_page'],$all['login_page_content']);
                $ret=$dbCPs->setEnable($cid,$jid,$enable);
                $msg=$dbSTM->setMsg('adminjudgers',1,'修改成功','评委列表已更新');
                // return redirect('/contest'.$cid.'/admin/judgers');
                return $dbSTM->retMsg(1,'修改成功','评委列表已更新');
            }
            if($action=='getmsg')
            {
                return $dbSTM->popMsg('adminjudgeredit'.$jid);
            }
        }
    }
    public function judgerqrcodere($cid)
    {
        $dbC=new Contests();
        $visitable=$dbC->enable($cid);
        if(!$visitable)return redirect('/visitwrong');
        return redirect('/contest'.$cid.'/admin/judgers');
    }
    public function judgerqrcode($cid,$jid)
    {
        $dbC=new Contests();
        $visitable=$dbC->enable($cid);
        if(!$visitable)return redirect('/visitwrong');
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isCA = $CTS->isContestAdmin($cid);
        $isCJ = $CTS->isContestJudger($cid);
        if($isCJ||($isA&&(!$isCA)))
            return redirect('/contest'.$cid.'/admin/error');
        if(!$isCA)
            return redirect('/contest'.$cid.'/admin/login');
        View::assign('page_title','评委二维码');
        
        $dbCJ=new ContestJudgers();
        $judger=$dbCJ->getJudger($cid,$jid);
        $dbQRC=new QuickResponseCode();
        View::assign([
            'jid'=>$jid,
            'logincode'=>$judger['logincode'],
            'jname'=>$judger['jname'],
            'enable'=>$judger['enable'],
            'qrcode'=>$dbQRC->genPic($cid,$jid,'L',6),
            'loginurl'=>$dbQRC->getUrl($cid,$jid)
        ]);
        
        View::assign([
            'cid'=>$cid,
            'logined'=>true,
            'tabbar'=>'judgers',
            'contest_name'=>$dbC->getSetting($cid,'name'),
            'contest_favicon'=>$dbC->getSetting($cid,'favicon'),
            'contest_logo'=>$dbC->getSetting($cid,'logo'),
            'contest_color'=>$dbC->getSetting($cid,'color'),
            'contest_accent_color'=>$dbC->getSetting($cid,'accent_color')
        ]);
        return View::fetch();
    }
    public function judgerqrcodeapi($cid,$action,$jid)
    {
        $dbC=new Contests();
        $visitable=$dbC->enable($cid);
        if(!$visitable)return redirect('/visitwrong');
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isCA = $CTS->isContestAdmin($cid);
        $isCJ = $CTS->isContestJudger($cid);
        if($isCJ||($isA&&(!$isCA)))
            return redirect('/contest'.$cid.'/admin/error');
        if(!$isCA)
            return redirect('/contest'.$cid.'/admin/login');
        $all = Request::param();
        $dbSTM = new SessionToMsg();
        if(Request::method() == 'POST')
        {
            header('Content-Type:application/json');
            if($action=='getmsg')
            {
                return $dbSTM->popMsg('adminjudgerqrcode'.$jid);
            }
        }
        if(Request::method() == 'GET')
        {
            if($action=='get')
            {
                header("Content-type:image/png");
                $dbQRC=new QuickResponseCode();
                $qrcode=$dbQRC->genPic($cid,$jid,'L',6);
                return '<img src="'.$qrcode.'" />';
            }
        }
    }
    
    public function points($cid)
    {
        $dbC=new Contests();
        $visitable=$dbC->enable($cid);
        if(!$visitable)return redirect('/visitwrong');
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isCA = $CTS->isContestAdmin($cid);
        $isCJ = $CTS->isContestJudger($cid);
        if($isCJ||($isA&&(!$isCA)))
            return redirect('/contest'.$cid.'/admin/error');
        if(!$isCA)
            return redirect('/contest'.$cid.'/admin/login');
        View::assign('page_title','分数列表');
        
        $points_order_key=$CTS->getPointsOrderKey($cid);
        $points_order_by=$CTS->getPointsOrderBy($cid);
        $dbCPs=new ContestPoints();
        if($points_order_key=='pid')
        {
            $points_order_key2='jid';
            $points_order_by2=$points_order_by;
            $points=$dbCPs->getPointsOrdered2($cid,$points_order_key,$points_order_by,$points_order_key2,$points_order_by2);
        }
        else if($points_order_key=='jid')
        {
            $points_order_key2='pid';
            $points_order_by2=$points_order_by;
            $points=$dbCPs->getPointsOrdered2($cid,$points_order_key,$points_order_by,$points_order_key2,$points_order_by2);
        }
        else
            $points=$dbCPs->getPointsOrdered($cid,$points_order_key,$points_order_by);
        View::assign([
            'points'=>$points,
            'points_order_key'=>$points_order_key,
            'points_order_by'=>$points_order_by
        ]);
        
        View::assign([
            'cid'=>$cid,
            'logined'=>true,
            'tabbar'=>'points',
            'contest_name'=>$dbC->getSetting($cid,'name'),
            'contest_favicon'=>$dbC->getSetting($cid,'favicon'),
            'contest_logo'=>$dbC->getSetting($cid,'logo'),
            'contest_color'=>$dbC->getSetting($cid,'color'),
            'contest_accent_color'=>$dbC->getSetting($cid,'accent_color')
        ]);
        return View::fetch();
    }
    public function pointsapi($cid,$action)
    {
        $dbC=new Contests();
        $visitable=$dbC->enable($cid);
        if(!$visitable)return redirect('/visitwrong');
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isCA = $CTS->isContestAdmin($cid);
        $isCJ = $CTS->isContestJudger($cid);
        if($isCJ||($isA&&(!$isCA)))
            return redirect('/contest'.$cid.'/admin/error');
        if(!$isCA)
            return redirect('/contest'.$cid.'/admin/login');
        $all = Request::param();
        $dbSTM = new SessionToMsg();
        if(Request::method() == 'POST')
        {
            header('Content-Type:application/json');
            if($action=='del')
            {
                $dbCPs=new ContestPoints();
                $dellist=json_decode($all['dellist'],true);
                foreach($dellist as $v)
                {
                    $ret=$dbCPs->delPoint($cid,$v['pid'],$v['jid']);
                }
                $msg=$dbSTM->setMsg('adminpoints',1,'删除成功','列表已更新');
                // return redirect('/contest'.$cid.'/admin/points');
                return $dbSTM->retMsg(1,'删除成功','列表已更新');
            }
            if($action=='order')
            {
                $ret=$CTS->setPointsOrder($cid,$all['points_order_key'],$all['points_order_by']);
                // return redirect('/contest'.$cid.'/admin/points');
                return $dbSTM->retMsg(1,'修改成功','排序方式已更新');
            }
            if($action=='getmsg')
            {
                return $dbSTM->popMsg('adminpoints');
            }
        }
        if(Request::method() == 'GET')
        {
            if($action=='export')
            {
                $dbCPs=new ContestPoints();
                return $dbCPs->exportPoints($cid);
            }
        }
    }
    
    public function settings($cid)
    {
        $dbC=new Contests();
        $visitable=$dbC->enable($cid);
        if(!$visitable)return redirect('/visitwrong');
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isCA = $CTS->isContestAdmin($cid);
        $isCJ = $CTS->isContestJudger($cid);
        if($isCJ||($isA&&(!$isCA)))
            return redirect('/contest'.$cid.'/admin/error');
        if(!$isCA)
            return redirect('/contest'.$cid.'/admin/login');
        View::assign('page_title','比赛设置');
        
        $dbGS=new GlobalSettings();
        View::assign([
            'cname'=>$dbC->getContestSetting($cid,'cname'),
            'cfavicon'=>$dbC->getContestSetting($cid,'cfavicon'),
            'clogo'=>$dbC->getContestSetting($cid,'clogo'),
            'ccolor'=>$dbC->getContestSetting($cid,'ccolor'),
            'caccent_color'=>$dbC->getContestSetting($cid,'caccent_color'),
            'enable'=>$dbC->getContestSetting($cid,'enable'),
            'min_max_mode'=>$dbC->getContestSetting($cid,'min_max_mode'),
            'screen_page_pic'=>$dbC->getContestSetting($cid,'screen_page_pic'),
            'screen_background_page_pic'=>$dbC->getContestSetting($cid,'screen_background_page_pic')
        ]);
        View::assign([
            'site_name'=>$dbGS->getSetting('name'),
            'site_favicon'=>$dbGS->getSetting('favicon'),
            'site_logo'=>$dbGS->getSetting('logo'),
            'site_color'=>$dbGS->getSetting('color'),
            'site_accent_color'=>$dbGS->getSetting('accent_color'),
            'default_screen_page_pic'=>$dbGS->getSetting('default_screen_page_pic'),
            'default_screen_background_page_pic'=>$dbGS->getSetting('default_screen_background_page_pic')
        ]);
        $color_list=['red','pink','purple','deep-purple','indigo','blue','light-blue','cyan','teal','green','light-green','lime','yellow','amber','orange','deep-orange','brown','grey','blue-grey'];
        $accent_color_list=['red','pink','purple','deep-purple','indigo','blue','light-blue','cyan','teal','green','light-green','lime','yellow','amber','orange','deep-orange'];
        View::assign([
            'color_list'=>$color_list,
            'accent_color_list'=>$accent_color_list
        ]);
        
        View::assign([
            'cid'=>$cid,
            'logined'=>true,
            'tabbar'=>'settings',
            'contest_name'=>$dbC->getSetting($cid,'name'),
            'contest_favicon'=>$dbC->getSetting($cid,'favicon'),
            'contest_logo'=>$dbC->getSetting($cid,'logo'),
            'contest_color'=>$dbC->getSetting($cid,'color'),
            'contest_accent_color'=>$dbC->getSetting($cid,'accent_color')
        ]);
        return View::fetch();
    }
    public function settingsapi($cid,$action)
    {
        $dbC=new Contests();
        $visitable=$dbC->enable($cid);
        if(!$visitable)return redirect('/visitwrong');
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isCA = $CTS->isContestAdmin($cid);
        $isCJ = $CTS->isContestJudger($cid);
        if($isCJ||($isA&&(!$isCA)))
            return redirect('/contest'.$cid.'/admin/error');
        if(!$isCA)
            return redirect('/contest'.$cid.'/admin/login');
        $all = Request::param();
        $dbSTM = new SessionToMsg();
        if(Request::method() == 'POST')
        {
            header('Content-Type:application/json');
            if($action=='edit')
            {
                $cid=(int)$cid;
                $cname=$all['cname'];
                $min_max_mode=$all['min_max_mode'];
                $ccolor=$all['ccolor'];
                $caccent_color=$all['caccent_color'];
                
                $ret=$dbC->setContestSetting($cid,'cname',$cname);
                $ret=$dbC->setContestSetting($cid,'min_max_mode',$min_max_mode);
                $ret=$dbC->setContestSetting($cid,'ccolor',$ccolor);
                $ret=$dbC->setContestSetting($cid,'caccent_color',$caccent_color);
                $msg=$dbSTM->setMsg('adminsettings',1,'修改成功','比赛设置已更新');
                // return redirect('/contest'.$cid.'/admin/settings');
                return $dbSTM->retMsg(1,'修改成功','比赛设置已更新');
            }
            if($action=='uploadfavicon')
            {
                $cid=(int)$cid;
                $file = request()->file('imagefavicon');
                if(empty($file)){
                    $msg=$dbSTM->setMsg('adminsettings',2,'上传失败','未选择任何文件');
                    return redirect('/contest'.$cid.'/admin/settings');
                    // return $dbSTM->retMsg(2,'上传失败','未选择任何文件');
                }
                $extension=$file->extension();
                if(!in_array($extension,array('ico','jpeg','jpg','png'))){
                    $msg=$dbSTM->setMsg('adminsettings',2,'上传失败','比赛网标的文件扩展名仅能为ico、jpeg、jpg和png中的一个');
                    return redirect('/contest'.$cid.'/admin/settings');
                    // return $dbSTM->retMsg(2,'上传失败','比赛网标的文件扩展名仅能为ico、jpeg、jpg和png中的一个');
                }
                if(filesize($file)/1024/1024>1){
                    $msg=$dbSTM->setMsg('adminsettings',2,'上传失败','比赛网标的文件大小在1MB以内');
                    return redirect('/contest'.$cid.'/admin/settings');
                    // return $dbSTM->retMsg(2,'上传失败','比赛网标的文件大小在1MB以内');
                }
                $saveName = Filesystem::disk('picture')->putFile('favicon',$file,'md5');
                if (!empty($saveName)) {
                    $saveName = "/uploads/".str_replace('\\', "/", $saveName);
                    $ret=$dbC->setContestSetting($cid,'cfavicon',$saveName);
                    $msg=$dbSTM->setMsg('adminsettings',1,'上传成功','可能需要清除缓存后才能更新');
                    return redirect('/contest'.$cid.'/admin/settings');
                    // return $dbSTM->retMsg(1,'上传成功','可能需要清除缓存后才能更新');
                }
            }
            if($action=='usefavicon')
            {
                $cid=(int)$cid;
                $ret=$dbC->setContestSetting($cid,'cfavicon','');
                $msg=$dbSTM->setMsg('adminsettings',1,'修改成功','可能需要清除缓存后才能更新');
                // return redirect('/contest'.$cid.'/admin/settings');
                return $dbSTM->retMsg(1,'修改成功','可能需要清除缓存后才能更新');
            }
            if($action=='uploadlogo')
            {
                $cid=(int)$cid;
                $file = request()->file('imagelogo');
                if(empty($file)){
                    $msg=$dbSTM->setMsg('adminsettings',2,'上传失败','未选择任何文件');
                    return redirect('/contest'.$cid.'/admin/settings');
                    // return $dbSTM->retMsg(2,'上传失败','未选择任何文件');
                }
                $extension=$file->extension();
                if(!in_array($extension,array('jpeg','jpg','png'))){
                    $msg=$dbSTM->setMsg('adminsettings',2,'上传失败','比赛图标的文件扩展名仅能为jpeg、jpg和png中的一个');
                    return redirect('/contest'.$cid.'/admin/settings');
                    // return $dbSTM->retMsg(2,'上传失败','比赛图标的文件扩展名仅能为jpeg、jpg和png中的一个');
                }
                if(filesize($file)/1024/1024>1){
                    $msg=$dbSTM->setMsg('adminsettings',2,'上传失败','比赛图标的文件大小在1MB以内');
                    return redirect('/contest'.$cid.'/admin/settings');
                    // return $dbSTM->retMsg(2,'上传失败','比赛图标的文件大小在1MB以内');
                }
                $saveName = Filesystem::disk('picture')->putFile('logo',$file,'md5');
                if (!empty($saveName)) {
                    $saveName = "/uploads/".str_replace('\\', "/", $saveName);
                    $ret=$dbC->setContestSetting($cid,'clogo',$saveName);
                    $msg=$dbSTM->setMsg('adminsettings',1,'上传成功','可能需要清除缓存后才能更新');
                    return redirect('/contest'.$cid.'/admin/settings');
                    // return $dbSTM->retMsg(1,'上传成功','可能需要清除缓存后才能更新');
                }
            }
            if($action=='uselogo')
            {
                $cid=(int)$cid;
                $ret=$dbC->setContestSetting($cid,'clogo','');
                $msg=$dbSTM->setMsg('adminsettings',1,'修改成功','可能需要清除缓存后才能更新');
                // return redirect('/contest'.$cid.'/admin/settings');
                return $dbSTM->retMsg(1,'修改成功','可能需要清除缓存后才能更新');
            }
            if($action=='uploadspic')
            {
                $cid=(int)$cid;
                $file = request()->file('imagespic');
                if(empty($file)){
                    $msg=$dbSTM->setMsg('adminsettings',2,'上传失败','未选择任何文件');
                    return redirect('/contest'.$cid.'/admin/settings');
                    // return $dbSTM->retMsg(2,'上传失败','未选择任何文件');
                }
                $extension=$file->extension();
                if(!in_array($extension,array('jpeg','jpg','png'))){
                    $msg=$dbSTM->setMsg('adminsettings',2,'上传失败','屏幕背景图片的文件扩展名仅能为jpeg、jpg和png中的一个');
                    return redirect('/contest'.$cid.'/admin/settings');
                    // return $dbSTM->retMsg(2,'上传失败','屏幕背景图片的文件扩展名仅能为jpeg、jpg和png中的一个');
                }
                if(filesize($file)/1024/1024>10){
                    $msg=$dbSTM->setMsg('adminsettings',2,'上传失败','屏幕背景图片的文件大小在10MB以内');
                    return redirect('/contest'.$cid.'/admin/settings');
                    // return $dbSTM->retMsg(2,'上传失败','屏幕背景图片的文件大小在10MB以内');
                }
                $saveName = Filesystem::disk('picture')->putFile('spic',$file,'md5');
                if (!empty($saveName)) {
                    $saveName = "/uploads/".str_replace('\\', "/", $saveName);
                    $ret=$dbC->setContestSetting($cid,'screen_page_pic',$saveName);
                    $msg=$dbSTM->setMsg('adminsettings',1,'上传成功','可能需要清除缓存后才能更新');
                    return redirect('/contest'.$cid.'/admin/settings');
                    // return $dbSTM->retMsg(1,'上传成功','可能需要清除缓存后才能更新');
                }
            }
            if($action=='usespic')
            {
                $cid=(int)$cid;
                $ret=$dbC->setContestSetting($cid,'screen_page_pic','');
                $msg=$dbSTM->setMsg('adminsettings',1,'修改成功','可能需要清除缓存后才能更新');
                // return redirect('/contest'.$cid.'/admin/settings');
                return $dbSTM->retMsg(1,'修改成功','可能需要清除缓存后才能更新');
            }
            if($action=='uploadsbgpic')
            {
                $cid=(int)$cid;
                $file = request()->file('imagesbgpic');
                if(empty($file)){
                    $msg=$dbSTM->setMsg('adminsettings',2,'上传失败','未选择任何文件');
                    return redirect('/contest'.$cid.'/admin/settings');
                    // return $dbSTM->retMsg(2,'上传失败','未选择任何文件');
                }
                $extension=$file->extension();
                if(!in_array($extension,array('jpeg','jpg','png'))){
                    $msg=$dbSTM->setMsg('adminsettings',2,'上传失败','屏幕背景页面背景图片的文件扩展名仅能为jpeg、jpg和png中的一个');
                    return redirect('/contest'.$cid.'/admin/settings');
                    // return $dbSTM->retMsg(2,'上传失败','屏幕背景页面背景图片的文件扩展名仅能为jpeg、jpg和png中的一个');
                }
                if(filesize($file)/1024/1024>10){
                    $msg=$dbSTM->setMsg('adminsettings',2,'上传失败','屏幕背景页面背景图片的文件大小在10MB以内');
                    return redirect('/contest'.$cid.'/admin/settings');
                    // return $dbSTM->retMsg(2,'上传失败','屏幕背景页面背景图片的文件大小在10MB以内');
                }
                $saveName = Filesystem::disk('picture')->putFile('sbgpic',$file,'md5');
                if (!empty($saveName)) {
                    $saveName = "/uploads/".str_replace('\\', "/", $saveName);
                    $ret=$dbC->setContestSetting($cid,'screen_background_page_pic',$saveName);
                    $msg=$dbSTM->setMsg('adminsettings',1,'上传成功','可能需要清除缓存后才能更新');
                    return redirect('/contest'.$cid.'/admin/settings');
                    // return $dbSTM->retMsg(1,'上传成功','可能需要清除缓存后才能更新');
                }
            }
            if($action=='usesbgpic')
            {
                $cid=(int)$cid;
                $ret=$dbC->setContestSetting($cid,'screen_background_page_pic','');
                $msg=$dbSTM->setMsg('adminsettings',1,'修改成功','可能需要清除缓存后才能更新');
                // return redirect('/contest'.$cid.'/admin/settings');
                return $dbSTM->retMsg(1,'修改成功','可能需要清除缓存后才能更新');
            }
            if($action=='getmsg')
            {
                return $dbSTM->popMsg('adminsettings');
            }
        }
    }
    
    public function control($cid)
    {
        $dbC=new Contests();
        $visitable=$dbC->enable($cid);
        if(!$visitable)return redirect('/visitwrong');
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isCA = $CTS->isContestAdmin($cid);
        $isCJ = $CTS->isContestJudger($cid);
        if($isCJ||($isA&&(!$isCA)))
            return redirect('/contest'.$cid.'/admin/error');
        if(!$isCA)
            return redirect('/contest'.$cid.'/admin/login');
        View::assign('page_title','现场控制');
        
        $dbCJ=new ContestJudgers();
        $dbCP=new ContestPlayers();
        $dbCPs=new ContestPoints();
        $judger_page=$dbC->getJudgerPage($cid);
        $judger_page_content=$dbC->getJudgerPageContentPre($cid);
        $judger_next_page=$dbC->getJudgerNextPage($cid);
        $judger_next_page_content=$dbC->getJudgerNextPageContentPre($cid);
        if($judger_page=='end')
            View::assign('ranked',$dbCP->isRanked($cid));
        if($judger_page=='score')
        {
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
                return redirect('/contest'.$cid.'/admin/control');
            }
            View::assign('score_status',$ret);
        }
        
        View::assign([
            'judger_page'=>$judger_page,
            'judger_page_content'=>$judger_page_content,
            'judger_next_page'=>$judger_next_page,
            'judger_next_page_content'=>$judger_next_page_content,
            'screen_page'=>$dbC->getScreenPage($cid),
            'screen_page_content'=>$dbC->getScreenPageContentPre($cid)
        ]);
        
        View::assign([
            'cid'=>$cid,
            'logined'=>true,
            'tabbar'=>'control',
            'contest_name'=>$dbC->getSetting($cid,'name'),
            'contest_favicon'=>$dbC->getSetting($cid,'favicon'),
            'contest_logo'=>$dbC->getSetting($cid,'logo'),
            'contest_color'=>$dbC->getSetting($cid,'color'),
            'contest_accent_color'=>$dbC->getSetting($cid,'accent_color')
        ]);
        return View::fetch();
    }
    public function controlapi($cid,$action)
    {
        $dbC=new Contests();
        $visitable=$dbC->enable($cid);
        if(!$visitable)return redirect('/visitwrong');
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isCA = $CTS->isContestAdmin($cid);
        $isCJ = $CTS->isContestJudger($cid);
        if($isCJ||($isA&&(!$isCA)))
            return redirect('/contest'.$cid.'/admin/error');
        if(!$isCA)
            return redirect('/contest'.$cid.'/admin/login');
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
                    $msg=$dbSTM->setMsg('admincontrol',1,'需要切换','控制台页面已更新');
                    // return redirect('/contest'.$cid.'/admin/control');
                    return $dbSTM->retMsg(1,'需要切换','控制台页面已更新');
                }
                if($screen_page!=$all['screen_page']||$screen_page_content!=$all['screen_page_content'])
                {
                    $msg=$dbSTM->setMsg('admincontrol',1,'需要切换','控制台页面已更新');
                    // return redirect('/contest'.$cid.'/admin/control');
                    return $dbSTM->retMsg(1,'需要切换','控制台页面已更新');
                }
                $msg=$dbSTM->setMsg('admincontrol',0,'无需切换','控制台页面无需更新');
                // return redirect('/contest'.$cid.'/admin/control');
                return $dbSTM->retMsg(0,'无需切换','控制台页面无需更新');
            }
            if($action=='getstatus')
            {
                $dbCJ=new ContestJudgers();
                $dbCP=new ContestPlayers();
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
                    return $dbSTM->retMsg(1,'需要切换','控制台页面已更新');
                }
                return $dbSTM->retMsg(0,'更新数据',json_encode($ret));
            }
            if($action=='judger')
            {
                $judger_next_page=$dbC->getJudgerNextPage($cid);
                $judger_next_page_content=$dbC->getJudgerNextPageContentPre($cid);
                $ret = $dbC->updateJudgerPage($cid,$judger_next_page,$judger_next_page_content);
                $ret = $dbC->updateJudgerNextPage($cid,$all['judger_step_page'],$all['judger_step_page_content']);
                $msg=$dbSTM->setMsg('admincontrol',1,'修改成功','评委页面已更新');
                // return redirect('/contest'.$cid.'/admin/control');
                return $dbSTM->retMsg(1,'修改成功','评委页面已更新');
            }
            if($action=='screen')
            {
                $ret = $dbC->updateScreenPage($cid,$all['screen_step_page'],$all['screen_step_page_content']);
                if($ret)
                {
                    $msg=$dbSTM->setMsg('admincontrol',1,'修改成功','屏幕页面已更新');
                    // return redirect('/contest'.$cid.'/admin/control');
                    return $dbSTM->retMsg(1,'修改成功','屏幕页面已更新');
                }
                else
                {
                    $msg=$dbSTM->setMsg('admincontrol',2,'修改错误','请输入正确的屏幕页面及内容');
                    // return redirect('/contest'.$cid.'/admin/control');
                    return $dbSTM->retMsg(2,'修改错误','请输入正确的屏幕页面及内容');
                }
            }
            if($action=='calc')
            {
                $dbCP=new ContestPlayers();
                $ret = $dbCP->calcRank($cid);
                $msg=$dbSTM->setMsg('admincontrol',1,'计算成功','排名已更新');
                // return redirect('/contest'.$cid.'/admin/control');
                return $dbSTM->retMsg(1,'计算成功','排名已更新');
            }
            if($action=='getmsg')
            {
                return $dbSTM->popMsg('admincontrol');
            }
        }
    }
}

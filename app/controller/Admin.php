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

class Admin
{
    public function index()
    {
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isGA = $CTS->isGlobalAdmin();
        if($isA&&(!$isGA))
            return redirect('/admin/error');
        if($isGA)
            return redirect('/admin/settings');
        return redirect('/admin/login');
    }
    
    public function error()
    {
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isGA = $CTS->isGlobalAdmin();
        if($isGA)
            return redirect('/admin/settings');
        if(!$isA)
            return redirect('/admin/login');
        View::assign('page_title','管理错误');
        
        if($isA)
        {
            $aid = $CTS->getAid();
            View::assign([
                'usertype'=>'admin',
                'aid'=>$aid
            ]);
        }
        
        $dbGS=new GlobalSettings();
        View::assign([
            'site_name'=>$dbGS->getSetting('name'),
            'site_favicon'=>$dbGS->getSetting('favicon'),
            'site_logo'=>$dbGS->getSetting('logo'),
            'site_color'=>$dbGS->getSetting('color'),
            'site_accent_color'=>$dbGS->getSetting('accent_color')
        ]);
        return View::fetch();
    }
    public function errorapi($action)
    {
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isGA = $CTS->isGlobalAdmin();
        if($isGA)
            return redirect('/admin/settings');
        if(!$isA)
            return redirect('/admin/login');
        $all = Request::param();
        $dbSTM = new SessionToMsg();
        if(Request::method() == 'GET')
        {
            if($action=='logout')
            {
                if($isA)
                    $ret = $CTS->delAid();
                return redirect('/admin/login');
            }
        }
    }
    
    public function login()
    {
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isGA = $CTS->isGlobalAdmin();
        if($isA&&(!$isGA))
            return redirect('/admin/error');
        if($isGA)
            return redirect('/admin/settings');
        View::assign('page_title','管理登录');
        
        $dbGS=new GlobalSettings();
        View::assign([
            'site_name'=>$dbGS->getSetting('name'),
            'site_favicon'=>$dbGS->getSetting('favicon'),
            'site_logo'=>$dbGS->getSetting('logo'),
            'site_color'=>$dbGS->getSetting('color'),
            'site_accent_color'=>$dbGS->getSetting('accent_color')
        ]);
        return View::fetch();
    }
    public function loginapi($action)
    {
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isGA = $CTS->isGlobalAdmin();
        if($isA&&(!$isGA))
            return redirect('/admin/error');
        if($isGA)
            return redirect('/admin/settings');
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
                    //$msg=$dbSTM->setMsg('globallogin',0,'登录成功','登录成功');
                    // return redirect('/admin');
                    return $dbSTM->retMsg(0,'登录成功','登录成功');
                }
                else
                {
                    $msg=$dbSTM->setMsg('globallogin',1,'登录错误','请输入正确的登录名称和登录密码');
                    // return redirect('/admin/login');
                    return $dbSTM->retMsg(1,'登录错误','请输入正确的登录名称和登录密码');
                }
            }
            if($action=='getmsg')
            {
                return $dbSTM->popMsg('globallogin');
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
                    //$msg=$dbSTM->setMsg('globallogin',0,'登录成功','登录成功');
                    return redirect('/admin');
                }
                else
                {
                    $msg=$dbSTM->setMsg('globallogin',1,'登录错误','请输入正确的登录名称和登录密码');
                    return redirect('/admin/login');
                }
            }
        }
    }
    
    public function logout()
    {
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isGA = $CTS->isGlobalAdmin();
        if($isA&&(!$isGA))
            return redirect('/admin/error');
        if(!$isA)
            return redirect('/admin/login');
        View::assign('page_title','管理退出登录');
        
        if($isGA)
        {
            $aid = $CTS->getAid();
            View::assign([
                'usertype'=>'admin',
                'aid'=>$aid
            ]);
        }
        
        $dbGS=new GlobalSettings();
        View::assign([
            'logined'=>true,
            'site_name'=>$dbGS->getSetting('name'),
            'site_favicon'=>$dbGS->getSetting('favicon'),
            'site_logo'=>$dbGS->getSetting('logo'),
            'site_color'=>$dbGS->getSetting('color'),
            'site_accent_color'=>$dbGS->getSetting('accent_color')
        ]);
        return View::fetch();
    }
    public function logoutapi($action)
    {
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isGA = $CTS->isGlobalAdmin();
        if($isA&&(!$isGA))
            return redirect('/admin/error');
        if(!$isA)
            return redirect('/admin/login');
        $all = Request::param();
        $dbSTM = new SessionToMsg();
        if(Request::method() == 'GET')
        {
            if($action=='logout')
            {
                $ret = $CTS->delAid();
                return redirect('/admin/login');
            }
        }
    }
    
    public function profile()
    {
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isGA = $CTS->isGlobalAdmin();
        if($isA&&(!$isGA))
            return redirect('/admin/error');
        if(!$isA)
            return redirect('/admin/login');
        View::assign('page_title','管理员档案');
        
        $aid = $CTS->getAid();
        $dbA = new Admins();
        $admin = $dbA->getProfile($aid);
        View::assign([
            'aname'=>$admin['aname'],
            'loginname'=>$admin['loginname'],
            'permission'=>$admin['permission']
        ]);
        
        $dbGS=new GlobalSettings();
        View::assign([
            'logined'=>true,
            'profile'=>true,
            'site_name'=>$dbGS->getSetting('name'),
            'site_favicon'=>$dbGS->getSetting('favicon'),
            'site_logo'=>$dbGS->getSetting('logo'),
            'site_color'=>$dbGS->getSetting('color'),
            'site_accent_color'=>$dbGS->getSetting('accent_color')
        ]);
        return View::fetch();
    }
    public function profileapi($action)
    {
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isGA = $CTS->isGlobalAdmin();
        if($isA&&(!$isGA))
            return redirect('/admin/error');
        if(!$isA)
            return redirect('/admin/login');
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
                    $msg=$dbSTM->setMsg('globalprofile',1,'修改成功','管理员名称已更新');
                    // return redirect('/admin/profile');
                    return $dbSTM->retMsg(1,'修改成功','管理员名称已更新');
                }
                else
                {
                    $msg=$dbSTM->setMsg('globalprofile',2,'修改错误','请输入正确的新管理员名称');
                    // return redirect('/admin/profile');
                    return $dbSTM->retMsg(2,'修改错误','请输入正确的新管理员名称');
                }
            }
            if($action=='loginname')
            {
                $dbA = new Admins();
                $ret = $dbA->tryLoginname($aid,$all['loginname']);
                if(!$ret)
                {
                    $msg=$dbSTM->setMsg('globalprofile',2,'修改错误','新的登录名称与其他管理员冲突');
                    // return redirect('/admin/profile');
                    return $dbSTM->retMsg(2,'修改错误','新的登录名称与其他管理员冲突');
                }
                $ret = $dbA->modLoginname($aid,$all['loginname']);
                if($ret)
                {
                    $msg=$dbSTM->setMsg('globalprofile',1,'修改成功','登录名称已更新');
                    // return redirect('/admin/profile');
                    return $dbSTM->retMsg(1,'修改成功','登录名称已更新');
                }
                else
                {
                    $msg=$dbSTM->setMsg('globalprofile',2,'修改错误','请输入由数字、大小写字母和下划线“_”组成的新的登录名称');
                    // return redirect('/admin/profile');
                    return $dbSTM->retMsg(2,'修改错误','请输入由数字、大小写字母和下划线“_”组成的新的登录名称');
                }
            }
            if($action=='password')
            {
                $dbA = new Admins();
                $ret = $dbA->tryPassword($aid,$all['passwordpre']);
                if(!$ret)
                {
                    $msg=$dbSTM->setMsg('globalprofile',2,'修改错误','请输入正确的旧登录密码');
                    // return redirect('/admin/profile');
                    return $dbSTM->retMsg(2,'修改错误','请输入正确的旧登录密码');
                }
                $ret = $dbA->modPassword($aid,$all['password']);
                if($ret)
                {
                    $msg=$dbSTM->setMsg('globalprofile',1,'修改成功','登录密码已更新');
                    // return redirect('/admin/profile');
                    return $dbSTM->retMsg(1,'修改成功','登录密码已更新');
                }
                else
                {
                    $msg=$dbSTM->setMsg('globalprofile',2,'修改错误','请输入正确的新登录密码');
                    // return redirect('/admin/profile');
                    return $dbSTM->retMsg(2,'修改错误','请输入正确的新登录密码');
                }
            }
            if($action=='getmsg')
            {
                return $dbSTM->popMsg('globalprofile');
            }
        }
    }
    
    public function admins()
    {
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isGA = $CTS->isGlobalAdmin();
        if($isA&&(!$isGA))
            return redirect('/admin/error');
        if(!$isA)
            return redirect('/admin/login');
        View::assign('page_title','管理员列表');
        
        $admins_order_key=$CTS->getAdminsOrderKey();
        $admins_order_by=$CTS->getAdminsOrderBy();
        $dbA=new Admins();
        $admins=$dbA->getAdminsOrdered($admins_order_key,$admins_order_by);
        View::assign([
            'admins'=>$admins,
            'admins_order_key'=>$admins_order_key,
            'admins_order_by'=>$admins_order_by
        ]);
        
        $dbGS=new GlobalSettings();
        View::assign([
            'logined'=>true,
            'tabbar'=>'admins',
            'site_name'=>$dbGS->getSetting('name'),
            'site_favicon'=>$dbGS->getSetting('favicon'),
            'site_logo'=>$dbGS->getSetting('logo'),
            'site_color'=>$dbGS->getSetting('color'),
            'site_accent_color'=>$dbGS->getSetting('accent_color')
        ]);
        return View::fetch();
    }
    public function adminsapi($action)
    {
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isGA = $CTS->isGlobalAdmin();
        if($isA&&(!$isGA))
            return redirect('/admin/error');
        if(!$isA)
            return redirect('/admin/login');
        $all = Request::param();
        $dbSTM = new SessionToMsg();
        if(Request::method() == 'POST')
        {
            header('Content-Type:application/json');
            if($action=='del')
            {
                $dbA=new Admins();
                $dellist=json_decode($all['dellist']);
                foreach($dellist as $v)
                {
                    $ret=$dbA->delAdmin($v);
                }
                $msg=$dbSTM->setMsg('globaladmins',1,'删除成功','列表已更新');
                // return redirect('/admin/admins');
                return $dbSTM->retMsg(1,'删除成功','列表已更新');
            }
            if($action=='order')
            {
                $ret=$CTS->setAdminsOrder($all['admins_order_key'],$all['admins_order_by']);
                // return redirect('/admin/admins');
                return $dbSTM->retMsg(1,'修改成功','排序方式已更新');
            }
            if($action=='getmsg')
            {
                return $dbSTM->popMsg('globaladmins');
            }
        }
    }
    public function adminadd()
    {
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isGA = $CTS->isGlobalAdmin();
        if($isA&&(!$isGA))
            return redirect('/admin/error');
        if(!$isA)
            return redirect('/admin/login');
        View::assign('page_title','添加管理员');
        
        $dbGS=new GlobalSettings();
        View::assign([
            'logined'=>true,
            'tabbar'=>'admins',
            'site_name'=>$dbGS->getSetting('name'),
            'site_favicon'=>$dbGS->getSetting('favicon'),
            'site_logo'=>$dbGS->getSetting('logo'),
            'site_color'=>$dbGS->getSetting('color'),
            'site_accent_color'=>$dbGS->getSetting('accent_color')
        ]);
        return View::fetch();
    }
    public function adminaddapi($action)
    {
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isGA = $CTS->isGlobalAdmin();
        if($isA&&(!$isGA))
            return redirect('/admin/error');
        if(!$isA)
            return redirect('/admin/login');
        $all = Request::param();
        $dbSTM = new SessionToMsg();
        if(Request::method() == 'POST')
        {
            header('Content-Type:application/json');
            if($action=='add')
            {
                $aid=$all['aid'];
                $loginname=$all['loginname'];
                $password=$all['password'];
                $dbA=new Admins();
                $admins=$dbA->getAdminsOrdered('aid','desc');
                if(is_null($aid)||strlen($aid)==0)
                {
                    if(count($admins)==0)
                        $aid=1;
                    else
                        $aid=$admins[0]['aid']+1;
                }
                else
                {
                    foreach($admins as $v)
                    {
                        if($v['aid']==$aid)
                        {
                            $msg=$dbSTM->setMsg('globaladminadd',2,'添加失败','管理员序号已存在');
                            // return redirect('/admin/adminadd');
                            return $dbSTM->retMsg(2,'添加失败','管理员序号已存在');
                        }
                    }
                }
                $admins=$dbA->getAdminsOrdered('loginname','desc');
                if(is_null($loginname)||strlen($loginname)==0)
                {
                    $flag=true;
                    while($flag)
                    {
                        $loginname=$dbA->genLoginname();
                        $flag=false;
                        foreach($admins as $v)
                        {
                            if($v['loginname']==$loginname)
                            {
                                $flag=true;
                                break;
                            }
                        }
                    }
                }
                else
                {
                    foreach($admins as $v)
                    {
                        if($v['loginname']==$loginname)
                        {
                            $msg=$dbSTM->setMsg('globaladminadd',2,'添加失败','登录码已存在');
                            // return redirect('/admin/adminadd');
                            return $dbSTM->retMsg(2,'添加失败','登录码已存在');
                        }
                    }
                }
                if(is_null($password)||strlen($password)==0)
                {
                    $password='12345678';
                }
                $ret=$dbA->addAdmin($aid,$all['aname'],$loginname,$all['enable'],$all['permission']);
                $ret=$dbA->modPassword($aid,$password);
                $msg=$dbSTM->setMsg('globaladmins',1,'添加成功','管理员列表已更新');
                // return redirect('/admin/admins');
                return $dbSTM->retMsg(1,'添加成功','管理员列表已更新');
            }
            if($action=='getmsg')
            {
                return $dbSTM->popMsg('globaladminadd');
            }
        }
    }
    public function admineditre()
    {
        return redirect('/admin/admins');
    }
    public function adminedit($aid)
    {
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isGA = $CTS->isGlobalAdmin();
        if($isA&&(!$isGA))
            return redirect('/admin/error');
        if(!$isA)
            return redirect('/admin/login');
        View::assign('page_title','修改管理员');
        
        $dbA=new Admins();
        $admin=$dbA->getAdmin($aid);
        View::assign([
            'aid'=>$aid,
            'loginname'=>$admin['loginname'],
            'aname'=>$admin['aname'],
            'enable'=>$admin['enable'],
            'permission'=>$admin['permission']
        ]);
        
        $dbGS=new GlobalSettings();
        View::assign([
            'logined'=>true,
            'tabbar'=>'admins',
            'site_name'=>$dbGS->getSetting('name'),
            'site_favicon'=>$dbGS->getSetting('favicon'),
            'site_logo'=>$dbGS->getSetting('logo'),
            'site_color'=>$dbGS->getSetting('color'),
            'site_accent_color'=>$dbGS->getSetting('accent_color')
        ]);
        return View::fetch();
    }
    public function admineditapi($action,$aid)
    {
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isGA = $CTS->isGlobalAdmin();
        if($isA&&(!$isGA))
            return redirect('/admin/error');
        if(!$isA)
            return redirect('/admin/login');
        $all = Request::param();
        $dbSTM = new SessionToMsg();
        if(Request::method() == 'POST')
        {
            header('Content-Type:application/json');
            if($action=='edit')
            {
                $aid=(int)$aid;
                $loginname=$all['loginname'];
                $password=$all['password'];
                $dbA=new Admins();
                $admins=$dbA->getAdminsOrdered('loginname','desc');
                foreach($admins as $v)
                {
                    if($v['loginname']==$loginname&&$v['aid']!=$aid)
                    {
                        $msg=$dbSTM->setMsg('globaladminedit'.$aid,2,'修改失败','登录码已存在');
                        // return redirect('/admin/adminedit'.$aid);
                        return $dbSTM->retMsg(2,'修改失败','登录码已存在');
                    }
                }
                $ret=$dbA->updateAdmin($aid,$all['aname'],$loginname,$all['enable'],$all['permission']);
                if(!is_null($password)&&strlen($password)!=0)
                {
                    $ret=$dbA->modPassword($aid,$password);
                }
                $msg=$dbSTM->setMsg('globaladmins',1,'修改成功','管理员列表已更新');
                // return redirect('/admin/admins');
                return $dbSTM->retMsg(1,'修改成功','管理员列表已更新');
            }
            if($action=='getmsg')
            {
                return $dbSTM->popMsg('globaladminedit'.$aid);
            }
        }
    }
    
    public function contests()
    {
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isGA = $CTS->isGlobalAdmin();
        if($isA&&(!$isGA))
            return redirect('/admin/error');
        if(!$isA)
            return redirect('/admin/login');
        View::assign('page_title','比赛列表');
        
        $contests_order_key=$CTS->getContestsOrderKey();
        $contests_order_by=$CTS->getContestsOrderBy();
        $dbC=new Contests();
        $contests=$dbC->getContestsOrdered($contests_order_key,$contests_order_by);
        View::assign([
            'contests'=>$contests,
            'contests_order_key'=>$contests_order_key,
            'contests_order_by'=>$contests_order_by
        ]);
        
        $dbGS=new GlobalSettings();
        View::assign([
            'logined'=>true,
            'tabbar'=>'contests',
            'site_name'=>$dbGS->getSetting('name'),
            'site_favicon'=>$dbGS->getSetting('favicon'),
            'site_logo'=>$dbGS->getSetting('logo'),
            'site_color'=>$dbGS->getSetting('color'),
            'site_accent_color'=>$dbGS->getSetting('accent_color')
        ]);
        return View::fetch();
    }
    public function contestsapi($action)
    {
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isGA = $CTS->isGlobalAdmin();
        if($isA&&(!$isGA))
            return redirect('/admin/error');
        if(!$isA)
            return redirect('/admin/login');
        $all = Request::param();
        $dbSTM = new SessionToMsg();
        if(Request::method() == 'POST')
        {
            header('Content-Type:application/json');
            if($action=='del')
            {
                $dbC=new Contests();
                $dbA=new Admins();
                $dbCJ=new ContestJudgers();
                $dbCP=new ContestPlayers();
                $dbCPs=new ContestPoints();
                $dellist=json_decode($all['dellist']);
                foreach($dellist as $v)
                {
                    $ret=$dbCPs->delContestPoints($v);
                    $ret=$dbCP->delContestPlayers($v);
                    $ret=$dbCJ->delContestJudgers($v);
                    $ret=$dbA->delContestAdmin($v);
                    $ret=$dbC->delContest($v);
                }
                $msg=$dbSTM->setMsg('globalcontests',1,'删除成功','列表已更新');
                // return redirect('/admin/contests');
                return $dbSTM->retMsg(1,'删除成功','列表已更新');
            }
            if($action=='order')
            {
                $ret=$CTS->setContestsOrder($all['contests_order_key'],$all['contests_order_by']);
                // return redirect('/admin/contests');
                return $dbSTM->retMsg(1,'修改成功','排序方式已更新');
            }
            if($action=='getmsg')
            {
                return $dbSTM->popMsg('globalcontests');
            }
        }
    }
    public function contestadd()
    {
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isGA = $CTS->isGlobalAdmin();
        if($isA&&(!$isGA))
            return redirect('/admin/error');
        if(!$isA)
            return redirect('/admin/login');
        View::assign('page_title','添加比赛');
        
        $dbGS=new GlobalSettings();
        View::assign([
            'default_screen_page_pic'=>$dbGS->getSetting('default_screen_page_pic'),
            'default_screen_background_page_pic'=>$dbGS->getSetting('default_screen_background_page_pic')
        ]);
        $color_list=['red','pink','purple','deep-purple','indigo','blue','light-blue','cyan','teal','green','light-green','lime','yellow','amber','orange','deep-orange','brown','grey','blue-grey'];
        $accent_color_list=['red','pink','purple','deep-purple','indigo','blue','light-blue','cyan','teal','green','light-green','lime','yellow','amber','orange','deep-orange'];
        View::assign([
            'color_list'=>$color_list,
            'accent_color_list'=>$accent_color_list
        ]);
        
        $dbGS=new GlobalSettings();
        View::assign([
            'logined'=>true,
            'tabbar'=>'contests',
            'site_name'=>$dbGS->getSetting('name'),
            'site_favicon'=>$dbGS->getSetting('favicon'),
            'site_logo'=>$dbGS->getSetting('logo'),
            'site_color'=>$dbGS->getSetting('color'),
            'site_accent_color'=>$dbGS->getSetting('accent_color')
        ]);
        return View::fetch();
    }
    public function contestaddapi($action)
    {
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isGA = $CTS->isGlobalAdmin();
        if($isA&&(!$isGA))
            return redirect('/admin/error');
        if(!$isA)
            return redirect('/admin/login');
        $all = Request::param();
        $dbSTM = new SessionToMsg();
        if(Request::method() == 'POST')
        {
            header('Content-Type:application/json');
            if($action=='add')
            {
                $cid=$all['cid'];
                $dbC=new Contests();
                $contests=$dbC->getContestsOrdered('cid','desc');
                if(is_null($cid)||strlen($cid)==0)
                {
                    if(count($contests)==0)
                        $cid=1;
                    else
                        $cid=$contests[0]['cid']+1;
                }
                else
                {
                    foreach($contests as $v)
                    {
                        if($v['cid']==$cid)
                        {
                            $msg=$dbSTM->setMsg('globalcontestadd',2,'添加失败','比赛序号已存在');
                            // return redirect('/admin/contestadd');
                            return $dbSTM->retMsg(2,'添加失败','比赛序号已存在');
                        }
                    }
                }
                $ret=$dbC->addContest($cid,$all['cname'],$all['ccolor'],$all['caccent_color'],$all['enable'],$all['min_max_mode']);
                $msg=$dbSTM->setMsg('globalcontests',1,'修改成功','比赛列表已更新');
                // return redirect('/admin/contests');
                return $dbSTM->retMsg(1,'修改成功','比赛列表已更新');
            }
            if($action=='getmsg')
            {
                return $dbSTM->popMsg('globalcontestadd');
            }
        }
    }
    public function contesteditre()
    {
        return redirect('/admin/contests');
    }
    public function contestedit($cid)
    {
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isGA = $CTS->isGlobalAdmin();
        if($isA&&(!$isGA))
            return redirect('/admin/error');
        if(!$isA)
            return redirect('/admin/login');
        View::assign('page_title','修改比赛');
        
        $dbC=new Contests();
        $dbGS=new GlobalSettings();
        $contest=$dbC->getContest($cid);
        View::assign([
            'cid'=>$cid,
            'cname'=>$contest['cname'],
            'cfavicon'=>$contest['cfavicon'],
            'clogo'=>$contest['clogo'],
            'ccolor'=>$contest['ccolor'],
            'caccent_color'=>$contest['caccent_color'],
            'enable'=>$contest['enable'],
            'min_max_mode'=>$contest['min_max_mode'],
            'screen_page_pic'=>$contest['screen_page_pic'],
            'screen_background_page_pic'=>$contest['screen_background_page_pic']
        ]);
        View::assign([
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
            'logined'=>true,
            'tabbar'=>'contests',
            'site_name'=>$dbGS->getSetting('name'),
            'site_favicon'=>$dbGS->getSetting('favicon'),
            'site_logo'=>$dbGS->getSetting('logo'),
            'site_color'=>$dbGS->getSetting('color'),
            'site_accent_color'=>$dbGS->getSetting('accent_color')
        ]);
        return View::fetch();
    }
    public function contesteditapi($action,$cid)
    {
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isGA = $CTS->isGlobalAdmin();
        if($isA&&(!$isGA))
            return redirect('/admin/error');
        if(!$isA)
            return redirect('/admin/login');
        $all = Request::param();
        $dbSTM = new SessionToMsg();
        if(Request::method() == 'POST')
        {
            header('Content-Type:application/json');
            if($action=='edit')
            {
                $cid=(int)$cid;
                $dbC=new Contests();
                $ret=$dbC->updateContest($cid,$all['cname'],$all['ccolor'],$all['caccent_color'],$all['enable'],$all['min_max_mode']);
                $msg=$dbSTM->setMsg('globalcontests',1,'修改成功','比赛列表已更新');
                // return redirect('/admin/contests');
                return $dbSTM->retMsg(1,'修改成功','比赛列表已更新');
            }
            if($action=='uploadfavicon')
            {
                $cid=(int)$cid;
                $file = request()->file('imagefavicon');
                if(empty($file)){
                    $msg=$dbSTM->setMsg('globalcontestedit'.$cid,2,'上传失败','未选择任何文件');
                    return redirect('/admin/contestedit'.$cid);
                    // return $dbSTM->retMsg(2,'上传失败','未选择任何文件');
                }
                $extension=$file->extension();
                if(!in_array($extension,array('ico','jpeg','jpg','png'))){
                    $msg=$dbSTM->setMsg('globalcontestedit'.$cid,2,'上传失败','比赛网标的文件扩展名仅能为ico、jpeg、jpg和png中的一个');
                    return redirect('/admin/contestedit'.$cid);
                    // return $dbSTM->retMsg(2,'上传失败','比赛网标的文件扩展名仅能为ico、jpeg、jpg和png中的一个');
                }
                if(filesize($file)/1024/1024>1){
                    $msg=$dbSTM->setMsg('globalcontestedit',2,'上传失败','比赛网标的文件大小在1MB以内');
                    return redirect('/admin/contestedit'.$cid);
                    // return $dbSTM->retMsg(2,'上传失败','比赛网标的文件大小在1MB以内');
                }
                $saveName = Filesystem::disk('picture')->putFile('favicon',$file,'md5');
                if (!empty($saveName)) {
                    $saveName = "/uploads/".str_replace('\\', "/", $saveName);
                    $dbC=new Contests();
                    $ret=$dbC->setContestSetting($cid,'cfavicon',$saveName);
                    $msg=$dbSTM->setMsg('globalcontestedit'.$cid,2,'上传成功','可能需要清除缓存后才能更新');
                    return redirect('/admin/contestedit'.$cid);
                    // return $dbSTM->retMsg(2,'上传成功','可能需要清除缓存后才能更新');
                }
            }
            if($action=='usefavicon')
            {
                $cid=(int)$cid;
                $dbC=new Contests();
                $ret=$dbC->setContestSetting($cid,'cfavicon','');
                $msg=$dbSTM->setMsg('globalcontestedit'.$cid,2,'修改成功','可能需要清除缓存后才能更新');
                // return redirect('/admin/contestedit'.$cid);
                return $dbSTM->retMsg(2,'修改成功','可能需要清除缓存后才能更新');
            }
            if($action=='uploadlogo')
            {
                $cid=(int)$cid;
                $file = request()->file('imagelogo');
                if(empty($file)){
                    $msg=$dbSTM->setMsg('globalcontestedit'.$cid,2,'上传失败','未选择任何文件');
                    return redirect('/admin/contestedit'.$cid);
                    // return $dbSTM->retMsg(2,'上传失败','未选择任何文件');
                }
                $extension=$file->extension();
                if(!in_array($extension,array('jpeg','jpg','png'))){
                    $msg=$dbSTM->setMsg('globalcontestedit'.$cid,2,'上传失败','比赛图标的文件扩展名仅能为jpeg、jpg和png中的一个');
                    return redirect('/admin/contestedit'.$cid);
                    // return $dbSTM->retMsg(2,'上传失败','比赛图标的文件扩展名仅能为jpeg、jpg和png中的一个');
                }
                if(filesize($file)/1024/1024>1){
                    $msg=$dbSTM->setMsg('globalcontestedit',2,'上传失败','比赛图标的文件大小在1MB以内');
                    return redirect('/admin/contestedit'.$cid);
                    // return $dbSTM->retMsg(2,'上传失败','比赛图标的文件大小在1MB以内');
                }
                $saveName = Filesystem::disk('picture')->putFile('logo',$file,'md5');
                if (!empty($saveName)) {
                    $saveName = "/uploads/".str_replace('\\', "/", $saveName);
                    $dbC=new Contests();
                    $ret=$dbC->setContestSetting($cid,'clogo',$saveName);
                    $msg=$dbSTM->setMsg('globalcontestedit'.$cid,2,'上传成功','可能需要清除缓存后才能更新');
                    return redirect('/admin/contestedit'.$cid);
                    // return $dbSTM->retMsg(2,'上传成功','可能需要清除缓存后才能更新');
                }
            }
            if($action=='uselogo')
            {
                $cid=(int)$cid;
                $dbC=new Contests();
                $ret=$dbC->setContestSetting($cid,'clogo','');
                $msg=$dbSTM->setMsg('globalcontestedit'.$cid,2,'修改成功','可能需要清除缓存后才能更新');
                // return redirect('/admin/contestedit'.$cid);
                return $dbSTM->retMsg(2,'修改成功','可能需要清除缓存后才能更新');
            }
            if($action=='uploadspic')
            {
                $cid=(int)$cid;
                $file = request()->file('imagespic');
                if(empty($file)){
                    $msg=$dbSTM->setMsg('globalcontestedit'.$cid,2,'上传失败','未选择任何文件');
                    return redirect('/admin/contestedit'.$cid);
                    // return $dbSTM->retMsg(2,'上传失败','未选择任何文件');
                }
                $extension=$file->extension();
                if(!in_array($extension,array('jpeg','jpg','png'))){
                    $msg=$dbSTM->setMsg('globalcontestedit'.$cid,2,'上传失败','屏幕背景图片的文件扩展名仅能为jpeg、jpg和png中的一个');
                    return redirect('/admin/contestedit'.$cid);
                    // return $dbSTM->retMsg(2,'上传失败','屏幕背景图片的文件扩展名仅能为jpeg、jpg和png中的一个');
                }
                if(filesize($file)/1024/1024>10){
                    $msg=$dbSTM->setMsg('globalcontestedit'.$cid,2,'上传失败','屏幕背景图片的文件大小在10MB以内');
                    return redirect('/admin/contestedit'.$cid);
                    // return $dbSTM->retMsg(2,'上传失败','屏幕背景图片的文件大小在10MB以内');
                }
                $saveName = Filesystem::disk('picture')->putFile('spic',$file,'md5');
                if (!empty($saveName)) {
                    $saveName = "/uploads/".str_replace('\\', "/", $saveName);
                    $dbC=new Contests();
                    $ret=$dbC->setContestSetting($cid,'screen_page_pic',$saveName);
                    $msg=$dbSTM->setMsg('globalcontestedit'.$cid,2,'上传成功','可能需要清除缓存后才能更新');
                    return redirect('/admin/contestedit'.$cid);
                    // return $dbSTM->retMsg(2,'上传成功','可能需要清除缓存后才能更新');
                }
            }
            if($action=='usespic')
            {
                $cid=(int)$cid;
                $dbC=new Contests();
                $ret=$dbC->setContestSetting($cid,'screen_page_pic','');
                $msg=$dbSTM->setMsg('globalcontestedit'.$cid,2,'修改成功','可能需要清除缓存后才能更新');
                // return redirect('/admin/contestedit'.$cid);
                return $dbSTM->retMsg(2,'修改成功','可能需要清除缓存后才能更新');
            }
            if($action=='uploadsbgpic')
            {
                $cid=(int)$cid;
                $file = request()->file('imagesbgpic');
                if(empty($file)){
                    $msg=$dbSTM->setMsg('globalcontestedit'.$cid,2,'上传失败','未选择任何文件');
                    return redirect('/admin/contestedit'.$cid);
                    // return $dbSTM->retMsg(2,'上传失败','未选择任何文件');
                }
                $extension=$file->extension();
                if(!in_array($extension,array('jpeg','jpg','png'))){
                    $msg=$dbSTM->setMsg('globalcontestedit'.$cid,2,'上传失败','屏幕背景页面背景图片的文件扩展名仅能为jpeg、jpg和png中的一个');
                    return redirect('/admin/contestedit'.$cid);
                    // return $dbSTM->retMsg(2,'上传失败','屏幕背景页面背景图片的文件扩展名仅能为jpeg、jpg和png中的一个');
                }
                if(filesize($file)/1024/1024>10){
                    $msg=$dbSTM->setMsg('globalcontestedit'.$cid,2,'上传失败','屏幕背景页面背景图片的文件大小在10MB以内');
                    return redirect('/admin/contestedit'.$cid);
                    // return $dbSTM->retMsg(2,'上传失败','屏幕背景页面背景图片的文件大小在10MB以内');
                }
                $saveName = Filesystem::disk('picture')->putFile('sbgpic',$file,'md5');
                if (!empty($saveName)) {
                    $saveName = "/uploads/".str_replace('\\', "/", $saveName);
                    $dbC=new Contests();
                    $ret=$dbC->setContestSetting($cid,'screen_background_page_pic',$saveName);
                    $msg=$dbSTM->setMsg('globalcontestedit'.$cid,2,'上传成功','可能需要清除缓存后才能更新');
                    return redirect('/admin/contestedit'.$cid);
                    // return $dbSTM->retMsg(2,'上传成功','可能需要清除缓存后才能更新');
                }
            }
            if($action=='usesbgpic')
            {
                $cid=(int)$cid;
                $dbC=new Contests();
                $ret=$dbC->setContestSetting($cid,'screen_background_page_pic','');
                $msg=$dbSTM->setMsg('globalcontestedit'.$cid,2,'修改成功','可能需要清除缓存后才能更新');
                // return redirect('/admin/contestedit'.$cid);
                return $dbSTM->retMsg(2,'修改成功','可能需要清除缓存后才能更新');
            }
            if($action=='getmsg')
            {
                return $dbSTM->popMsg('globalcontestedit'.$cid);
            }
        }
    }
    
    public function settings()
    {
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isGA = $CTS->isGlobalAdmin();
        if($isA&&(!$isGA))
            return redirect('/admin/error');
        if(!$isA)
            return redirect('/admin/login');
        View::assign('page_title','站点设置');
        
        $dbGS=new GlobalSettings();
        View::assign([
            'sname'=>$dbGS->getGlobalSetting('site_name'),
            'surl'=>$dbGS->getGlobalSetting('site_url'),
            'sfavicon'=>$dbGS->getGlobalSetting('site_favicon'),
            'slogo'=>$dbGS->getGlobalSetting('site_logo'),
            'scolor'=>$dbGS->getGlobalSetting('site_color'),
            'saccent_color'=>$dbGS->getGlobalSetting('site_accent_color'),
            'sdefault_screen_page_pic'=>$dbGS->getGlobalSetting('site_default_screen_page_pic'),
            'sdefault_screen_background_page_pic'=>$dbGS->getGlobalSetting('site_default_screen_background_page_pic')
        ]);
        View::assign([
            'env_name'=>$dbGS->getEnvSetting('site.name'),
            'env_url'=>$dbGS->getEnvSetting('site.url'),
            'env_favicon'=>$dbGS->getEnvSetting('site.favicon'),
            'env_logo'=>$dbGS->getEnvSetting('site.logo'),
            'env_color'=>$dbGS->getEnvSetting('site.color'),
            'env_accent_color'=>$dbGS->getEnvSetting('site.accent_color'),
            'env_default_screen_page_pic'=>$dbGS->getEnvSetting('site.default_screen_page_pic'),
            'env_default_screen_background_page_pic'=>$dbGS->getEnvSetting('site.default_screen_background_page_pic')
        ]);
        $color_list=['red','pink','purple','deep-purple','indigo','blue','light-blue','cyan','teal','green','light-green','lime','yellow','amber','orange','deep-orange','brown','grey','blue-grey'];
        $accent_color_list=['red','pink','purple','deep-purple','indigo','blue','light-blue','cyan','teal','green','light-green','lime','yellow','amber','orange','deep-orange'];
        View::assign([
            'color_list'=>$color_list,
            'accent_color_list'=>$accent_color_list
        ]);
        
        View::assign([
            'logined'=>true,
            'tabbar'=>'settings',
            'site_name'=>$dbGS->getSetting('name'),
            'site_favicon'=>$dbGS->getSetting('favicon'),
            'site_logo'=>$dbGS->getSetting('logo'),
            'site_color'=>$dbGS->getSetting('color'),
            'site_accent_color'=>$dbGS->getSetting('accent_color')
        ]);
        return View::fetch();
    }
    public function settingsapi($action)
    {
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isGA = $CTS->isGlobalAdmin();
        if($isA&&(!$isGA))
            return redirect('/admin/error');
        if(!$isA)
            return redirect('/admin/login');
        $all = Request::param();
        $dbSTM = new SessionToMsg();
        if(Request::method() == 'POST')
        {
            header('Content-Type:application/json');
            if($action=='edit')
            {
                $sname=$all['sname'];
                $surl=$all['surl'];
                $scolor=$all['scolor'];
                $saccent_color=$all['saccent_color'];
                
                $dbGS=new GlobalSettings();
                $ret=$dbGS->setGlobalSetting('site_name',$sname);
                $ret=$dbGS->setGlobalSetting('site_url',$surl);
                $ret=$dbGS->setGlobalSetting('site_color',$scolor);
                $ret=$dbGS->setGlobalSetting('site_accent_color',$saccent_color);
                $msg=$dbSTM->setMsg('globalsettings',1,'修改成功','站点设置已更新');
                // return redirect('/admin/settings');
                return $dbSTM->retMsg(1,'修改成功','站点设置已更新');
            }
            if($action=='uploadfavicon')
            {
                $file = request()->file('imagefavicon');
                if(empty($file)){
                    $msg=$dbSTM->setMsg('globalsettings',2,'上传失败','未选择任何文件');
                    return redirect('/admin/settings');
                    // return $dbSTM->retMsg(2,'上传失败','未选择任何文件');
                }
                $extension=$file->extension();
                if(!in_array($extension,array('ico','jpeg','jpg','png'))){
                    $msg=$dbSTM->setMsg('globalsettings',2,'上传失败','站点网标的文件扩展名仅能为ico、jpeg、jpg和png中的一个');
                    return redirect('/admin/settings');
                    // return $dbSTM->retMsg(2,'上传失败','站点网标的文件扩展名仅能为ico、jpeg、jpg和png中的一个');
                }
                if(filesize($file)/1024/1024>1){
                    $msg=$dbSTM->setMsg('globalsettings',2,'上传失败','站点网标的文件大小在1MB以内');
                    return redirect('/admin/settings');
                    // return $dbSTM->retMsg(2,'上传失败','站点网标的文件大小在1MB以内');
                }
                $saveName = Filesystem::disk('public')->putFile('favicon',$file,'md5');
                if (!empty($saveName)) {
                    $saveName = "/storage/".str_replace('\\', "/", $saveName);
                    $dbGS=new GlobalSettings();
                    $ret=$dbGS->setGlobalSetting('site_favicon',$saveName);
                    $msg=$dbSTM->setMsg('globalsettings',1,'上传成功','可能需要清除缓存后才能更新');
                    return redirect('/admin/settings');
                    // return $dbSTM->retMsg(1,'上传成功','可能需要清除缓存后才能更新');
                }
            }
            if($action=='usefavicon')
            {
                $dbGS=new GlobalSettings();
                $ret=$dbGS->setGlobalSetting('site_favicon','');
                $msg=$dbSTM->setMsg('globalsettings',1,'修改成功','可能需要清除缓存后才能更新');
                // return redirect('/admin/settings');
                return $dbSTM->retMsg(1,'修改成功','可能需要清除缓存后才能更新');
            }
            if($action=='uploadlogo')
            {
                $file = request()->file('imagelogo');
                if(empty($file)){
                    $msg=$dbSTM->setMsg('globalsettings',2,'上传失败','未选择任何文件');
                    return redirect('/admin/settings');
                    // return $dbSTM->retMsg(2,'上传失败','未选择任何文件');
                }
                $extension=$file->extension();
                if(!in_array($extension,array('jpeg','jpg','png'))){
                    $msg=$dbSTM->setMsg('globalsettings',2,'上传失败','站点图标的文件扩展名仅能为jpeg、jpg和png中的一个');
                    return redirect('/admin/settings');
                    // return $dbSTM->retMsg(2,'上传失败','站点图标的文件扩展名仅能为jpeg、jpg和png中的一个');
                }
                if(filesize($file)/1024/1024>1){
                    $msg=$dbSTM->setMsg('globalsettings',2,'上传失败','站点图标的文件大小在1MB以内');
                    return redirect('/admin/settings');
                    // return $dbSTM->retMsg(2,'上传失败','站点图标的文件大小在1MB以内');
                }
                $saveName = Filesystem::disk('public')->putFile('logo',$file,'md5');
                if (!empty($saveName)) {
                    $saveName = "/storage/".str_replace('\\', "/", $saveName);
                    $dbGS=new GlobalSettings();
                    $ret=$dbGS->setGlobalSetting('site_logo',$saveName);
                    $msg=$dbSTM->setMsg('globalsettings',1,'上传成功','可能需要清除缓存后才能更新');
                    return redirect('/admin/settings');
                    // return $dbSTM->retMsg(1,'上传成功','可能需要清除缓存后才能更新');
                }
            }
            if($action=='uselogo')
            {
                $dbGS=new GlobalSettings();
                $ret=$dbGS->setGlobalSetting('site_logo','');
                $msg=$dbSTM->setMsg('globalsettings',1,'修改成功','可能需要清除缓存后才能更新');
                // return redirect('/admin/settings');
                return $dbSTM->retMsg(1,'修改成功','可能需要清除缓存后才能更新');
            }
            if($action=='uploadspic')
            {
                $file = request()->file('imagespic');
                if(empty($file)){
                    $msg=$dbSTM->setMsg('globalsettings',2,'上传失败','未选择任何文件');
                    return redirect('/admin/settings');
                    // return $dbSTM->retMsg(2,'上传失败','未选择任何文件');
                }
                $extension=$file->extension();
                if(!in_array($extension,array('jpeg','jpg','png'))){
                    $msg=$dbSTM->setMsg('globalsettings',2,'上传失败','默认屏幕背景图片的文件扩展名仅能为jpeg、jpg和png中的一个');
                    return redirect('/admin/settings');
                    // return $dbSTM->retMsg(2,'上传失败','默认屏幕背景图片的文件扩展名仅能为jpeg、jpg和png中的一个');
                }
                if(filesize($file)/1024/1024>10){
                    $msg=$dbSTM->setMsg('globalsettings',2,'上传失败','默认屏幕背景图片的文件大小在10MB以内');
                    return redirect('/admin/settings');
                    // return $dbSTM->retMsg(2,'上传失败','默认屏幕背景图片的文件大小在10MB以内');
                }
                $saveName = Filesystem::disk('public')->putFile('spic',$file,'md5');
                if (!empty($saveName)) {
                    $saveName = "/storage/".str_replace('\\', "/", $saveName);
                    $dbGS=new GlobalSettings();
                    $ret=$dbGS->setGlobalSetting('site_default_screen_page_pic',$saveName);
                    $msg=$dbSTM->setMsg('globalsettings',1,'上传成功','可能需要清除缓存后才能更新');
                    return redirect('/admin/settings');
                    // return $dbSTM->retMsg(1,'上传成功','可能需要清除缓存后才能更新');
                }
            }
            if($action=='usespic')
            {
                $dbGS=new GlobalSettings();
                $ret=$dbGS->setGlobalSetting('site_default_screen_page_pic','');
                $msg=$dbSTM->setMsg('globalsettings',1,'修改成功','可能需要清除缓存后才能更新');
                // return redirect('/admin/settings');
                return $dbSTM->retMsg(1,'修改成功','可能需要清除缓存后才能更新');
            }
            if($action=='uploadsbgpic')
            {
                $file = request()->file('imagesbgpic');
                if(empty($file)){
                    $msg=$dbSTM->setMsg('globalsettings',2,'上传失败','未选择任何文件');
                    return redirect('/admin/settings');
                    // return $dbSTM->retMsg(2,'上传失败','未选择任何文件');
                }
                $extension=$file->extension();
                if(!in_array($extension,array('jpeg','jpg','png'))){
                    $msg=$dbSTM->setMsg('globalsettings',2,'上传失败','默认屏幕背景页面背景图片的文件扩展名仅能为jpeg、jpg和png中的一个');
                    return redirect('/admin/settings');
                    // return $dbSTM->retMsg(2,'上传失败','默认屏幕背景页面背景图片的文件扩展名仅能为jpeg、jpg和png中的一个');
                }
                if(filesize($file)/1024/1024>10){
                    $msg=$dbSTM->setMsg('globalsettings',2,'上传失败','默认屏幕背景页面背景图片的文件大小在10MB以内');
                    return redirect('/admin/settings');
                    // return $dbSTM->retMsg(2,'上传失败','默认屏幕背景页面背景图片的文件大小在10MB以内');
                }
                $saveName = Filesystem::disk('public')->putFile('sbgpic',$file,'md5');
                if (!empty($saveName)) {
                    $saveName = "/storage/".str_replace('\\', "/", $saveName);
                    $dbGS=new GlobalSettings();
                    $ret=$dbGS->setGlobalSetting('site_default_screen_background_page_pic',$saveName);
                    $msg=$dbSTM->setMsg('globalsettings',1,'上传成功','可能需要清除缓存后才能更新');
                    return redirect('/admin/settings');
                    // return $dbSTM->retMsg(1,'上传成功','可能需要清除缓存后才能更新');
                }
            }
            if($action=='usesbgpic')
            {
                $dbGS=new GlobalSettings();
                $ret=$dbGS->setGlobalSetting('site_default_screen_background_page_pic','');
                $msg=$dbSTM->setMsg('globalsettings',1,'修改成功','可能需要清除缓存后才能更新');
                // return redirect('/admin/settings');
                return $dbSTM->retMsg(1,'修改成功','可能需要清除缓存后才能更新');
            }
            if($action=='getmsg')
            {
                return $dbSTM->popMsg('globalsettings');
            }
        }
    }
}

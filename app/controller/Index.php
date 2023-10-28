<?php
namespace app\controller;
use think\facade\View;
use think\facade\Request;

use app\model\GlobalSettings;
use app\model\Contests;
use app\model\CookieToSession;

class Index
{
    public function index()
    {
        View::assign('page_title','首页');
        $dbC=new Contests();
        $contests=$dbC->getEnableContests();
        foreach($contests as &$c)
        {
            $c['cname']=$dbC->getSetting($c['cid'],'name');
            $c['cfavicon']=$dbC->getSetting($c['cid'],'favicon');
            $c['clogo']=$dbC->getSetting($c['cid'],'logo');
        }
        unset($c);
        View::assign('contests',$contests);
        
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
    public function contestre()
    {
        return redirect('/');
    }
    public function contest($cid)
    {
        $dbC=new Contests();
        $visitable=$dbC->enable($cid);
        if(!$visitable)return redirect('/visitwrong');
        $CTS = new CookieToSession();
        $isA = $CTS->isAdmin();
        $isCJ = $CTS->isContestJudger($cid);
        if($isA)
            return redirect('/contest'.$cid.'/admin');
        if($isCJ)
            return redirect('/contest'.$cid.'/judger');
        return redirect('/contest'.$cid.'/judger/login');
    }
    public function visitwrong()
    {
        View::assign('page_title','访问错误');
        
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
    public function usage()
    {
        View::assign('page_title','使用手册');
        
        $dbGS=new GlobalSettings();
        View::assign([
            'usage'=>1,
            'site_name'=>$dbGS->getSetting('name'),
            'site_favicon'=>$dbGS->getSetting('favicon'),
            'site_logo'=>$dbGS->getSetting('logo'),
            'site_color'=>$dbGS->getSetting('color'),
            'site_accent_color'=>$dbGS->getSetting('accent_color')
        ]);
        return View::fetch();
    }
}

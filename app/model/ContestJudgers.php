<?php
namespace app\model;
use think\Model;

use app\model\Contests;
use app\model\CookieToSession;

class ContestJudgers extends Model
{
    protected $pk = ['cid', 'jid'];
    public function genLogincode()
    {
        $ret="........";
        for($i=0;$i<8;$i++)
        {
            $tmp=rand(0,61);
            if($tmp>=36)
                $ret[$i]=chr($tmp-36+ord('a'));
            else if($tmp>=10)
                $ret[$i]=chr($tmp-10+ord('A'));
            else
                $ret[$i]=chr($tmp+ord('0'));
        }
        return $ret;
    }
    public function updatePage($cid,$jid)
    {
        $dbC = new Contests();
        $judger_next_page = $dbC->getJudgerNextPage($cid);
        $judger_next_page_content = $dbC->getJudgerNextPageContentPre($cid);
        $judger_page = $dbC->getJudgerPage($cid);
        $judger_page_content = $dbC->getJudgerPageContentPre($cid);
        $login_page = ContestJudgers::where('cid', $cid)->where('jid', $jid)->value('login_page');
        $login_page_content = ContestJudgers::where('cid', $cid)->where('jid', $jid)->value('login_page_content');
        if($login_page=='end')return false;
        if(($login_page==$judger_next_page)&&($login_page_content==$judger_next_page_content))
            return false;
        if(($login_page==$judger_page)&&($login_page_content==$judger_page_content))
            return false;
        $ret = ContestJudgers::where('cid', $cid)->where('jid', $jid)->update([
            'login_page'=>$judger_page,
            'login_page_content'=>$judger_page_content
        ]);
        return true;
    }
    public function toNextPage($cid,$jid)
    {
        $dbC = new Contests();
        $judger_next_page = $dbC->getJudgerNextPage($cid);
        $judger_next_page_content = $dbC->getJudgerNextPageContentPre($cid);
        $login_page = ContestJudgers::where('cid', $cid)->where('jid', $jid)->value('login_page');
        $login_page_content = ContestJudgers::where('cid', $cid)->where('jid', $jid)->value('login_page_content');
        if($login_page=='end')return false;
        if(($login_page==$judger_next_page)&&($login_page_content==$judger_next_page_content))
            return false;
        $ret = ContestJudgers::where('cid', $cid)->where('jid', $jid)->update([
            'login_page'=>$judger_next_page,
            'login_page_content'=>$judger_next_page_content
        ]);
        return true;
    }
    public function syncPage($cid,$jid)
    {
        $dbC = new Contests();
        $judger_page = $dbC->getJudgerPage($cid);
        $judger_page_content = $dbC->getJudgerPageContentPre($cid);
        $login_page = ContestJudgers::where('cid', $cid)->where('jid', $jid)->value('login_page');
        $login_page_content = ContestJudgers::where('cid', $cid)->where('jid', $jid)->value('login_page_content');
        if(($login_page==$judger_page)&&($login_page_content==$judger_page_content))
            return false;
        $ret = ContestJudgers::where('cid', $cid)->where('jid', $jid)->update([
            'login_page'=>$judger_page,
            'login_page_content'=>$judger_page_content
        ]);
        return true;
    }
    public function getPage($cid,$jid)
    {
        $ret = ContestJudgers::updatePage($cid,$jid);
        return ContestJudgers::where('cid', $cid)->where('jid', $jid)->value('login_page');
    }
    public function getPageContent($cid,$jid)
    {
        $ret = ContestJudgers::updatePage($cid,$jid);
        $ret = ContestJudgers::where('cid', $cid)->where('jid', $jid)->value('login_page_content');
        return json_decode('['.$ret.']');
    }
    public function getPageContentPre($cid,$jid)
    {
        $ret = ContestJudgers::updatePage($cid,$jid);
        return ContestJudgers::where('cid', $cid)->where('jid', $jid)->value('login_page_content');
    }
    public function addJudger($cid,$jid,$jname,$logincode,$enable,$login_page,$login_page_content)
    {
        return ContestJudgers::insert([
            'cid'=>$cid,
            'jid'=>$jid,
            'jname'=>$jname,
            'logincode'=>$logincode,
            'enable'=>$enable,
            'login_page'=>$login_page,
            'login_page_content'=>$login_page_content
        ]);
    }
    public function delJudger($cid,$jid)
    {
        return ContestJudgers::where('cid',$cid)->where('jid',$jid)->delete();
    }
    public function delContestJudgers($cid)
    {
        return ContestJudgers::where('cid',$cid)->delete();
    }
    public function getJudger($cid,$jid)
    {
        return ContestJudgers::where('cid',$cid)->where('jid',$jid)->find()->toArray();
    }
    public function updateJudger($cid,$jid,$jname,$logincode,$enable,$login_page,$login_page_content)
    {
        return ContestJudgers::where('cid',$cid)->where('jid',$jid)->update([
            'jname'=>$jname,
            'logincode'=>$logincode,
            'enable'=>$enable,
            'login_page'=>$login_page,
            'login_page_content'=>$login_page_content
        ]);
    }
    public function getJudgers($cid)
    {
        return ContestJudgers::where('cid', $cid)->select()->toArray();
    }
    public function getJudgersOrdered($cid,$judgers_order_key,$judgers_order_by)
    {
        return ContestJudgers::where('cid', $cid)->order($judgers_order_key,$judgers_order_by)->select()->toArray();
    }
    public function getJudgersOrdered2($cid,$judgers_order_key,$judgers_order_by,$judgers_order_key2,$judgers_order_by2)
    {
        return ContestJudgers::where('cid', $cid)->order([
            $judgers_order_key=>$judgers_order_by,
            $judgers_order_key2=>$judgers_order_by2
        ])->select()->toArray();
    }
    public function getEnableJudgers($cid)
    {
        return ContestJudgers::where('cid', $cid)->where('enable',1)->select()->toArray();
    }
    public function getEnableJudgersOrdered($cid,$judgers_order_key,$judgers_order_by)
    {
        return ContestJudgers::where('cid', $cid)->where('enable',1)->order($judgers_order_key,$judgers_order_by)->select()->toArray();
    }
    public function getEnableJudgersOrdered2($cid,$judgers_order_key,$judgers_order_by,$judgers_order_key2,$judgers_order_by2)
    {
        return ContestJudgers::where('cid', $cid)->where('enable',1)->order([
            $judgers_order_key=>$judgers_order_by,
            $judgers_order_key2=>$judgers_order_by2
        ])->select()->toArray();
    }
    public function login($cid,$logincode)
    {
        $jid = ContestJudgers::where('cid',$cid)->where('logincode',$logincode)->where('enable',1)->value('jid');
        if(is_null($jid))
            return false;
        $dbCTS = new CookieToSession();
        $ret = $dbCTS->setJid($cid,$jid);
        return true;
    }
}
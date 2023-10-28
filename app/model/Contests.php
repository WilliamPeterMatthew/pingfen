<?php
namespace app\model;
use think\Model;

use app\model\GlobalSettings;
use app\model\ContestPoints;

class Contests extends Model
{
    protected $pk = 'cid';
    public function enable($cid)
    {
        $contests=Contests::select()->toArray();
        $flag=false;
        foreach($contests as $c)
        {
            if($c['cid']==$cid)
            {
                if($c['enable'])
                    $flag=true;
            }
        }
        return $flag;
    }
    public function addContest($cid,$cname,$ccolor,$caccent_color,$enable,$min_max_mode)
    {
        return Contests::insert([
            'cid'=>$cid,
            'cname'=>$cname,
            'ccolor'=>$ccolor,
            'caccent_color'=>$caccent_color,
            'enable'=>$enable,
            'min_max_mode'=>$min_max_mode
        ]);
    }
    public function delContest($cid)
    {
        return Contests::where('cid',$cid)->delete();
    }
    public function getContest($cid)
    {
        return Contests::where('cid',$cid)->find()->toArray();
    }
    public function updateContest($cid,$cname,$ccolor,$caccent_color,$enable,$min_max_mode)
    {
        return Contests::where('cid',$cid)->update([
            'cname'=>$cname,
            'ccolor'=>$ccolor,
            'caccent_color'=>$caccent_color,
            'enable'=>$enable,
            'min_max_mode'=>$min_max_mode
        ]);
    }
    public function getContests()
    {
        return Contests::order('cid','desc')->select()->toArray();
    }
    public function getContestsOrdered($contests_order_key,$contests_order_by)
    {
        return Contests::order($contests_order_key,$contests_order_by)->select()->toArray();
    }
    public function getCids()
    {
        return Contests::column('cid');
    }
    public function getEnableContests()
    {
        return Contests::where('enable',1)->order('cid','desc')->select()->toArray();
    }
    public function getEnableContestsOrdered($contests_order_key,$contests_order_by)
    {
        return Contests::where('enable',1)->order($contests_order_key,$contests_order_by)->select()->toArray();
    }
    public function getEnableCids()
    {
        return Contests::where('enable',1)->column('cid');
    }
    public function getSetting($cid,$setting_name)
    {
        $ret = Contests::where('cid', $cid)->value('c'.$setting_name);
        if(is_null($ret)||strlen($ret)==0){
            $dbGS = new GlobalSettings();
            $ret=$dbGS->getSetting($setting_name);
        }
        return $ret;
    }
    public function getContestSetting($cid,$setting_name)
    {
        return $ret = Contests::where('cid', $cid)->value($setting_name);
    }
    public function setContestSetting($cid,$setting_name,$setting_value)
    {
        $ret=Contests::where('cid', $cid)->update([
            $setting_name=>$setting_value
        ]);
        return true;
    }
    public function autoNextPage($cid)
    {
        $judger_next_page=Contests::where('cid', $cid)->value('judger_next_page');
        $judger_next_page_content=Contests::where('cid', $cid)->value('judger_next_page_content');
        $ret=Contests::where('cid', $cid)->update([
            'judger_page'=>$judger_next_page,
            'judger_page_content'=>$judger_next_page_content
        ]);
        if($judger_next_page=='wait')
        {
            $ret=Contests::where('cid', $cid)->update([
                'judger_next_page'=>'score'
            ]);
        }
        return true;
    }
    public function updateJudgerPage($cid,$judger_page,$judger_page_content)
    {
        return Contests::where('cid', $cid)->update([
            'judger_page'=>$judger_page,
            'judger_page_content'=>$judger_page_content
        ]);
    }
    public function getJudgerPage($cid)
    {
        return Contests::where('cid', $cid)->value('judger_page');
    }
    public function getJudgerPageContent($cid)
    {
        $ret=Contests::where('cid', $cid)->value('judger_page_content');
        return json_decode('['.$ret.']');
    }
    public function getJudgerPageContentPre($cid)
    {
        return Contests::where('cid', $cid)->value('judger_page_content');
    }
    public function updateJudgerNextPage($cid,$judger_next_page,$judger_next_page_content)
    {
        return Contests::where('cid', $cid)->update([
            'judger_next_page'=>$judger_next_page,
            'judger_next_page_content'=>$judger_next_page_content
        ]);
    }
    public function getJudgerNextPage($cid)
    {
        return Contests::where('cid', $cid)->value('judger_next_page');
    }
    public function getJudgerNextPageContent($cid)
    {
        $ret=Contests::where('cid', $cid)->value('judger_next_page_content');
        return json_decode('['.$ret.']');
    }
    public function getJudgerNextPageContentPre($cid)
    {
        return Contests::where('cid', $cid)->value('judger_next_page_content');
    }
    public function updateScreenPage($cid,$screen_page,$screen_page_content)
    {
        return Contests::where('cid', $cid)->update([
            'screen_page'=>$screen_page,
            'screen_page_content'=>$screen_page_content
        ]);
    }
    public function getScreenPage($cid)
    {
        return Contests::where('cid', $cid)->value('screen_page');
    }
    public function getScreenPageContent($cid)
    {
        $ret=Contests::where('cid', $cid)->value('screen_page_content');
        return json_decode('['.$ret.']');
    }
    public function getScreenPageContentPre($cid)
    {
        return Contests::where('cid', $cid)->value('screen_page_content');
    }
    public function getScreenPagePic($cid)
    {
        $ret = Contests::where('cid', $cid)->value('screen_page_pic');
        if(is_null($ret)||strlen($ret)==0){
            $dbGS = new GlobalSettings();
            $ret=$dbGS->getSetting('default_screen_page_pic');
        }
        return $ret;
    }
    public function getScreenBackgroundPagePic($cid)
    {
        $ret = Contests::where('cid', $cid)->value('screen_background_page_pic');
        if(is_null($ret)||strlen($ret)==0){
            $dbGS = new GlobalSettings();
            $ret=$dbGS->getSetting('default_screen_background_page_pic');
        }
        return $ret;
    }
}
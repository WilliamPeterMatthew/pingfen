<?php
namespace app\model;
use think\Model;

use app\model\Contests;

class ContestPoints extends Model
{
    protected $pk = ['cid', 'pid', 'jid'];
    public function setEnable($cid,$jid,$enable)
    {
        $ret = ContestPoints::where('cid',$cid)->where('jid',$jid)->update([
            'enable'=>$enable
        ]);
        return true;
    }
    public function getPoint($cid,$pid,$jid)
    {
        $ret = ContestPoints::where('cid',$cid)->where('pid',$pid)->where('jid',$jid)->value('point');
        if(is_null($ret))
            return false;
        return $ret;
    }
    public function getPoints($cid)
    {
        return ContestPoints::where('cid', $cid)->select()->toArray();
    }
    public function getEnablePoints($cid)
    {
        return ContestPoints::where('cid', $cid)->where('enable',1)->select()->toArray();
    }
    public function setPoint($cid,$pid,$jid,$point)
    {
        $ret = ContestPoints::insert([
            'cid'=>$cid,
            'pid'=>$pid,
            'jid'=>$jid,
            'point'=>$point,
            'enable'=>1
        ]);
        if($ret)return true;
        return false;
    }
    public function delPoint($cid,$pid,$jid)
    {
        return ContestPoints::where('cid',$cid)->where('pid',$pid)->where('jid',$jid)->delete();
    }
    public function delJudgerPoints($cid,$jid)
    {
        return ContestPoints::where('cid',$cid)->where('jid',$jid)->delete();
    }
    public function delPlayerPoints($cid,$pid)
    {
        return ContestPoints::where('cid',$cid)->where('pid',$pid)->delete();
    }
    public function delContestPoints($cid)
    {
        return ContestPoints::where('cid',$cid)->delete();
    }
    public function getPlayerPointJudgers($cid,$pid)
    {
        return ContestPoints::where('cid',$cid)->where('pid',$pid)->column('jid');
    }
    public function getPlayerPointEnableJudgers($cid,$pid)
    {
        return ContestPoints::where('cid',$cid)->where('pid',$pid)->where('enable',1)->column('jid');
    }
    public function getPlayerPoints($cid,$pid)
    {
        return ContestPoints::where('cid',$cid)->where('pid',$pid)->select()->toArray();
    }
    public function getPlayerEnablePoints($cid,$pid)
    {
        return ContestPoints::where('cid',$cid)->where('pid',$pid)->where('enable',1)->select()->toArray();
    }
    public function getPointsOrdered($cid,$points_order_key,$points_order_by)
    {
        return ContestPoints::where('cid', $cid)->order($points_order_key,$points_order_by)->select()->toArray();
    }
    public function getPointsOrdered2($cid,$points_order_key,$points_order_by,$points_order_key2,$points_order_by2)
    {
        return ContestPoints::where('cid', $cid)->order([
            $points_order_key=>$points_order_by,
            $points_order_key2=>$points_order_by2
        ])->select()->toArray();
    }
    public function calcScore($cid,$pid)
    {
        $points = ContestPoints::where('cid',$cid)->where('pid',$pid)->where('enable',1)->select()->toArray();
        if(is_null($points))
            return 0;
        $summ=0;
        $lenn=count($points);
        for($i=0;$i<$lenn;$i++)
        {
            $summ+=$points[$i]['point'];
            $ret = ContestPoints::where('cid',$cid)->where('pid',$pid)->where('jid',$points[$i]['jid'])->update(['type'=>'']);
        }
        $dbC = new Contests();
        if($dbC->getContestSetting($cid,'min_max_mode'))
        {
            if(count($points)<=2)
                return 0;
            $minn=$points[0]['point'];
            $mini=0;
            $maxx=$points[0]['point'];
            $maxi=0;
            for($i=1;$i<$lenn;$i++)
            {
                if($maxx<$points[$i]['point'])
                {
                    $maxx=$points[$i]['point'];
                    $maxi=$i;
                }
                if($minn>=$points[$i]['point'])
                {
                    $minn=$points[$i]['point'];
                    $mini=$i;
                }
            }
            $summ-=$maxx+$minn;
            $ret = ContestPoints::where('cid',$cid)->where('pid',$pid)->where('jid',$points[$maxi]['jid'])->update(['type'=>'max']);
            $ret = ContestPoints::where('cid',$cid)->where('pid',$pid)->where('jid',$points[$mini]['jid'])->update(['type'=>'min']);
            $lenn-=2;
        }
        return round($summ/$lenn,2);
    }
    function exportPoints($cid)
    {
        $ED=new ExportData();
        $points=ContestPoints::where('cid', $cid)->order(['pid'=>'asc','jid'=>'asc'])->select()->toArray();
        foreach($points as &$p)
        {
            unset($p['cid']);
            unset($p['enable']);
        }
        unset($p);
        return $ED->export_excel('contest'.$cid.'-points.csv',['选手序号','评委序号','打分','是否最高/最低分'],$points);
    }
}
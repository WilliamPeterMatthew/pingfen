<?php
namespace app\model;
use think\Model;

use app\model\ContestPoints;
use app\model\ExportData;

class ContestPlayers extends Model
{
    protected $pk = ['cid', 'pid'];
    public function addPlayer($cid,$pid,$pname,$sequence)
    {
        return ContestPlayers::insert([
            'cid'=>$cid,
            'pid'=>$pid,
            'pname'=>$pname,
            'sequence'=>$sequence
        ]);
    }
    public function delPlayer($cid,$pid)
    {
        return ContestPlayers::where('cid',$cid)->where('pid',$pid)->delete();
    }
    public function delContestPlayers($cid)
    {
        return ContestPlayers::where('cid',$cid)->delete();
    }
    public function getPlayer($cid,$pid)
    {
        return ContestPlayers::where('cid',$cid)->where('pid',$pid)->find()->toArray();
    }
    public function updatePlayer($cid,$pid,$pname,$sequence)
    {
        return ContestPlayers::where('cid',$cid)->where('pid',$pid)->update([
            'pname'=>$pname,
            'sequence'=>$sequence
        ]);
    }
    public function getRankPlayers($cid,$rank)
    {
        return ContestPlayers::where('cid', $cid)->where('rank', $rank)->select()->toArray();
    }
    public function getPlayers($cid)
    {
        return ContestPlayers::where('cid', $cid)->select()->toArray();
    }
    public function getPlayersOrdered($cid,$players_order_key,$players_order_by)
    {
        return ContestPlayers::where('cid', $cid)->order($players_order_key,$players_order_by)->select()->toArray();
    }
    public function isRanked($cid)
    {
        $players = ContestPlayers::where('cid',$cid)->select()->toArray();
        foreach($players as $v)
        {
            if(is_null($v['rank'])||strlen($v['rank'])==0)
                return false;
        }
        return true;
    }
    public function calcRank($cid)
    {
        $players = ContestPlayers::where('cid',$cid)->select()->toArray();
        $dbCPs = new ContestPoints();
        foreach($players as $v)
        {
            $ret = ContestPlayers::where('cid',$cid)->where('pid',$v['pid'])->update(['point'=>0,'rank'=>0]);
            $res = $dbCPs->calcScore($cid,$v['pid']);
            $ret = ContestPlayers::where('cid',$cid)->where('pid',$v['pid'])->update(['point'=>$res]);
        }
        $players = ContestPlayers::where('cid',$cid)->where('point','>',0)->order('point','desc')->select()->toArray();
        $lenn=count($players);
        $players[0]['rank']=1;
        for($i=1;$i<$lenn;$i++)
        {
            if($players[$i]['point']==$players[$i-1]['point'])
                $players[$i]['rank']=$players[$i-1]['rank'];
            else
                $players[$i]['rank']=$i+1;
        }
        foreach($players as $v)
            $ret = ContestPlayers::where('cid',$cid)->where('pid',$v['pid'])->update(['rank'=>$v['rank']]);
        return true;
    }
    public function calcPoint($cid,$pid)
    {
        $ret = ContestPlayers::where('cid',$cid)->where('pid',$pid)->update(['point'=>0,'rank'=>0]);
        $dbCPs = new ContestPoints();
        $res = $dbCPs->calcScore($cid,$pid);
        $ret = ContestPlayers::where('cid',$cid)->where('pid',$pid)->update(['point'=>$res]);
        return true;
    }
    function exportPlayers($cid)
    {
        $ED=new ExportData();
        $players=ContestPlayers::where('cid', $cid)->order('sequence','asc')->select()->toArray();
        foreach($players as &$p)
        {
            unset($p['cid']);
        }
        unset($p);
        return $ED->export_excel('contest'.$cid.'-players.csv',['选手序号','选手名称','选手顺序','选手最终得分','选手排名'],$players);
    }
}
<?php
namespace app\model;
use think\Model;
use think\facade\Cookie;
use think\facade\Session;

use app\model\Admins;
use app\model\Contests;

class CookieToSession extends Model
{
    protected $name = 'Contests';//只是占位用的
    protected $pk = 'cid';
    public function isAdmin()
    {
        if(Session::has('aid'))return true;
        if(Cookie::has('aid'))
        {
            Session::set('aid',Cookie::get('aid'));
            return true;
        }
        return false;
    }
    public function getAid()
    {
        if(Session::has('aid'))return Session::get('aid');
        if(Cookie::has('aid'))
        {
            Session::set('aid',Cookie::get('aid'));
            return Session::get('aid');
        }
        return false;
    }
    public function setAid($aid)
    {
        $dbC = new Contests();
        $Cids = $dbC->getCids();
        foreach($Cids as $cid)
        {
            Cookie::delete('jid'.$cid);
            Session::delete('jid'.$cid);
        }
        Cookie::set('aid',$aid);
        Session::set('aid',$aid);
        return true;
    }
    public function delAid()
    {
        Cookie::delete('aid');
        Session::delete('aid');
        $dbC = new Contests();
        $Cids = $dbC->getCids();
        foreach($Cids as $c)
        {
            Cookie::delete('players_order_key'.$c);
            Session::delete('players_order_key'.$c);
            Cookie::delete('players_order_by'.$c);
            Session::delete('players_order_by'.$c);
            Cookie::delete('judgers_order_key'.$c);
            Session::delete('judgers_order_key'.$c);
            Cookie::delete('judgers_order_by'.$c);
            Session::delete('judgers_order_by'.$c);
            Cookie::delete('points_order_key'.$c);
            Session::delete('points_order_key'.$c);
            Cookie::delete('points_order_by'.$c);
            Session::delete('points_order_by'.$c);
        }
        Cookie::delete('admins_order_key');
        Session::delete('admins_order_key');
        Cookie::delete('admins_order_by');
        Session::delete('admins_order_by');
        Cookie::delete('contests_order_key');
        Session::delete('contests_order_key');
        Cookie::delete('contests_order_by');
        Session::delete('contests_order_by');
        return true;
    }
    public function isGlobalAdmin()
    {
        if(Session::has('aid'))
        {
            $aid = Session::get('aid');
            $dbA = new Admins();
            $ret = $dbA->pdGlobalPermission($aid);
            return $ret;
        }
        if(Cookie::has('aid'))
        {
            Session::set('aid',Cookie::get('aid'));
            $aid = Session::get('aid');
            $dbA = new Admins();
            $ret = $dbA->pdGlobalPermission($aid);
            return $ret;
        }
        return false;
    }
    public function isContestAdmin($cid)
    {
        if(Session::has('aid'))
        {
            $aid = Session::get('aid');
            $dbA = new Admins();
            $ret = $dbA->pdPermission($aid,$cid);
            return $ret;
        }
        if(Cookie::has('aid'))
        {
            Session::set('aid',Cookie::get('aid'));
            Session::set('players_order_key'.$cid,Cookie::get('players_order_key'.$cid));
            Session::set('players_order_by'.$cid,Cookie::get('players_order_by'.$cid));
            Session::set('judgers_order_key'.$cid,Cookie::get('judgers_order_key'.$cid));
            Session::set('judgers_order_by'.$cid,Cookie::get('judgers_order_by'.$cid));
            Session::set('points_order_key'.$cid,Cookie::get('points_order_key'.$cid));
            Session::set('points_order_by'.$cid,Cookie::get('points_order_by'.$cid));
            Session::set('admins_order_key',Cookie::get('admins_order_key'));
            Session::set('admins_order_by',Cookie::get('admins_order_by'));
            Session::set('contests_order_key',Cookie::get('contests_order_key'));
            Session::set('contests_order_by',Cookie::get('contests_order_by'));
            $aid = Session::get('aid');
            $dbA = new Admins();
            $ret = $dbA->pdPermission($aid,$cid);
            return $ret;
        }
        if(Session::has('jid'.$cid))
            return false;
        if(Cookie::has('jid'.$cid))
        {
            Session::set('jid'.$cid,Cookie::get('jid'.$cid));
            return false;
        }
        return false;
    }
    public function isContestJudger($cid)
    {
        if(Session::has('aid'))
        {
            $aid = Session::get('aid');
            $dbA = new Admins();
            $ret = $dbA->pdPermission($aid,$cid);
            return false;
        }
        if(Cookie::has('aid'))
        {
            Session::set('aid',Cookie::get('aid'));
            Session::set('players_order_key'.$cid,Cookie::get('players_order_key'.$cid));
            Session::set('players_order_by'.$cid,Cookie::get('players_order_by'.$cid));
            Session::set('judgers_order_key'.$cid,Cookie::get('judgers_order_key'.$cid));
            Session::set('judgers_order_by'.$cid,Cookie::get('judgers_order_by'.$cid));
            Session::set('points_order_key'.$cid,Cookie::get('points_order_key'.$cid));
            Session::set('points_order_by'.$cid,Cookie::get('points_order_by'.$cid));
            Session::set('admins_order_key',Cookie::get('admins_order_key'));
            Session::set('admins_order_by',Cookie::get('admins_order_by'));
            Session::set('contests_order_key',Cookie::get('contests_order_key'));
            Session::set('contests_order_by',Cookie::get('contests_order_by'));
            $aid = Session::get('aid');
            $dbA = new Admins();
            $ret = $dbA->pdPermission($aid,$cid);
            return false;
        }
        if(Session::has('jid'.$cid))
            return true;
        if(Cookie::has('jid'.$cid))
        {
            Session::set('jid'.$cid,Cookie::get('jid'.$cid));
            return true;
        }
        return false;
    }
    public function getJid($cid)
    {
        if(Session::has('jid'.$cid))return Session::get('jid'.$cid);
        if(Cookie::has('jid'.$cid))
        {
            Session::set('jid'.$cid,Cookie::get('jid'.$cid));
            return Session::get('jid'.$cid);
        }
        return false;
    }
    public function setJid($cid,$jid)
    {
        Cookie::delete('aid');
        Session::delete('aid');
        $dbC = new Contests();
        $Cids = $dbC->getCids();
        foreach($Cids as $c)
        {
            Cookie::delete('players_order_key'.$c);
            Session::delete('players_order_key'.$c);
            Cookie::delete('players_order_by'.$c);
            Session::delete('players_order_by'.$c);
            Cookie::delete('judgers_order_key'.$c);
            Session::delete('judgers_order_key'.$c);
            Cookie::delete('judgers_order_by'.$c);
            Session::delete('judgers_order_by'.$c);
            Cookie::delete('points_order_key'.$c);
            Session::delete('points_order_key'.$c);
            Cookie::delete('points_order_by'.$c);
            Session::delete('points_order_by'.$c);
        }
        Cookie::delete('admins_order_key');
        Session::delete('admins_order_key');
        Cookie::delete('admins_order_by');
        Session::delete('admins_order_by');
        Cookie::delete('contests_order_key');
        Session::delete('contests_order_key');
        Cookie::delete('contests_order_by');
        Session::delete('contests_order_by');
        Cookie::set('jid'.$cid,$jid);
        Session::set('jid'.$cid,$jid);
        return true;
    }
    public function delJid($cid)
    {
        Cookie::delete('jid'.$cid);
        Session::delete('jid'.$cid);
    }
    public function delAllJid($cid)
    {
        $Cids = $dbC->getCids();
        foreach($Cids as $cid)
        {
            Cookie::delete('jid'.$cid);
            Session::delete('jid'.$cid);
        }
    }
    public function getPlayersOrderKey($cid)
    {
        if(Session::has('players_order_key'.$cid))return Session::get('players_order_key'.$cid);
        else if(Cookie::has('players_order_key'.$cid))
        {
            Session::set('players_order_key'.$cid,Cookie::get('players_order_key'.$cid));
            Session::set('players_order_by'.$cid,Cookie::get('players_order_by'.$cid));
            return Session::get('players_order_key'.$cid);
        }
        else
        {
            Cookie::set('players_order_key'.$cid,'sequence');
            Session::set('players_order_key'.$cid,'sequence');
            Cookie::set('players_order_by'.$cid,'asc');
            Session::set('players_order_by'.$cid,'asc');
            return Session::get('players_order_key'.$cid);
        }
    }
    public function getPlayersOrderBy($cid)
    {
        if(Session::has('players_order_by'.$cid))return Session::get('players_order_by'.$cid);
        else if(Cookie::has('players_order_by'.$cid))
        {
            Session::set('players_order_key'.$cid,Cookie::get('players_order_key'.$cid));
            Session::set('players_order_by'.$cid,Cookie::get('players_order_by'.$cid));
            return Session::get('players_order_by'.$cid);
        }
        else
        {
            Cookie::set('players_order_key'.$cid,'sequence');
            Session::set('players_order_key'.$cid,'sequence');
            Cookie::set('players_order_by'.$cid,'asc');
            Session::set('players_order_by'.$cid,'asc');
            return Session::get('players_order_by'.$cid);
        }
    }
    public function setPlayersOrder($cid,$players_order_key,$players_order_by)
    {
        Cookie::set('players_order_key'.$cid,$players_order_key);
        Session::set('players_order_key'.$cid,$players_order_key);
        Cookie::set('players_order_by'.$cid,$players_order_by);
        Session::set('players_order_by'.$cid,$players_order_by);
        return true;
    }
    public function getJudgersOrderKey($cid)
    {
        if(Session::has('judgers_order_key'.$cid))return Session::get('judgers_order_key'.$cid);
        else if(Cookie::has('judgers_order_key'.$cid))
        {
            Session::set('judgers_order_key'.$cid,Cookie::get('judgers_order_key'.$cid));
            Session::set('judgers_order_by'.$cid,Cookie::get('judgers_order_by'.$cid));
            return Session::get('judgers_order_key'.$cid);
        }
        else
        {
            Cookie::set('judgers_order_key'.$cid,'jid');
            Session::set('judgers_order_key'.$cid,'jid');
            Cookie::set('judgers_order_by'.$cid,'asc');
            Session::set('judgers_order_by'.$cid,'asc');
            return Session::get('judgers_order_key'.$cid);
        }
    }
    public function getJudgersOrderBy($cid)
    {
        if(Session::has('judgers_order_by'.$cid))return Session::get('judgers_order_by'.$cid);
        else if(Cookie::has('judgers_order_by'.$cid))
        {
            Session::set('judgers_order_key'.$cid,Cookie::get('judgers_order_key'.$cid));
            Session::set('judgers_order_by'.$cid,Cookie::get('judgers_order_by'.$cid));
            return Session::get('judgers_order_by'.$cid);
        }
        else
        {
            Cookie::set('judgers_order_key'.$cid,'jid');
            Session::set('judgers_order_key'.$cid,'jid');
            Cookie::set('judgers_order_by'.$cid,'asc');
            Session::set('judgers_order_by'.$cid,'asc');
            return Session::get('judgers_order_by'.$cid);
        }
    }
    public function setJudgersOrder($cid,$judgers_order_key,$judgers_order_by)
    {
        Cookie::set('judgers_order_key'.$cid,$judgers_order_key);
        Session::set('judgers_order_key'.$cid,$judgers_order_key);
        Cookie::set('judgers_order_by'.$cid,$judgers_order_by);
        Session::set('judgers_order_by'.$cid,$judgers_order_by);
        return true;
    }
    public function getPointsOrderKey($cid)
    {
        if(Session::has('points_order_key'.$cid))return Session::get('points_order_key'.$cid);
        else if(Cookie::has('points_order_key'.$cid))
        {
            Session::set('points_order_key'.$cid,Cookie::get('points_order_key'.$cid));
            Session::set('points_order_by'.$cid,Cookie::get('points_order_by'.$cid));
            return Session::get('points_order_key'.$cid);
        }
        else
        {
            Cookie::set('points_order_key'.$cid,'pid');
            Session::set('points_order_key'.$cid,'pid');
            Cookie::set('points_order_by'.$cid,'asc');
            Session::set('points_order_by'.$cid,'asc');
            return Session::get('points_order_key'.$cid);
        }
    }
    public function getPointsOrderBy($cid)
    {
        if(Session::has('points_order_by'.$cid))return Session::get('points_order_by'.$cid);
        else if(Cookie::has('points_order_by'.$cid))
        {
            Session::set('points_order_key'.$cid,Cookie::get('points_order_key'.$cid));
            Session::set('points_order_by'.$cid,Cookie::get('points_order_by'.$cid));
            return Session::get('points_order_by'.$cid);
        }
        else
        {
            Cookie::set('points_order_key'.$cid,'pid');
            Session::set('points_order_key'.$cid,'pid');
            Cookie::set('points_order_by'.$cid,'asc');
            Session::set('points_order_by'.$cid,'asc');
            return Session::get('points_order_by'.$cid);
        }
    }
    public function setPointsOrder($cid,$points_order_key,$points_order_by)
    {
        Cookie::set('points_order_key'.$cid,$points_order_key);
        Session::set('points_order_key'.$cid,$points_order_key);
        Cookie::set('points_order_by'.$cid,$points_order_by);
        Session::set('points_order_by'.$cid,$points_order_by);
        return true;
    }
    public function getAdminsOrderKey()
    {
        if(Session::has('admins_order_key'))return Session::get('admins_order_key');
        else if(Cookie::has('admins_order_key'))
        {
            Session::set('admins_order_key',Cookie::get('admins_order_key'));
            Session::set('admins_order_by',Cookie::get('admins_order_by'));
            return Session::get('admins_order_key');
        }
        else
        {
            Cookie::set('admins_order_key','aid');
            Session::set('admins_order_key','aid');
            Cookie::set('admins_order_by','asc');
            Session::set('admins_order_by','asc');
            return Session::get('admins_order_key');
        }
    }
    public function getAdminsOrderBy()
    {
        if(Session::has('admins_order_by'))return Session::get('admins_order_by');
        else if(Cookie::has('admins_order_by'))
        {
            Session::set('admins_order_key',Cookie::get('admins_order_key'));
            Session::set('admins_order_by',Cookie::get('admins_order_by'));
            return Session::get('admins_order_by');
        }
        else
        {
            Cookie::set('admins_order_key','aid');
            Session::set('admins_order_key','aid');
            Cookie::set('admins_order_by','asc');
            Session::set('admins_order_by','asc');
            return Session::get('admins_order_by');
        }
    }
    public function setAdminsOrder($admins_order_key,$admins_order_by)
    {
        Cookie::set('admins_order_key',$admins_order_key);
        Session::set('admins_order_key',$admins_order_key);
        Cookie::set('admins_order_by',$admins_order_by);
        Session::set('admins_order_by',$admins_order_by);
        return true;
    }
    public function getContestsOrderKey()
    {
        if(Session::has('contests_order_key'))return Session::get('contests_order_key');
        else if(Cookie::has('contests_order_key'))
        {
            Session::set('contests_order_key',Cookie::get('contests_order_key'));
            Session::set('contests_order_by',Cookie::get('contests_order_by'));
            return Session::get('contests_order_key');
        }
        else
        {
            Cookie::set('contests_order_key','cid');
            Session::set('contests_order_key','cid');
            Cookie::set('contests_order_by','asc');
            Session::set('contests_order_by','asc');
            return Session::get('contests_order_key');
        }
    }
    public function getContestsOrderBy()
    {
        if(Session::has('contests_order_by'))return Session::get('contests_order_by');
        else if(Cookie::has('contests_order_by'))
        {
            Session::set('contests_order_key',Cookie::get('contests_order_key'));
            Session::set('contests_order_by',Cookie::get('contests_order_by'));
            return Session::get('contests_order_by');
        }
        else
        {
            Cookie::set('contests_order_key','cid');
            Session::set('contests_order_key','cid');
            Cookie::set('contests_order_by','asc');
            Session::set('contests_order_by','asc');
            return Session::get('contests_order_by');
        }
    }
    public function setContestsOrder($contests_order_key,$contests_order_by)
    {
        Cookie::set('contests_order_key',$contests_order_key);
        Session::set('contests_order_key',$contests_order_key);
        Cookie::set('contests_order_by',$contests_order_by);
        Session::set('contests_order_by',$contests_order_by);
        return true;
    }
}
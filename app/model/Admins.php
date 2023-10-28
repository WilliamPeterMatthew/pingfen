<?php
namespace app\model;
use think\Model;

class Admins extends Model
{
    protected $pk = 'aid';
    public function getPermission($aid)
    {
        $ret = Admins::where('aid', $aid)->value('permission');
        return json_decode('['.$ret.']');
    }
    public function pdPermission($aid,$cid)
    {
        $ret = Admins::where('aid', $aid)->value('permission');
        $per = json_decode('['.$ret.']');
        return (in_array(-1,$per)||in_array($cid,$per));
    }
    public function pdGlobalPermission($aid)
    {
        $ret = Admins::where('aid', $aid)->value('permission');
        $per = json_decode('['.$ret.']');
        return in_array(-1,$per);
    }
    public function getProfile($aid)
    {
        return Admins::where('aid',$aid)->find()->toArray();
    }
    public function addAdmin($aid,$aname,$loginname,$enable,$permission)
    {
        return Admins::insert([
            'aid'=>$aid,
            'aname'=>$aname,
            'loginname'=>$loginname,
            'enable'=>$enable,
            'permission'=>$permission,
            'password'=>'12345678'
        ]);
    }
    public function delAdmin($aid)
    {
        return Admins::where('aid',$aid)->delete();
    }
    public function delContestAdmin($cid)
    {
        $scid=strval($cid);
        $admins=Admins::select()->toArray();
        foreach($admins as $v)
        {
            $sper = $v['permission'];
            if($sper===$scid)
                $ret=Admins::where('aid',$v['aid'])->delete();
            else
            {
                $per = json_decode('['.$sper.']');
                if(in_array($cid,$per))
                {
                    $sper=str_replace($cid,'',$sper);
                    $sper=str_replace(',,',',',$sper);
                    if(substr($sper,0,1)===',')
                        $sper=substr($sper,1,strlen($sper)-1);
                    if(substr($sper,strlen($sper)-1,1)===',')
                        $sper=substr($sper,0,strlen($sper)-1);
                    $ret=Admins::where('aid',$v['aid'])->update(['permission'=>$sper]);
                }
            }
        }
    }
    public function getAdmin($aid)
    {
        return Admins::where('aid',$aid)->find()->toArray();
    }
    public function updateAdmin($aid,$aname,$loginname,$enable,$permission)
    {
        return Admins::where('aid',$aid)->update([
            'aname'=>$aname,
            'loginname'=>$loginname,
            'enable'=>$enable,
            'permission'=>$permission
        ]);
    }
    public function getAdmins()
    {
        return Admins::select()->toArray();
    }
    public function getAdminsOrdered($admins_order_key,$admins_order_by)
    {
        return Admins::order($admins_order_key,$admins_order_by)->select()->toArray();
    }
    public function getEnableAdmins()
    {
        return Admins::where('enable',1)->select()->toArray();
    }
    public function getEnableAdminsOrdered($admins_order_key,$admins_order_by)
    {
        return Admins::where('enable',1)->order($admins_order_key,$admins_order_by)->select()->toArray();
    }
    public function modAname($aid,$aname)
    {
        return Admins::where('aid', $aid)->update(['aname'=>$aname]);
    }
    public function modLoginname($aid,$loginname)
    {
        return Admins::where('aid', $aid)->update(['loginname'=>$loginname]);
    }
    public function tryLoginname($aid,$loginname)
    {
        $said = Admins::where('loginname', $loginname)->where('enable',1)->value('aid');
        return (is_null($said)||$said==$aid);
    }
    public function genLoginname()
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
    public function modPassword($aid,$password)
    {
        return Admins::where('aid', $aid)->update(['password'=>md5($password)]);
    }
    public function tryPassword($aid,$password)
    {
        $pass = Admins::where('aid', $aid)->value('password');
        return $pass==md5($password);
    }
    public function login($loginname,$password)
    {
        $aid = Admins::where('loginname',$loginname)->where('password',md5($password))->where('enable',1)->value('aid');
        if(is_null($aid))
            return false;
        $dbCTS = new CookieToSession();
        $ret = $dbCTS->setAid($aid);
        return true;
    }
}
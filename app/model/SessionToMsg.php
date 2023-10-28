<?php
namespace app\model;
use think\Model;
use think\facade\Session;

class SessionToMsg extends Model
{
    protected $name = 'Contests';//只是占位用的
    protected $pk = 'cid';
    public function setMsg($area,$code,$title,$content)
    {
        Session::set('msg'.$area.'has',true);
        Session::set('msg'.$area.'code',$code);
        Session::set('msg'.$area.'title',$title);
        Session::set('msg'.$area.'content',$content);
    }
    public function hasMsg($area)
    {
        if(Session::has('msg'.$area.'has'))return true;
        return false;
    }
    public function getMsg($area)
    {
        if(Session::has('msg'.$area.'has'))
        {
            $code=Session::get('msg'.$area.'code');
            $title=Session::get('msg'.$area.'title');
            $content=Session::get('msg'.$area.'content');
            return json_encode(['code'=>$code,'title'=>$title,'content'=>$content]);
        }
        return 'false';
    }
    public function delMsg($area)
    {
        if(Session::has('msg'.$area.'has'))
        {
            Session::delete('msg'.$area.'has');
            Session::delete('msg'.$area.'code');
            Session::delete('msg'.$area.'title');
            Session::delete('msg'.$area.'content');
            return true;
        }
        return false;
    }
    public function popMsg($area)
    {
        if(Session::has('msg'.$area.'has'))
        {
            $code=Session::get('msg'.$area.'code');
            $title=Session::get('msg'.$area.'title');
            $content=Session::get('msg'.$area.'content');
            Session::delete('msg'.$area.'has');
            Session::delete('msg'.$area.'code');
            Session::delete('msg'.$area.'title');
            Session::delete('msg'.$area.'content');
            return json_encode(['code'=>$code,'title'=>$title,'content'=>$content]);
        }
        return 'false';
    }
    public function retMsg($code,$title,$content)
    {
        return json_encode(['code'=>$code,'title'=>$title,'content'=>$content]);
    }
}
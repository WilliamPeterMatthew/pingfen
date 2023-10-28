<?php
namespace app\model;
use think\Model;

use app\model\GlobalSettings;
use app\model\ContestJudgers;

class QuickResponseCode extends Model
{
    protected $name = 'Contests';//只是占位用的
    protected $pk = 'cid';
    public function genPic($cid,$jid,$level='L',$size=4)
    {
        require("../extend/PhpQrcode/phpqrcode.php");
        $qRcode = new \QRcode();
        $dbGS=new GlobalSettings();
        $url = $dbGS->getSetting('url');
        $dbCJ=new ContestJudgers();
        $judger=$dbCJ->getJudger($cid,$jid);
        $logincode=$judger['logincode'];
        $data = $url.'/contest'.$cid.'/judger/loginapilogin?logincode='.$logincode;
        $qRcode->png($data,false,$level,$size);
        $imagestring = base64_encode(ob_get_contents());
        ob_end_clean();
        return 'data:image/png;base64,'.$imagestring;
    }
    public function getUrl($cid,$jid)
    {
        $dbGS=new GlobalSettings();
        $url = $dbGS->getSetting('url');
        $dbCJ=new ContestJudgers();
        $judger=$dbCJ->getJudger($cid,$jid);
        $logincode=$judger['logincode'];
        return $url.'/contest'.$cid.'/judger/loginapilogin?logincode='.$logincode;
    }
}
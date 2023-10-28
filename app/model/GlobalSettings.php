<?php
namespace app\model;
use think\facade\Env;
use think\Model;

class GlobalSettings extends Model
{
    public function getSetting($setting_name)
    {
        $ret = GlobalSettings::where('setting_name', 'site_'.$setting_name)->value('setting_value');
        if(is_null($ret)||strlen($ret)==0)
            $ret=Env::get('site.'.$setting_name);
        return $ret;
    }
    public function getGlobalSetting($setting_name)
    {
        return GlobalSettings::where('setting_name', $setting_name)->value('setting_value');
    }
    public function setGlobalSetting($setting_name,$setting_value)
    {
        $ret=GlobalSettings::where('setting_name', $setting_name)->update([
            'setting_value'=>$setting_value
        ]);
        return true;
    }
    public function getEnvSetting($setting_name)
    {
        return Env::get($setting_name);
    }
}
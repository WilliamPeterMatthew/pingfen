<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\facade\Route;
Route::pattern([
    'aid'   => '\d+',
    'cid'   => '\d+',
    'pid'   => '\d+',
    'jid'   => '\d+',
    'action'=> '[a-zA-Z]+',
]);

Route::rule('contest:cid/moderator/indexapi:action', '/moderator/indexapi');
Route::rule('contest:cid/moderator', '/moderator/index');

Route::rule('contest:cid/show/rank', '/show/rank');
Route::rule('contest:cid/show/view', '/show/view');
Route::rule('contest:cid/show/group', '/show/group');
Route::rule('contest:cid/show/background', '/show/background');
Route::rule('contest:cid/show/indexapi:action', '/show/indexapi');
Route::rule('contest:cid/show', '/show/index');

Route::rule('contest:cid/admin/controlapi:action', '/control/controlapi');
Route::rule('contest:cid/admin/control', '/control/control');
Route::rule('contest:cid/admin/settingsapi:action', '/control/settingsapi');
Route::rule('contest:cid/admin/settings', '/control/settings');
Route::rule('contest:cid/admin/pointsapi:action', '/control/pointsapi');
Route::rule('contest:cid/admin/points', '/control/points');
Route::rule('contest:cid/admin/judgerqrcodeapi:action:jid', '/control/judgerqrcodeapi');
Route::rule('contest:cid/admin/judgerqrcode:jid', '/control/judgerqrcode');
Route::rule('contest:cid/admin/judgerqrcode', '/control/judgerqrcodere');//redirect
Route::rule('contest:cid/admin/judgereditapi:action:jid', '/control/judgereditapi');
Route::rule('contest:cid/admin/judgeredit:jid', '/control/judgeredit');
Route::rule('contest:cid/admin/judgeredit', '/control/judgereditre');//redirect
Route::rule('contest:cid/admin/judgeraddapi:action', '/control/judgeraddapi');
Route::rule('contest:cid/admin/judgeradd', '/control/judgeradd');
Route::rule('contest:cid/admin/judgersapi:action', '/control/judgersapi');
Route::rule('contest:cid/admin/judgers', '/control/judgers');
Route::rule('contest:cid/admin/playereditapi:action:pid', '/control/playereditapi');
Route::rule('contest:cid/admin/playeredit:pid', '/control/playeredit');
Route::rule('contest:cid/admin/playeredit', '/control/playereditre');//redirect
Route::rule('contest:cid/admin/playeraddapi:action', '/control/playeraddapi');
Route::rule('contest:cid/admin/playeradd', '/control/playeradd');
Route::rule('contest:cid/admin/playersapi:action', '/control/playersapi');
Route::rule('contest:cid/admin/players', '/control/players');
Route::rule('contest:cid/admin/profileapi:action', '/control/profileapi');
Route::rule('contest:cid/admin/profile', '/control/profile');
Route::rule('contest:cid/admin/logoutapi:action', '/control/logoutapi');
Route::rule('contest:cid/admin/logout', '/control/logout');
Route::rule('contest:cid/admin/loginapi:action', '/control/loginapi');
Route::rule('contest:cid/admin/login', '/control/login');
Route::rule('contest:cid/admin/errorapi:action', '/control/errorapi');
Route::rule('contest:cid/admin/error', '/control/error');
Route::rule('contest:cid/admin', '/control/index');

Route::rule('contest:cid/judger/end', '/judger/end');
Route::rule('contest:cid/judger/scoreapi:action', '/judger/scoreapi');
Route::rule('contest:cid/judger/score', '/judger/score');
Route::rule('contest:cid/judger/waitapi:action', '/judger/waitapi');
Route::rule('contest:cid/judger/wait', '/judger/wait');
Route::rule('contest:cid/judger/logoutapi:action', '/judger/logoutapi');
Route::rule('contest:cid/judger/logout', '/judger/logout');
Route::rule('contest:cid/judger/loginapi:action', '/judger/loginapi');
Route::rule('contest:cid/judger/login', '/judger/login');
Route::rule('contest:cid/judger/errorapi:action', '/judger/errorapi');
Route::rule('contest:cid/judger/error', '/judger/error');
Route::rule('contest:cid/judger', '/judger/index');

Route::rule('contest:cid', '/index/contest');
Route::rule('contest', '/index/contestre');//redirect

Route::rule('admin/settingsapi:action', '/admin/settingsapi');
// Route::rule('admin/settings', '/admin/settings');
Route::rule('admin/contesteditapi:action:cid', '/admin/contesteditapi');
Route::rule('admin/contestedit:cid', '/admin/contestedit');
Route::rule('admin/contestedit', '/admin/contesteditre');//redirect
Route::rule('admin/contestaddapi:action', '/admin/contestaddapi');
// Route::rule('admin/contestadd', '/admin/contestadd');
Route::rule('admin/contestsapi:action', '/admin/contestsapi');
// Route::rule('admin/contests', '/admin/contests');
Route::rule('admin/admineditapi:action:aid', '/admin/admineditapi');
Route::rule('admin/adminedit:aid', '/admin/adminedit');
Route::rule('admin/adminedit', '/admin/admineditre');//redirect
Route::rule('admin/adminaddapi:action', '/admin/adminaddapi');
// Route::rule('admin/adminadd', '/admin/adminadd');
Route::rule('admin/adminsapi:action', '/admin/adminsapi');
// Route::rule('admin/admins', '/admin/admins');
Route::rule('admin/profileapi:action', '/admin/profileapi');
// Route::rule('admin/profile', '/admin/profile');
Route::rule('admin/logoutapi:action', '/admin/logoutapi');
// Route::rule('admin/logout', '/admin/logout');
Route::rule('admin/loginapi:action', '/admin/loginapi');
// Route::rule('admin/login', '/admin/login');
Route::rule('admin/errorapi:action', '/admin/errorapi');
// Route::rule('admin/error', '/admin/error');
// Route::rule('admin/index', '/admin/index');

Route::rule('usage', '/index/usage');
Route::rule('visitwrong', '/index/visitwrong');
// Route::rule('index/index', '/index/index');

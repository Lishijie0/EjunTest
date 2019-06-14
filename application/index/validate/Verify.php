<?php
namespace app\index\validate;
use think\Validate;
class Verify extends Validate
{
    //规则
    protected $rule = [
        'vipname'  =>  'require',   //会员名称
        'vipno'  =>  'require',       //卡号
        'viptypeid' =>  'require',   //卡类型
        'mobil' =>  'require',   //手机号
        'birthday' =>  'require',   //出生日期
        'empid' =>  'require',   //V顾问
        'sex' =>  'require',   //性别 1男 2 女
    ];
    //返回信息
    protected $message = [
        'vipname.require'  =>  '请填写用户名',
        'vipno.require'  =>  '请填写卡号',
        'viptypeid.require'  =>  '请选择卡类型',
        'mobil.require'  =>  '请填写手机号',
        'birthday.require'  =>  '请填写出生日期',
        'empid.require'  =>  '请选择V顾问',
        'sex.require'  =>  '请选择性别',
    ];
    // 场景
    protected $scene = [
        'add'   =>  ['vipname','vipno','viptypeid','mobil','birthday','empid','sex'],
        // 'edit'  =>  ['email'],
    ];    
}
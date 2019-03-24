<?php

namespace Kapi\Oauth;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Kapi\Model\ApiModel;
use Kapi\Model\OauthModel;
use Exception;
use Config;

class Koauth
{
  protected $isPaginate = false;
  protected $pagiNum;

  public function paginate($num=15)
  {
    $this->isPaginate = true;
    $this->pagiNum = $num;
    return $this;
  }

  public function checkApp(){
    // check api app
    $ApiApps = ApiModel::where('key',\Request::get(Config::get('kapi.oauth.key') ? Config::get('kapi.oauth.key') : 'kapi_key'))
                          ->where('secret',\Request::get(Config::get('kapi.oauth.secret') ? Config::get('kapi.oauth.secret') : 'kapi_secret'))
                          //->where('redirect_uri',\Request::get(Config::get('kapi.oauth.redirect') ? Config::get('kapi.oauth.redirect') : 'kapi_redirect'))
                          ->where('block',false)
                          ->where('active',true)
                          ->where(function ($query){
                            if (Config::get('kapi.approval')) {
                              $query->where('approve',true);
                            }
                          })
                          ->first();

    if(empty($ApiApps)){
      $App = [
        "status" => false
      ];
      return $App;
    } else {
      $App = [
        "appID" => $ApiApps['id'],
        "redirect" => $ApiApps['redirect_uri'],
        "status" => true
      ];
      return $App;
    }
  }

  public function appInfo(){
    // check api app
    $appInfo = ApiModel::where('key',\Request::get(Config::get('kapi.oauth.key') ? Config::get('kapi.oauth.key') : 'kapi_key'))
                          ->where('secret',\Request::get(Config::get('kapi.oauth.secret') ? Config::get('kapi.oauth.secret') : 'kapi_secret'))
                          ->where('block',false)
                          ->where('active',true)
                          ->where(function ($query){
                            if (Config::get('kapi.approval')) {
                              $query->where('approve',true);
                            }
                          })
                          ->first();

    return $appInfo;
  }



  public function checkOauth($appID,$authUserID){
    // check api app
    $OauthCheck = OauthModel::where('kapi_app_id',$appID)
                          ->where('auth_user',$authUserID)
                          ->first();
    if(empty($OauthCheck)){
      return false;
    }
    return true;
  }

  public function checkOSecret(){
    // check api app
    $OauthSecret = ApiModel::where('osecret',\Request::get('osecret'))
                          ->first();
    if($OauthSecret['osecret'] !== null){
      return true;
    }
    return false;
  }

  public function acceptApp($appID,$authUserID){
    if ($this->checkOauth($appID,$authUserID) === false) {
      $oauth = new OauthModel;
      $oauth->kapi_app_id = $appID;
      $oauth->auth_user = $authUserID;
      $oauth->save();
      return $oauth;
    }
    return "already accepted this app";
  }
  // encrypt data which use guzzle query
  public function sendEncrypToken($authUserID){
    return Crypt::encryptString($authUserID);
  }
  // decrypToken from guzzle query data
  public function decrypToken($token){
    try {
      return Crypt::decryptString($token);
    } catch (\Exception $e) {
      return "token structure error ";
    }

  }

  public function authUserOauth($authUserID){
    if($this->ispaginate){
      $authUserOauth = OauthModel::where('auth_user',$authUserID)
                                  ->paginate($this->pagiNum);
      return $authUserOauth;
    }
    $authUserOauth = OauthModel::where('auth_user',$authUserID)->get();
    return $authUserOauth;
  }

  public function revoke($oauthID,$authUserID){
    $authUserOauth = OauthModel::where('id',$oauthID)
                                ->where('auth_user',$authUserID)
                                ->first();
    $authUserOauth->delete();
    return "done";
  }
  public function revokeAll($authUserID){
    $authUserOauth = OauthModel::where('auth_user',$authUserID)->delete();
    return "done";
  }
}

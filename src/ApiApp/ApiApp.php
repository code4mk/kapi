<?php

namespace Kapi\ApiApp;

use Keygen\Keygen;
use Kapi\Model\ApiModel;
use Illuminate\Http\Request;
use Config;

class ApiApp
{
  protected $appType;
  protected $isOutput = false;
  protected $isPaginate = false;
  protected $pagiNum;


  public function __construct()
  {
    $this->oauth('app');

  }
  public function oauth($status = 'oauth')
  {
    $this->appType = $status;
    return $this;
  }

  public function output()
  {
    $this->isOutput = true;
    return $this;
  }
  public function paginate($num=15)
  {
    $this->isPaginate = true;
    $this->pagiNum = $num;
    return $this;
  }

  public function create($userID, $appImage = '')
  {
    $apiApp = new ApiModel;
    $apiApp->guard = 'user';
    $apiApp->user = $userID;
    $apiApp->app_type = $this->appType;
    $apiApp->key = Keygen::bytes(20)->hex()->generate();
    $apiApp->secret = Keygen::bytes(30)->hex()->generate();
    $apiApp->name = \Request::get('name');
    $apiApp->image = $appImage;
    $apiApp->description = \Request::get('description');
    $apiApp->uri = \Request::get('uri');
    $apiApp->redirect_uri = \Request::get('redirect_uri');

    if (Config::get('kapi.approval') === false) {
      $apiApp->approve = true;
    }
    $apiApp->save();

    if ($this->appType === 'oauth') {
      $apiApp->osecret = Keygen::bytes(30)->hex()->generate() . 'kapi' . $apiApp->id;
      $apiApp->save();
    }

    if($this->isOutput === true){
      return $apiApp;
    }

  }

  public function owner(){
    $ApiApps = ApiModel::where('key',\Request::header(Config::get('kapi.app.key') ? Config::get('kapi.app.key') : 'kapi_key'))
                          ->where('secret',\Request::header(Config::get('kapi.app.secret') ? Config::get('kapi.app.secret') : 'kapi_secret'))
                          ->where('block',false)
                          ->where('active',true)
                          ->where(function ($query){
                            if (Config::get('kapi.approval')) {
                              $query->where('approve',true);
                            }
                          })
                          ->first();
    $owner = [
      "id" => $ApiApps['user'],
      "guard" => $ApiApps['user']
    ];
    return $owner;
  }

  public function update($appID, $authUserID, $appImage = '')
  {
    $data = [
      'name' => \Request::get('name'),
      'description' => \Request::get('description'),
      'image' => $appImage,
      'uri' => \Request::get('uri'),
      'redirect_uri' => \Request::get('redirect_uri')
    ];

    $apiApp = ApiModel::where('id',$appID)
                        ->where('user',$authUserID)
                        ->update($data);
  }

  public function showAuthUserApps($userID)
  {
    if($this->isPaginate){
      $UserApiApp = ApiModel::where('user',$userID)
                          ->where('app_type','app')
                          ->orderBy('id', 'desc')
                          ->paginate($this->pagiNum);
      return $UserApiApp;
    }
    $UserApiApp = ApiModel::where('user',$userID)
                        ->where('app_type','app')
                        ->get();
    return $UserApiApp;
  }

  public function showAuthUserOauth($userID)
  {
    if($this->isPaginate){
      $UserApiAppOauth = ApiModel::where('user',$userID)
                          ->where('app_type','oauth')
                          ->paginate($this->pagiNum);
      return $UserApiAppOauth;
    }
    $UserApiAppOauth = ApiModel::where('user',$userID)
                        ->where('app_type','oauth')
                        ->get();
    return $UserApiAppOauth;
  }

  public function getAppInfo($appID, $authUserID)
  {
    $apiApp = ApiModel::where('id',$appID)
                      ->where('user',$authUserID)
                      ->first();
    return $apiApp;
  }

  public function destroyApp($appID, $authUserID)
  {
    $apiApp = ApiModel::where('id',$appID)
                      ->where('user',$authUserID)
                      ->delete();
  }

  public function refreshSecret($appID, $authUserID)
  {
    $data = [
      'secret' => Keygen::bytes(30)->hex()->generate()
    ];

    $apiApp = ApiModel::where('id',$appID)
                      ->where('user',$authUserID)
                      ->update($data);
  }

  public function activeApp($appID, $authUserID)
  {
    $apiApp = ApiModel::where('id',$appID)
                      ->where('user',$authUserID)
                      ->update(['active'=> true]);
  }

  public function deactiveApp($appID, $authUserID)
  {
    $apiApp = ApiModel::where('id',$appID)
                      ->where('user',$authUserID)
                      ->update(['active'=> false]);
  }

  public function apiAppApproval()
  {
    if($this->isPaginate){
      $apps = ApiModel::where('approve',false)
                        ->where('app_type','app')
                        ->paginate($this->pagiNum);
      return $apps;
    }
    $apps = ApiModel::where('approve',false)
                      ->where('app_type','app')
                      ->get();
    return $apps;
  }

  public function apiOauthApproval()
  {
    if($this->isPaginate){
      $apps = ApiModel::where('approve',false)
                        ->where('app_type','oauth')
                        ->paginate($this->pagiNum);
      return $apps;
    }
    $apps = ApiModel::where('approve',false)
                      ->where('app_type','oauth')
                      ->get();
    return $apps;
  }

  public function apiInfo($appID)
  {
    $apiApp = ApiModel::where('id',$appID)
                      ->first();
    return $apiApp;
  }


  public function apiAllApps()
  {
    if($this->isPaginate){
      $apiApps = ApiModel::where('app_type','app')
                          ->paginate($this->pagiNum);
      return $apiApps;
    }
    $apiApps = ApiModel::where('app_type','app')->get();
    return $apiApps;
  }

  public function apiAllOauth()
  {
    if($this->isPaginate){
      $apiOauth = ApiModel::where('app_type','oauth')
                          ->paginate($this->pagiNum);
      return $apiOauth;
    }
    $apiOauth = ApiModel::where('app_type','oauth')->get();
    return $apiOauth;
  }

  public function apiApprove($appID)
  {
    $apiApp = ApiModel::find($appID);
    $apiApp->approve = true;
    $apiApp->save();
  }

  public function apiBlock($appID)
  {
    $apiApp = ApiModel::find($appID);
    $apiApp->block = true;
    $apiApp->save();
  }

  public function apiUnblock($appID)
  {
    $apiApp = ApiModel::find($appID);
    $apiApp->block = false;
    $apiApp->save();
  }

  public function apiBlockAppLists()
  {
    if($this->isPaginate){
      $apiApps = ApiModel::where('block',true)
                          ->where('app_type','app')
                          ->paginate($this->pagiNum);
      return $apiApps;
    }
    $apiApps = ApiModel::where('block',true)
                        ->where('app_type','app')
                        ->get();
    return $apiApps;
  }

  public function apiBlockOauthLists()
  {
    if($this->isPaginate){
      $apiOauth = ApiModel::where('block',true)
                          ->where('app_type','oauth')
                          ->paginate($this->pagiNum);
      return $apiOauth;
    }
    $apiOauth = ApiModel::where('block',true)
                        ->where('app_type','oauth')
                        ->get();
    return $apiOauth;
  }

  public function apiDestroy($appID)
  {
    $apiApp = ApiModel::where('id',$appID)
                      ->delete();
  }

  public function apiAppLive()
  {
    if($this->isPaginate){
      $apiApps = ApiModel::where('approve',true)
                          ->where('app_type','app')
                          ->paginate($this->pagiNum);
      return $apiApps;
    }
    $apiApps = ApiModel::where('approve',true)
                        ->where('app_type','app')
                        ->get();
    return $apiApps;
  }

  public function apiAppDeactivated()
  {
    if($this->isPaginate){
      $apiApps = ApiModel::where('approve',true)
                          ->where('active',false)
                          ->paginate($this->pagiNum);
      return $apiApps;
    }
    $apiApps = ApiModel::where('approve',true)
                        ->where('active',false)
                        ->get();
    return $apiApps;
  }

  public function apiOauthLive()
  {
    if($this->isPaginate){
      $apiOauth = ApiModel::where('approve',true)
                          ->where('app_type','oauth')
                          ->paginate($this->pagiNum);
      return $apiOauth;
    }
    $apiOauth = ApiModel::where('approve',true)
                        ->where('app_type','oauth')
                        ->get();
    return $apiOauth;
  }

  public function apiOauthDeactivated()
  {
    if($this->isPaginate){
      $apiOauth = ApiModel::where('approve',true)
                          ->where('active',false)
                          ->paginate($this->pagiNum);
      return $apiOauth;
    }
    $apiOauth = ApiModel::where('approve',true)
                        ->where('active',false)
                        ->get();
    return $apiOauth;
  }
}

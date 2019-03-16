# kapi
Create API app system

# Kapi methods

~ developer(user)

* create($userID,$image)
* update($appID,$authUserID,$image)
* showAuthUserApps(userID)
* showAuthUserOauth($userID)
* getAppInfo($appID,$authUserID)
* destroyApp($appID,$authUserID)
* refreshSecret($appID,$authUserID)
* activeApp($appID,$authUserID)
* deactiveApp($appID,$authUserID)

~ admin/super-admin/owner

* apiAppApproval()
* apiOauthApproval()
* apiInfo($appID)
* apiAllApps() `only api apps`
* apiAllOauth() `oauth lists`
* apiApprove($appID)
* apiBlock($appID)
* apiUnblock($appID)
* apiBlockAppLists()`api app`
* apiBlockOauthLists() `oauth`
* apiDestroy($appID)
* apiAppLive `api app`
* apiOauthLive `oauth`

# Koauth method

* checkApp()
* appInfo()
* checkOauth($appID,$authUserID)
* acceptApp($appID,$authUserID)
* sendEncrypToken($authUserID)
* decrypToken($token)
* authUserOauth($authUserID)
* revoke($oauthID,$authUserID)
* revokeAll($authUserID)

<?php

return [
  // single page access data by owner (spa) / or ajax Middleware
  "spa" => [
    // barear token use only for admin/owner
    // barearTokenName that will be your <header> name (axios , guzzle)
    "barearTokenName" => "Authorization",
    // define your desired secret token
    // set header in axios or guzzle . example <Authorization =  bearer barearToken>
    "barearToken" => "mysecret12"
  ],

  // api purpose <header> <axios|guzzle>
  "app" => [
    "key" => "client-key",
    "secret" => "client-secret"
  ],

  // oauth token purpose <query> <axios|guzzle>
  "oauth" => [
    "key" => "client-key",
    "secret" => "client-secret",
    "redirect" => "redirect-uri"
  ],
  //  approval (boolean value)
  "approval" => false,
];

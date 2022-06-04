<?php
class Config {
  public static $hostname = 'quill.dev';
  public static $base_url = 'http://quill.dev/';
  public static $gaid = '';

  // MySQL (default)
  public static $dbType = 'mysql';
  public static $dbHost = '127.0.0.1';
  public static $dbName = 'quill';
  public static $dbUsername = 'quill';
  public static $dbPassword = '';

  // Sqlite
  // public static $dbType = 'sqlite';
  // public static $dbFilePath = './example.db';

  public static $jwtSecret = 'xxx';

  public static $twitterClientID = '';
  public static $twitterClientSecret = '';

  public static $atlasToken = '';
  public static $mapTileURL = '';

  // https://developers.google.com/maps/documentation/javascript/get-api-key
  public static $googleMapsAPIKey = '';
}


<?php
/**
 * MySQLに接続しデータを追加する
 *
 */

// 以下のコメントを外すと実行時エラーが発生した際にエラー内容が表示される
// ini_set('display_errors', 'On');
// ini_set('error_reporting', E_ALL);

//-------------------------------------------------
// 初期値
//-------------------------------------------------
define('DEFAULT_LV', 1);
define('DEFAULT_EXP', 1);
define('DEFAULT_MONEY', 3000);

//-------------------------------------------------
// 準備
//-------------------------------------------------
require_once('../common.php');

// 実行したいSQL
$sql1 = 'INSERT INTO User(lv, exp, money) VALUES(:lv, :exp, :money)';
$sql2 = 'SELECT LAST_INSERT_ID() as id';  // AUTO INCREMENTした値を取得する


//-------------------------------------------------
// SQLを実行
//-------------------------------------------------
try{
	startDB();

  //-------------------------------------------------
  // 新規にレコードを作成
  //-------------------------------------------------
  // SQL準備
  // 実行
  exeDB($sql1, [[':lv', DEFAULT_LV], [':exp',   DEFAULT_EXP], [':money', DEFAULT_MONEY]]);

  //-------------------------------------------------
  // AUTO INCREMENTした値を取得
  //-------------------------------------------------
  // SQL準備
  // 実行
  // 実行結果から1レコード取ってくる
  $buff = getDB($sql2);
}
catch( PDOException $e ) {
  sendResponse(false, 'Database error: '.$e->getMessage());  // 本来エラーメッセージはサーバ内のログへ保存する(悪意のある人間にヒントを与えない)
  exit(1);
}

//-------------------------------------------------
// 実行結果を返却
//-------------------------------------------------
resultResponse($buff, $buff['id'], 'Database error: can not fetch LAST_INSERT_ID()');

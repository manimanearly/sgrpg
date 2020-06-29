<?php
/**
 * ガチャAPI
 *
 */

// 以下のコメントを外すと実行時エラーが発生した際にエラー内容が表示される
// ini_set('display_errors', 'On');
// ini_set('error_reporting', E_ALL);

//-------------------------------------------------
// 定数
//-------------------------------------------------
// キャラクター数
define('MAX_CHARA', 10);

// ガチゃ1回の価格
define('GACHA_PRICE', 300);


//-------------------------------------------------
// 引数を受け取る
//-------------------------------------------------
// ユーザーIDを受け取る
$uid = isset($_GET['uid'])?  $_GET['uid']:null;

// Validation
if( ($uid === null) || (!is_numeric($uid)) ){
  sendResponse(false, 'Invalid uid');
  exit(1);
}

//-------------------------------------------------
// 準備
//-------------------------------------------------
require_once('../common.php');

//---------------------------
// 実行したいSQL
//---------------------------
// Userテーブルから所持金を取得
$sql1 = 'SELECT money FROM User WHERE id=:userid';

// Userテーブルの所持金を減産
$sql2 = 'UPDATE User SET money=money-:price WHERE id=:userid';

// UserCharaテーブルにキャラクターを追加
$sql3 = 'INSERT INTO UserChara(user_id, chara_id) VALUES(:userid,:charaid)';

// Charaテーブルから1レコード取得
$sql4 = 'SELECT * FROM Chara WHERE id=:charaid';


//-------------------------------------------------
// SQLを実行
//-------------------------------------------------
try{
	startDB();

	// トランザクション開始
	transactionStart();

	//---------------------------
	// 所持金の残高を取得
	//---------------------------
	$buff = getDB($sql1, [[':userid', $uid]]);

	// ユーザーが存在しているかチェック
	if( $buff === false ){
		sendResponse(false, 'Not Found User');
    	exit(1);
	}

	// 残高が足りているかチェック
	if( $buff['money'] < GACHA_PRICE ){
		sendResponse(false, 'The balance is not enough');
		exit(1);
	}

	//---------------------------
	// 残高を減らす
	//---------------------------
	exeDB($sql2, [[':price', GACHA_PRICE], [':userid', $uid]]);

	//---------------------------
	// キャラクターを抽選
	//---------------------------
	$charaid = random_int(1, MAX_CHARA);

	//---------------------------
	// キャラクターを所有
	//---------------------------
	exeDB($sql3, [[':userid', $uid], [':charaid', $charaid]]);

	//---------------------------
	// キャラクター情報を取得
	//---------------------------
	$chara = getDB($sql4, [[':charaid', $charaid]]);

	//---------------------------
	// トランザクション確定
	//---------------------------
	transactionEnd();
}
catch( PDOException $e ) {
	// ロールバック
	transactionBack();

	sendResponse(false, 'Database error: '.$e->getMessage());  // 本来エラーメッセージはサーバ内のログへ保存する(悪意のある人間にヒントを与えない)
	exit(1);
}

//-------------------------------------------------
// 実行結果を返却
//-------------------------------------------------
resultResponse($buff, $chara, 'System Error');

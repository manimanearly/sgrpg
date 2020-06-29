<?php

$dbh = null;
$sth = null;

function startDB(){
	global $dbh;
	$dsn  = 'mysql:dbname=sgrpg;host=127.0.0.1';  // 接続先を定義
	$user = 'senpai';      // MySQLのユーザーID
	$pw   = 'indocurry';   // MySQLのパスワード

	$dbh = new PDO($dsn, $user, $pw);   // 接続
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  // エラーモード
}

function exeDB($sql, $bind=[]){
	global $dbh, $sth;
	// SQL準備
	$sth = $dbh->prepare($sql);
	if(count($bind) > 0){
		foreach($bind as $vals)
			$sth->bindValue($vals[0], $vals[1], PDO::PARAM_INT);
	}

	// 実行
	$sth->execute();
}

function getDB($sql, $bind=[]){
	global $sth;
	exeDB($sql, $bind);
	return $sth->fetch(PDO::FETCH_ASSOC);
}

/**
 * 実行結果をJSON形式で返却する
 *
 * @param boolean $status
 * @param array   $value
 * @return void
 */
function sendResponse($status, $value=[]){
	header('Content-type: application/json');
 	echo json_encode([
		'status' => $status,
		'result' => $value
	]);
}

function resultResponse($buff, $trueresult, $falseresult){
	if( $buff === false ){
		sendResponse(false, $falseresult);
	}
	// データを正常に取得
	else{
		sendResponse(true, $trueresult);
	}
}

function transactionStart(){
	global $dbh;
	$dbh->beginTransaction();
}

function transactionEnd(){
	global $dbh;
	$dbh->commit();
}

function transactionBack(){
	global $dbh;
	$dbh->rollBack();
}

?>

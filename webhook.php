<?php

/**
 * Copyright 2016 LINE Corporation
 *
 * LINE Corporation licenses this file to you under the Apache License,
 * version 2.0 (the "License"); you may not use this file except in compliance
 * with the License. You may obtain a copy of the License at:
 *
 *   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

require_once('./LINEBotTiny.php');
require_once('./vendor/autoload.php');

use Carbon\Carbon;

$channelAccessToken = 'hy0mK6UwxH+2ooPGZpUr9mGknMfgOcRYZPwL7B5b5AMQGWVVdluOSsjveDfPlsu8riTNl45G0mJcpngdQ+oldHdqyVLSa15qR6H0naE+l5q7yf3ETynO7bV0PqmvZzcvg0fJEn5D/UFnkSo/QHv+rQdB04t89/1O/w1cDnyilFU=';
$channelSecret = 'c5f2a1532069465224d1183ac4256997';

$client = new LINEBotTiny($channelAccessToken, $channelSecret);
foreach ($client->parseEvents() as $event) {
	switch ($event['type']) {
		case 'message':
			$message = $event['message'];
			switch ($message['type']) {
				case 'text':
					if ($message['text'] == '電話') {
						$client->replyMessage([
							'replyToken' => $event['replyToken'],
							'messages' => [
								[
								'type' => 'text',
								'text' => "ポエム \n お店の電話番号は046-231-7422になります。"
								]
							]
						]);
					} elseif ($message['text'] == 'アクセス') {
						$client->replyMessage([
							'replyToken' => $event['replyToken'],
							'messages' => [
								[
								'type' => 'location',
								'title' => 'ポエム',
								'address' => '〒243-0401 神奈川県海老名市東柏ケ谷２丁目２４−３０',
								'latitude' => 35.471467,
								'longitude' => 139.426090,
								]
							]
						]);
					} elseif ($message['text'] == '予約') {
						$time = Carbon::now('Asia/Tokyo')->format('Y-m-d\TH:i');
						$timeAddOneMonth = Carbon::now('Asia/Tokyo')->addMonth()->format('Y-m-d\TH:i');
						$client->replyMessage([
							'replyToken' => $event['replyToken'],
							'messages' => [
								[
								"type" => "template",
								"altText" => "予約されますか？",
									"template" => [ 
										"type" => "confirm",
										"text" => "予約しますか? \n (1か月先までご予約できます。)",
										"actions" => [
											[	
											"type" => "datetimepicker",
											"label" => "日時選択へ",
											"data" => "datestring",
											"initial" => $time, 
											"max" => $timeAddOneMonth, 
											"min" => $time, 
											"mode" => "datetime",
											],
											[	
											"type" => "postback",
											"label" => "予約しない",
											"data" => "action=back",
											]	
										]
									]
								]
							]
						]);
					} else {
						$time = Carbon::now('Asia/Tokyo')->format('Y-m-d\TH:i');
						$client->replyMessage([
							'replyToken' => $event['replyToken'],
							'messages' => [
								[
								'type' => 'text',
								'text' => $time, 
								]
							]	
						]);
					}
					break;
				case 'location':
					$client->replyMessage([
						'replyToken' => $event['replyToken'],
						'messages' => [
							[
							'type' => 'location',
							'title' => 'ポエム',
							'address' => '神奈川県海老名市東柏ケ谷２丁目２４−３０',
							'latitude' => 35.471467,
							'longitude' => 139.426090,
							]
						]
					]);
					break;
				default:
					$client->replyMessage([
						'replyToken' => $event['replyToken'],
						'messages' => [
							[
							'type' => 'text',
							'text' => '位置情報かメッセージしか対応していません。'
							]
						]
					]);	
					break;
			}
		case 'postback':
			$postback = null;
			$postback = $event['postback']['data'];
			$datetime = $event['postback']['params']['datetime'];
			if ($postback == 'datestring') {
				$client->replyMessage([
					'replyToken' => $event['replyToken'],
					'messages' => [ 
						[
						'type' => 'template',
						'altText' => '人数選択',
							'template' => [
								'type' => 'buttons',
								'text' => "人数を選択してください\n(*5人以上のご予約は直接お電話にてお願いします。)",
								'actions' => array(
									array(	
									'type' => 'postback',
									'label' => '１人',
									'data' => 'numberOfPeople=1&date=' . $datetime,
									),
									array(	
									'type' => 'postback',
									'label' => '２人',
									'data' => 'numberOfPeople=2&date=' . $datetime,
									),
									array(	
									'type' => 'postback',
									'label' => '３人',
									'data' => 'numberOfPeople=3&date=' . $datetime,
									),
									array(	
									'type' => 'postback',
									'label' => '４人',
									'data' => 'numberOfPeople=4&date=' . $datetime,
									),
								),
							]
						]
					]	
				]);
			} elseif ($postback == 'action=back') {
				$client->replyMessage([
					'replyToken' => $event['replyToken'],
					'messages' => [
						[
						'type' => 'text',
						'text' => '何もしませんでした',
						]
					]
				]);
			} elseif ($postback == 'action=first') {
				$client->replyMessage([
					'replyToken' => $event['replyToken'],
					'messages' => [
						[
						'type' => 'text',
						'text' => '予約を取りやめました。',
						]
					]
				]);
			} elseif (substr($postback, 0, 14) === 'numberOfPeople')  {
				parse_str($postback, $data);
				$date = $data['date'];	
				$datetime = str_replace('T', '', $date);	
				$datetimeFormat = Carbon::parse($datetime)->format('Y年m月d日　H時i分');	
				$numberOfPeople = $data['numberOfPeople'];
				switch ($numberOfPeople) {
					case 1:
						$client->replyMessage([
							'replyToken' => $event['replyToken'],
							'messages' => [
								[
								'type' => 'template',
								'altText' => '予約確認中',
									'template' => [
										'type' => 'confirm',
										'text' => $datetimeFormat . 'から' . $numberOfPeople . '人様のご予約でよろしいでしょうか。',
										'actions' => array( 
											array(	
											'type' => 'postback',
											'label' => 'はい',
											'data' => 'reservation&confirmNumberOfPeople=' . $numberOfPeople . '&confirmDatetime=' . $datetimeFormat,
											),
											array(
											'type' => 'postback',
											'label' => 'いいえ',
											'data' => 'action=first',
											)
										)
									]
								]
							]
						]);
						break;
					case 2:
						$client->replyMessage([
							'replyToken' => $event['replyToken'],
							'messages' => [
								[
								'type' => 'template',
								'altText' => '予約確認中',
									'template' => [
										'type' => 'confirm',
										'text' => $datetimeFormat . 'から' . $numberOfPeople . '人様のご予約でよろしいでしょうか。',
										'actions' => array( 
											array(	
											'type' => 'postback',
											'label' => 'はい',
											'data' => 'reservation&confirmNumberOfPeople=' . $numberOfPeople . '&confirmDatetime=' . $datetimeFormat,
											),
											array(
											'type' => 'postback',
											'label' => 'いいえ',
											'data' => 'action=first',
											)
										)
									]
								]
							]
						]);
						break;
					case 3:
						$client->replyMessage([
							'replyToken' => $event['replyToken'],
							'messages' => [
								[
								'type' => 'template',
								'altText' => '予約確認中',
									'template' => [
										'type' => 'confirm',
										'text' => $datetimeFormat . 'から' . $numberOfPeople . '人様のご予約でよろしいでしょうか。',
										'actions' => array( 
											array(	
											'type' => 'postback',
											'label' => 'はい',
											'data' => 'reservation&confirmNumberOfPeople=' . $numberOfPeople . '&confirmDatetime=' . $datetimeFormat,
											),
											array(
											'type' => 'postback',
											'label' => 'いいえ',
											'data' => 'action=first',
											)
										)
									]
								]
							]
						]);
						break;
					case 4:
						$client->replyMessage([
							'replyToken' => $event['replyToken'],
							'messages' => [
								[
								'type' => 'template',
								'altText' => '予約確認中',
									'template' => [
										'type' => 'confirm',
										'text' => $datetimeFormat . 'から' . $numberOfPeople . '人様のご予約でよろしいでしょうか。',
										'actions' => array( 
											array(	
											'type' => 'postback',
											'label' => 'はい',
											'data' => 'reservation&confirmNumberOfPeople=' . $numberOfPeople . '&confirmDatetime=' . $datetimeFormat,
											),
											array(
											'type' => 'postback',
											'label' => 'いいえ',
											'data' => 'action=first',
											)
										)
									]
								]
							]
						]);
						break;
					default:
						$client->replyMessage([
							'replyToken' => $event['replyToken'],
							'messages' => [
								[
								'type' => 'text',
								'text' => "不正なアクセスです。最初からお願いします。",
								]
							]
						]);
						break;
				}
			} elseif (strstr($postback, 'reservation', true))  {
				parse_str($postback, $data);
				$date = $data['date'];	
				$datetime = str_replace('T', '', $date);	
				$numberOfPeople = $data['numberOfPeople'];
				try {
				    $dbh = new PDO('mysql:host=localhost; dbname=procir_nagai127;charset=utf8;', 'nagai127', '2c7vcx1u47');
				} catch (PDOException $e) {
					$client->replyMessage([
						'replyToken' => $event['replyToken'],
						'messages' => [
							[
							'type' => 'text',
							'text' => 'データベースとの接続に失敗しました。',
							]
						]
					]);
				        exit;
				}
				$sql = "INSERT INTO bookings (booking_number, booking_date, created_at) VALUES (:booking_number, :booking_date, now())";
				$stmt = $dbh->prepare($sql);
				$params = array(':booking_number' => $numberOfPeople, ':booking_date' => $date);	
				$stmt->execute($params);
				$client->replyMessage([
					'replyToken' => $event['replyToken'],
					'messages' => [
						[
						'type' => 'text',
						'text' => '予約完了しました',
						]
					]
				]);

			} else {
				$client->replyMessage([
					'replyToken' => $event['replyToken'],
					'messages' => [
						[
						'type' => 'text',
						'text' => $postback . "\n条件分岐間違ってね？",
						]
					]
				]);
			}
			break;
		default:
			error_log('Unsupported event type: ' . $event['type']);
			break;
	}
}

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

$channelAccessToken = 'hy0mK6UwxH+2ooPGZpUr9mGknMfgOcRYZPwL7B5b5AMQGWVVdluOSsjveDfPlsu8riTNl45G0mJcpngdQ+oldHdqyVLSa15qR6H0naE+l5q7yf3ETynO7bV0PqmvZzcvg0fJEn5D/UFnkSo/QHv+rQdB04t89/1O/w1cDnyilFU=';
$channelSecret = 'c5f2a1532069465224d1183ac4256997';
$postback = null;

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
								'text' => 'お店の電話番号は046-231-7422になります。'
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
						$client->replyMessage([
							'replyToken' => $event['replyToken'],
							'messages' => [
								[
								"type" => "template",
								"altText" => "予約されますか？",
									"template" => [ 
										"type" => "confirm",
										"text" => "予約しますか?",
										"actions" => [
											[	
											"type" => "datetimepicker",
											"label" => "日時選択へ",
											"data" => "datestring",
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
						$client->replyMessage([
							'replyToken' => $event['replyToken'],
							'messages' => [
								[
								'type' => 'text',
								'text' => error_log(print_r($event, true)), 
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
			$postback = $event['postback']['data'];
			if ($postback == 'datestring') {
				$client->replyMessage([
					'replyToken' => $event['replyToken'],
					'messages' => [ 
						[
						'type' => 'template',
						'altText' => '人数選択',
							'template' => [
								'type' => 'buttons',
								'text' => '人数を選択してください',
								'actions' => array(
									array(	
									'type' => 'postback',
									'label' => '２人',
									'data' => 'numberOfPeople=2',
									),
									array(	
									'type' => 'postback',
									'label' => '３人',
									'data' => 'numberOfPeople=3',
									),
									array(	
									'type' => 'postback',
									'label' => '４人',
									'data' => 'numberOfPeople=4',
									),
									array(	
									'type' => 'postback',
									'label' => '５人以上',
									'data' => 'numberOfPeople=5',
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
			}
			break;
		default:
			error_log('Unsupported event type: ' . $event['type']);
			break;
	}
};

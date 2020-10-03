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
								'text' => '07021550888'
								]
							]
						]);
					} elseif ($message['text'] == 'アクセス') {
						$client->replyMessage([
							'replyToken' => $event['replyToken'],
							'messages' => [
								[
								'type' => 'text',
								'text' => 'さがみ野だよ'
								]
							]
						]);
					} elseif ($message['text'] == '予約') {
						$client->replyMessage([
							'replyToken' => $event['replyToken'],
							'messages' => [
						/*		"type": "template",
								"altText": "this is a confirm template",
								"template": {
									"type": "confirm",
									"text": "Are you sure?",
									"actions": [
										{
										"type": "message",
										"label": "Yes",
										"text": "yes"
										},
										{
										"type": "message",
										"label": "No",
										"text": "no"
										}
									]
								}
*/
								[
								"type": "text",
								"text": "予約で大丈夫ですか？",
								]
							]
						]);
					} else {
						$client->replyMessage([
							'replyToken' => $event['replyToken'],
							'messages' => [
								[
								'type' => 'text',
								'text' =>$message['text'], 
								]
							]	
						]);
					}
					break;
				case 'location':
					$lat = $message['latitude'];
					$lng = $message['longitude'];
					$hotUrl = 'http://webservice.recruit.co.jp/hotpepper/gourmet/v1/?key=e8a77202e4c8db72&lat=' . $lat . '&lng=' . $lng . '&range=5&order=4&format=json';
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $hotUrl);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					$result = curl_exec($ch);
					curl_close($ch);
					$data = json_decode($result, true);
					if (!empty($data['results']['shop'])) {
						$shopInfo0 = $data['results']['shop']['0']['name'] . "\n" . 'URL:' . $data['results']['shop']['0']['urls']['pc'];
						$shopInfo1 = $data['results']['shop']['1']['name'] . "\n" . 'URL:' . $data['results']['shop']['1']['urls']['pc'];
						$shopInfo2 = $data['results']['shop']['2']['name'] . "\n" . 'URL:' . $data['results']['shop']['2']['urls']['pc'];
						$shopInfo3 = $data['results']['shop']['3']['name'] . "\n" . 'URL:' . $data['results']['shop']['3']['urls']['pc'];
						$shopInfo4 = $data['results']['shop']['4']['name'] . "\n" . 'URL:' . $data['results']['shop']['4']['urls']['pc'];
						$client->replyMessage([
							'replyToken' => $event['replyToken'],
							'messages' => [
								[
								'type' => 'text',
								'text' => $shopInfo0
								],
								[
								'type' => 'text',
								'text' => $shopInfo1
								],
								[
								'type' => 'text',
								'text' => $shopInfo2
								],
								[
								'type' => 'text',
								'text' => $shopInfo3
								],
								[
								'type' => 'text',
								'text' => $shopInfo4
								]
							]
						]);
					} else {
						$client->replyMessage([
							'replyToken' => $event['replyToken'],
							'messages' => [
								[
								'type' => 'text',
								'text' => '近くにお店がありません'
								]
							]
						]);	
					}
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
		default:
			error_log('Unsupported event type: ' . $event['type']);
			break;
	}
};

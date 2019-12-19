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
$a = 'アカウント凍結しちゃうヨ';

$client = new LINEBotTiny($channelAccessToken, $channelSecret);
foreach ($client->parseEvents() as $event) {
    switch ($event['type']) {
        case 'message':
            $message = $event['message'];
            switch ($message['type']) {
                case 'text':
                    $client->replyMessage([
                        'replyToken' => $event['replyToken'],
                        'messages' => [
                            [
                                'type' => 'text',
                                //'text' => $message['id']
                                'text' => $a
                            ]
                        ]
                    ]);
                    break;
                case 'location':
                    //$data = $event['location'];
                    $lat = $message['latitude'];
                    $lng = $message['longitude'];
                    $hotUrl = 'https://webservise.recruit.co.jp/hotpepper/gourmet/v1/?key=e8a77202e4c8db72&lat=' . $lat . '&lng=' . $lng . '&range=5&order=4';

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $hotUrl);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $result = curl_exec($ch);
                    curl_close($ch);
                    $client->replyMessage([
                        'replyToken' => $event['replyToken'],
                        'messages' => [
                             [
                                'type' => 'text',
                                'text' => $result
                             ]
                         ]
                     ]);
                    break;
                default:
                    error_log('Unsupported message type: ' . $message['type']);
                    break;
                }
        default:
            error_log('Unsupported event type: ' . $event['type']);
            break;
    }
};

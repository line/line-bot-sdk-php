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

namespace LINE\Parser\Tests;

use LINE\Constants\MessageContentProviderType;
use LINE\Constants\StickerResourceType;
use LINE\Constants\ThingsEventType;
use LINE\Constants\ThingsResultContentType;
use LINE\Parser\EventRequestParser;
use LINE\Webhook\Model\GroupSource;
use LINE\Webhook\Model\RoomSource;
use LINE\Webhook\Model\UserSource;
use PHPUnit\Framework\TestCase;

class EventRequestParserTest extends TestCase
{
    private static $json = <<<JSON
    {
     "destination":"U0123456789abcdef0123456789abcd",
     "events":[
      {
       "type":"message",
       "mode":"active",
       "timestamp":12345678901234,
       "source":{
        "type":"user",
        "userId":"userid"
       },
       "webhookEventId":"testwebhookeventid",
       "deliveryContext":{
        "isRedelivery":false
       },
       "replyToken":"replytoken",
       "message":{
        "id":"contentid",
        "type":"text",
        "text":"message (love)",
        "emojis": [
          {
            "index": 8,
            "length": 6,
            "productId": "5ac1bfd5040ab15980c9b435",
            "emojiId": "001"
          }
        ]
       }
      },
      {
       "type":"message",
       "mode":"active",
       "timestamp":12345678901234,
       "source":{
        "type":"group",
        "groupId":"groupid"
       },
       "webhookEventId":"testwebhookeventid",
       "deliveryContext":{
        "isRedelivery":false
       },
       "replyToken":"replytoken",
       "message":{
        "id":"contentid",
        "type":"image",
        "contentProvider":{
         "type":"external",
         "originalContentUrl":"https://example.com/test.jpg",
         "previewImageUrl":"https://example.com/test-preview.jpg"
        },
        "imageSet": {
          "id": "E005D41A7288F41B65593ED38FF6E9834B046AB36A37921A56BC236F13A91855",
          "index": 1,
          "total": 1
        }
       }
      },
      {
       "type":"message",
       "mode":"active",
       "timestamp":12345678901234,
       "source":{
        "type":"group",
        "groupId":"groupid",
        "userId":"userid"
       },
       "webhookEventId":"testwebhookeventid",
       "deliveryContext":{
        "isRedelivery":false
       },
       "replyToken":"replytoken",
       "message":{
        "id":"contentid",
        "type":"audio",
        "duration":10000,
        "contentProvider":{
         "type":"external",
         "originalContentUrl":"https://example.com/test.m4a"
        }
       }
      },
      {
       "type":"message",
       "mode":"active",
       "timestamp":12345678901234,
       "source":{
        "type":"room",
        "roomId":"roomid"
       },
       "webhookEventId":"testwebhookeventid",
       "deliveryContext":{
        "isRedelivery":false
       },
       "replyToken":"replytoken",
       "message":{
        "id":"contentid",
        "type":"video",
        "duration":10000,
        "contentProvider":{
         "type":"external",
         "originalContentUrl":"https://example.com/test.mp4",
         "previewImageUrl":"https://example.com/test-preview.jpg"
        }
       }
      },
      {
       "type":"message",
       "mode":"active",
       "timestamp":12345678901234,
       "source":{
        "type":"room",
        "roomId":"roomid",
        "userId":"userid"
       },
       "webhookEventId":"testwebhookeventid",
       "deliveryContext":{
        "isRedelivery":false
       },
       "replyToken":"replytoken",
       "message":{
        "id":"contentid",
        "type":"audio",
        "duration":10000,
        "contentProvider":{
         "type":"external",
         "originalContentUrl":"https://example.com/test.m4a"
        }
       }
      },
      {
       "type":"message",
       "mode":"active",
       "timestamp":12345678901234,
       "source":{
        "type":"user",
        "userId":"userid"
       },
       "webhookEventId":"testwebhookeventid",
       "deliveryContext":{
        "isRedelivery":false
       },
       "replyToken":"replytoken",
       "message":{
        "id":"contentid",
        "type":"location",
        "title":"label",
        "address":"tokyo",
        "latitude":-34.12,
        "longitude":134.23
       }
      },
      {
       "type":"message",
       "mode":"active",
       "timestamp":12345678901234,
       "source":{
        "type":"user",
        "userId":"userid"
       },
       "webhookEventId":"testwebhookeventid",
       "deliveryContext":{
        "isRedelivery":false
       },
       "replyToken":"replytoken",
       "message":{
        "id":"contentid",
        "type":"location",
        "address":"tokyo",
        "latitude":-34.12,
        "longitude":134.23
       }
      },
      {
       "type":"message",
       "mode":"active",
       "timestamp":12345678901234,
       "source":{
        "type":"user",
        "userId":"userid"
       },
       "webhookEventId":"testwebhookeventid",
       "deliveryContext":{
        "isRedelivery":false
       },
       "replyToken":"replytoken",
       "message":{
        "id":"contentid",
        "type":"location",
        "title":"label",
        "latitude":-34.12,
        "longitude":134.23
       }
      },
      {
       "type":"message",
       "mode":"active",
       "timestamp":12345678901234,
       "source":{
        "type":"user",
        "userId":"userid"
       },
       "webhookEventId":"testwebhookeventid",
       "deliveryContext":{
        "isRedelivery":false
       },
       "replyToken":"replytoken",
       "message":{
        "id":"contentid",
        "type":"sticker",
        "packageId":"1",
        "stickerId":"2",
        "stickerResourceType":"STATIC"
       }
      },
      {
       "type":"message",
       "mode":"active",
       "timestamp":12345678901234,
       "source":{
        "type":"user",
        "userId":"userid"
       },
       "webhookEventId":"testwebhookeventid",
       "deliveryContext":{
        "isRedelivery":false
       },
       "replyToken":"replytoken",
       "message":{
        "id":"contentid",
        "type":"sticker",
        "packageId":"12287",
        "stickerId":"738839",
        "stickerResourceType":"MESSAGE",
        "keywords": ["Anticipation","Sparkle","Straight face","Staring","Thinking"],
        "text": "Let's\\nhang out\\nthis weekend!"
       }
      },
      {
       "type":"follow",
       "mode":"active",
       "timestamp":12345678901234,
       "source":{
        "type":"user",
        "userId":"userid"
       },
       "webhookEventId":"testwebhookeventid",
       "deliveryContext":{
        "isRedelivery":false
       },
       "replyToken":"replytoken"
      },
      {
       "type":"unfollow",
       "mode":"active",
       "timestamp":12345678901234,
       "source":{
        "type":"user",
        "userId":"userid"
       },
       "webhookEventId":"testwebhookeventid",
       "deliveryContext":{
        "isRedelivery":false
       }
      },
      {
       "type":"join",
       "mode":"active",
       "timestamp":12345678901234,
       "source":{
        "type":"user",
        "userId":"userid"
       },
       "webhookEventId":"testwebhookeventid",
       "deliveryContext":{
        "isRedelivery":false
       },
       "replyToken":"replytoken"
      },
      {
       "type":"leave",
       "mode":"active",
       "timestamp":12345678901234,
       "source":{
        "type":"user",
        "userId":"userid"
       },
       "webhookEventId":"testwebhookeventid",
       "deliveryContext":{
        "isRedelivery":false
       }
      },
      {
       "type":"postback",
       "mode":"active",
       "timestamp":12345678901234,
       "source":{
        "type":"user",
        "userId":"userid"
       },
       "webhookEventId":"testwebhookeventid",
       "deliveryContext":{
        "isRedelivery":false
       },
       "replyToken":"replytoken",
       "postback":{
        "data":"postback"
       }
      },
      {
       "type":"beacon",
       "mode":"active",
       "timestamp":12345678901234,
       "source":{
        "type":"user",
        "userId":"userid"
       },
       "webhookEventId":"testwebhookeventid",
       "deliveryContext":{
        "isRedelivery":false
       },
       "replyToken":"replytoken",
       "beacon":{
        "hwid":"bid",
        "type":"enter",
        "dm":"1234567890abcdef"
       }
      },
      {
       "type":"__unknown__",
       "mode":"active",
       "timestamp":12345678901234,
       "source":{
        "type":"user",
        "userId":"userid"
       },
       "webhookEventId":"testwebhookeventid",
       "deliveryContext":{
        "isRedelivery":false
       }
      },
      {
       "type":"__unknown__",
       "mode":"active",
       "timestamp":12345678901234,
       "source":{
        "type":"__unknown__"
       },
       "webhookEventId":"testwebhookeventid",
       "deliveryContext":{
        "isRedelivery":false
       }
      },
      {
       "type":"message",
       "mode":"active",
       "timestamp":12345678901234,
       "source":{
        "type":"user",
        "userId":"userid"
       },
       "webhookEventId":"testwebhookeventid",
       "deliveryContext":{
        "isRedelivery":false
       },
       "replyToken":"replytoken",
       "message":{
        "id":"contentid",
        "type":"__unknown__"
       }
      },
      {
       "replyToken": "replytoken",
       "type": "message",
       "mode":"active",
       "timestamp": 1462629479859,
       "source": {
        "type": "user",
        "userId": "userid"
       },
       "webhookEventId":"testwebhookeventid",
       "deliveryContext":{
        "isRedelivery":false
       },
       "message": {
        "id": "325708",
        "type": "file",
        "fileName": "file.txt",
        "fileSize": 2138
       }
      },
      {
       "replyToken": "replytoken",
       "type": "postback",
       "mode":"active",
       "timestamp": 1501234567890,
       "source": {
        "type": "user",
        "userId": "userid"
       },
       "webhookEventId":"testwebhookeventid",
       "deliveryContext":{
        "isRedelivery":false
       },
       "postback": {
        "data":"postback",
        "params": {
          "date": "2013-04-01"
        }
       }
      },
      {
       "replyToken": "replytoken",
       "type": "postback",
       "mode":"active",
       "timestamp": 1501234567890,
       "source": {
        "type": "user",
        "userId": "userid"
       },
       "webhookEventId":"testwebhookeventid",
       "deliveryContext":{
        "isRedelivery":false
       },
       "postback": {
        "data":"postback",
        "params": {
          "time": "10:00"
        }
       }
      },
      {
       "replyToken": "replytoken",
       "type": "postback",
       "mode":"active",
       "timestamp": 1501234567890,
       "source": {
        "type": "user",
        "userId": "userid"
       },
       "webhookEventId":"testwebhookeventid",
       "deliveryContext":{
        "isRedelivery":false
       },
       "postback": {
        "data":"postback",
        "params": {
          "datetime": "2013-04-01T10:00"
        }
       }
      },
      {
       "replyToken": "replytoken",
       "type": "accountLink",
       "mode":"standby",
       "timestamp": 1501234567890,
       "source": {
        "type": "user",
        "userId": "userid"
       },
       "webhookEventId":"testwebhookeventid",
       "deliveryContext":{
        "isRedelivery":false
       },
       "link": {
        "result": "ok",
        "nonce": "1234567890abcdefghijklmnopqrstuvwxyz"
       }
      },
      {
       "replyToken": "replytoken",
       "type": "accountLink",
       "mode":"active",
       "timestamp": 1501234567890,
       "source": {
        "type": "user",
        "userId": "userid"
       },
       "webhookEventId":"testwebhookeventid",
       "deliveryContext":{
        "isRedelivery":false
       },
       "link": {
        "result": "failed",
        "nonce": "1234567890abcdefghijklmnopqrstuvwxyz"
       }
      },
      {
       "type":"memberJoined",
       "mode":"active",
       "timestamp":12345678901234,
       "source":{
        "type":"group",
        "groupId":"groupid"
       },
       "webhookEventId":"testwebhookeventid",
       "deliveryContext":{
        "isRedelivery":false
       },
       "joined": {
        "members": [
         {
          "type": "user",
          "userId": "U4af4980629..."
         },
         {
          "type": "user",
          "userId": "U91eeaf62d9..."
         }
        ]
       },
       "replyToken":"replytoken"
      },
      {
       "type":"memberLeft",
       "mode":"active",
       "timestamp":12345678901234,
       "source":{
        "type":"group",
        "groupId":"groupid"
       },
       "webhookEventId":"testwebhookeventid",
       "deliveryContext":{
        "isRedelivery":false
       },
       "left": {
        "members": [
         {
          "type": "user",
          "userId": "U4af4980629..."
         },
         {
          "type": "user",
          "userId": "U91eeaf62d9..."
         }
        ]
       }
      },
      {
       "type":"things",
       "mode":"active",
       "timestamp":12345678901234,
       "source":{
        "type":"user",
        "userId":"userid"
       },
       "webhookEventId":"testwebhookeventid",
       "deliveryContext":{
        "isRedelivery":false
       },
       "replyToken":"replytoken",
       "things":{
        "deviceId":"t2c449c9d1",
        "type": "link"
       }
      },
      {
       "type":"things",
       "mode":"active",
       "timestamp":12345678901234,
       "source":{
        "type":"user",
        "userId":"userid"
       },
       "webhookEventId":"testwebhookeventid",
       "deliveryContext":{
        "isRedelivery":false
       },
       "replyToken":"replytoken",
       "things":{
        "deviceId":"t2c449c9d1",
        "type": "unlink"
       }
      },
      {
       "type": "things",
       "mode":"active",
       "timestamp":12345678901234,
       "source":{
        "type":"user",
        "userId":"userid"
       },
       "webhookEventId":"testwebhookeventid",
       "deliveryContext":{
        "isRedelivery":false
       },
       "replyToken":"replytoken",
       "things": {
        "type": "scenarioResult",
        "deviceId": "t2c449c9d1",
        "result": {
         "scenarioId": "dummy_scenario_id",
         "revision": 2,
         "startTime": 1547817845950,
         "endTime": 1547817845952,
         "resultCode": "success",
         "bleNotificationPayload": "AQ==",
         "actionResults": [
          {
           "type": "binary",
           "data": "/w=="
          }
         ]
        }
       }
      },
      {
       "type":"message",
       "mode":"active",
       "timestamp":12345678901234,
       "source":{
        "type":"user",
        "userId":"userid"
       },
       "webhookEventId":"testwebhookeventid",
       "deliveryContext":{
        "isRedelivery":false
       },
       "replyToken":"replytoken",
       "message":{
        "id":"contentid",
        "type":"text",
        "text":"message without emoji"
       }
      },
      {
       "type":"unsend",
       "timestamp":12345678901234,
       "source":{
        "type": "group",
        "groupId":"groupid",
        "userId":"userid"
       },
       "webhookEventId":"testwebhookeventid",
       "deliveryContext":{
        "isRedelivery":false
       },
       "unsend": {
            "messageId": "325708"
       }
      },
      {
       "type":"videoPlayComplete",
       "timestamp":12345678901234,
       "source":{
        "type": "group",
        "groupId":"groupid",
        "userId":"userid"
       },
       "webhookEventId":"testwebhookeventid",
       "deliveryContext":{
        "isRedelivery":false
       },
       "videoPlayComplete": {
        "trackingId": "track_id"
       },
       "replyToken":"replytoken"
      },
      {
       "replyToken": "nHuyWiB7yP5Zw52FIkcQobQuGDXCTA",
       "type": "message",
       "mode": "active",
       "timestamp": 1462629479859,
       "source": {
        "type": "user",
        "userId": "U4af4980629..."
       },
       "webhookEventId":"testwebhookeventid",
       "deliveryContext":{
        "isRedelivery":false
       },
       "message": {
        "id": "325708",
        "type": "text",
        "text": "@example Hello, world! (love)",
        "mention": {
         "mentionees": [
          {
           "type": "user",
           "index": 0,
           "length": 8,
           "userId": "U0123456789abcd0123456789abcdef"
          }
         ]
        }
       }
      },
      {
       "replyToken": "nHuyWiB7yP5Zw52FIkcQobQuGDXCTA",
       "type": "message",
       "mode": "active",
       "timestamp": 1462629479859,
       "source": {
        "type": "user",
        "userId": "U0123456789abcd0123456789abcdef"
       },
       "webhookEventId":"testwebhookeventid",
       "deliveryContext":{
        "isRedelivery":false
       },
       "message": {
        "id": "325708",
        "type": "text",
        "text": "@example message without mentionee userId",
        "mention": {
         "mentionees": [
          {
           "type": "all",
           "index": 0,
           "length": 8
          }
         ]
        }
       }
      },
      {
       "replyToken": "nHuyWiB7yP5Zw52FIkcQobQuGDXCTA",
       "type": "message",
       "mode": "active",
       "timestamp": 1462629479859,
       "source": {
        "type": "user",
        "userId": "U0123456789abcd0123456789abcdef"
       },
       "webhookEventId":"testwebhookeventid",
       "deliveryContext":{
        "isRedelivery":false
       },
       "message": {
        "id": "325708",
        "type": "text",
        "text": "message without mention"
       }
      },
      {
       "type":"message",
       "mode":"active",
       "timestamp":12345678901234,
       "source":{
        "type":"group",
        "groupId":"groupid"
       },
       "webhookEventId":"testwebhookeventid",
       "deliveryContext":{
        "isRedelivery":false
       },
       "replyToken":"replytoken",
       "message":{
        "id":"contentid",
        "type":"image",
        "contentProvider":{
         "type":"external",
         "originalContentUrl":"https://example.com/test.jpg",
         "previewImageUrl":"https://example.com/test-preview.jpg"
        }
       }
      },
      {
       "type":"message",
       "mode":"active",
       "timestamp":12345678901234,
       "source":{
        "type":"group",
        "groupId":"groupid"
       },
       "webhookEventId":"testwebhookeventid",
       "deliveryContext":{
        "isRedelivery":false
       },
       "replyToken":"replytoken",
       "message":{
        "id":"contentid",
        "type":"image",
        "contentProvider":{
         "type":"external",
         "originalContentUrl":"https://example.com/test.jpg",
         "previewImageUrl":"https://example.com/test-preview.jpg"
        },
        "imageSet": {
          "id": "E005D41A7288F41B65593ED38FF6E9834B046AB36A37921A56BC236F13A91855"
        }
       }
      },
      {
       "type":"message",
       "mode":"active",
       "timestamp":12345678901234,
       "source":{
        "type":"user",
        "userId":"userid"
       },
       "webhookEventId":"testwebhookeventid",
       "deliveryContext":{
        "isRedelivery":true
       },
       "replyToken":"replytoken",
       "message":{
        "id":"contentid",
        "type":"text",
        "text":"message (love)",
        "emojis": [
          {
            "index": 8,
            "length": 6,
            "productId": "5ac1bfd5040ab15980c9b435",
            "emojiId": "001"
          }
        ]
       }
      },
      {
        "type": "activated",
        "timestamp": 12345678901234,
        "mode": "active",
        "source": {
          "type": "user",
          "userId": "userid"
        },
        "chatControl": {
          "expireAt": 1462629479860
        },
        "webhookEventId": "testwebhookeventid",
        "deliveryContext": {
          "isRedelivery": false
        }
      },
      {
        "type": "deactivated",
        "timestamp": 12345678901234,
        "mode": "active",
        "source": {
          "type": "user",
          "userId": "userid"
        },
        "webhookEventId": "testwebhookeventid",
        "deliveryContext": {
          "isRedelivery": false
        }
      },
      {
        "type": "botSuspended",
        "timestamp": 12345678901234,
        "mode": "active",
        "webhookEventId": "testwebhookeventid",
        "deliveryContext": {
          "isRedelivery": false
        }
      },
      {
        "type": "botResumed",
        "timestamp": 12345678901234,
        "mode": "active",
        "webhookEventId": "testwebhookeventid",
        "deliveryContext": {
          "isRedelivery": false
        }
      },
      {
        "type": "delivery",
        "timestamp": 12345678901234,
        "mode": "active",
        "source": {
          "type": "user",
          "userId": "userid"
        },
        "delivery": {
          "data": "deliverydata"
        },
        "webhookEventId": "testwebhookeventid",
        "deliveryContext": {
          "isRedelivery": false
        }
      },
      {
        "type": "module",
        "timestamp": 12345678901234,
        "mode": "active",
        "module": {
          "type": "attached",
          "botId": "botid",
          "scopes": ["a", "b"]
        },
        "webhookEventId": "testwebhookeventid",
        "deliveryContext": {
          "isRedelivery": false
        }
      },
      {
        "type": "module",
        "timestamp": 12345678901234,
        "mode": "active",
        "module": {
          "type": "detached",
          "botId": "botid",
          "reason": "bot deleted"
        },
        "webhookEventId": "testwebhookeventid",
        "deliveryContext": {
          "isRedelivery": false
        }
      }
     ]
    }
    JSON;

    /**
     * @throws \LINE\Parser\Exception\InvalidEventRequestException
     * @throws \LINE\Parser\Exception\InvalidEventSourceException
     * @throws \LINE\Parser\Exception\InvalidSignatureException
     */
    public function testParseEventRequest()
    {
        $parsedEvents = EventRequestParser::parseEventRequest(
            body: self::$json,
            channelSecret: 'testsecret',
            signature: self::getSignature('testsecret'),
        );
        $eventArrays = json_decode(self::$json, true)["events"];

        $this->assertEquals($parsedEvents->getDestination(), 'U0123456789abcdef0123456789abcd');

        $events = $parsedEvents->getEvents();
        $this->assertEquals(count($events), 46);

        {
            // text
            $event = $events[0];
            $source = $event->getSource();
            $this->assertEquals(12345678901234, $event->getTimestamp());
            $this->assertEquals('active', $event->getMode());
            $this->assertTrue($source instanceof UserSource);
            $this->assertEquals('userid', $source->getUserId());
            $this->assertEquals('testwebhookeventid', $event->getWebhookEventId());
            $this->assertFalse($event->getDeliveryContext()->getIsRedelivery());
            $this->assertJsonStringEqualsJsonString(json_encode($eventArrays[0]), $event->__toString());
            $this->assertInstanceOf(\LINE\Webhook\Model\MessageEvent::class, $event);
            $this->assertInstanceOf(\LINE\Webhook\Model\TextMessageContent::class, $event->getMessage());
            $this->assertEquals('replytoken', $event->getReplyToken());
            $this->assertEquals('contentid', $event->getMessage()->getId());
            $this->assertEquals('text', $event->getMessage()->getType());
            $this->assertEquals('message (love)', $event->getMessage()->getText());
            $emojiInfo = $event->getMessage()->getEmojis()[0];
            $this->assertEquals(8, $emojiInfo->getIndex());
            $this->assertEquals(6, $emojiInfo->getLength());
            $this->assertEquals('5ac1bfd5040ab15980c9b435', $emojiInfo->getProductId());
            $this->assertEquals('001', $emojiInfo->getEmojiId());
        }

        {
            // image
            $event = $events[1];
            $source = $event->getSource();
            $this->assertTrue($source instanceof GroupSource);
            $this->assertEquals('groupid', $source->getGroupId());
            $this->assertEquals(null, $source->getUserId());
            $this->assertEquals('testwebhookeventid', $event->getWebhookEventId());
            $this->assertFalse($event->getDeliveryContext()->getIsRedelivery());
            $this->assertJsonStringEqualsJsonString(json_encode($eventArrays[1]), $event->__toString());
            $this->assertInstanceOf(\LINE\Webhook\Model\MessageEvent::class, $event);
            $this->assertInstanceOf(\LINE\Webhook\Model\ImageMessageContent::class, $event->getMessage());
            $this->assertEquals('replytoken', $event->getReplyToken());
            $this->assertEquals('image', $event->getMessage()->getType());
            $this->assertEquals('contentid', $event->getMessage()->getId());
            $this->assertTrue($event->getMessage()->getContentProvider()->getType() == MessageContentProviderType::EXTERNAL);
            $this->assertEquals(
                'https://example.com/test.jpg',
                $event->getMessage()->getContentProvider()->getOriginalContentUrl()
            );
            $this->assertEquals(
                'https://example.com/test-preview.jpg',
                $event->getMessage()->getContentProvider()->getPreviewImageUrl()
            );
            $this->assertEquals(
                'E005D41A7288F41B65593ED38FF6E9834B046AB36A37921A56BC236F13A91855',
                $event->getMessage()->getImageSet()->getId()
            );
            $this->assertEquals(1, $event->getMessage()->getImageSet()->getIndex());
            $this->assertEquals(1, $event->getMessage()->getImageSet()->getTotal());
        }

        {
            // audio (group event & it has user ID)
            $event = $events[2];
            $source = $event->getSource();
            $this->assertTrue($source instanceof GroupSource);
            $this->assertEquals('groupid', $source->getGroupId());
            $this->assertEquals('testwebhookeventid', $event->getWebhookEventId());
            $this->assertFalse($event->getDeliveryContext()->getIsRedelivery());
            $this->assertJsonStringEqualsJsonString(json_encode($eventArrays[2]), $event->__toString());
            $this->assertInstanceOf(\LINE\Webhook\Model\MessageEvent::class, $event);
            $this->assertInstanceOf(\LINE\Webhook\Model\AudioMessageContent::class, $event->getMessage());
            $this->assertEquals('userid', $source->getUserId());
            $this->assertEquals('replytoken', $event->getReplyToken());
            $this->assertEquals('audio', $event->getMessage()->getType());
            $this->assertEquals('contentid', $event->getMessage()->getId());
            $this->assertEquals(10000, $event->getMessage()->getDuration());
            $this->assertTrue($event->getMessage()->getContentProvider()->getType() == MessageContentProviderType::EXTERNAL);
            $this->assertEquals(
                'https://example.com/test.m4a',
                $event->getMessage()->getContentProvider()->getOriginalContentUrl()
            );
        }

        {
            // video
            $event = $events[3];
            $source = $event->getSource();
            $this->assertTrue($source instanceof RoomSource);
            $this->assertEquals('roomid', $source->getRoomId());
            $this->assertEquals(null, $source->getUserId());
            $this->assertEquals('testwebhookeventid', $event->getWebhookEventId());
            $this->assertFalse($event->getDeliveryContext()->getIsRedelivery());
            $this->assertJsonStringEqualsJsonString(json_encode($eventArrays[3]), $event->__toString());
            $this->assertInstanceOf(\LINE\Webhook\Model\MessageEvent::class, $event);
            $this->assertInstanceOf(\LINE\Webhook\Model\VideoMessageContent::class, $event->getMessage());
            $this->assertEquals('replytoken', $event->getReplyToken());
            $this->assertEquals('video', $event->getMessage()->getType());
            $this->assertEquals(10000, $event->getMessage()->getDuration());
            $this->assertTrue($event->getMessage()->getContentProvider()->getType() == MessageContentProviderType::EXTERNAL);
            $this->assertEquals(
                'https://example.com/test.mp4',
                $event->getMessage()->getContentProvider()->getOriginalContentUrl()
            );
            $this->assertEquals(
                'https://example.com/test-preview.jpg',
                $event->getMessage()->getContentProvider()->getPreviewImageUrl()
            );
        }

        {
            // audio
            $event = $events[4];
            $source = $event->getSource();
            $this->assertTrue($source instanceof RoomSource);
            $this->assertEquals('userid', $source->getUserId());
            $this->assertEquals('testwebhookeventid', $event->getWebhookEventId());
            $this->assertFalse($event->getDeliveryContext()->getIsRedelivery());
            $this->assertInstanceOf(\LINE\Webhook\Model\MessageEvent::class, $event);
            $this->assertInstanceOf(\LINE\Webhook\Model\AudioMessageContent::class, $event->getMessage());
            $this->assertEquals('replytoken', $event->getReplyToken());
            $this->assertEquals('audio', $event->getMessage()->getType());
        }

        {
            // location
            $event = $events[5];
            $source = $event->getSource();
            $this->assertEquals('testwebhookeventid', $event->getWebhookEventId());
            $this->assertFalse($event->getDeliveryContext()->getIsRedelivery());
            $this->assertInstanceOf(\LINE\Webhook\Model\MessageEvent::class, $event);
            $this->assertInstanceOf(\LINE\Webhook\Model\LocationMessageContent::class, $event->getMessage());
            $this->assertEquals('replytoken', $event->getReplyToken());
            $this->assertEquals('location', $event->getMessage()->getType());
            $this->assertEquals('label', $event->getMessage()->getTitle());
            $this->assertEquals('tokyo', $event->getMessage()->getAddress());
            $this->assertEquals('-34.12', $event->getMessage()->getLatitude());
            $this->assertEquals('134.23', $event->getMessage()->getLongitude());
        }

        {
            // location when not set title attribute
            $event = $events[6];
            $source = $event->getSource();
            $this->assertEquals('testwebhookeventid', $event->getWebhookEventId());
            $this->assertFalse($event->getDeliveryContext()->getIsRedelivery());
            $this->assertInstanceOf(\LINE\Webhook\Model\MessageEvent::class, $event);
            $this->assertInstanceOf(\LINE\Webhook\Model\LocationMessageContent::class, $event->getMessage());
            $this->assertEquals('replytoken', $event->getReplyToken());
            $this->assertEquals('location', $event->getMessage()->getType());
            $this->assertNull($event->getMessage()->getTitle());
            $this->assertEquals('tokyo', $event->getMessage()->getAddress());
            $this->assertEquals('-34.12', $event->getMessage()->getLatitude());
            $this->assertEquals('134.23', $event->getMessage()->getLongitude());
        }

        {
            // location when not set address attribute
            $event = $events[7];
            $source = $event->getSource();
            $this->assertEquals('testwebhookeventid', $event->getWebhookEventId());
            $this->assertFalse($event->getDeliveryContext()->getIsRedelivery());
            $this->assertInstanceOf(\LINE\Webhook\Model\MessageEvent::class, $event);
            $this->assertInstanceOf(\LINE\Webhook\Model\LocationMessageContent::class, $event->getMessage());
            $this->assertEquals('replytoken', $event->getReplyToken());
            $this->assertEquals('location', $event->getMessage()->getType());
            $this->assertEquals('label', $event->getMessage()->getTitle());
            $this->assertNull($event->getMessage()->getAddress());
            $this->assertEquals('-34.12', $event->getMessage()->getLatitude());
            $this->assertEquals('134.23', $event->getMessage()->getLongitude());
        }

        {
            // sticker
            $event = $events[8];
            $source = $event->getSource();
            $this->assertEquals('testwebhookeventid', $event->getWebhookEventId());
            $this->assertFalse($event->getDeliveryContext()->getIsRedelivery());
            $this->assertInstanceOf(\LINE\Webhook\Model\MessageEvent::class, $event);
            $this->assertInstanceOf(\LINE\Webhook\Model\StickerMessageContent::class, $event->getMessage());
            $this->assertEquals('replytoken', $event->getReplyToken());
            $this->assertEquals('sticker', $event->getMessage()->getType());
            $this->assertEquals(1, $event->getMessage()->getPackageId());
            $this->assertEquals(2, $event->getMessage()->getStickerId());
            $this->assertEquals(StickerResourceType::STATIC_IMAGE, $event->getMessage()->getStickerResourceType());
            $this->assertEquals(null, $event->getMessage()->getKeywords());
            $this->assertEquals(null, $event->getMessage()->getText());
        }

        {
            // sticker with text
            $event = $events[9];
            $source = $event->getSource();
            $this->assertEquals('testwebhookeventid', $event->getWebhookEventId());
            $this->assertFalse($event->getDeliveryContext()->getIsRedelivery());
            $this->assertInstanceOf(\LINE\Webhook\Model\MessageEvent::class, $event);
            $this->assertInstanceOf(\LINE\Webhook\Model\StickerMessageContent::class, $event->getMessage());
            $this->assertEquals('replytoken', $event->getReplyToken());
            $this->assertEquals('sticker', $event->getMessage()->getType());
            $this->assertEquals(12287, $event->getMessage()->getPackageId());
            $this->assertEquals(738839, $event->getMessage()->getStickerId());
            $this->assertEquals(StickerResourceType::MESSAGE, $event->getMessage()->getStickerResourceType());
            $this->assertEquals(
                ['Anticipation', 'Sparkle', 'Straight face', 'Staring', 'Thinking'],
                $event->getMessage()->getKeywords()
            );
            $this->assertEquals("Let's\nhang out\nthis weekend!", $event->getMessage()->getText());
        }

        {
            // follow
            $event = $events[10];
            $source = $event->getSource();
            $this->assertEquals('testwebhookeventid', $event->getWebhookEventId());
            $this->assertFalse($event->getDeliveryContext()->getIsRedelivery());
            $this->assertInstanceOf(\LINE\Webhook\Model\FollowEvent::class, $event);
            $this->assertEquals('replytoken', $event->getReplyToken());
        }

        {
            // unfollow
            $event = $events[11];
            $source = $event->getSource();
            $this->assertEquals('testwebhookeventid', $event->getWebhookEventId());
            $this->assertFalse($event->getDeliveryContext()->getIsRedelivery());
            $this->assertInstanceOf(\LINE\Webhook\Model\UnfollowEvent::class, $event);
        }

        {
            // join
            $event = $events[12];
            $source = $event->getSource();
            $this->assertEquals('testwebhookeventid', $event->getWebhookEventId());
            $this->assertFalse($event->getDeliveryContext()->getIsRedelivery());
            $this->assertInstanceOf(\LINE\Webhook\Model\JoinEvent::class, $event);
            $this->assertEquals('replytoken', $event->getReplyToken());
        }

        {
            // leave
            $event = $events[13];
            $source = $event->getSource();
            $this->assertEquals('testwebhookeventid', $event->getWebhookEventId());
            $this->assertFalse($event->getDeliveryContext()->getIsRedelivery());
            $this->assertInstanceOf(\LINE\Webhook\Model\LeaveEvent::class, $event);
        }

        {
            // postback
            $event = $events[14];
            $source = $event->getSource();
            $this->assertEquals('testwebhookeventid', $event->getWebhookEventId());
            $this->assertFalse($event->getDeliveryContext()->getIsRedelivery());
            $this->assertInstanceOf(\LINE\Webhook\Model\PostbackEvent::class, $event);
            $this->assertEquals('replytoken', $event->getReplyToken());
            $this->assertEquals('postback', $event->getPostback()->getData());
            $this->assertEquals(null, $event->getPostback()->getParams());
        }

        {
            // beacon
            $event = $events[15];
            $source = $event->getSource();
            $this->assertEquals('testwebhookeventid', $event->getWebhookEventId());
            $this->assertFalse($event->getDeliveryContext()->getIsRedelivery());
            $this->assertInstanceOf(\LINE\Webhook\Model\BeaconEvent::class, $event);
            $this->assertEquals('replytoken', $event->getReplyToken());
            $this->assertEquals('bid', $event->getBeacon()->getHwid());
            $this->assertEquals('enter', $event->getBeacon()->getType());
            $this->assertEquals(
                "1234567890abcdef",
                $event->getBeacon()->getDm()
            );
        }

        {
            // unknown event (event source: user)
            $event = $events[16];
            $source = $event->getSource();
            $this->assertInstanceOf(\LINE\Webhook\Model\Event::class, $event);
            $this->assertEquals('__unknown__', $event->getType());
            $this->assertEquals('__unknown__', $event->jsonSerialize()->type); // with unprocessed event body
            $this->assertEquals(12345678901234, $event->getTimestamp());
            $this->assertEquals('active', $event->getMode());
            $this->assertTrue($source instanceof UserSource);
            $this->assertEquals('userid', $source->getUserId());
            $this->assertEquals('testwebhookeventid', $event->getWebhookEventId());
            $this->assertFalse($event->getDeliveryContext()->getIsRedelivery());
            $this->assertEquals(true, $source instanceof UserSource);
        }

        {
            // unknown event (event source: unknown)
            $event = $events[17];
            $source = $event->getSource();
            $this->assertInstanceOf(\LINE\Webhook\Model\Event::class, $event);
            $this->assertEquals('__unknown__', $event->getType());
            $this->assertEquals('__unknown__', $event->jsonSerialize()->type); // with unprocessed event body
            $this->assertEquals(12345678901234, $event->getTimestamp());
            $this->assertEquals('active', $event->getMode());
            $this->assertEquals('testwebhookeventid', $event->getWebhookEventId());
            $this->assertFalse($event->getDeliveryContext()->getIsRedelivery());
        }

        {
            // message event & unknown message event
            $event = $events[18];
            $source = $event->getSource();
            $this->assertInstanceOf(\LINE\Webhook\Model\MessageEvent::class, $event);
            $message = $event->getMessage();
            $this->assertInstanceOf(\LINE\Webhook\Model\MessageContent::class, $message);
            $this->assertEquals('__unknown__', $message->getType());
            $this->assertEquals('testwebhookeventid', $event->getWebhookEventId());
            $this->assertFalse($event->getDeliveryContext()->getIsRedelivery());
        }

        {
            // file message
            $event = $events[19];
            $source = $event->getSource();
            $this->assertEquals('testwebhookeventid', $event->getWebhookEventId());
            $this->assertFalse($event->getDeliveryContext()->getIsRedelivery());
            $this->assertInstanceOf(\LINE\Webhook\Model\MessageEvent::class, $event);
            $this->assertInstanceOf(\LINE\Webhook\Model\FileMessageContent::class, $event->getMessage());
            $this->assertEquals('file.txt', $event->getMessage()->getFileName());
            $this->assertEquals('2138', $event->getMessage()->getFileSize());
            $this->assertEquals('325708', $event->getMessage()->getId());
            $this->assertEquals('file', $event->getMessage()->getType());
        }

        {
            // postback date
            $event = $events[20];
            $source = $event->getSource();
            $this->assertEquals('testwebhookeventid', $event->getWebhookEventId());
            $this->assertFalse($event->getDeliveryContext()->getIsRedelivery());
            $this->assertInstanceOf(\LINE\Webhook\Model\PostbackEvent::class, $event);
            $this->assertEquals('replytoken', $event->getReplyToken());
            $this->assertEquals('postback', $event->getPostback()->getData());
            $this->assertEquals(["date" => "2013-04-01"], $event->getPostback()->getParams());
        }

        {
            // postback time
            $event = $events[21];
            $source = $event->getSource();
            $this->assertEquals('testwebhookeventid', $event->getWebhookEventId());
            $this->assertFalse($event->getDeliveryContext()->getIsRedelivery());
            $this->assertInstanceOf(\LINE\Webhook\Model\PostbackEvent::class, $event);
            $this->assertEquals('replytoken', $event->getReplyToken());
            $this->assertEquals('postback', $event->getPostback()->getData());
            $this->assertEquals(["time" => "10:00"], $event->getPostback()->getParams());
        }

        {
            // postback datetime
            $event = $events[22];
            $source = $event->getSource();
            $this->assertEquals('testwebhookeventid', $event->getWebhookEventId());
            $this->assertFalse($event->getDeliveryContext()->getIsRedelivery());
            $this->assertInstanceOf(\LINE\Webhook\Model\PostbackEvent::class, $event);
            $this->assertEquals('replytoken', $event->getReplyToken());
            $this->assertEquals('postback', $event->getPostback()->getData());
            $this->assertEquals(["datetime" => "2013-04-01T10:00"], $event->getPostback()->getParams());
        }

        {
            // account link - success
            $event = $events[23];
            $source = $event->getSource();
            $this->assertEquals('testwebhookeventid', $event->getWebhookEventId());
            $this->assertFalse($event->getDeliveryContext()->getIsRedelivery());
            $this->assertInstanceOf(\LINE\Webhook\Model\AccountLinkEvent::class, $event);
            $this->assertEquals('replytoken', $event->getReplyToken());
            $this->assertEquals(1501234567890, $event->getTimestamp());
            $this->assertEquals('standby', $event->getMode());
            $this->assertEquals("ok", $event->getLink()->getResult());
            $this->assertEquals("1234567890abcdefghijklmnopqrstuvwxyz", $event->getLink()->getNonce());
        }

        {
            // account link - failed
            $event = $events[24];
            $source = $event->getSource();
            $this->assertEquals('testwebhookeventid', $event->getWebhookEventId());
            $this->assertFalse($event->getDeliveryContext()->getIsRedelivery());
            $this->assertInstanceOf(\LINE\Webhook\Model\AccountLinkEvent::class, $event);
            $this->assertEquals('replytoken', $event->getReplyToken());
            $this->assertEquals(1501234567890, $event->getTimestamp());
            $this->assertEquals('active', $event->getMode());
            $this->assertEquals("failed", $event->getLink()->getResult());
            $this->assertEquals("1234567890abcdefghijklmnopqrstuvwxyz", $event->getLink()->getNonce());
        }

        {
            // member join
            $event = $events[25];
            $source = $event->getSource();
            $this->assertEquals('testwebhookeventid', $event->getWebhookEventId());
            $this->assertFalse($event->getDeliveryContext()->getIsRedelivery());
            $this->assertInstanceOf(\LINE\Webhook\Model\MemberJoinedEvent::class, $event);
            $this->assertEquals('replytoken', $event->getReplyToken());
            $this->assertEquals(12345678901234, $event->getTimestamp());
            $this->assertEquals('active', $event->getMode());
            $members = $event->getJoined()->getMembers();
            $this->assertEquals(["type" => "user", "userId" => "U4af4980629..."], $members[0]);
            $this->assertEquals(["type" => "user", "userId" => "U91eeaf62d9..."], $members[1]);
        }

        {
            // member leave
            $event = $events[26];
            $source = $event->getSource();
            $this->assertEquals('testwebhookeventid', $event->getWebhookEventId());
            $this->assertFalse($event->getDeliveryContext()->getIsRedelivery());
            $this->assertInstanceOf(\LINE\Webhook\Model\MemberLeftEvent::class, $event);
            $this->assertEquals(12345678901234, $event->getTimestamp());
            $this->assertEquals('active', $event->getMode());
            $members = $event->getLeft()->getMembers();
            $this->assertEquals(["type" => "user", "userId" => "U4af4980629..."], $members[0]);
            $this->assertEquals(["type" => "user", "userId" => "U91eeaf62d9..."], $members[1]);
        }

        {
            // things
            $event = $events[27];
            $source = $event->getSource();
            $this->assertEquals('testwebhookeventid', $event->getWebhookEventId());
            $this->assertFalse($event->getDeliveryContext()->getIsRedelivery());
            $this->assertInstanceOf(\LINE\Webhook\Model\ThingsEvent::class, $event);
            $this->assertEquals('replytoken', $event->getReplyToken());
            /** @var \LINE\Webhook\Model\LinkThingsContent $things */
            $things = $event->getThings();
            $this->assertInstanceOf(\LINE\Webhook\Model\LinkThingsContent::class, $things);
            $this->assertEquals('t2c449c9d1', $things->getDeviceId());
            $this->assertEquals(ThingsEventType::DEVICE_LINKED, $things->getType());
        }

        {
            // things
            $event = $events[28];
            $source = $event->getSource();
            $this->assertEquals('testwebhookeventid', $event->getWebhookEventId());
            $this->assertFalse($event->getDeliveryContext()->getIsRedelivery());
            $this->assertInstanceOf(\LINE\Webhook\Model\ThingsEvent::class, $event);
            $this->assertEquals('replytoken', $event->getReplyToken());
            /** @var \LINE\Webhook\Model\UnlinkThingsContent $things */
            $things = $event->getThings();
            $this->assertInstanceOf(\LINE\Webhook\Model\UnlinkThingsContent::class, $things);
            $this->assertEquals('t2c449c9d1', $things->getDeviceId());
            $this->assertEquals(ThingsEventType::DEVICE_UNLINKED, $things->getType());
        }

        {
            // things
            $event = $events[29];
            $source = $event->getSource();
            $this->assertEquals('testwebhookeventid', $event->getWebhookEventId());
            $this->assertFalse($event->getDeliveryContext()->getIsRedelivery());
            $this->assertInstanceOf(\LINE\Webhook\Model\ThingsEvent::class, $event);
            $this->assertEquals('replytoken', $event->getReplyToken());
            /** @var \LINE\Webhook\Model\ScenarioResultThingsContent $things */
            $things = $event->getThings();
            $this->assertInstanceOf(\LINE\Webhook\Model\ScenarioResultThingsContent::class, $things);
            $this->assertEquals('t2c449c9d1', $things->getDeviceId());
            $this->assertEquals(ThingsEventType::SCENARIO_RESULT, $things->getType());
            $scenarioResult = $things->getResult();
            $this->assertEquals('dummy_scenario_id', $scenarioResult->getScenarioId());
            $this->assertEquals(2, $scenarioResult->getRevision());
            $this->assertEquals(1547817845950, $scenarioResult->getStartTime());
            $this->assertEquals(1547817845952, $scenarioResult->getEndTime());
            $this->assertEquals('success', $scenarioResult->getResultCode());
            $this->assertEquals('AQ==', $scenarioResult->getBleNotificationPayload());
            $actionResults = $scenarioResult->getActionResults();
            $this->assertEquals(ThingsResultContentType::TYPE_BINARY, $actionResults[0]->getType());
            $this->assertEquals('/w==', $actionResults[0]->getData());
        }

        {
            // text without emoji
            $event = $events[30];
            $source = $event->getSource();
            $this->assertEquals(12345678901234, $event->getTimestamp());
            $this->assertEquals('active', $event->getMode());
            $this->assertTrue($source instanceof UserSource);
            $this->assertEquals('userid', $source->getUserId());
            $this->assertEquals('testwebhookeventid', $event->getWebhookEventId());
            $this->assertFalse($event->getDeliveryContext()->getIsRedelivery());
            $this->assertInstanceOf(\LINE\Webhook\Model\MessageEvent::class, $event);
            $this->assertInstanceOf(\LINE\Webhook\Model\TextMessageContent::class, $event->getMessage());
            $this->assertEquals('replytoken', $event->getReplyToken());
            $this->assertEquals('contentid', $event->getMessage()->getId());
            $this->assertEquals('text', $event->getMessage()->getType());
            $this->assertEquals('message without emoji', $event->getMessage()->getText());
            $this->assertEquals([], $event->getMessage()->getEmojis());
        }

        {
            // unsend event
            $event = $events[31];
            $source = $event->getSource();
            $this->assertEquals('testwebhookeventid', $event->getWebhookEventId());
            $this->assertFalse($event->getDeliveryContext()->getIsRedelivery());
            $this->assertInstanceOf(\LINE\Webhook\Model\UnsendEvent::class, $event);
            $this->assertEquals('325708', $event->getUnsend()->getMessageId());
        }

        {
            // video play complete event
            $event = $events[32];
            $source = $event->getSource();
            $this->assertEquals(
                'testwebhookeventid',
                $event->getWebhookEventId()
            );
            $this->assertInstanceOf(\LINE\Webhook\Model\VideoPlayCompleteEvent::class, $event);
            $this->assertEquals('track_id', $event->getVideoPlayComplete()->getTrackingId());
        }

        {
            // text
            $event = $events[33];
            $source = $event->getSource();
            $this->assertEquals(1462629479859, $event->getTimestamp());
            $this->assertEquals('active', $event->getMode());
            $this->assertTrue($source instanceof UserSource);
            $this->assertEquals('U4af4980629...', $source->getUserId());
            $this->assertEquals('testwebhookeventid', $event->getWebhookEventId());
            $this->assertFalse($event->getDeliveryContext()->getIsRedelivery());
            $this->assertInstanceOf(\LINE\Webhook\Model\MessageEvent::class, $event);
            $this->assertInstanceOf(\LINE\Webhook\Model\TextMessageContent::class, $event->getMessage());
            $this->assertEquals('nHuyWiB7yP5Zw52FIkcQobQuGDXCTA', $event->getReplyToken());
            $this->assertEquals('325708', $event->getMessage()->getId());
            $this->assertEquals('text', $event->getMessage()->getType());
            $this->assertEquals('@example Hello, world! (love)', $event->getMessage()->getText());
            $mentioneeInfo = $event->getMessage()->getMention()->getMentionees()[0];
            $this->assertInstanceOf(\LINE\Webhook\Model\UserMentionee::class, $mentioneeInfo);
            $this->assertEquals(0, $mentioneeInfo->getIndex());
            $this->assertEquals(8, $mentioneeInfo->getLength());
            $this->assertEquals('U0123456789abcd0123456789abcdef', $mentioneeInfo->getUserId());
        }

        {
            // text without mentionee userId
            $event = $events[34];
            $source = $event->getSource();
            $this->assertEquals(1462629479859, $event->getTimestamp());
            $this->assertEquals('active', $event->getMode());
            $this->assertTrue($source instanceof UserSource);
            $this->assertEquals('U0123456789abcd0123456789abcdef', $source->getUserId());
            $this->assertEquals('testwebhookeventid', $event->getWebhookEventId());
            $this->assertFalse($event->getDeliveryContext()->getIsRedelivery());
            $this->assertInstanceOf(\LINE\Webhook\Model\MessageEvent::class, $event);
            $this->assertInstanceOf(\LINE\Webhook\Model\TextMessageContent::class, $event->getMessage());
            $this->assertEquals('nHuyWiB7yP5Zw52FIkcQobQuGDXCTA', $event->getReplyToken());
            $this->assertEquals('325708', $event->getMessage()->getId());
            $this->assertEquals('text', $event->getMessage()->getType());
            $this->assertEquals('@example message without mentionee userId', $event->getMessage()->getText());
            $mentioneeInfo = $event->getMessage()->getMention()->getMentionees()[0];
            $this->assertInstanceOf(\LINE\Webhook\Model\AllMentionee::class, $mentioneeInfo);
            $this->assertEquals(0, $mentioneeInfo->getIndex());
            $this->assertEquals(8, $mentioneeInfo->getLength());
        }

        {
            // text without mention
            $event = $events[35];
            $source = $event->getSource();
            $this->assertEquals(1462629479859, $event->getTimestamp());
            $this->assertEquals('active', $event->getMode());
            $this->assertTrue($source instanceof UserSource);
            $this->assertEquals('U0123456789abcd0123456789abcdef', $source->getUserId());
            $this->assertEquals('testwebhookeventid', $event->getWebhookEventId());
            $this->assertFalse($event->getDeliveryContext()->getIsRedelivery());
            $this->assertInstanceOf(\LINE\Webhook\Model\MessageEvent::class, $event);
            $this->assertInstanceOf(\LINE\Webhook\Model\TextMessageContent::class, $event->getMessage());
            $this->assertEquals('nHuyWiB7yP5Zw52FIkcQobQuGDXCTA', $event->getReplyToken());
            $this->assertEquals('325708', $event->getMessage()->getId());
            $this->assertEquals('text', $event->getMessage()->getType());
            $this->assertEquals('message without mention', $event->getMessage()->getText());
            $this->assertEquals(null, $event->getMessage()->getMention());
        }

        {
            // Only included when multiple images are sent simultaneously.
            $event = $events[36];
            $source = $event->getSource();
            $this->assertTrue($source instanceof GroupSource);
            $this->assertEquals('groupid', $source->getGroupId());
            $this->assertEquals(null, $source->getUserId());
            $this->assertEquals('testwebhookeventid', $event->getWebhookEventId());
            $this->assertFalse($event->getDeliveryContext()->getIsRedelivery());
            $this->assertJsonStringEqualsJsonString(json_encode($eventArrays[36]), $event->__toString());
            $this->assertInstanceOf(\LINE\Webhook\Model\MessageEvent::class, $event);
            $this->assertInstanceOf(\LINE\Webhook\Model\ImageMessageContent::class, $event->getMessage());
            $this->assertEquals('replytoken', $event->getReplyToken());
            $this->assertEquals('image', $event->getMessage()->getType());
            $this->assertEquals('contentid', $event->getMessage()->getId());
            $this->assertTrue($event->getMessage()->getContentProvider()->getType() == MessageContentProviderType::EXTERNAL);
            $this->assertEquals(
                'https://example.com/test.jpg',
                $event->getMessage()->getContentProvider()->getOriginalContentUrl()
            );
            $this->assertEquals(
                'https://example.com/test-preview.jpg',
                $event->getMessage()->getContentProvider()->getPreviewImageUrl()
            );
            $this->assertEquals(null, $event->getMessage()->getImageSet());
        }

        {
            // However, it won't be included if the sender is using LINE 11.15 or earlier for Android.
            $event = $events[37];
            $source = $event->getSource();
            $this->assertTrue($source instanceof GroupSource);
            $this->assertEquals('groupid', $source->getGroupId());
            $this->assertEquals(null, $source->getUserId());
            $this->assertEquals('testwebhookeventid', $event->getWebhookEventId());
            $this->assertFalse($event->getDeliveryContext()->getIsRedelivery());
            $this->assertJsonStringEqualsJsonString(json_encode($eventArrays[37]), $event->__toString());
            $this->assertInstanceOf(\LINE\Webhook\Model\MessageEvent::class, $event);
            $this->assertInstanceOf(\LINE\Webhook\Model\ImageMessageContent::class, $event->getMessage());
            $this->assertEquals('replytoken', $event->getReplyToken());
            $this->assertEquals('image', $event->getMessage()->getType());
            $this->assertEquals('contentid', $event->getMessage()->getId());
            $this->assertTrue($event->getMessage()->getContentProvider()->getType() == MessageContentProviderType::EXTERNAL);
            $this->assertEquals(
                'https://example.com/test.jpg',
                $event->getMessage()->getContentProvider()->getOriginalContentUrl()
            );
            $this->assertEquals(
                'https://example.com/test-preview.jpg',
                $event->getMessage()->getContentProvider()->getPreviewImageUrl()
            );
            $this->assertEquals(
                'E005D41A7288F41B65593ED38FF6E9834B046AB36A37921A56BC236F13A91855',
                $event->getMessage()->getImageSet()->getId()
            );
            $this->assertNull($event->getMessage()->getImageSet()->getIndex());
            $this->assertNull($event->getMessage()->getImageSet()->getTotal());
        }

        {
            // text (redelivered)
            $event = $events[38];
            $source = $event->getSource();
            $this->assertEquals(12345678901234, $event->getTimestamp());
            $this->assertEquals('active', $event->getMode());
            $this->assertTrue($source instanceof UserSource);
            $this->assertEquals('userid', $source->getUserId());
            $this->assertEquals('testwebhookeventid', $event->getWebhookEventId());
            $this->assertTrue($event->getDeliveryContext()->getIsRedelivery());
            $this->assertJsonStringEqualsJsonString(json_encode($eventArrays[38]), $event->__toString());
            $this->assertInstanceOf(\LINE\Webhook\Model\MessageEvent::class, $event);
            $this->assertInstanceOf(\LINE\Webhook\Model\TextMessageContent::class, $event->getMessage());
            $this->assertEquals('replytoken', $event->getReplyToken());
            $this->assertEquals('contentid', $event->getMessage()->getId());
            $this->assertEquals('text', $event->getMessage()->getType());
            $this->assertEquals('message (love)', $event->getMessage()->getText());
            $emojiInfo = $event->getMessage()->getEmojis()[0];
            $this->assertEquals(8, $emojiInfo->getIndex());
            $this->assertEquals(6, $emojiInfo->getLength());
            $this->assertEquals('5ac1bfd5040ab15980c9b435', $emojiInfo->getProductId());
            $this->assertEquals('001', $emojiInfo->getEmojiId());
        }

        {
            // activated
            $event = $events[39];
            $source = $event->getSource();
            $this->assertEquals(12345678901234, $event->getTimestamp());
            $this->assertEquals('active', $event->getMode());
            $this->assertTrue($source instanceof UserSource);
            $this->assertEquals('userid', $source->getUserId());
            $this->assertInstanceOf(\LINE\Webhook\Model\ActivatedEvent::class, $event);
            $this->assertInstanceOf(\LINE\Webhook\Model\ChatControl::class, $event->getChatControl());
            $this->assertEquals(1462629479860, $event->getChatControl()->getExpireAt());
            $this->assertEquals('testwebhookeventid', $event->getWebhookEventId());
            $this->assertFalse($event->getDeliveryContext()->getIsRedelivery());
        }

        {
            // deactivated
            $event = $events[40];
            $source = $event->getSource();
            $this->assertEquals(12345678901234, $event->getTimestamp());
            $this->assertEquals('active', $event->getMode());
            $this->assertTrue($source instanceof UserSource);
            $this->assertEquals('userid', $source->getUserId());
            $this->assertInstanceOf(\LINE\Webhook\Model\DeactivatedEvent::class, $event);
            $this->assertEquals('testwebhookeventid', $event->getWebhookEventId());
            $this->assertFalse($event->getDeliveryContext()->getIsRedelivery());
        }

        {
            // botSuspended
            $event = $events[41];
            $this->assertEquals(12345678901234, $event->getTimestamp());
            $this->assertEquals('active', $event->getMode());
            $this->assertInstanceOf(\LINE\Webhook\Model\BotSuspendedEvent::class, $event);
            $this->assertEquals('testwebhookeventid', $event->getWebhookEventId());
            $this->assertFalse($event->getDeliveryContext()->getIsRedelivery());
        }

        {
            // botResumed
            $event = $events[42];
            $this->assertEquals(12345678901234, $event->getTimestamp());
            $this->assertEquals('active', $event->getMode());
            $this->assertInstanceOf(\LINE\Webhook\Model\BotResumedEvent::class, $event);
            $this->assertEquals('testwebhookeventid', $event->getWebhookEventId());
            $this->assertFalse($event->getDeliveryContext()->getIsRedelivery());
        }

        {
            // delivery
            $event = $events[43];
            $source = $event->getSource();
            $this->assertEquals(12345678901234, $event->getTimestamp());
            $this->assertEquals('active', $event->getMode());
            $this->assertTrue($source instanceof UserSource);
            $this->assertEquals('userid', $source->getUserId());
            $this->assertInstanceOf(\LINE\Webhook\Model\PnpDeliveryCompletionEvent::class, $event);
            $this->assertInstanceOf(\LINE\Webhook\Model\PnpDelivery::class, $event->getDelivery());
            $this->assertEquals('deliverydata', $event->getDelivery()->getData());
            $this->assertEquals('testwebhookeventid', $event->getWebhookEventId());
            $this->assertFalse($event->getDeliveryContext()->getIsRedelivery());
        }

        {
            // module (attached)
            $event = $events[44];
            $this->assertEquals(12345678901234, $event->getTimestamp());
            $this->assertEquals('active', $event->getMode());
            $this->assertInstanceOf(\LINE\Webhook\Model\ModuleEvent::class, $event);
            /** @var \LINE\Webhook\Model\AttachedModuleContent $module */
            $module = $event->getModule();

            $this->assertInstanceOf(\LINE\Webhook\Model\AttachedModuleContent::class, $module);
            $this->assertEquals('botid', $module->getBotId());
            $this->assertEquals('b', $module->getScopes()[1]);
            $this->assertEquals('testwebhookeventid', $event->getWebhookEventId());
            $this->assertFalse($event->getDeliveryContext()->getIsRedelivery());
        }

        {
            // module (detached)
            $event = $events[45];
            $this->assertEquals(12345678901234, $event->getTimestamp());
            $this->assertEquals('active', $event->getMode());
            $this->assertInstanceOf(\LINE\Webhook\Model\ModuleEvent::class, $event);
            /** @var \LINE\Webhook\Model\DetachedModuleContent $module */
            $module = $event->getModule();

            $this->assertInstanceOf(\LINE\Webhook\Model\DetachedModuleContent::class, $event->getModule());
            $this->assertEquals('botid', $module->getBotId());
            $this->assertEquals('bot deleted', $module->getReason());
            $this->assertEquals('testwebhookeventid', $event->getWebhookEventId());
            $this->assertFalse($event->getDeliveryContext()->getIsRedelivery());
        }
    }

    private static function getSignature(string $secret): string
    {
        return base64_encode(hash_hmac('sha256', self::$json, $secret, true));
    }
}

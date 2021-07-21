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

namespace LINE\Tests\LINEBot;

use LINE\LINEBot;
use LINE\LINEBot\Constant\StickerResourceType;
use LINE\LINEBot\Event\AccountLinkEvent;
use LINE\LINEBot\Event\BeaconDetectionEvent;
use LINE\LINEBot\Event\FollowEvent;
use LINE\LINEBot\Event\JoinEvent;
use LINE\LINEBot\Event\LeaveEvent;
use LINE\LINEBot\Event\MemberJoinEvent;
use LINE\LINEBot\Event\MemberLeaveEvent;
use LINE\LINEBot\Event\MessageEvent\AudioMessage;
use LINE\LINEBot\Event\MessageEvent\FileMessage;
use LINE\LINEBot\Event\MessageEvent\ImageMessage;
use LINE\LINEBot\Event\MessageEvent\LocationMessage;
use LINE\LINEBot\Event\MessageEvent\StickerMessage;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use LINE\LINEBot\Event\MessageEvent\UnknownMessage;
use LINE\LINEBot\Event\MessageEvent\VideoMessage;
use LINE\LINEBot\Event\PostbackEvent;
use LINE\LINEBot\Event\Things\ThingsResultAction;
use LINE\LINEBot\Event\ThingsEvent;
use LINE\LINEBot\Event\VideoPlayCompleteEvent;
use LINE\LINEBot\Event\UnfollowEvent;
use LINE\LINEBot\Event\UnknownEvent;
use LINE\Tests\LINEBot\Util\DummyHttpClient;
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
    "groupId":"groupid",
    "userId":"userid"
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
   "replyToken":"replytoken",
   "message":{
    "id":"contentid",
    "type":"sticker",
    "packageId":"1",
    "stickerId":"2",
    "stickerResourceType":"STATIC",
    "keywords": ["a","b","c","d","e","f","g","h","i","j","k","l","m","n"]
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
   "replyToken":"replytoken"
  },
  {
   "type":"unfollow",
   "mode":"active",
   "timestamp":12345678901234,
   "source":{
    "type":"user",
    "userId":"userid"
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
   "replyToken":"replytoken"
  },
  {
   "type":"leave",
   "mode":"active",
   "timestamp":12345678901234,
   "source":{
    "type":"user",
    "userId":"userid"
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
   }
  },
  {
   "type":"__unknown__",
   "mode":"active",
   "timestamp":12345678901234,
   "source":{
    "type":"__unknown__"
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
   "message": {
    "id": "325708",
    "type": "text",
    "text": "@example Hello, world! (love)",
    "mention": {
     "mentionees": [
      {
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
   "message": {
    "id": "325708",
    "type": "text",
    "text": "@example message without mentionee userId",
    "mention": {
     "mentionees": [
      {
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
   "message": {
    "id": "325708",
    "type": "text",
    "text": "message without mention"
   }
  }
 ]
}
JSON;

    /**
     * @throws LINEBot\Exception\InvalidEventRequestException
     * @throws LINEBot\Exception\InvalidEventSourceException
     * @throws LINEBot\Exception\InvalidSignatureException
     */
    public function testParseEventRequest()
    {
        $bot = new LINEBot(new DummyHttpClient($this, function () {
        }), ['channelSecret' => 'testsecret']);
        list($destination, $events) = $bot->parseEventRequest(
            $this::$json,
            'Q4tp1jGo39vhlcbd4QiQ/9I+zoJDwGIkPP22wgoOjDI=',
            false
        );
        $eventArrays = json_decode($this::$json, true)["events"];

        $this->assertEquals($destination, 'U0123456789abcdef0123456789abcd');

        $this->assertEquals(count($events), 35);

        {
            // text
            $event = $events[0];
            $this->assertEquals(12345678901234, $event->getTimestamp());
            $this->assertEquals('active', $event->getMode());
            $this->assertTrue($event->isUserEvent());
            $this->assertEquals('userid', $event->getUserId());
            $this->assertEquals('userid', $event->getEventSourceId());
            $this->assertEquals($eventArrays[0], $event->getEvent());
            $this->assertInstanceOf('LINE\LINEBot\Event\MessageEvent', $event);
            $this->assertInstanceOf('LINE\LINEBot\Event\MessageEvent\TextMessage', $event);
            /** @var TextMessage $event */
            $this->assertEquals('replytoken', $event->getReplyToken());
            $this->assertEquals('contentid', $event->getMessageId());
            $this->assertEquals('text', $event->getMessageType());
            $this->assertEquals('message (love)', $event->getText());
            $emojiInfo = $event->getEmojis()[0];
            $this->assertEquals(8, $emojiInfo->getIndex());
            $this->assertEquals(6, $emojiInfo->getLength());
            $this->assertEquals('5ac1bfd5040ab15980c9b435', $emojiInfo->getProductId());
            $this->assertEquals('001', $emojiInfo->getEmojiId());
        }

        {
            // image
            $event = $events[1];
            $this->assertTrue($event->isGroupEvent());
            $this->assertEquals('groupid', $event->getGroupId());
            $this->assertEquals('groupid', $event->getEventSourceId());
            $this->assertEquals(null, $event->getUserId());
            $this->assertEquals($eventArrays[1], $event->getEvent());
            $this->assertInstanceOf('LINE\LINEBot\Event\MessageEvent\ImageMessage', $event);
            /** @var ImageMessage $event */
            $this->assertEquals('replytoken', $event->getReplyToken());
            $this->assertEquals('image', $event->getMessageType());
            $this->assertEquals('contentid', $event->getMessageId());
            $this->assertTrue($event->getContentProvider()->isExternal());
            $this->assertEquals(
                'https://example.com/test.jpg',
                $event->getContentProvider()->getOriginalContentUrl()
            );
            $this->assertEquals(
                'https://example.com/test-preview.jpg',
                $event->getContentProvider()->getPreviewImageUrl()
            );
        }

        {
            // audio (group event & it has user ID)
            $event = $events[2];
            $this->assertTrue($event->isGroupEvent());
            $this->assertEquals('groupid', $event->getGroupId());
            $this->assertEquals('groupid', $event->getEventSourceId());
            $this->assertEquals($eventArrays[2], $event->getEvent());
            $this->assertInstanceOf('LINE\LINEBot\Event\MessageEvent\AudioMessage', $event);
            $this->assertEquals('userid', $event->getUserId());
            /** @var AudioMessage $event */
            $this->assertEquals('replytoken', $event->getReplyToken());
            $this->assertEquals('audio', $event->getMessageType());
            $this->assertEquals('contentid', $event->getMessageId());
            $this->assertEquals(10000, $event->getDuration());
            $this->assertTrue($event->getContentProvider()->isExternal());
            $this->assertEquals(
                'https://example.com/test.m4a',
                $event->getContentProvider()->getOriginalContentUrl()
            );
        }

        {
            // video
            $event = $events[3];
            $this->assertTrue($event->isRoomEvent());
            $this->assertEquals('roomid', $event->getRoomId());
            $this->assertEquals('roomid', $event->getEventSourceId());
            $this->assertEquals(null, $event->getUserId());
            $this->assertEquals($eventArrays[3], $event->getEvent());
            $this->assertInstanceOf('LINE\LINEBot\Event\MessageEvent\VideoMessage', $event);
            /** @var VideoMessage $event */
            $this->assertEquals('replytoken', $event->getReplyToken());
            $this->assertEquals('video', $event->getMessageType());
            $this->assertEquals(10000, $event->getDuration());
            $this->assertTrue($event->getContentProvider()->isExternal());
            $this->assertEquals(
                'https://example.com/test.mp4',
                $event->getContentProvider()->getOriginalContentUrl()
            );
            $this->assertEquals(
                'https://example.com/test-preview.jpg',
                $event->getContentProvider()->getPreviewImageUrl()
            );
        }

        {
            // audio
            $event = $events[4];
            $this->assertTrue($event->isRoomEvent());
            $this->assertEquals('userid', $event->getUserId());
            $this->assertInstanceOf('LINE\LINEBot\Event\MessageEvent\AudioMessage', $event);
            /** @var AudioMessage $event */
            $this->assertEquals('replytoken', $event->getReplyToken());
            $this->assertEquals('audio', $event->getMessageType());
        }

        {
            // location
            $event = $events[5];
            $this->assertInstanceOf('LINE\LINEBot\Event\MessageEvent\LocationMessage', $event);
            /** @var LocationMessage $event */
            $this->assertEquals('replytoken', $event->getReplyToken());
            $this->assertEquals('location', $event->getMessageType());
            $this->assertEquals('label', $event->getTitle());
            $this->assertEquals('tokyo', $event->getAddress());
            $this->assertEquals('-34.12', $event->getLatitude());
            $this->assertEquals('134.23', $event->getLongitude());
        }

        {
            // location when not set title attribute
            $event = $events[6];
            $this->assertInstanceOf('LINE\LINEBot\Event\MessageEvent\LocationMessage', $event);
            /** @var LocationMessage $event */
            $this->assertEquals('replytoken', $event->getReplyToken());
            $this->assertEquals('location', $event->getMessageType());
            $this->assertNull($event->getTitle());
            $this->assertEquals('tokyo', $event->getAddress());
            $this->assertEquals('-34.12', $event->getLatitude());
            $this->assertEquals('134.23', $event->getLongitude());
        }

        {
            // location when not set address attribute
            $event = $events[7];
            $this->assertInstanceOf('LINE\LINEBot\Event\MessageEvent\LocationMessage', $event);
            /** @var LocationMessage $event */
            $this->assertEquals('replytoken', $event->getReplyToken());
            $this->assertEquals('location', $event->getMessageType());
            $this->assertEquals('label', $event->getTitle());
            $this->assertNull($event->getAddress());
            $this->assertEquals('-34.12', $event->getLatitude());
            $this->assertEquals('134.23', $event->getLongitude());
        }

        {
            // sticker
            $event = $events[8];
            $this->assertInstanceOf('LINE\LINEBot\Event\MessageEvent\StickerMessage', $event);
            /** @var StickerMessage $event */
            $this->assertEquals('replytoken', $event->getReplyToken());
            $this->assertEquals('sticker', $event->getMessageType());
            $this->assertEquals(1, $event->getPackageId());
            $this->assertEquals(2, $event->getStickerId());
            $this->assertEquals(StickerResourceType::STATIC_IMAGE, $event->getStickerResourceType());
            $this->assertEquals(
                ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n'],
                $event->getKeywords()
            );
        }

        {
            // follow
            $event = $events[9];
            $this->assertInstanceOf('LINE\LINEBot\Event\FollowEvent', $event);
            /** @var FollowEvent $event */
            $this->assertEquals('replytoken', $event->getReplyToken());
        }

        {
            // unfollow
            $event = $events[10];
            $this->assertInstanceOf('LINE\LINEBot\Event\UnfollowEvent', $event);
            /** @var UnfollowEvent $event */
            $this->assertTrue($event->getReplyToken() === null);
        }

        {
            // join
            $event = $events[11];
            $this->assertInstanceOf('LINE\LINEBot\Event\JoinEvent', $event);
            /** @var JoinEvent $event */
            $this->assertEquals('replytoken', $event->getReplyToken());
        }

        {
            // leave
            $event = $events[12];
            $this->assertInstanceOf('LINE\LINEBot\Event\LeaveEvent', $event);
            /** @var LeaveEvent $event */
            $this->assertTrue($event->getReplyToken() === null);
        }

        {
            // postback
            $event = $events[13];
            $this->assertInstanceOf('LINE\LINEBot\Event\PostbackEvent', $event);
            /** @var PostbackEvent $event */
            $this->assertEquals('replytoken', $event->getReplyToken());
            $this->assertEquals('postback', $event->getPostbackData());
            $this->assertEquals(null, $event->getPostbackParams());
        }

        {
            // beacon
            $event = $events[14];
            $this->assertInstanceOf('LINE\LINEBot\Event\BeaconDetectionEvent', $event);
            /** @var BeaconDetectionEvent $event */
            $this->assertEquals('replytoken', $event->getReplyToken());
            $this->assertEquals('bid', $event->getHwid());
            $this->assertEquals('enter', $event->getBeaconEventType());
            $this->assertEquals("\x12\x34\x56\x78\x90\xab\xcd\xef", $event->getDeviceMessage());
        }

        {
            // unknown event (event source: user)
            $event = $events[15];
            $this->assertInstanceOf('LINE\LINEBot\Event\UnknownEvent', $event);
            /** @var UnknownEvent $event */
            $this->assertEquals('__unknown__', $event->getType());
            $this->assertEquals('__unknown__', $event->getEventBody()['type']); // with unprocessed event body
            $this->assertEquals(null, $event->getReplyToken());
            $this->assertEquals(12345678901234, $event->getTimestamp());
            $this->assertEquals('active', $event->getMode());
            $this->assertEquals('userid', $event->getEventSourceId());
            $this->assertEquals('userid', $event->getUserId());
            $this->assertEquals(true, $event->isUserEvent());
        }

        {
            // unknown event (event source: unknown)
            $event = $events[16];
            $this->assertInstanceOf('LINE\LINEBot\Event\UnknownEvent', $event);
            /** @var UnknownEvent $event */
            $this->assertEquals('__unknown__', $event->getType());
            $this->assertEquals('__unknown__', $event->getEventBody()['type']); // with unprocessed event body
            $this->assertEquals(null, $event->getReplyToken());
            $this->assertEquals(12345678901234, $event->getTimestamp());
            $this->assertEquals('active', $event->getMode());
            $this->assertEquals(null, $event->getEventSourceId());
            $this->assertEquals(true, $event->isUnknownEvent());
        }

        {
            // message event & unknown message event
            $event = $events[17];
            $this->assertInstanceOf('LINE\LINEBot\Event\MessageEvent', $event);
            $this->assertInstanceOf('LINE\LINEBot\Event\MessageEvent\UnknownMessage', $event);
            /** @var UnknownMessage $event */
            $this->assertEquals('__unknown__', $event->getMessageBody()['type']);
        }

        {
            // file message
            $event = $events[18];
            $this->assertInstanceOf('LINE\LINEBot\Event\MessageEvent', $event);
            $this->assertInstanceOf('LINE\LINEBot\Event\MessageEvent\FileMessage', $event);
            /** @var FileMessage $event */
            $this->assertEquals('file.txt', $event->getFileName());
            $this->assertEquals('2138', $event->getFileSize());
            $this->assertEquals('325708', $event->getMessageId());
            $this->assertEquals('file', $event->getMessageType());
        }

        {
            // postback date
            $event = $events[19];
            $this->assertInstanceOf('LINE\LINEBot\Event\PostbackEvent', $event);
            /** @var PostbackEvent $event */
            $this->assertEquals('replytoken', $event->getReplyToken());
            $this->assertEquals('postback', $event->getPostbackData());
            $this->assertEquals(["date" => "2013-04-01"], $event->getPostbackParams());
        }

        {
            // postback time
            $event = $events[20];
            $this->assertInstanceOf('LINE\LINEBot\Event\PostbackEvent', $event);
            /** @var PostbackEvent $event */
            $this->assertEquals('replytoken', $event->getReplyToken());
            $this->assertEquals('postback', $event->getPostbackData());
            $this->assertEquals(["time" => "10:00"], $event->getPostbackParams());
        }

        {
            // postback datetime
            $event = $events[21];
            $this->assertInstanceOf('LINE\LINEBot\Event\PostbackEvent', $event);
            /** @var PostbackEvent $event */
            $this->assertEquals('replytoken', $event->getReplyToken());
            $this->assertEquals('postback', $event->getPostbackData());
            $this->assertEquals(["datetime" => "2013-04-01T10:00"], $event->getPostbackParams());
        }

        {
            // account link - success
            $event = $events[22];
            $this->assertInstanceOf('LINE\LINEBot\Event\AccountLinkEvent', $event);
            /** @var AccountLinkEvent $event */
            $this->assertEquals('replytoken', $event->getReplyToken());
            $this->assertEquals(1501234567890, $event->getTimestamp());
            $this->assertEquals('standby', $event->getMode());
            $this->assertEquals("ok", $event->getResult());
            $this->assertEquals(true, $event->isSuccess());
            $this->assertEquals(false, $event->isFailed());
            $this->assertEquals("1234567890abcdefghijklmnopqrstuvwxyz", $event->getNonce());
        }

        {
            // account link - failed
            $event = $events[23];
            $this->assertInstanceOf('LINE\LINEBot\Event\AccountLinkEvent', $event);
            /** @var AccountLinkEvent $event */
            $this->assertEquals('replytoken', $event->getReplyToken());
            $this->assertEquals(1501234567890, $event->getTimestamp());
            $this->assertEquals('active', $event->getMode());
            $this->assertEquals("failed", $event->getResult());
            $this->assertEquals(false, $event->isSuccess());
            $this->assertEquals(true, $event->isFailed());
            $this->assertEquals("1234567890abcdefghijklmnopqrstuvwxyz", $event->getNonce());
        }

        {
            // member join
            $event = $events[24];
            $this->assertInstanceOf('LINE\LINEBot\Event\MemberJoinEvent', $event);
            /** @var MemberJoinEvent $event */
            $this->assertEquals('replytoken', $event->getReplyToken());
            $this->assertEquals(12345678901234, $event->getTimestamp());
            $this->assertEquals('active', $event->getMode());
            $members = $event->getMembers();
            $this->assertEquals(["type" => "user", "userId" => "U4af4980629..."], $members[0]);
            $this->assertEquals(["type" => "user", "userId" => "U91eeaf62d9..."], $members[1]);
        }

        {
            // member leave
            $event = $events[25];
            $this->assertInstanceOf('LINE\LINEBot\Event\MemberLeaveEvent', $event);
            /** @var MemberLeaveEvent $event */
            $this->assertTrue($event->getReplyToken() === null);
            $this->assertEquals(12345678901234, $event->getTimestamp());
            $this->assertEquals('active', $event->getMode());
            $members = $event->getMembers();
            $this->assertEquals(["type" => "user", "userId" => "U4af4980629..."], $members[0]);
            $this->assertEquals(["type" => "user", "userId" => "U91eeaf62d9..."], $members[1]);
        }

        {
            // things
            $event = $events[26];
            $this->assertInstanceOf('LINE\LINEBot\Event\ThingsEvent', $event);
            /** @var ThingsEvent $event */
            $this->assertEquals('replytoken', $event->getReplyToken());
            $this->assertEquals('t2c449c9d1', $event->getDeviceId());
            $this->assertEquals(ThingsEvent::TYPE_DEVICE_LINKED, $event->getThingsEventType());
        }

        {
            // things
            $event = $events[27];
            $this->assertInstanceOf('LINE\LINEBot\Event\ThingsEvent', $event);
            /** @var ThingsEvent $event */
            $this->assertEquals('replytoken', $event->getReplyToken());
            $this->assertEquals('t2c449c9d1', $event->getDeviceId());
            $this->assertEquals(ThingsEvent::TYPE_DEVICE_UNLINKED, $event->getThingsEventType());
        }

        {
            // things
            $event = $events[28];
            $this->assertInstanceOf('LINE\LINEBot\Event\ThingsEvent', $event);
            /** @var ThingsEvent $event */
            $this->assertEquals('replytoken', $event->getReplyToken());
            $this->assertEquals('t2c449c9d1', $event->getDeviceId());
            $this->assertEquals(ThingsEvent::TYPE_SCENARIO_RESULT, $event->getThingsEventType());
            $this->assertEquals('dummy_scenario_id', $event->getScenarioResult()->getScenarioId());
            $scenarioResult = $event->getScenarioResult();
            $this->assertEquals(2, $scenarioResult->getRevision());
            $this->assertEquals(1547817845950, $scenarioResult->getStartTime());
            $this->assertEquals(1547817845952, $scenarioResult->getEndTime());
            $this->assertEquals('success', $scenarioResult->getResultCode());
            $this->assertEquals('AQ==', $scenarioResult->getBleNotificationPayload());
            $actionResults = $scenarioResult->getActionResults();
            $this->assertEquals(ThingsResultAction::TYPE_BINARY, $actionResults[0]->getType());
            $this->assertEquals('/w==', $actionResults[0]->getData());
        }

        {
            // text without emoji
            $event = $events[29];
            $this->assertEquals(12345678901234, $event->getTimestamp());
            $this->assertEquals('active', $event->getMode());
            $this->assertTrue($event->isUserEvent());
            $this->assertEquals('userid', $event->getUserId());
            $this->assertEquals('userid', $event->getEventSourceId());
            $this->assertInstanceOf('LINE\LINEBot\Event\MessageEvent', $event);
            $this->assertInstanceOf('LINE\LINEBot\Event\MessageEvent\TextMessage', $event);
            /** @var TextMessage $event */
            $this->assertEquals('replytoken', $event->getReplyToken());
            $this->assertEquals('contentid', $event->getMessageId());
            $this->assertEquals('text', $event->getMessageType());
            $this->assertEquals('message without emoji', $event->getText());
            $this->assertEquals(null, $event->getEmojis());
        }

        {
            // unsend event
            $event = $events[30];
            $this->assertInstanceOf('LINE\LINEBot\Event\UnsendEvent', $event);
            /** @var UnsendMessage $event */
            $this->assertEquals('325708', $event->getUnsendMessageId());
        }

        {
            // video play complete event
            $event = $events[31];
            $this->assertInstanceOf('LINE\LINEBot\Event\VideoPlayCompleteEvent', $event);
            /** @var UnsendMessage $event */
            $this->assertEquals('track_id', $event->getTrackingId());
        }

        {
            // text
            $event = $events[32];
            $this->assertEquals(1462629479859, $event->getTimestamp());
            $this->assertEquals('active', $event->getMode());
            $this->assertTrue($event->isUserEvent());
            $this->assertEquals('U4af4980629...', $event->getUserId());
            $this->assertEquals('U4af4980629...', $event->getEventSourceId());
            $this->assertInstanceOf('LINE\LINEBot\Event\MessageEvent', $event);
            $this->assertInstanceOf('LINE\LINEBot\Event\MessageEvent\TextMessage', $event);
            /** @var TextMessage $event */
            $this->assertEquals('nHuyWiB7yP5Zw52FIkcQobQuGDXCTA', $event->getReplyToken());
            $this->assertEquals('325708', $event->getMessageId());
            $this->assertEquals('text', $event->getMessageType());
            $this->assertEquals('@example Hello, world! (love)', $event->getText());
            $mentioneeInfo = $event->getMentionees()[0];
            $this->assertEquals(0, $mentioneeInfo->getIndex());
            $this->assertEquals(8, $mentioneeInfo->getLength());
            $this->assertEquals('U0123456789abcd0123456789abcdef', $mentioneeInfo->getUserId());
        }

        {
            // text without mentionee userId
            $event = $events[33];
            $this->assertEquals(1462629479859, $event->getTimestamp());
            $this->assertEquals('active', $event->getMode());
            $this->assertTrue($event->isUserEvent());
            $this->assertEquals('U0123456789abcd0123456789abcdef', $event->getUserId());
            $this->assertEquals('U0123456789abcd0123456789abcdef', $event->getEventSourceId());
            $this->assertInstanceOf('LINE\LINEBot\Event\MessageEvent', $event);
            $this->assertInstanceOf('LINE\LINEBot\Event\MessageEvent\TextMessage', $event);
            /** @var TextMessage $event */
            $this->assertEquals('nHuyWiB7yP5Zw52FIkcQobQuGDXCTA', $event->getReplyToken());
            $this->assertEquals('325708', $event->getMessageId());
            $this->assertEquals('text', $event->getMessageType());
            $this->assertEquals('@example message without mentionee userId', $event->getText());
            $mentioneeInfo = $event->getMentionees()[0];
            $this->assertEquals(0, $mentioneeInfo->getIndex());
            $this->assertEquals(8, $mentioneeInfo->getLength());
            $this->assertEquals(null, $mentioneeInfo->getUserId());
        }

        {
            // text without mention
            $event = $events[34];
            $this->assertEquals(1462629479859, $event->getTimestamp());
            $this->assertEquals('active', $event->getMode());
            $this->assertTrue($event->isUserEvent());
            $this->assertEquals('U0123456789abcd0123456789abcdef', $event->getUserId());
            $this->assertEquals('U0123456789abcd0123456789abcdef', $event->getEventSourceId());
            $this->assertInstanceOf('LINE\LINEBot\Event\MessageEvent', $event);
            $this->assertInstanceOf('LINE\LINEBot\Event\MessageEvent\TextMessage', $event);
            /** @var TextMessage $event */
            $this->assertEquals('nHuyWiB7yP5Zw52FIkcQobQuGDXCTA', $event->getReplyToken());
            $this->assertEquals('325708', $event->getMessageId());
            $this->assertEquals('text', $event->getMessageType());
            $this->assertEquals('message without mention', $event->getText());
            $this->assertEquals(null, $event->getMentionees());
        }
    }
}

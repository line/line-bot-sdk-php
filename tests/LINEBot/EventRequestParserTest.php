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
use LINE\LINEBot\Event\BeaconDetectionEvent;
use LINE\LINEBot\Event\FollowEvent;
use LINE\LINEBot\Event\JoinEvent;
use LINE\LINEBot\Event\LeaveEvent;
use LINE\LINEBot\Event\MessageEvent\AudioMessage;
use LINE\LINEBot\Event\MessageEvent\ImageMessage;
use LINE\LINEBot\Event\MessageEvent\LocationMessage;
use LINE\LINEBot\Event\MessageEvent\StickerMessage;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use LINE\LINEBot\Event\MessageEvent\VideoMessage;
use LINE\LINEBot\Event\PostbackEvent;
use LINE\LINEBot\Event\UnfollowEvent;
use LINE\Tests\LINEBot\Util\DummyHttpClient;

class EventRequestParserTest extends \PHPUnit_Framework_TestCase
{
    private static $json = <<<JSON
{
 "events":[
  {
   "type":"message",
   "timestamp":12345678901234,
   "source":{
    "type":"user",
    "userId":"userid"
   },
   "replyToken":"replytoken",
   "message":{
    "id":"contentid",
    "type":"text",
    "text":"message"
   }
  },
  {
   "type":"message",
   "timestamp":12345678901234,
   "source":{
    "type":"group",
    "groupId":"groupid"
   },
   "replyToken":"replytoken",
   "message":{
    "id":"contentid",
    "type":"image"
   }
  },
  {
   "type":"message",
   "timestamp":12345678901234,
   "source":{
    "type":"room",
    "roomId":"roomid"
   },
   "replyToken":"replytoken",
   "message":{
    "id":"contentid",
    "type":"video"
   }
  },
  {
   "type":"message",
   "timestamp":12345678901234,
   "source":{
    "type":"room",
    "roomId":"roomid"
   },
   "replyToken":"replytoken",
   "message":{
    "id":"contentid",
    "type":"audio"
   }
  },
  {
   "type":"message",
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
    "stickerId":"2"
   }
  },
  {
   "type":"follow",
   "timestamp":12345678901234,
   "source":{
    "type":"user",
    "userId":"userid"
   },
   "replyToken":"replytoken"
  },
  {
   "type":"unfollow",
   "timestamp":12345678901234,
   "source":{
    "type":"user",
    "userId":"userid"
   }
  },
  {
   "type":"join",
   "timestamp":12345678901234,
   "source":{
    "type":"user",
    "userId":"userid"
   },
   "replyToken":"replytoken"
  },
  {
   "type":"leave",
   "timestamp":12345678901234,
   "source":{
    "type":"user",
    "userId":"userid"
   }
  },
  {
   "type":"postback",
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
   "timestamp":12345678901234,
   "source":{
    "type":"user",
    "userId":"userid"
   },
   "replyToken":"replytoken",
   "beacon":{
    "hwid":"bid",
    "type":"enter"
   }
  }
 ]
}
JSON;

    public function testParseEventRequest()
    {
        $bot = new LINEBot(new DummyHttpClient($this, function () {
        }), ['channelSecret' => 'testsecret']);
        $events = $bot->parseEventRequest($this::$json, 'Nq7AExtg27CQRfM3ngKtQxtVeIM/757ZTyDOrxQtWNg=');

        $this->assertEquals(count($events), 12);

        {
            // text
            $event = $events[0];
            $this->assertEquals(12345678901234, $event->getTimestamp());
            $this->assertTrue($event->isUserEvent());
            $this->assertEquals('userid', $event->getUserId());
            $this->assertInstanceOf('LINE\LINEBot\Event\MessageEvent', $event);
            $this->assertInstanceOf('LINE\LINEBot\Event\MessageEvent\TextMessage', $event);
            /** @var TextMessage $event */
            $this->assertEquals('replytoken', $event->getReplyToken());
            $this->assertEquals('contentid', $event->getMessageId());
            $this->assertEquals('message', $event->getText());
        }

        {
            // image
            $event = $events[1];
            $this->assertTrue($event->isGroupEvent());
            $this->assertEquals('groupid', $event->getGroupId());
            $this->assertInstanceOf('LINE\LINEBot\Event\MessageEvent\ImageMessage', $event);
            /** @var ImageMessage $event */
            $this->assertEquals('replytoken', $event->getReplyToken());
        }

        {
            // video
            $event = $events[2];
            $this->assertTrue($event->isRoomEvent());
            $this->assertEquals('roomid', $event->getRoomId());
            $this->assertInstanceOf('LINE\LINEBot\Event\MessageEvent\VideoMessage', $event);
            /** @var VideoMessage $event */
            $this->assertEquals('replytoken', $event->getReplyToken());
        }

        {
            // audio
            $event = $events[3];
            $this->assertInstanceOf('LINE\LINEBot\Event\MessageEvent\AudioMessage', $event);
            /** @var AudioMessage $event */
            $this->assertEquals('replytoken', $event->getReplyToken());
        }

        {
            // location
            $event = $events[4];
            $this->assertInstanceOf('LINE\LINEBot\Event\MessageEvent\LocationMessage', $event);
            /** @var LocationMessage $event */
            $this->assertEquals('replytoken', $event->getReplyToken());
            $this->assertEquals('label', $event->getTitle());
            $this->assertEquals('tokyo', $event->getAddress());
            $this->assertEquals('-34.12', $event->getLatitude());
            $this->assertEquals('134.23', $event->getLongitude());
        }

        {
            // sticker
            $event = $events[5];
            $this->assertInstanceOf('LINE\LINEBot\Event\MessageEvent\StickerMessage', $event);
            /** @var StickerMessage $event */
            $this->assertEquals('replytoken', $event->getReplyToken());
            $this->assertEquals(1, $event->getPackageId());
            $this->assertEquals(2, $event->getStickerId());
        }

        {
            // follow
            $event = $events[6];
            $this->assertInstanceOf('LINE\LINEBot\Event\FollowEvent', $event);
            /** @var FollowEvent $event */
            $this->assertEquals('replytoken', $event->getReplyToken());
        }

        {
            // unfollow
            $event = $events[7];
            $this->assertInstanceOf('LINE\LINEBot\Event\UnfollowEvent', $event);
            /** @var UnfollowEvent $event */
            $this->assertTrue($event->getReplyToken() === null);
        }

        {
            // join
            $event = $events[8];
            $this->assertInstanceOf('LINE\LINEBot\Event\JoinEvent', $event);
            /** @var JoinEvent $event */
            $this->assertEquals('replytoken', $event->getReplyToken());
        }

        {
            // leave
            $event = $events[9];
            $this->assertInstanceOf('LINE\LINEBot\Event\LeaveEvent', $event);
            /** @var LeaveEvent $event */
            $this->assertTrue($event->getReplyToken() === null);
        }

        {
            // postback
            $event = $events[10];
            $this->assertInstanceOf('LINE\LINEBot\Event\PostbackEvent', $event);
            /** @var PostbackEvent $event */
            $this->assertEquals('replytoken', $event->getReplyToken());
            $this->assertEquals('postback', $event->getPostbackData());
        }

        {
            // beacon
            $event = $events[11];
            $this->assertInstanceOf('LINE\LINEBot\Event\BeaconDetectionEvent', $event);
            /** @var BeaconDetectionEvent $event */
            $this->assertEquals('replytoken', $event->getReplyToken());
            $this->assertEquals('bid', $event->getHwid());
        }
    }
}

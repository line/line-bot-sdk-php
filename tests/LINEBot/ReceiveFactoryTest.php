<?php
/**
 * Copyright 2016 LINE Corporation
 *
 * LINE Corporation licenses this file to you under the Apache License,
 * version 2.0 (the "License"); you may not use this file except in compliance
 * with the License. You may obtain a copy of the License at:
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */
namespace LINE\Tests\LINEBot;

use LINE\LINEBot\Receive\Message\Text;
use LINE\LINEBot\Receive\Operation\AddContact;
use LINE\LINEBot\Receive\ReceiveFactory;

class ReceiveFactoryTest extends \PHPUnit_Framework_TestCase
{
    private static $config = [
        'channelId' => '1441301333',
        'channelSecret' => 'testsecret',
        'channelMid' => 'u0a556cffd4da0dd89c94fb36e36e1cdc',
    ];

    private static $json = <<<JSON
{
  "result":[
    {
      "from":"u206d25c2ea6bd87c17655609a1c37cb8",
      "fromChannel":"1341301815",
      "to":["u0cc15697597f61dd8b01cea8b027050e"],
      "toChannel":"1441301333",
      "eventType":"138311609000106303",
      "id":"ABCDEF-12345678901",
      "content":{
        "id":"325708",
        "createdTime":1332394961610,
        "from":"uff2aec188e58752ee1fb0f9507c6529a",
        "to":["u0a556cffd4da0dd89c94fb36e36e1cdc"],
        "toType":1,
        "contentType":1,
        "text":"hello"
      }
    },
    {
      "from":"u206d25c2ea6bd87c17655609a1c37cb8",
      "fromChannel":"1341301815",
      "to":["u0cc15697597f61dd8b01cea8b027050e"],
      "toChannel":"1441301333",
      "eventType":"138311609100106403",
      "id":"ABCDEF-12345678902",
      "content":{
        "revision":2469,
        "opType":4,
        "params":[
          "u0f3bfc598b061eba02183bfc5280886a",
          null,
          null
        ]
      }
    }
  ]
}
JSON;

    public function testCreateFromJSON()
    {
        $reqs = ReceiveFactory::createFromJSON($this::$config, $this::$json);
        $this->assertEquals(sizeof($reqs), 2);

        {
            $req = $reqs[0];
            $this->assertInstanceOf('\LINE\LINEBot\Receive\Message\Text', $req);
            /** @var Text $req */
            $this->assertTrue($req->isMessage());
            $this->assertFalse($req->isOperation());
            $this->assertTrue($req->isValidEvent());
            $this->assertTrue($req->isSentMe());

            $this->assertEquals($req->getId(), 'ABCDEF-12345678901');
            $this->assertEquals($req->getContentId(), '325708');
            $this->assertEquals($req->getCreatedTime(), '1332394961610');
            $this->assertEquals($req->getFromMid(), 'uff2aec188e58752ee1fb0f9507c6529a');

            $this->assertTrue($req->isText());
            $this->assertEquals($req->getText(), 'hello');
        }

        {
            $req = $reqs[1];
            $this->assertInstanceOf('\LINE\LINEBot\Receive\Operation\AddContact', $req);
            /** @var AddContact $req */
            $this->assertFalse($req->isMessage());
            $this->assertTrue($req->isOperation());
            $this->assertTrue($req->isValidEvent());

            $this->assertTrue($req->isAddContact());
            $this->assertEquals($req->getRevision(), '2469');
            $this->assertEquals($req->getFromMid(), 'u0f3bfc598b061eba02183bfc5280886a');
        }
    }
}
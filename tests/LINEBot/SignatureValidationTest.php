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

use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\GuzzleHTTPClient;
use LINE\LINEBot\Receive\Message\Text;
use LINE\LINEBot\Receive\ReceiveFactory;
use LINE\LINEBot\SignatureValidator;

class SignatureValidationTest extends \PHPUnit_Framework_TestCase
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

    public function testValidateSignature()
    {
        $this->assertTrue(SignatureValidator::validateSignature(
            $this::$json,
            $this::$config['channelSecret'],
            'kPXp0nPWSzfWAapWHiesbcztpKnXJoX8krCa1CcTghk='
        ));
        $this->assertFalse(SignatureValidator::validateSignature(
            $this::$json,
            $this::$config['channelSecret'],
            'XXX'
        ));
    }

    public function testValidateSignatureByReceive()
    {
        $reqs = ReceiveFactory::createFromJSON($this::$config, $this::$json);
        /** @var Text $req */
        $req = $reqs[0];
        $this->assertTrue($req->validateSignature($this::$json, 'kPXp0nPWSzfWAapWHiesbcztpKnXJoX8krCa1CcTghk='));
        $this->assertFalse($req->validateSignature($this::$json, 'XXX'));
    }

    public function testValidateSignatureByBot()
    {
        $bot = new LINEBot($this::$config, new GuzzleHTTPClient($this::$config));
        $this->assertTrue($bot->validateSignature($this::$json, 'kPXp0nPWSzfWAapWHiesbcztpKnXJoX8krCa1CcTghk='));
        $this->assertFalse($bot->validateSignature($this::$json, 'XXX'));
    }

    /**
     * @expectedException \LINE\LINEBot\Exception\InvalidSignatureException
     */
    public function testValidateSignatureWithEmptySignature()
    {
        SignatureValidator::validateSignature($this::$json, $this::$config['channelSecret'], '');
    }
}
<?php

/**
 * Copyright 2018 LINE Corporation
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
use LINE\LINEBot\Constant\Flex\ComponentLayout;
use LINE\LINEBot\MessageBuilder\RawMessageBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
use LINE\Tests\LINEBot\Util\DummyHttpClient;
use PHPUnit\Framework\TestCase;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\BoxComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\ButtonComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\IconComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\ImageComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\TextComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ContainerBuilder\CarouselContainerBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ContainerBuilder\BubbleContainerBuilder;
use LINE\LINEBot\MessageBuilder\FlexMessageBuilder;
use LINE\LINEBot\Constant\Flex\ComponentIconSize;
use LINE\LINEBot\Constant\Flex\ComponentImageSize;
use LINE\LINEBot\Constant\Flex\ComponentImageAspectRatio;
use LINE\LINEBot\Constant\Flex\ComponentImageAspectMode;
use LINE\LINEBot\Constant\Flex\ComponentFontSize;
use LINE\LINEBot\Constant\Flex\ComponentFontWeight;
use LINE\LINEBot\Constant\Flex\ComponentMargin;
use LINE\LINEBot\Constant\Flex\ComponentSpacing;
use LINE\LINEBot\Constant\Flex\ComponentButtonStyle;
use LINE\LINEBot\Constant\Flex\ComponentButtonHeight;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\SpacerComponentBuilder;
use LINE\LINEBot\Constant\Flex\ComponentSpaceSize;
use LINE\LINEBot\Constant\Flex\ComponentGravity;
use LINE\LINEBot\QuickReplyBuilder\ButtonBuilder\QuickReplyButtonBuilder;
use LINE\LINEBot\QuickReplyBuilder\QuickReplyMessageBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;

class SendFlexTest extends TestCase
{
    public function testReplyFlex()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit_Framework_TestCase $testRunner */
            $testRunner->assertEquals('POST', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/message/reply', $url);

            $testRunner->assertEquals('REPLY-TOKEN', $data['replyToken']);
            $testRunner->assertEquals(1, count($data['messages']));

            $message = $data['messages'][0];
            $json = <<<JSON
{
  "type": "flex",
  "altText": "alt test",
  "contents": {
    "type": "bubble",
    "body": {
      "type": "box",
      "layout": "vertical",
      "contents": [
        {
          "type": "text",
          "text": "Hello,"
        },
        {
          "type": "text",
          "text": "World!"
        }
      ]
    }
  },
  "quickReply": {
    "items": [
      {
        "type": "action",
        "action": {
          "type": "message",
          "label": "reply1",
          "text": "Reply1"
        }
      },
      {
        "type": "action",
        "action": {
          "type": "message",
          "label": "reply2",
          "text": "Reply2"
        }
      }
    ]
  }
}
JSON;
            $testRunner->assertEquals(json_decode($json, true), $message);

            return ['status' => 200];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->replyMessage(
            'REPLY-TOKEN',
            FlexMessageBuilder::builder()
                ->setAltText('alt test')
                ->setContents(
                    BubbleContainerBuilder::builder()
                        ->setBody(
                            BoxComponentBuilder::builder()
                                ->setLayout(ComponentLayout::VERTICAL)
                                ->setContents([
                                    new TextComponentBuilder('Hello,'),
                                    new TextComponentBuilder('World!')
                                ])
                        )
                )
                ->setQuickReply(
                    new QuickReplyMessageBuilder([
                        new QuickReplyButtonBuilder(
                            new MessageTemplateActionBuilder('reply1', 'Reply1')
                        ),
                        new QuickReplyButtonBuilder(
                            new MessageTemplateActionBuilder('reply2', 'Reply2')
                        )
                    ])
                )
        );

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(200, $res->getJSONDecodedBody()['status']);

        $res = $bot->replyMessage(
            'REPLY-TOKEN',
            new RawMessageBuilder(
                [
                    'type' => 'flex',
                    'altText' => 'alt test',
                    'contents' => [
                        'type' => 'bubble',
                        'body' => [
                            'type' => 'box',
                            'layout' => 'vertical',
                            'contents' => [
                                [
                                    'type' => 'text',
                                    'text' => 'Hello,'
                                ],
                                [
                                    'type' => 'text',
                                    'text' => 'World!'
                                ]
                            ]
                        ]
                    ],
                    'quickReply' => [
                        'items' => [
                            [
                                'type' => 'action',
                                'action' => [
                                    'type' => 'message',
                                    'label' => 'reply1',
                                    'text' => 'Reply1'
                                ]
                            ],
                            [
                                'type' => 'action',
                                'action' => [
                                    'type' => 'message',
                                    'label' => 'reply2',
                                    'text' => 'Reply2'
                                ]
                            ]
                        ]
                    ]
                ]
            )
        );

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(200, $res->getJSONDecodedBody()['status']);
    }

    public function testPushRestaurant()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit_Framework_TestCase $testRunner */
            $testRunner->assertEquals('POST', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/message/push', $url);

            $testRunner->assertEquals('DESTINATION', $data['to']);
            $testRunner->assertEquals(1, count($data['messages']));

            $message = $data['messages'][0];
            $json = <<<JSON
{
  "type": "flex",
  "altText": "Restaurant",
  "contents": {
    "type": "bubble",
    "hero": {
      "type": "image",
      "url": "https://example.com/cafe.png",
      "size": "full",
      "aspectRatio": "20:13",
      "aspectMode": "cover",
      "action": {
        "type": "uri",
        "uri": "https://example.com"
      }
    },
    "body": {
      "type": "box",
      "layout": "vertical",
      "contents": [
        {
          "type": "text",
          "text": "Brown Cafe",
          "weight": "bold",
          "size": "xl"
        },
        {
          "type": "box",
          "layout": "baseline",
          "margin": "md",
          "contents": [
            {
              "type": "icon",
              "size": "sm",
              "url": "https://example.com/gold_star.png"
            },
            {
              "type": "icon",
              "size": "sm",
              "url": "https://example.com/gold_star.png"
            },
            {
              "type": "icon",
              "size": "sm",
              "url": "https://example.com/gold_star.png"
            },
            {
              "type": "icon",
              "size": "sm",
              "url": "https://example.com/gold_star.png"
            },
            {
              "type": "icon",
              "size": "sm",
              "url": "https://example.com/gray_star.png"
            },
            {
              "type": "text",
              "text": "4.0",
              "size": "sm",
              "color": "#999999",
              "margin": "md",
              "flex": 0
            }
          ]
        },
        {
          "type": "box",
          "layout": "vertical",
          "margin": "lg",
          "spacing": "sm",
          "contents": [
            {
              "type": "box",
              "layout": "baseline",
              "spacing": "sm",
              "contents": [
                {
                  "type": "text",
                  "text": "Place",
                  "color": "#aaaaaa",
                  "size": "sm",
                  "flex": 1
                },
                {
                  "type": "text",
                  "text": "Miraina Tower, 4-1-6 Shinjuku, Tokyo",
                  "wrap": true,
                  "color": "#666666",
                  "size": "sm",
                  "flex": 5
                }
              ]
            },
            {
              "type": "box",
              "layout": "baseline",
              "spacing": "sm",
              "contents": [
                {
                  "type": "text",
                  "text": "Time",
                  "color": "#aaaaaa",
                  "size": "sm",
                  "flex": 1
                },
                {
                  "type": "text",
                  "text": "10:00 - 23:00",
                  "wrap": true,
                  "color": "#666666",
                  "size": "sm",
                  "flex": 5
                }
              ]
            }
          ]
        }
      ]
    },
    "footer": {
      "type": "box",
      "layout": "vertical",
      "spacing": "sm",
      "contents": [
        {
          "type": "button",
          "style": "link",
          "height": "sm",
          "action": {
            "type": "uri",
            "label": "CALL",
            "uri": "https://example.com"
          }
        },
        {
          "type": "button",
          "style": "link",
          "height": "sm",
          "action": {
            "type": "uri",
            "label": "WEBSITE",
            "uri": "https://example.com"
          }
        },
        {
          "type": "spacer",
          "size": "sm"
        }
      ],
      "flex": 0
    }
  }
}
JSON;
            $testRunner->assertEquals(json_decode($json, true), $message);

            return ['status' => 200];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $goldStar = IconComponentBuilder::builder()
            ->setSize(ComponentIconSize::SM)
            ->setUrl('https://example.com/gold_star.png');
        $grayStar = IconComponentBuilder::builder()
            ->setSize(ComponentIconSize::SM)
            ->setUrl('https://example.com/gray_star.png');
        $res = $bot->pushMessage(
            'DESTINATION',
            FlexMessageBuilder::builder()
                ->setAltText('Restaurant')
                ->setContents(
                    BubbleContainerBuilder::builder()
                        ->setHero(
                            ImageComponentBuilder::builder()
                                ->setUrl('https://example.com/cafe.png')
                                ->setSize(ComponentImageSize::FULL)
                                ->setAspectRatio(ComponentImageAspectRatio::R20TO13)
                                ->setAspectMode(ComponentImageAspectMode::COVER)
                                ->setAction(new UriTemplateActionBuilder(null, 'https://example.com'))
                        )
                        ->setBody(
                            BoxComponentBuilder::builder()
                                ->setLayout(ComponentLayout::VERTICAL)
                                ->setContents([
                                    TextComponentBuilder::builder()
                                        ->setText('Brown Cafe')
                                        ->setWeight(ComponentFontWeight::BOLD)
                                        ->setSize(ComponentFontSize::XL),
                                    BoxComponentBuilder::builder()
                                        ->setLayout(ComponentLayout::BASELINE)
                                        ->setMargin(ComponentMargin::MD)
                                        ->setContents([
                                            $goldStar,
                                            $goldStar,
                                            $goldStar,
                                            $goldStar,
                                            $grayStar,
                                            TextComponentBuilder::builder()
                                                ->setText('4.0')
                                                ->setSize(ComponentFontSize::SM)
                                                ->setColor('#999999')
                                                ->setMargin(ComponentMargin::MD)
                                                ->setFlex(0)
                                        ]),
                                    BoxComponentBuilder::builder()
                                        ->setLayout(ComponentLayout::VERTICAL)
                                        ->setMargin(ComponentMargin::LG)
                                        ->setSpacing(ComponentSpacing::SM)
                                        ->setContents([
                                            BoxComponentBuilder::builder()
                                                ->setLayout(ComponentLayout::BASELINE)
                                                ->setSpacing(ComponentSpacing::SM)
                                                ->setContents([
                                                    TextComponentBuilder::builder()
                                                        ->setText('Place')
                                                        ->setColor('#aaaaaa')
                                                        ->setSize(ComponentFontSize::SM)
                                                        ->setFlex(1),
                                                    TextComponentBuilder::builder()
                                                        ->setText('Miraina Tower, 4-1-6 Shinjuku, Tokyo')
                                                        ->setWrap(true)
                                                        ->setColor('#666666')
                                                        ->setSize(ComponentFontSize::SM)
                                                        ->setFlex(5)
                                                ]),
                                            BoxComponentBuilder::builder()
                                                ->setLayout(ComponentLayout::BASELINE)
                                                ->setSpacing(ComponentSpacing::SM)
                                                ->setContents([
                                                    TextComponentBuilder::builder()
                                                        ->setText('Time')
                                                        ->setColor('#aaaaaa')
                                                        ->setSize(ComponentFontSize::SM)
                                                        ->setFlex(1),
                                                    TextComponentBuilder::builder()
                                                        ->setText('10:00 - 23:00')
                                                        ->setWrap(true)
                                                        ->setColor('#666666')
                                                        ->setSize(ComponentFontSize::SM)
                                                        ->setFlex(5)
                                                ])

                                        ])
                                ])
                        )
                        ->setFooter(
                            BoxComponentBuilder::builder()
                                ->setLayout(ComponentLayout::VERTICAL)
                                ->setSpacing(ComponentSpacing::SM)
                                ->setFlex(0)
                                ->setContents([
                                    ButtonComponentBuilder::builder()
                                        ->setStyle(ComponentButtonStyle::LINK)
                                        ->setHeight(ComponentButtonHeight::SM)
                                        ->setAction(
                                            new UriTemplateActionBuilder(
                                                'CALL',
                                                'https://example.com'
                                            )
                                        ),
                                    ButtonComponentBuilder::builder()
                                        ->setStyle(ComponentButtonStyle::LINK)
                                        ->setHeight(ComponentButtonHeight::SM)
                                        ->setAction(
                                            new UriTemplateActionBuilder(
                                                'WEBSITE',
                                                'https://example.com'
                                            )
                                        ),
                                    SpacerComponentBuilder::builder()
                                        ->setSize(ComponentSpaceSize::SM)
                                ])
                        )
                )
        );

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(200, $res->getJSONDecodedBody()['status']);

        $res = $bot->pushMessage(
            'DESTINATION',
            new RawMessageBuilder(
                [
                    'type' => 'flex',
                    'altText' => 'Restaurant',
                    'contents' => [
                        'type' => 'bubble',
                        'hero' => [
                            'type' => 'image',
                            'url' => 'https://example.com/cafe.png',
                            'size' => 'full',
                            'aspectRatio' => '20:13',
                            'aspectMode' => 'cover',
                            'action' => [
                                'type' => 'uri',
                                'uri' => 'https://example.com'
                            ]
                        ],
                        'body' => [
                            'type' => 'box',
                            'layout' => 'vertical',
                            'contents' => [
                                [
                                    'type' => 'text',
                                    'text' => 'Brown Cafe',
                                    'weight' => 'bold',
                                    'size' => 'xl'
                                ],
                                [
                                    'type' => 'box',
                                    'layout' => 'baseline',
                                    'margin' => 'md',
                                    'contents' => [
                                        [
                                            'type' => 'icon',
                                            'size' => 'sm',
                                            'url' => 'https://example.com/gold_star.png'
                                        ],
                                        [
                                            'type' => 'icon',
                                            'size' => 'sm',
                                            'url' => 'https://example.com/gold_star.png'
                                        ],
                                        [
                                            'type' => 'icon',
                                            'size' => 'sm',
                                            'url' => 'https://example.com/gold_star.png'
                                        ],
                                        [
                                            'type' => 'icon',
                                            'size' => 'sm',
                                            'url' => 'https://example.com/gold_star.png'
                                        ],
                                        [
                                            'type' => 'icon',
                                            'size' => 'sm',
                                            'url' => 'https://example.com/gray_star.png'
                                        ],
                                        [
                                            'type' => 'text',
                                            'text' => '4.0',
                                            'size' => 'sm',
                                            'color' => '#999999',
                                            'margin' => 'md',
                                            'flex' => 0
                                        ]
                                    ]
                                ],
                                [
                                    'type' => 'box',
                                    'layout' => 'vertical',
                                    'margin' => 'lg',
                                    'spacing' => 'sm',
                                    'contents' => [
                                        [
                                            'type' => 'box',
                                            'layout' => 'baseline',
                                            'spacing' => 'sm',
                                            'contents' => [
                                                [
                                                    'type' => 'text',
                                                    'text' => 'Place',
                                                    'color' => '#aaaaaa',
                                                    'size' => 'sm',
                                                    'flex' => 1
                                                ],
                                                [
                                                    'type' => 'text',
                                                    'text' => 'Miraina Tower, 4-1-6 Shinjuku, Tokyo',
                                                    'wrap' => true,
                                                    'color' => '#666666',
                                                    'size' => 'sm',
                                                    'flex' => 5
                                                ]
                                            ]
                                        ],
                                        [
                                            'type' => 'box',
                                            'layout' => 'baseline',
                                            'spacing' => 'sm',
                                            'contents' => [
                                                [
                                                    'type' => 'text',
                                                    'text' => 'Time',
                                                    'color' => '#aaaaaa',
                                                    'size' => 'sm',
                                                    'flex' => 1
                                                ],
                                                [
                                                    'type' => 'text',
                                                    'text' => '10:00 - 23:00',
                                                    'wrap' => true,
                                                    'color' => '#666666',
                                                    'size' => 'sm',
                                                    'flex' => 5
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'footer' => [
                            'type' => 'box',
                            'layout' => 'vertical',
                            'spacing' => 'sm',
                            'contents' => [
                                [
                                    'type' => 'button',
                                    'style' => 'link',
                                    'height' => 'sm',
                                    'action' => [
                                        'type' => 'uri',
                                        'label' => 'CALL',
                                        'uri' => 'https://example.com'
                                    ]
                                ],
                                [
                                    'type' => 'button',
                                    'style' => 'link',
                                    'height' => 'sm',
                                    'action' => [
                                        'type' => 'uri',
                                        'label' => 'WEBSITE',
                                        'uri' => 'https://example.com'
                                    ]
                                ],
                                [
                                    'type' => 'spacer',
                                    'size' => 'sm'
                                ]
                            ],
                            'flex' => 0
                        ]
                    ]
                ]
            )
        );

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(200, $res->getJSONDecodedBody()['status']);
    }

    public function testPushShopping()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit_Framework_TestCase $testRunner */
            $testRunner->assertEquals('POST', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/message/push', $url);

            $testRunner->assertEquals('DESTINATION', $data['to']);
            $testRunner->assertEquals(1, count($data['messages']));

            $message = $data['messages'][0];
            $json = <<<JSON
{
  "type": "flex",
  "altText": "Shopping",
  "contents": {
    "type": "carousel",
    "contents": [
      {
        "type": "bubble",
        "hero": {
          "type": "image",
          "size": "full",
          "aspectRatio": "20:13",
          "aspectMode": "cover",
          "url": "https://example.com/photo1.png"
        },
        "body": {
          "type": "box",
          "layout": "vertical",
          "spacing": "sm",
          "contents": [
            {
              "type": "text",
              "text": "Arm Chair, White",
              "wrap": true,
              "weight": "bold",
              "size": "xl"
            },
            {
              "type": "box",
              "layout": "baseline",
              "contents": [
                {
                  "type": "text",
                  "text": "$49",
                  "wrap": true,
                  "weight": "bold",
                  "size": "xl",
                  "flex": 0
                },
                {
                  "type": "text",
                  "text": ".99",
                  "wrap": true,
                  "weight": "bold",
                  "size": "sm",
                  "flex": 0
                }
              ]
            }
          ]
        },
        "footer": {
          "type": "box",
          "layout": "vertical",
          "spacing": "sm",
          "contents": [
            {
              "type": "button",
              "style": "primary",
              "action": {
                "type": "uri",
                "label": "Add to Cart",
                "uri": "https://example.com"
              }
            },
            {
              "type": "button",
              "action": {
                "type": "uri",
                "label": "Add to wishlist",
                "uri": "https://example.com"
              }
            }
          ]
        }
      },
      {
        "type": "bubble",
        "hero": {
          "type": "image",
          "size": "full",
          "aspectRatio": "20:13",
          "aspectMode": "cover",
          "url": "https://example.com/photo2.png"
        },
        "body": {
          "type": "box",
          "layout": "vertical",
          "spacing": "sm",
          "contents": [
            {
              "type": "text",
              "text": "Metal Desk Lamp",
              "wrap": true,
              "weight": "bold",
              "size": "xl"
            },
            {
              "type": "box",
              "layout": "baseline",
              "contents": [
                {
                  "type": "text",
                  "text": "$11",
                  "wrap": true,
                  "weight": "bold",
                  "size": "xl",
                  "flex": 0
                },
                {
                  "type": "text",
                  "text": ".99",
                  "wrap": true,
                  "weight": "bold",
                  "size": "sm",
                  "flex": 0
                }
              ]
            },
            {
              "type": "text",
              "text": "Temporarily out of stock",
              "wrap": true,
              "size": "xxs",
              "margin": "md",
              "color": "#ff5551",
              "flex": 0
            }
          ]
        },
        "footer": {
          "type": "box",
          "layout": "vertical",
          "spacing": "sm",
          "contents": [
            {
              "type": "button",
              "style": "primary",
              "color": "#aaaaaa",
              "action": {
                "type": "uri",
                "label": "Add to Cart",
                "uri": "https://example.com"
              }
            },
            {
              "type": "button",
              "action": {
                "type": "uri",
                "label": "Add to wishlist",
                "uri": "https://example.com"
              }
            }
          ]
        }
      },
      {
        "type": "bubble",
        "body": {
          "type": "box",
          "layout": "vertical",
          "spacing": "sm",
          "contents": [
            {
              "type": "button",
              "flex": 1,
              "gravity": "center",
              "action": {
                "type": "uri",
                "label": "See more",
                "uri": "https://example.com"
              }
            }
          ]
        }
      }
    ]
  }
}
JSON;
            $testRunner->assertEquals(json_decode($json, true), $message);

            return ['status' => 200];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->pushMessage(
            'DESTINATION',
            FlexMessageBuilder::builder()
                ->setAltText('Shopping')
                ->setContents(
                    CarouselContainerBuilder::builder()
                        ->setContents([
                            BubbleContainerBuilder::builder()
                                ->setHero(
                                    ImageComponentBuilder::builder()
                                        ->setSize(ComponentImageSize::FULL)
                                        ->setAspectRatio(ComponentImageAspectRatio::R20TO13)
                                        ->setAspectMode(ComponentImageAspectMode::COVER)
                                        ->setUrl('https://example.com/photo1.png')
                                )
                                ->setBody(
                                    BoxComponentBuilder::builder()
                                        ->setLayout(ComponentLayout::VERTICAL)
                                        ->setSpacing(ComponentSpacing::SM)
                                        ->setContents([
                                            TextComponentBuilder::builder()
                                                ->setText('Arm Chair, White')
                                                ->setWrap(true)
                                                ->setWeight(ComponentFontWeight::BOLD)
                                                ->setSize(ComponentFontSize::XL),
                                            BoxComponentBuilder::builder()
                                                ->setLayout(ComponentLayout::BASELINE)
                                                ->setContents([
                                                    TextComponentBuilder::builder()
                                                        ->setText('$49')
                                                        ->setWrap(true)
                                                        ->setWeight(ComponentFontWeight::BOLD)
                                                        ->setSize(ComponentFontSize::XL)
                                                        ->setFlex(0),
                                                    TextComponentBuilder::builder()
                                                        ->setText('.99')
                                                        ->setWrap(true)
                                                        ->setWeight(ComponentFontWeight::BOLD)
                                                        ->setSize(ComponentFontSize::SM)
                                                        ->setFlex(0)
                                                ])
                                        ])
                                )
                                ->setFooter(
                                    BoxComponentBuilder::builder()
                                        ->setLayout(ComponentLayout::VERTICAL)
                                        ->setSpacing(ComponentSpacing::SM)
                                        ->setContents([
                                            ButtonComponentBuilder::builder()
                                                ->setStyle(ComponentButtonStyle::PRIMARY)
                                                ->setAction(
                                                    new UriTemplateActionBuilder(
                                                        'Add to Cart',
                                                        'https://example.com'
                                                    )
                                                ),
                                            ButtonComponentBuilder::builder()
                                                ->setAction(
                                                    new UriTemplateActionBuilder(
                                                        'Add to wishlist',
                                                        'https://example.com'
                                                    )
                                                )
                                        ])
                                ),
                            BubbleContainerBuilder::builder()
                                ->setHero(
                                    ImageComponentBuilder::builder()
                                        ->setSize(ComponentImageSize::FULL)
                                        ->setAspectRatio(ComponentImageAspectRatio::R20TO13)
                                        ->setAspectMode(ComponentImageAspectMode::COVER)
                                        ->setUrl('https://example.com/photo2.png')
                                )
                                ->setBody(
                                    BoxComponentBuilder::builder()
                                        ->setLayout(ComponentLayout::VERTICAL)
                                        ->setSpacing(ComponentSpacing::SM)
                                        ->setContents([
                                            TextComponentBuilder::builder()
                                                ->setText('Metal Desk Lamp')
                                                ->setWrap(true)
                                                ->setWeight(ComponentFontWeight::BOLD)
                                                ->setSize(ComponentFontSize::XL),
                                            BoxComponentBuilder::builder()
                                                ->setLayout(ComponentLayout::BASELINE)
                                                ->setContents([
                                                    TextComponentBuilder::builder()
                                                        ->setText('$11')
                                                        ->setWrap(true)
                                                        ->setWeight(ComponentFontWeight::BOLD)
                                                        ->setSize(ComponentFontSize::XL)
                                                        ->setFlex(0),
                                                    TextComponentBuilder::builder()
                                                        ->setText('.99')
                                                        ->setWrap(true)
                                                        ->setWeight(ComponentFontWeight::BOLD)
                                                        ->setSize(ComponentFontSize::SM)
                                                        ->setFlex(0)
                                                ]),
                                            TextComponentBuilder::builder()
                                                ->setText('Temporarily out of stock')
                                                ->setWrap(true)
                                                ->setSize(ComponentFontSize::XXS)
                                                ->setMargin(ComponentMargin::MD)
                                                ->setColor('#ff5551')
                                                ->setFlex(0)
                                        ])
                                )
                                ->setFooter(
                                    BoxComponentBuilder::builder()
                                        ->setLayout(ComponentLayout::VERTICAL)
                                        ->setSpacing(ComponentSpacing::SM)
                                        ->setContents([
                                            ButtonComponentBuilder::builder()
                                                ->setStyle(ComponentButtonStyle::PRIMARY)
                                                ->setColor('#aaaaaa')
                                                ->setAction(
                                                    new UriTemplateActionBuilder(
                                                        'Add to Cart',
                                                        'https://example.com'
                                                    )
                                                ),
                                            ButtonComponentBuilder::builder()
                                                ->setAction(
                                                    new UriTemplateActionBuilder(
                                                        'Add to wishlist',
                                                        'https://example.com'
                                                    )
                                                )
                                        ])
                                ),
                            BubbleContainerBuilder::builder()
                                ->setBody(
                                    BoxComponentBuilder::builder()
                                        ->setLayout(ComponentLayout::VERTICAL)
                                        ->setSpacing(ComponentSpacing::SM)
                                        ->setContents([
                                            ButtonComponentBuilder::builder()
                                                ->setFlex(1)
                                                ->setGravity(ComponentGravity::CENTER)
                                                ->setAction(
                                                    new UriTemplateActionBuilder(
                                                        'See more',
                                                        'https://example.com'
                                                    )
                                                )
                                        ])
                                )
                        ])
                )
        );

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(200, $res->getJSONDecodedBody()['status']);

        $res = $bot->pushMessage(
            'DESTINATION',
            new RawMessageBuilder(
                [
                    'type' => 'flex',
                    'altText' => 'Shopping',
                    'contents' => [
                        'type' => 'carousel',
                        'contents' => [
                            [
                                'type' => 'bubble',
                                'hero' => [
                                    'type' => 'image',
                                    'size' => 'full',
                                    'aspectRatio' => '20:13',
                                    'aspectMode' => 'cover',
                                    'url' => 'https://example.com/photo1.png'
                                ],
                                'body' => [
                                    'type' => 'box',
                                    'layout' => 'vertical',
                                    'spacing' => 'sm',
                                    'contents' => [
                                        [
                                            'type' => 'text',
                                            'text' => 'Arm Chair, White',
                                            'wrap' => true,
                                            'weight' => 'bold',
                                            'size' => 'xl'
                                        ],
                                        [
                                            'type' => 'box',
                                            'layout' => 'baseline',
                                            'contents' => [
                                                [
                                                    'type' => 'text',
                                                    'text' => '$49',
                                                    'wrap' => true,
                                                    'weight' => 'bold',
                                                    'size' => 'xl',
                                                    'flex' => 0
                                                ],
                                                [
                                                    'type' => 'text',
                                                    'text' => '.99',
                                                    'wrap' => true,
                                                    'weight' => 'bold',
                                                    'size' => 'sm',
                                                    'flex' => 0
                                                ]
                                            ]
                                        ]
                                    ]
                                ],
                                'footer' => [
                                    'type' => 'box',
                                    'layout' => 'vertical',
                                    'spacing' => 'sm',
                                    'contents' => [
                                        [
                                            'type' => 'button',
                                            'style' => 'primary',
                                            'action' => [
                                                'type' => 'uri',
                                                'label' => 'Add to Cart',
                                                'uri' => 'https://example.com'
                                            ]
                                        ],
                                        [
                                            'type' => 'button',
                                            'action' => [
                                                'type' => 'uri',
                                                'label' => 'Add to wishlist',
                                                'uri' => 'https://example.com'
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            [
                                'type' => 'bubble',
                                'hero' => [
                                    'type' => 'image',
                                    'size' => 'full',
                                    'aspectRatio' => '20:13',
                                    'aspectMode' => 'cover',
                                    'url' => 'https://example.com/photo2.png'
                                ],
                                'body' => [
                                    'type' => 'box',
                                    'layout' => 'vertical',
                                    'spacing' => 'sm',
                                    'contents' => [
                                        [
                                            'type' => 'text',
                                            'text' => 'Metal Desk Lamp',
                                            'wrap' => true,
                                            'weight' => 'bold',
                                            'size' => 'xl'
                                        ],
                                        [
                                            'type' => 'box',
                                            'layout' => 'baseline',
                                            'contents' => [
                                                [
                                                    'type' => 'text',
                                                    'text' => '$11',
                                                    'wrap' => true,
                                                    'weight' => 'bold',
                                                    'size' => 'xl',
                                                    'flex' => 0
                                                ],
                                                [
                                                    'type' => 'text',
                                                    'text' => '.99',
                                                    'wrap' => true,
                                                    'weight' => 'bold',
                                                    'size' => 'sm',
                                                    'flex' => 0
                                                ]
                                            ]
                                        ],
                                        [
                                            'type' => 'text',
                                            'text' => 'Temporarily out of stock',
                                            'wrap' => true,
                                            'size' => 'xxs',
                                            'margin' => 'md',
                                            'color' => '#ff5551',
                                            'flex' => 0
                                        ]
                                    ]
                                ],
                                'footer' => [
                                    'type' => 'box',
                                    'layout' => 'vertical',
                                    'spacing' => 'sm',
                                    'contents' => [
                                        [
                                            'type' => 'button',
                                            'style' => 'primary',
                                            'color' => '#aaaaaa',
                                            'action' => [
                                                'type' => 'uri',
                                                'label' => 'Add to Cart',
                                                'uri' => 'https://example.com'
                                            ]
                                        ],
                                        [
                                            'type' => 'button',
                                            'action' => [
                                                'type' => 'uri',
                                                'label' => 'Add to wishlist',
                                                'uri' => 'https://example.com'
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            [
                                'type' => 'bubble',
                                'body' => [
                                    'type' => 'box',
                                    'layout' => 'vertical',
                                    'spacing' => 'sm',
                                    'contents' => [
                                        [
                                            'type' => 'button',
                                            'flex' => 1,
                                            'gravity' => 'center',
                                            'action' => [
                                                'type' => 'uri',
                                                'label' => 'See more',
                                                'uri' => 'https://example.com'
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            )
        );

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(200, $res->getJSONDecodedBody()['status']);
    }
}

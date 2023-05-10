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

namespace LINE\LINEBot\KitchenSink\EventHandler\MessageHandler\Flex;

use LINE\Clients\MessagingApi\Model\AltUri;
use LINE\Clients\MessagingApi\Model\FlexBox;
use LINE\Clients\MessagingApi\Model\FlexBubble;
use LINE\Clients\MessagingApi\Model\FlexButton;
use LINE\Clients\MessagingApi\Model\FlexCarousel;
use LINE\Clients\MessagingApi\Model\FlexImage;
use LINE\Clients\MessagingApi\Model\FlexMessage;
use LINE\Clients\MessagingApi\Model\FlexText;
use LINE\Clients\MessagingApi\Model\URIAction;
use LINE\Constants\ActionType;
use LINE\Constants\Flex\ComponentButtonStyle;
use LINE\Constants\Flex\ComponentFontSize;
use LINE\Constants\Flex\ComponentFontWeight;
use LINE\Constants\Flex\ComponentGravity;
use LINE\Constants\Flex\ComponentImageAspectMode;
use LINE\Constants\Flex\ComponentImageAspectRatio;
use LINE\Constants\Flex\ComponentImageSize;
use LINE\Constants\Flex\ComponentLayout;
use LINE\Constants\Flex\ComponentMargin;
use LINE\Constants\Flex\ComponentSpacing;
use LINE\Constants\Flex\ComponentType;
use LINE\Constants\Flex\ContainerType;
use LINE\Constants\MessageType;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class FlexSampleShopping
{
    private static $items = [
        '111' => [
            'photo' => 'https://example.com/photo1.png',
            'name' => 'Arm Chair, White',
            'price' => 49.99,
            'stock' => true,
        ],
        '112' => [
            'photo' => 'https://example.com/photo2.png',
            'name' => 'Metal Desk Lamp',
            'price' => 11.99,
            'stock' => false,
        ],
    ];

    /**
     * Create sample shopping flex message
     *
     * @return \LINE\Clients\MessagingApi\Model\FlexMessage
     */
    public static function get(): FlexMessage
    {
        return new FlexMessage([
            'type' => MessageType::FLEX,
            'altText' => 'Shopping',
            'contents' => new FlexCarousel([
                'type' => ContainerType::CAROUSEL,
                'contents' => [
                    self::createItemBubble(111),
                    self::createItemBubble(112),
                    self::createMoreBubble(),
                ],
            ]),
        ]);
    }

    private static function createItemBubble($itemId): FlexBubble
    {
        $item = self::$items[$itemId];
        return new FlexBubble([
            'type' => ContainerType::BUBBLE,
            'hero' => self::createItemHeroBlock($item),
            'body' => self::createItemBodyBlock($item),
            'footer' => self::createItemFooterBlock($item),
        ]);
    }

    private static function createItemHeroBlock($item): FlexImage
    {
        return new FlexImage([
            'type' => ComponentType::IMAGE,
            'url' => $item['photo'],
            'size' => ComponentImageSize::FULL,
            'aspectRatio' => ComponentImageAspectRatio::R20TO13,
            'aspectMode' => ComponentImageAspectMode::COVER,
        ]);
    }

    private static function createItemBodyBlock($item): FlexBox
    {
        $price = explode('.', number_format($item['price'], 2));
        $components = [
            new FlexText([
                'type' => ComponentType::TEXT,
                'text' => $item['name'],
                'wrap' => true,
                'weight' => ComponentFontWeight::BOLD,
                'size' => ComponentFontSize::XL,
            ]),
            new FlexBox([
                'type' => ComponentType::BOX,
                'layout' => ComponentLayout::BASELINE,
                'contents' => [
                    new FlexText([
                        'type' => ComponentType::TEXT,
                        'text' => '$' . $price[0],
                        'wrap' => true,
                        'weight' => ComponentFontWeight::BOLD,
                        'size' => ComponentFontSize::XL,
                        'flex' => 0,
                    ]),
                    new FlexText([
                        'type' => ComponentType::TEXT,
                        'text' => '.' . $price[1],
                        'wrap' => true,
                        'weight' => ComponentFontWeight::BOLD,
                        'size' => ComponentFontSize::SM,
                        'flex' => 0,
                    ]),
                ],
            ])
        ];

        if (!$item['stock']) {
            $components[] = new FlexText([
                'type' => ComponentType::TEXT,
                'text' => 'Temporarily out of stock',
                'wrap' => true,
                'size' => ComponentFontSize::XXS,
                'margin' => ComponentMargin::MD,
                'color' => '#ff5551',
                'flex' => 0,
            ]);
        }

        return new FlexBox([
            'type' => ComponentType::BOX,
            'layout' => ComponentLayout::VERTICAL,
            'spacing' => ComponentSpacing::SM,
            'contents' => $components,
        ]);
    }

    private static function createItemFooterBlock($item): FlexBox
    {
        $color = $item['stock'] ? null : '#aaaaaa';
        $cartButton = new FlexButton([
            'type' => ComponentType::BUTTON,
            'style' => ComponentButtonStyle::PRIMARY,
            'color' => $color,
            'action' => new URIAction([
                'type' => ActionType::URI,
                'label' => 'Add to Cart',
                'uri' => 'https://example.com',
                'altUri' => new AltUri(['desktop' => 'https://example.com#desktop']),
            ]),
        ]);

        $wishButton = new FlexButton([
            'type' => ComponentType::BUTTON,
            'action' => new URIAction([
                'type' => ActionType::URI,
                'label' => 'Add to wishlist',
                'uri' => 'https://example.com',
                'altUri' => new AltUri(['desktop' => 'https://example.com#desktop']),
            ]),
        ]);

        return new FlexBox([
            'type' => ComponentType::BOX,
            'layout' => ComponentLayout::VERTICAL,
            'spacing' => ComponentSpacing::SM,
            'contents' => [$cartButton, $wishButton],
        ]);
    }

    private static function createMoreBubble(): FlexBubble
    {
        return new FlexBubble([
            'type' => ContainerType::BUBBLE,
            'body' => new FlexBox([
                'type' => ComponentType::BOX,
                'layout' => ComponentLayout::VERTICAL,
                'spacing' => ComponentSpacing::SM,
                'contents' => [
                    new FlexButton([
                        'type' => ComponentType::BUTTON,
                        'flex' => 1,
                        'gravity' => ComponentGravity::CENTER,
                        'action' => new URIAction([
                            'type' => ActionType::URI,
                            'label' => 'See more',
                            'uri' => 'https://example.com',
                            'altUri' => new AltUri(['desktop' => 'https://example.com#desktop']),
                        ]),
                    ]),
                ],
            ]),
        ]);
    }
}

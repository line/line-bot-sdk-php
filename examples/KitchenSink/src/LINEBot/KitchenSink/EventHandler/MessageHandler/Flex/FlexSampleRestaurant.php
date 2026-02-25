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
use LINE\Clients\MessagingApi\Model\FlexComponent;
use LINE\Clients\MessagingApi\Model\FlexIcon;
use LINE\Clients\MessagingApi\Model\FlexImage;
use LINE\Clients\MessagingApi\Model\FlexMessage;
use LINE\Clients\MessagingApi\Model\FlexSpacer;
use LINE\Clients\MessagingApi\Model\FlexSpan;
use LINE\Clients\MessagingApi\Model\FlexText;
use LINE\Clients\MessagingApi\Model\URIAction;
use LINE\Constants\Flex\BubbleContainerSize;
use LINE\Constants\Flex\ComponentButtonHeight;
use LINE\Constants\Flex\ComponentButtonStyle;
use LINE\Constants\Flex\ComponentFontSize;
use LINE\Constants\Flex\ComponentFontWeight;
use LINE\Constants\Flex\ComponentIconSize;
use LINE\Constants\Flex\ComponentImageAspectMode;
use LINE\Constants\Flex\ComponentImageAspectRatio;
use LINE\Constants\Flex\ComponentImageSize;
use LINE\Constants\Flex\ComponentLayout;
use LINE\Constants\Flex\ComponentMargin;
use LINE\Constants\Flex\ComponentSpaceSize;
use LINE\Constants\Flex\ComponentSpacing;

/**
 * @SuppressWarnings("PHPMD.CouplingBetweenObjects")
 */
class FlexSampleRestaurant
{
    /**
     * Create sample restaurant flex message
     *
     * @return \LINE\MessagingApi\Model\FlexMessage
     */
    public static function get(): FlexMessage
    {
        return new FlexMessage([
            'altText' => 'Restaurant',
            'contents' => new FlexBubble([
                'hero' => self::createHeroBlock(),
                'body' => self::createBodyBlock(),
                'footer' => self::createFooterBlock(),
                'size' => BubbleContainerSize::GIGA,
            ])
        ]);
    }

    private static function createHeroBlock(): FlexComponent
    {
        return new FlexImage([
            'url' => 'https://example.com/cafe.png',
            'size' => ComponentImageSize::FULL,
            'aspectRatio' => ComponentImageAspectRatio::R20TO13,
            'aspectMode' => ComponentImageAspectMode::COVER,
            'action' => new URIAction([
                'label' => 'cafe hero',
                'uri' => 'https://example.com',
                'altUri' => new AltUri(['desktop' => 'https://example.com#desktop']),
            ]),
        ]);
    }

    private static function createBodyBlock()
    {
        return new FlexBox([
            'layout' => ComponentLayout::VERTICAL,
            'backgroundColor' => '#fafafa',
            'paddingAll' => '8%',
            'contents' => [
                // Title
                new FlexText([
                    'text' => 'Brown Cafe',
                    'weight' => ComponentFontWeight::BOLD,
                    'size' => ComponentFontSize::XL,
                ]),
                self::createBodyReview(),
                self::createBodyInfoBlock(),
            ],
        ]);
    }

    private static function createBodyReview(): FlexBox
    {
        $goldStar = new FlexIcon([
            'url' => 'https://example.com/gold_star.png',
            'size' => ComponentIconSize::SM,
        ]);
        $grayStar = new FlexIcon([
            'url' => 'https://example.com/gray_star.png',
            'size' => ComponentIconSize::SM,
        ]);
        $point = new FlexText([
            'text' => '4.0',
            'size' => ComponentFontSize::SM,
            'color' => '#999999',
            'margin' => ComponentMargin::MD,
            'flex' => 0,
        ]);

        return new FlexBox([
            'layout' => ComponentLayout::BASELINE,
            'margin' => ComponentMargin::MD,
            'contents' => [$goldStar, $goldStar, $goldStar, $goldStar, $grayStar, $point],
        ]);
    }

    private static function createBodyInfoBlock(): FlexBox
    {
        $place = new FlexBox([
            'layout' => ComponentLayout::BASELINE,
            'spacing' => ComponentSpacing::SM,
            'contents' => [
                new FlexText([
                    'text' => 'Place',
                    'color' => '#aaaaaa',
                    'size' => ComponentFontSize::SM,
                    'flex' => 1,
                ]),
                new FlexText([
                    'text' => 'Miraina Tower, 4-1-6 Shinjuku, Tokyo',
                    'wrap' => true,
                    'color' => '#666666',
                    'size' => ComponentFontSize::SM,
                    'flex' => 5,
                ]),
            ],
        ]);
        $time = new FlexBox([
            'layout' => ComponentLayout::BASELINE,
            'spacing' => ComponentSpacing::SM,
            'contents' => [
                new FlexText([
                    'text' => 'Time',
                    'color' => '#aaaaaa',
                    'size' => ComponentFontSize::SM,
                    'flex' => 1,
                ]),
                new FlexText([
                    'text' => '10:00 - 23:00',
                    'wrap' => true,
                    'color' => '#666666',
                    'size' => ComponentFontSize::SM,
                    'flex' => 5,
                    'contents' => [
                        new FlexSpan([
                            'text' => '10:00',
                        ]),
                        new FlexSpan([
                            'text' => '-',
                            'color' => '#a0a0a0',
                            'size' => ComponentFontSize::XS,
                        ]),
                        new FlexSpan([
                            'text' => '23:00',
                        ]),
                    ],
                ]),
            ],
        ]);

        return new FlexBox([
            'layout' => ComponentLayout::VERTICAL,
            'margin' => ComponentMargin::LG,
            'spacing' => ComponentSpacing::SM,
            'contents' => [$place, $time],
        ]);
    }

    private static function createFooterBlock()
    {
        $callButton = new FlexButton([
            'style' => ComponentButtonStyle::LINK,
            'height' => ComponentButtonHeight::SM,
            'action' => new URIAction([
                'label' => 'CALL',
                'uri' => 'https://example.com',
                'altUri' => new AltUri(['desktop' => 'https://example.com#desktop']),
            ]),
        ]);
        $websiteButton = new FlexButton([
            'style' => ComponentButtonStyle::LINK,
            'height' => ComponentButtonHeight::SM,
            'action' => new URIAction([
                'label' => 'WEBSITE',
                'uri' => 'https://example.com',
                'altUri' => new AltUri(['desktop' => 'https://example.com#desktop']),
            ]),
        ]);
        $spacer = new FlexSpacer(['size' => ComponentSpaceSize::SM]);

        return new FlexBox([
            'layout' => ComponentLayout::VERTICAL,
            'spacing' => ComponentSpacing::SM,
            'flex' => 0,
            'backgroundColor' => '#fafafa',
            'borderColor' => '#e0e0e0',
            'borderWidth' => '1px',
            'contents' => [$callButton, $websiteButton, $spacer],
        ]);
    }
}

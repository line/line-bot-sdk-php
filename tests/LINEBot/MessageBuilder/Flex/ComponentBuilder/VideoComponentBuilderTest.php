<?php

/**
 * Copyright 2022 LINE Corporation
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

namespace LINE\Tests\LINEBot\MessageBuilder\Flex\ComponentBuilder;

use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\ImageComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\VideoComponentBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
use PHPUnit\Framework\TestCase;
use LINE\LINEBot\Constant\Flex\ComponentImageSize;
use LINE\LINEBot\Constant\Flex\ComponentImageAspectMode;
use LINE\LINEBot\Constant\Flex\ComponentImageAspectRatio;
use LINE\LINEBot\Constant\Flex\ComponentPosition;
use LINE\LINEBot\Constant\Flex\ComponentSpacing;

class VideoComponentBuilderTest extends TestCase
{
    public function test()
    {
        $json = <<<JSON
{
  "type": "video",
  "url": "https://example.com/video.mp4",
  "previewUrl": "https://example.com/video_preview.jpg",
  "altContent": {
    "type": "image",
    "size": "full",
    "aspectRatio": "20:13",
    "aspectMode": "cover",
    "url": "https://example.com/image.jpg"
  },
  "aspectRatio": "20:13",
  "action": {
    "type": "uri",
    "label": "OK",
    "uri": "http://example.com/page/222"
  }
}
JSON;

        $componentBuilder = new VideoComponentBuilder(
            'https://example.com/video.mp4',
            'https://example.com/video_preview.jpg',
            ImageComponentBuilder::builder()
                ->setUrl('https://example.com/image.jpg')
                ->setSize(ComponentImageSize::FULL)
                ->setAspectRatio(ComponentImageAspectRatio::R20TO13)
                ->setAspectMode(ComponentImageAspectMode::COVER),
            ComponentImageAspectRatio::R20TO13,
            new UriTemplateActionBuilder('OK', 'http://example.com/page/222')
        );
        $this->assertEquals(json_decode($json, true), $componentBuilder->build());

        $componentBuilder = VideoComponentBuilder::builder()
            ->setUrl('https://example.com/video.mp4')
            ->setPreviewUrl('https://example.com/video_preview.jpg')
            ->setAltContent(
                ImageComponentBuilder::builder()
                ->setUrl('https://example.com/image.jpg')
                ->setSize(ComponentImageSize::FULL)
                ->setAspectRatio(ComponentImageAspectRatio::R20TO13)
                ->setAspectMode(ComponentImageAspectMode::COVER)
            )
            ->setAspectRatio(ComponentImageAspectRatio::R20TO13)
            ->setAction(new UriTemplateActionBuilder('OK', 'http://example.com/page/222'));
        $this->assertEquals(json_decode($json, true), $componentBuilder->build());
    }
}

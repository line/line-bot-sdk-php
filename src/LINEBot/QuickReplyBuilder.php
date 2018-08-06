<?php
/**
 * Created by PhpStorm.
 * User: vocolboy
 * Date: 2018/8/6
 * Time: 下午7:44
 */

namespace LINE\LINEBot;

interface QuickReplyBuilder
{
    /**
     * Builds message structure.
     *
     * @return array Built message structure.
     */
    public function buildQuickReply();
}
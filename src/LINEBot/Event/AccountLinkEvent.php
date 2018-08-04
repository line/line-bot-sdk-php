<?php
/**
 * @author<rendy, rendyananta66@gmail.com>
 * at 04/08/18
 */

namespace LINE\LINEBot\Event;


class AccountLinkEvent extends BaseEvent
{

    const RESULT_OK = "ok";

    const RESULT_FAILED = "failed";

    /**
     * AccountLinkEvent constructor.
     * @param array $event
     */
    public function __construct(array $event)
    {
        parent::__construct($event);
    }

    /**
     * Return result of account link request
     * is it success or failed
     *
     * @return mixed
     */
    public function getResult()
    {
        return $this->event["link"]["result"];
    }

    /**
     * Get the user nonce
     *
     * @return mixed
     */
    public function getNonce()
    {
        return $this->event["link"]["nonce"];
    }

    /**
     * Check is the account link success
     *
     * @return bool
     */
    public function isSuccess()
    {
        return $this->getResult() == self::RESULT_OK;
    }

    /**
     * Check is the account link failed
     *
     * @return bool
     */
    public function isFailed()
    {
        return $this->getResult() == self::RESULT_FAILED;
    }
}
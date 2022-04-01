<?php

namespace App\Contracts\Bot;

interface Message
{
    /**
     * @return string
     */
    public function getMessageType();

    /**
     * @return array
     */
    public function getMessageData();

    /**
     * @return array
     */
    public function getMentionedList();
}
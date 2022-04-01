<?php

namespace App\Contracts\Bot;

interface Bot
{
    /**
     * 发送消息
     * @param array $message
     * @param array|null $mentionedList
     * @return bool
     */
    public function sendMessage(array $message, array $mentionedList = null);

    /**
     * 发送文本消息
     * @param string $content
     * @param array|null $mentionedList
     * @return bool
     */
    public function sendTextMessage(string $content, array $mentionedList = null);

    /**
     * 发送Markdown消息
     * @param string $content
     * @param array|null $mentionedList
     * @return array|null
     */
    public function sendMarkdownMessage($content, array $mentionedList = null);

    /**
     * 发送图片消息
     * @param string $content
     * @param array|null $mentionedList
     * @return array|null
     */
    public function sendImageMessage($content, $fileMD5, array $mentionedList = null);

    /**
     * 发送文章消息
     * @param array $articles
     * @param array|null $mentionedList
     * @return array|null
     */
    public function sendNewMessage($articles, array $mentionedList = null);

    /**
     * 发送文件消息
     * @param string $mediaId
     * @param array|null $mentionedList
     * @return array|null
     */
    public function sendFileMessage($mediaId, array $mentionedList = null);
}
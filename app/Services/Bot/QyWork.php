<?php

namespace App\Services\Bot;

use App\Contracts\Bot\Bot;
use App\Services\Service;

class QyWork extends Service implements Bot
{
    use HasHttpRequests;

    const BASE_URL = 'https://qyapi.weixin.qq.com/cgi-bin/webhook/send?key=';

    /**
     * @inheritDoc
     */
    public function sendMessage(array $message, array $mentionedList = null): bool
    {
        if (!empty($mentionedList)) {
            $message['mentioned_list'] = $mentionedList;
        }

        $response = $this->httpPostJson($this->resolveUrl(), $message);

        if (!$response->ok() || $response->json('errcode') !== 0) {
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function sendTextMessage(string $content, array $mentionedList = null): bool
    {
        return $this->sendMessage([
            'msgtype' => 'text',
            'text' => [
                'content' => $content,
            ],
        ], $mentionedList);
    }

    /**
     * @inheritDoc
     */
    public function sendMarkdownMessage($content, array $mentionedList = null)
    {
        return $this->sendMessage([
            'msgtype' => 'markdown',
            'markdown' => [
                'content' => $content,
            ],
        ], $mentionedList);
    }

    /**
     * @inheritDoc
     */
    public function sendImageMessage($content, $fileMD5, array $mentionedList = null)
    {
        return $this->sendMessage([
            'msgtype' => 'image',
            'image' => [
                'base64' => $content,
                'md5' => $fileMD5,
            ],
        ], $mentionedList);
    }

    /**
     * @inheritDoc
     */
    public function sendNewMessage($articles, array $mentionedList = null)
    {
        return $this->sendMessage([
            'msgtype' => 'news',
            'news' => [
                'articles' => $articles,
            ],
        ], $mentionedList);
    }

    /**
     * @inheritDoc
     */
    public function sendFileMessage($mediaId, array $mentionedList = null)
    {
        return $this->sendMessage([
            'msgtype' => 'file',
            'file' => [
                'media_id' => $mediaId,
            ],
        ], $mentionedList);
    }

    /**
     * 解析URL
     * @return string
     */
    protected function resolveUrl(): string
    {
        $key = $this->config['key'];

        return self::BASE_URL . $key;
    }
}

<?php

namespace App\Services\Bot;

use App\Contracts\Bot\Bot;
use App\Services\Service;

class DingTalk extends Service implements Bot
{
    use HasHttpRequests;

    const BASE_URL = 'https://oapi.dingtalk.com/robot/send?access_token=';

    /**
     * @inheritDoc
     */
    public function sendMessage(array $message, array $mentionedList = null)
    {
        if ($mentionedList) {
            $mentionedOpts = [];
            if (($isAllIndex = array_search('@all', $mentionedList)) !== false) {
                $mentionedOpts['isAtAll'] = true;
                array_splice($mentionedList, $isAllIndex, 1);
            }

            $mentionedOpts['atUserIds'] = $mentionedList;

            $message['at'] = $mentionedOpts;
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
     * 解析URL
     * @return string
     */
    protected function resolveUrl(): string
    {
        $key = $this->config['key'];

        return self::BASE_URL . $key;
    }

    public function sendMarkdownMessage($content, array $mentionedList = null)
    {
        // TODO: Implement sendMarkdownMessage() method.
    }

    public function sendImageMessage($content, $fileMD5, array $mentionedList = null)
    {
        // TODO: Implement sendImageMessage() method.
    }

    public function sendNewMessage($articles, array $mentionedList = null)
    {
        // TODO: Implement sendNewMessage() method.
    }

    public function sendFileMessage($mediaId, array $mentionedList = null)
    {
        // TODO: Implement sendFileMessage() method.
    }
}

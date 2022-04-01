<?php

namespace App\Services\Wework;

use Symfony\Component\EventDispatcher\EventDispatcher;

class WeworkManager
{
    // 加载器
    protected $wxLoader = null;

    // WxWorkHelper.dll
    protected $wxHelperDLLPath = '';

    # 可指定WXWork.exe路径，也可以设置为空
    protected $wxworkExePath = '';

    protected $dispatcher;

    public function __construct($libsPath, $wxworkExePath = '')
    {
        $this->dispatcher = new EventDispatcher();

        $wxworkLoaderPath = $libsPath . DIRECTORY_SEPARATOR . sprintf('WxWorkLoader_%s.dll', 'x64');
        $wxworkLoaderPath = realpath($wxworkLoaderPath);
        if (!file_exists($wxworkLoaderPath)) {
            throw new \RuntimeException('libs path error or WxWorkLoader not exist');
        }

        $com = new \COM('DynamicWrapper');
        $this->wxLoader = dl($wxworkLoaderPath);

        $this->initLoader();

        $this->wxHelperDLLPath = sprintf('%s/WxWorkHelper_%s.dll', $libsPath, $this->get_user_wxwork_version());
        if (!file_exists($this->wxHelperDLLPath)) {
            throw new \RuntimeException(sprintf('lib file：%s not exist', $this->wxHelperDLLPath));
        }

        if ($wxworkExePath != '' && !file_exists($wxworkExePath)) {
            throw new \RuntimeException('WXWork.exe is the path set correctly?');
        }

        $this->wxworkExePath = $wxworkExePath;
    }

    protected function initLoader()
    {
        # 使用utf8编码
        $this->wxLoader->UseUtf8();

        # 初始化接口回调
        $this->wxLoader->InitWxWorkSocket(function ($clientId) { // connect
            $this->dispatcher->dispatch(new WeworkConnectedEvent($clientId));
        }, function ($clientId, $data, $length) { // recv
            $this->dispatcher->dispatch(new WeworkReceiveEvent($clientId, $data, $length));
        }, function ($clientId) { // close
            $this->dispatcher->dispatch(new WeworkClosedEvent($clientId));
        });
    }

    public function add_callback_handler($callback_handler)
    {
        add_callback_handler($callback_handler);
    }

    // @REQUIRE_WXLOADER()
    public function get_user_wxwork_version()
    {
        $out = create_string_buffer(20);
        $this->wxLoader->GetUserWxWorkVersion($out);

        return $out;
        // return $out . value . decode('utf-8');
    }

    // @REQUIRE_WXLOADER()
    public function manager_wxwork($smart = true)
    {
        if ($smart) {
            return $this->wxLoader->InjectWxWork($this->wxHelperDLLPath, $this->wxworkExePath);
        } else {
            return $this->wxLoader->InjectWxWorkMultiOpen($this->wxHelperDLLPath, $this->wxworkExePath);
        }
    }

    // @REQUIRE_WXLOADER()
    public function manager_wxwork_by_pid($wxwork_pid)
    {
        return $this->wxLoader->InjectWxWorkPid($wxwork_pid, $this->wxHelperDLLPath);
    }

    // @REQUIRE_WXLOADER()
    public function close_manager()
    {
        return $this->wxLoader->DestroyWxWork();
    }

    // @REQUIRE_WXLOADER()
    public function send_message($client_id, $message_type, $params)
    {
        $send_data = [
            'type' => $message_type,
            'data' => $params,
        ];

        return $this->wxLoader->SendWxWorkData($client_id, json_encode($send_data));
    }

    public function send_text($client_id, $conversation_id, $text)
    {
        $data = [
            'conversation_id' => $conversation_id,
            'content' => $text,
        ];

        return $this->send_message($client_id, MessageType::MT_SEND_TEXT_MSG, $data);
    }

    public function send_image($client_id, $conversation_id, $image_path)
    {
        $data = [
            'conversation_id' => $conversation_id,
            'file' => $image_path,
        ];

        return $this->send_message($client_id, MessageType::MT_SEND_IMAGE_MSG, $data);
    }

    public function send_file($client_id, $conversation_id, $file)
    {
        $data = [
            'conversation_id' => $conversation_id,
            'file' => $file,
        ];

        return $this->send_message($client_id, MessageType::MT_SEND_FILE_MSG, $data);
    }

    public function send_link($client_id, $conversation_id, $title, $desc, $url, $image_url)
    {
        $data = [
            'conversation_id' => $conversation_id,
            'title' => $title,
            'desc' => $desc,
            'url' => $url,
            'image_url' => $image_url,
        ];

        return $this->send_message($client_id, MessageType::MT_SEND_LINK_MSG, $data);
    }

    public function send_video($client_id, $conversation_id, $video_path)
    {
        $data = [
            'conversation_id' => $conversation_id,
            'file' => $video_path,
        ];

        return $this->send_message($client_id, MessageType::MT_SEND_VIDEO_MSG, $data);
    }
}

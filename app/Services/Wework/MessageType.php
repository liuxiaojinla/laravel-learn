<?php

namespace App\Services\Wework;

class MessageType
{
    const MT_APP_READY_MSG = 11024;

    const MT_PARAMS_ERROR_MSG = 11025;

    const MT_USER_LOGIN = 11026;

    const MT_USER_LOGOUT = 11027;

    const MT_LOGIN_QRCODE_MSG = 11028;

    const MT_SEND_TEXT_MSG = 11029;

    const MT_SEND_IMAGE_MSG = 11030;

    const MT_SEND_FILE_MSG = 11031;

    const MT_SEND_LINK_MSG = 11033;

    const MT_SEND_VIDEO_MSG = 11067;

    const MT_SEND_PERSON_CARD_MSG = 11034;

    const MT_RECV_TEXT_MSG = 11041;

    const MT_RECV_IMG_MSG = 11042;

    const MT_RECV_VIDEO_MSG = 11043;

    const MT_RECV_VOICE_MSG = 11044;

    const MT_RECV_FILE_MSG = 11045;

    const MT_RECV_LOCATION_MSG = 11046;

    const MT_RECV_LINK_CARD_MSG = 11047;

    const MT_RECV_EMOTION_MSG = 11048;

    const MT_RECV_RED_PACKET_MSG = 11049;

    const MT_RECV_PERSON_CARD_MSG = 11050;

    const MT_RECV_OTHER_MSG = 11051;
}
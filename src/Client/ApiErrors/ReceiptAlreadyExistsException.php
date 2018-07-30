<?php

namespace SzuniSoft\SzamlazzHu\Client\ApiErrors;


class ReceiptAlreadyExistsException extends ClientException {

    /**
     * More detailed info of exception.
     *
     * @return string
     */
    function getInfo()
    {
        return 'The caller ID (unique field) you just used already exists on the server.';
    }
}
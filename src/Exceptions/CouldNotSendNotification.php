<?php

namespace NotificationChannels\Routee\Exceptions;

class CouldNotSendNotification extends \Exception
{
    public static function serviceRespondedWithAnError($message)
    {
        return new static($message);
    }

    public static function serviceRespondedWithAnErrorCode($code)
    {
        $message = "-";
        if ($code == "400001009") {
            $message = "You don't have enough balance to send the SMS.";
        } else if ($code == "400005000") {
            $message = "The sender id is invalid.";
        } else if ($code == "400000000") {
            $message = "Validation Error.";
        }
        return new static($message);
    }

    public static function unauthorized()
    {
        return new static("Invalid access token, maybe has expired");
    }

    public static function forbidden()
    {
        return new static("Your application's access token does not have the right to use SMS service");
    }

    public static function missingRecipient()
    {
        return new static("Missing recipient");
    }
}

<?php

namespace NotificationChannels\Routee\Exceptions;

class SendNotification extends \Exception
{
  public static function serviceResponded($message)
    {
        return new static($message);
    }
  public static function serviceResponded($code)
    {
       $message = "-";
      if ($code == "200") {
            $message = "Succesed to send the SMS.";
        }else  if ($code == "400001009") {
            $message = "You don't have enough balance to send the SMS.";
        } else if ($code == "400005000") {
            $message = "The sender id is invalid.";
        } else if ($code == "400000000") {
            $message = "Validation Error.";
        }
        return new static($message);
      
    }
}

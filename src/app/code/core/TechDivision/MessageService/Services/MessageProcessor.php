<?php

namespace TechDivision\MessageService\Services;

use TechDivision\MessageService\Interfaces\MessageInterface;

class MessageProcessor  {

    public function process(MessageInterface $message) {
        error_log("Now process message {$message->getMessageId()}");
    }

}
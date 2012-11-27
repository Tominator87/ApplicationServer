<?php

namespace TechDivision\MessagingService\Services;

use TechDivision\MessagingServiceClient\Interfaces\MessageInterface;

class MessageProcessor  {

    public function process(MessageInterface $message) {
        error_log("Now process message {$message->getMessageId()}");
    }

}
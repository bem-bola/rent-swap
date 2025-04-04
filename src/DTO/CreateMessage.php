<?php

namespace App\DTO;


class CreateMessage {
    public function __construct(
        public string $content,
        public string $slugConversation,
    ){

    }
}
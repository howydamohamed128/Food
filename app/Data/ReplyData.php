<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class ReplyData extends Data
{
    public function __construct(
        public int $id,
        public string $message,
        public string $reply_at
    ) {}

    public static function fromModel($reply): self
    {
        return new self(
            id: $reply?->id,
            message: $reply?->message,
            reply_at: $reply?->parent?->reply_at?->format('Y-m-d H:i:s') ?? ''
        );
    }
}

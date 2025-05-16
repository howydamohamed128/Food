<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class ContactData extends Data
{
    public function __construct(
        public int $id,
        public string $message,
        public ?string $send_at,
        public ?ReplyData $reply,

    ) {}

    public static function fromModel($contact): self
    {
        return new self(
            id: $contact->id,
            message: $contact->message,
            send_at: $contact?->send_at?->format('Y-m-d H:i:s') ?? '',
            reply: $contact->reply ? ReplyData::fromModel($contact->reply) : null
        );
    }
}

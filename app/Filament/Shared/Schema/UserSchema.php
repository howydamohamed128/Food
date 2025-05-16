<?php

namespace App\Filament\Shared\Schema;

use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class UserSchema {
    static public function make() {
        return new static();
    }

    public function toArray() {
        return [
            SpatieMediaLibraryFileUpload::make('avatar')
                ->columnSpan(['xl' => 2])
                ->nullable(),

            TextInput::make('name')
                ->required(),

            TextInput::make('email')
                ->required()
                ->email()
                ->autocomplete("off")
                ->columnSpan(['sm' => 2, 'xl' => 1])
                ->unique(ignoreRecord: true),
            TextInput::make('phone')
                ->prefix("+966")
                ->required()
                ->unique(ignoreRecord: true)
                ->autocomplete("off")
                ->columnSpan(['sm' => 2]),

            TextInput::make('password')
                ->password()
                ->required(fn(string $operation): bool => $operation === 'create')
                ->confirmed()
                ->autocomplete("new-password"),
            TextInput::make('password_confirmation')
                ->password()
                ->required(fn(string $operation): bool => $operation === 'create')
                ->autocomplete("off"),

            Toggle::make('active')->default(1)
                ->onColor('success')
                ->offColor('danger')
        ];
    }



    public static function __callStatic(string $name, array $arguments) {
     return (new static)->toArray();
    }
}

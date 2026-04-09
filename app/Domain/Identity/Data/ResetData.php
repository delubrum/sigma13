<?php

declare(strict_types=1);

namespace App\Domain\Identity\Data;

use Spatie\LaravelData\Attributes\Validation\Confirmed;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Regex;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

final class ResetData extends Data
{
    public function __construct(
        #[Required]
        public readonly string $token,

        #[Required]
        public readonly string $email,

        #[Required, Min(10), Max(100), Confirmed, Regex('/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/')]
        public readonly string $password,

        #[Required]
        public readonly string $password_confirmation,
    ) {}

    /**
     * Definición manual de mensajes para evitar el traductor de Laravel.
     *
     * @return array<string, string>
     */
    public static function messages(): array
    {
        return [
            'password.required' => 'La nueva contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 10 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.regex' => 'La contraseña debe incluir mayúsculas, números y símbolos.',
            'email.required' => 'El correo electrónico es necesario.',
        ];
    }
}

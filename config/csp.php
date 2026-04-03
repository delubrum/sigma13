<?php

use Spatie\Csp\Directive;
use Spatie\Csp\Keyword;
use Spatie\Csp\Nonce\RandomString;
use Spatie\Csp\Presets\Basic;

return [
    'presets' => [
        Basic::class,
    ],

    'directives' => [
        [Directive::SCRIPT, [Keyword::UNSAFE_EVAL, Keyword::UNSAFE_INLINE]],
        [Directive::SCRIPT, ['self', 'unsafe-inline', 'unsafe-eval']],
        [Directive::STYLE, ['self', 'unsafe-inline']],
        [Directive::FONT, ['self', 'data:', 'https://fonts.bunny.net']],
        [Directive::CONNECT, ['self', 'ws:', 'wss:', 'http:', 'https:']],
        [Directive::IMG, ['self', 'data:', 'blob:']],
        [Directive::OBJECT, [Keyword::NONE]],
        [Directive::FRAME, [Keyword::NONE]],
    ],

    'report_only_presets' => [
        //
    ],

    'report_only_directives' => [
        //
    ],

    'report_uri' => env('CSP_REPORT_URI', ''),

    'enabled' => env('CSP_ENABLED', true),

    'enabled_while_hot_reloading' => env('CSP_ENABLED_WHILE_HOT_RELOADING', false),

    'nonce_generator' => RandomString::class,

    'nonce_enabled' => env('CSP_NONCE_ENABLED', true),
];

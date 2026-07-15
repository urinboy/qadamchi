<?php
namespace Qadamchi\Support;

/**
 * Version — freymvork versiyasining yagona manbai (single source of truth).
 *
 * Bitta joyda saqlanadi va hamma joydan shu yerdan o'qiladi:
 *   - CLI banner / --version flag (qadamchi bin)
 *   - config/app.php 'version'
 *   - hujjatlar / installer
 *
 * O'zgartirish uchun faqat shu const'ni yangilang.
 */
final class Version
{
    public const VERSION = '3.1.0';

    public static function string(): string
    {
        return self::VERSION;
    }
}
<?php
namespace Qadamchi\Database;

/**
 * Seeder bazasi. run() ni implement qiling. call() boshqa seeder'larni ishga tushiradi.
 */
abstract class Seeder
{
    abstract public function run(): void;

    public function call(string $class): void
    {
        (new $class)->run();
    }
}
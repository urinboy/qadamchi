<?php
namespace Qadamchi\Contracts;

/**
 * PSR-11 ga mos container interfeysi (o'z nusxamiz, Composer'siz).
 * B versiyasida \Psr\Container\ContainerInterface ga almashtiriladi — imzo bir xil.
 */
interface ContainerInterface
{
    public function get(string $id);
    public function has(string $id): bool;
}
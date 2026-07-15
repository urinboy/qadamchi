<?php
namespace Qadamchi\Auth;

/**
 * Guard — autentifikatsiya qo'riqchisi (A versiyada bitta web guard).
 * B versiyada token/api guard qo'shilishi mumkin.
 */
interface Guard
{
    public function check(): bool;
    public function guest(): bool;
    public function user();
    public function id();
}
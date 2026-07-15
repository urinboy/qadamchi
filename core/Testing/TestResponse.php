<?php
namespace Qadamchi\Testing;

class TestResponse
{
    protected string $output;
    protected int $status;

    public function __construct(string $output, int $status)
    {
        $this->output = $output;
        $this->status = $status;
    }

    public function status(): int { return $this->status; }
    public function getOutput(): string { return $this->output; }

    public function assertOk(): bool { return $this->status === 200; }
    public function assertStatus(int $code): bool { return $this->status === $code; }

    public function json(): ?array
    {
        $decoded = json_decode($this->output, true);
        return is_array($decoded) ? $decoded : null;
    }

    public function see(string $needle): bool { return str_contains($this->output, $needle); }
}
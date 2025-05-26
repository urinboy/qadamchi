<?php
class ErrorHandler {
    public static function handle($exception) {
        echo "Error: " . $exception->getMessage();
    }
}
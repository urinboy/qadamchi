<?php
require_once __DIR__.'/Schema.php';

class Migration {
    protected $schema;
    public function __construct() {
        $this->schema = new Schema();
    }
}
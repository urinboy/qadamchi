<?php
use Migration;

class CreateUsersTable extends Migration {
    public function up() {
        $this->schema->create('users', function($table) {
            $table->bigIncrements('id');
            $table->timestamps();
        });
    }
    public function down() {
        $this->schema->drop('users');
    }
}


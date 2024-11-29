<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CommentHistories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            Schema::create('comment_histories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('comment_id')->constrained()->onDelete('cascade');
                $table->text('content');
                $table->timestamp('edited_at');
            });
        } catch (\Exception $e) {
            $this->down();
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comment_histories');
    }
}

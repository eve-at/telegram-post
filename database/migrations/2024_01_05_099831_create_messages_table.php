<?php

use App\Models\Channel;
use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            //$table->unsignedBigInteger('channel_id');
            //$table->unsignedBigInteger('post_id');
            $table->foreignIdFor(Channel::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Post::class)->constrained()->cascadeOnDelete();
            $table->text('text'); // full body of the post message (empji, text, source, footer)
            $table->boolean('status')->default(false); // 0 - draft, 1 - published
            $table->timestamps();
            $table->timestamp('published_at')->nullable();

            //$table->foreign('channel_id')->references('id')->on('channels');
            //$table->foreign('post_id')->references('id')->on('posts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};

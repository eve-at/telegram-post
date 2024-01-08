<?php

use App\Models\Channel;
use App\Models\Post;
use App\Models\User;
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
            $table->foreignIdFor(Channel::class)->constrained()->cascadeOnDelete();
            $table->morphs('messagable');
            $table->string('message_id')->nullable(); // Telegram message ID
            $table->longText('body')->nullable(); // final body with source
            $table->boolean('status')->default(false); // 0 - draft, 1 - published
            $table->timestamps();
            $table->timestamp('published_at')->nullable();
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

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
            $table->timestamp('publish_at'); // desired publish datetime
            $table->timestamp('published_at')->nullable(); // real publish datetime

            $table->boolean('ad')->default(false);
            $table->integer('ad_hours_on_top')->nullable(); // how many hours the ad must remain on top
            $table->timestamp('ad_top_until')->nullable();
            $table->integer('ad_remove_after_hours')->nullable(); // in how many hours after the publish date the ad must be removed
            $table->timestamp('ad_deleted_at')->nullable();
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

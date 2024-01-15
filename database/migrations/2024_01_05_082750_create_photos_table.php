<?php

use App\Models\Channel;
use App\Models\File;
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
        Schema::create('photos', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Channel::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class)->constrained()->restrictOnDelete();
            $table->string('title');
            $table->boolean('show_title')->default(true);
            $table->text('body')->nullable(); // max 1024 characters
            $table->boolean('show_signature')->default(true);
            $table->string('source')->nullable();
            $table->string('filename');
            $table->string('file_id')->nullable(); // will be obtained after delayed upload
            $table->string('file_unique_id')->nullable(); // will be obtained after delayed upload
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photos');
    }
};

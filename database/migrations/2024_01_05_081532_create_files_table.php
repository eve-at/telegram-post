<?php

use App\Models\Channel;
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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Channel::class)->constrained()->cascadeOnDelete();
            $table->set('type', ['photo', 'video', 'document']);
            $table->string('filename')->nullable(); // can be deleted after uploading
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
        Schema::dropIfExists('files');
    }
};

<?php

use App\Models\File;
use App\Models\MediaGroup;
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
        Schema::create('media_group_files', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(MediaGroup::class)->constrained()->cascadeOnDelete();
            $table->integer('order')->default(0);
            $table->set('type', ['photo', 'video', 'document']);
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
        Schema::dropIfExists('media_group_files');
    }
};

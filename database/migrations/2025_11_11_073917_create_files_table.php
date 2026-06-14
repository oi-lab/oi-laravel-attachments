<?php

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
            $table->uuid()->unique();
            $table->string('filename_disk'); // nom sur le disque
            $table->string('filename_download'); // nom original
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('mimetype'); // mime type
            $table->integer('filesize'); // en bytes
            $table->integer('width')->nullable(); // pour images
            $table->integer('height')->nullable(); // pour images
            $table->string('storage')->default('local'); // local, s3, etc.
            $table->string('md5', 32)->nullable(); // hash MD5 du fichier
            $table->json('metadata')->nullable(); // métadonnées additionnelles (FileMetadataValueObject)
            $table->foreignId('folder_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->json('props')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('folder_id');
            $table->index('md5');
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

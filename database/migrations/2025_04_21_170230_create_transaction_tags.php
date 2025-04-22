<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('key')->index();
            $table->text('value');
            $table->string('locale', 10)->index();
            $table->timestamps();

            $table->unique(['key', 'locale']);
        });

        Schema::create('translation_tags', function (Blueprint $table) {
            $table->foreignId('translation_id')
                ->constrained()
                ->onDelete('cascade');
            $table->string('tag')->index();
            $table->timestamps();

            $table->primary(['translation_id', 'tag']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translation_tags');
        Schema::dropIfExists('translations');
    }
};

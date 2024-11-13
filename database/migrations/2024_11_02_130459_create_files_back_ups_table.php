<?php

use App\Models\Files;
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
        Schema::create('files_back_ups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('editor_name');
            $table->foreignIdFor(Files::class)->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files_back_ups');
    }
};

<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table): void {
            $table->index('category');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table): void {
            $table->dropIndex(['category']);
            $table->dropIndex(['created_at']);
        });
    }
};

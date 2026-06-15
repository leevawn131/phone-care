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
        if (! Schema::hasColumn('warranty_claims', 'attachments')) {
            Schema::table('warranty_claims', function (Blueprint $table): void {
                $table->json('attachments')->nullable()->after('issue_description');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('warranty_claims', 'attachments')) {
            Schema::table('warranty_claims', function (Blueprint $table): void {
                $table->dropColumn('attachments');
            });
        }
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('distribution_stock_audits', function (Blueprint $table) {
            $table->id();
            $table->string('event_type', 30);
            $table->string('item_sku');
            $table->string('item_name');
            $table->string('variant_sku')->nullable();
            $table->string('old_size')->nullable();
            $table->string('new_size')->nullable();
            $table->integer('quantity_change');
            $table->integer('old_stock');
            $table->integer('new_stock');
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('user_nim')->nullable();
            $table->string('user_email')->nullable();
            $table->string('user_name')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('created_at');
            $table->index('event_type');
            $table->index(['reference_type', 'reference_id']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('distribution_stock_audits');
    }
};

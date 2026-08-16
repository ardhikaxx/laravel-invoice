<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('company_name')->nullable();
            $table->string('tax_identification_number')->nullable();
            $table->text('billing_address')->nullable();
            $table->text('shipping_address')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('invoice_number')->unique();
            $table->string('status')->default('draft')->index();
            $table->foreignId('customer_id')->nullable()->constrained('invoice_customers')->nullOnDelete();
            
            // Financial amounts using decimal. Assuming max 999,999,999,999.99
            $table->decimal('subtotal', 16, 4)->default(0);
            $table->decimal('discount_amount', 16, 4)->default(0);
            $table->decimal('tax_amount', 16, 4)->default(0);
            $table->decimal('shipping_fee', 16, 4)->default(0);
            $table->decimal('service_fee', 16, 4)->default(0);
            $table->decimal('adjustment', 16, 4)->default(0);
            $table->decimal('grand_total', 16, 4)->default(0);
            $table->decimal('paid_amount', 16, 4)->default(0);
            
            $table->string('currency', 3)->default('IDR');
            
            $table->date('invoice_date');
            $table->date('due_date')->nullable();
            $table->text('notes')->nullable();
            $table->text('terms')->nullable();
            $table->json('metadata')->nullable();
            
            // External polymorphic relation if developer wants to link to their own User/Model
            $table->nullableMorphs('billable');
            
            $table->string('verification_token')->unique()->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->string('name');
            $table->string('sku')->nullable();
            $table->text('description')->nullable();
            
            $table->decimal('quantity', 10, 2)->default(1);
            $table->string('unit')->nullable();
            $table->decimal('unit_price', 16, 4);
            $table->decimal('discount', 16, 4)->default(0);
            $table->decimal('tax', 16, 4)->default(0);
            $table->decimal('subtotal', 16, 4);
            $table->decimal('total', 16, 4);
            
            $table->json('metadata')->nullable();
            
            // Polymorphic relation to link to developer's Product model
            $table->nullableMorphs('reference');
            
            $table->timestamps();
        });

        Schema::create('invoice_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->decimal('amount', 16, 4);
            $table->string('currency', 3)->default('IDR');
            $table->string('payment_method')->nullable();
            $table->string('transaction_reference')->nullable()->index();
            $table->timestamp('paid_at')->nullable();
            $table->string('status')->default('pending')->index();
            $table->string('gateway')->nullable();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_payments');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('invoice_customers');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique()->index();
            $table->text('description')->nullable();
            $table->string('image_url')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique()->index();
            $table->string('subtitle')->nullable();
            $table->text('description');
            $table->text('fabric_details')->nullable();
            $table->text('care_guide')->nullable();
            $table->unsignedInteger('base_price_xof');
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_new_arrival')->default(true);
            $table->timestamps();
        });

        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('size'); // S, M, L, XL, XXL, Sur-Mesure
            $table->string('color_name'); // ex: Noir Soirée, Ivoire, Or Sublime
            $table->string('color_hex', 10)->default('#111111');
            $table->string('sku')->unique()->index();
            $table->unsignedInteger('stock_quantity')->default(5);
            $table->unsignedInteger('price_xof_override')->nullable();
            $table->timestamps();
        });

        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('url');
            $table->string('alt_text')->nullable();
            $table->boolean('is_hover')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique()->index();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_email')->nullable();
            $table->string('delivery_zone'); // 'dakar', 'region_sn', 'international_gp'
            $table->text('delivery_address');
            $table->string('currency_used')->default('XOF'); // 'XOF' or 'EUR'
            $table->unsignedInteger('subtotal_xof');
            $table->unsignedInteger('shipping_amount_xof')->default(0);
            $table->unsignedInteger('total_amount_xof');
            $table->decimal('total_amount_eur', 10, 2);
            $table->string('payment_method'); // 'wave', 'orange_money', 'cod', 'stripe'
            $table->string('payment_status')->default('pending'); // 'pending', 'paid', 'failed'
            $table->string('order_status')->default('pending'); // 'pending', 'processing', 'in_transit', 'delivered', 'cancelled'
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('product_variant_id')->constrained('product_variants')->cascadeOnDelete();
            $table->string('product_name');
            $table->string('size');
            $table->string('color_name');
            $table->unsignedInteger('unit_price_xof');
            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedInteger('subtotal_xof');
            $table->timestamps();
        });

        Schema::create('back_in_stock_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->constrained('product_variants')->cascadeOnDelete();
            $table->string('contact_info');
            $table->string('contact_type')->default('whatsapp'); // 'whatsapp' or 'email'
            $table->string('status')->default('pending'); // 'pending', 'notified'
            $table->timestamp('notified_at')->nullable();
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('back_in_stock_subscriptions');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
    }
};

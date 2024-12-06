<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('address_id')->nullable()->constrained('user_addresses')->cascadeOnDelete();
            $table->uuid('uuid')->unique()->index();
            $table->string('name');
            $table->string('image');
            $table->integer('item_condition')->comment('1=良好; 2=目立った傷や汚れなし; 3=やや傷や汚れあり; 4=状態が悪い');
            $table->text('description');
            $table->string('brand')->nullable();
            $table->decimal('price', 10, 0);
            $table->boolean('is_sold')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items');
    }
}

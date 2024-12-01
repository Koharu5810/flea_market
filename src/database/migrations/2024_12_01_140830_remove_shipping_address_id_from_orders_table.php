<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveShippingAddressIdFromOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'shipping_address_id')) {
                // 外部キーが存在するか確認
                $foreignKeys = Schema::getConnection()
                    ->getDoctrineSchemaManager()
                    ->listTableForeignKeys('orders');

                foreach ($foreignKeys as $foreignKey) {
                    if ($foreignKey->getName() === 'orders_shipping_address_id_foreign') {
                        $table->dropForeign(['shipping_address_id']);
                    }
                }
                // カラムを削除
                $table->dropColumn('shipping_address_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('shipping_address_id')->constrained()->cascadeOnDelete();
        });
    }
}

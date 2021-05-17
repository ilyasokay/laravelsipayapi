<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->bigIncrements('id')->startingValue(10000000000);
            $table->string('description');
            $table->text('bill_address1');
            $table->text('bill_address2');
            $table->text('bill_city');
            $table->string('bill_postcode');
            $table->string('bill_state');
            $table->string('bill_country');
            $table->string('bill_email');
            $table->string('bill_phone');

            $table->timestamps();

            $table->foreignId('order_id')
            ->constrained()
            ->cascadeOnUpdate()
            ->cascadeOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bills');
    }
}

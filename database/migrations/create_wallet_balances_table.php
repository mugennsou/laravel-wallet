<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWalletBalancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wallet_balances', function (Blueprint $table) {
            $table->increments('id');
            $table->string('wallet_id');
            $table->string('currency');
            $table->bigInteger('amount');
            $table->timestamps();

            $table->unique([
                'wallet_id',
                'currency',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wallet_balances');
    }
}

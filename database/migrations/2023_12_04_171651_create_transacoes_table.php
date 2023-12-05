<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transacoes', function (Blueprint $table) {
            $table->id();
            $table->integer('sender_id');

            $table->string('description');
            $table->decimal('amount', 8, 2);

            $table->string('account_sender', 10);
            $table->foreign('account_sender')->references('code')->on('contas');

            $table->string('account_receiver', 10);
            $table->foreign('account_receiver')->references('code')->on('contas');
            $table->foreign('sender_id')->references('id')->on('users');


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
        Schema::dropIfExists('transacoes');
    }
};

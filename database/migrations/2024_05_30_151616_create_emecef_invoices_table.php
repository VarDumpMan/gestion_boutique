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
        Schema::create('emecef_invoices', function (Blueprint $table) {
            $table->id();
            $table->string("invoice_uuid")->unique();
            $table->string("code_mecef")->unique();
            $table->string("nim_mecef");
            $table->string("compteurs_mecef");
            $table->string("qrcode_mecef");
            $table->string("date_mecef");

            // Utilisation de integer() pour int(10)
            $table->integer('transaction_id')->unsigned();

            // Ajout de la contrainte de clé étrangère manuellement
            $table->foreign('transaction_id')
                ->references('id')
                ->on('transactions')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            // Ajout de la colonne de suppression douce
            $table->softDeletes();

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
        Schema::dropIfExists('emecef_invoices');
    }
};

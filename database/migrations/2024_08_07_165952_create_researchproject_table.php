<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('researchprojects', function (Blueprint $table) {
            $table->id('researchid'); //for computer identification
            $table->string('researchnumber')->unique();//for human identification
            $table->unsignedBigInteger('proposalidfk');
            $table->string('projectstatus')->default('active'); //paused,cancelled,completed
            $table->boolean('iscompleted')->default(false);
            $table->foreign('proposalidfk')->references('proposalid')->on('proposals')->onDelete('restrict');
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
        Schema::dropIfExists('researchprojects');
    }
};

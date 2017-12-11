<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateContenedoresTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('contenedores', function(Blueprint $table)
		{
			$table->string('id', 32)->primary()->comment('		');
			$table->integer('clues_id')->unsigned()->index('fk_contenedores_clues_idx');
			$table->string('servidor_id', 4);
			$table->integer('incremento');
			$table->integer('tipos_contenedores_id')->unsigned()->index('fk_contenedores_tipos_contenedores_idx');
			$table->integer('marcas_id')->unsigned()->index('fk_contenedores_marcas_idx');
			$table->integer('modelos_id')->unsigned()->nullable()->index('fk_contenedores_modelos_idx');
			$table->integer('estatus_contenedor_id')->unsigned()->nullable()->index('fk_contenedores_estatus_idx');
			$table->string('serie', 40);
			$table->string('folio', 45)->nullable();
			$table->string('tipos_mantenimiento', 3)->comment('IND - Indefinido DIA - DiarioSEM - SemanalQUI - QuincenalMES - Mensual');
			$table->string('temperatura_minima', 45);
			$table->string('temperatura_maxima', 45);
			$table->string('usuario_id');
			$table->timestamps();
			$table->softDeletes();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('contenedores');
	}

}

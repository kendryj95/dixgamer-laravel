<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // Arreglo de usuarios a agregar por medio de un comando
        $users = [
          [
            'Nombre' => 'Melvin',
            'Level'=> 'Administrador',
            'Contra'=>\Hash::make('123456')
          ],
          [
            'Nombre' => 'Wilmer',
            'Level'=> 'Analista',
            'Contra'=>\Hash::make('123456')
          ],
          [
            'Nombre' => 'Andres',
            'Level'=> 'Vendedor',
            'Contra'=>\Hash::make('123456')
          ],
          [
            'Nombre' => 'Pedro',
            'Level'=> 'Asistente',
            'Contra'=>\Hash::make('123456')
          ]
        ];

        // Recorremos los usuarios para insertarlos
        foreach ($users as $data) {
          // La clase DB nos permite hacer consultas a la base de datos
          // por medio del metodo insert pasamos un arreglo con las variables
          \DB::table('usuarios')->insert([
            'Nombre'=>$data['Nombre'],
            'Level'=>$data['Level'],
            'Contra'=>\Hash::make('123456')
          ]);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    // Proveedores ficticios para demostrar el módulo
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'Distribuciones FarmaVida SAC',
                'ruc' => '20123456789',
                'phone' => '987654321',
                'email' => 'ventas@farmavida.test',
                'address' => 'Av. Salud 120, Lima',
            ],
            [
                'name' => 'Laboratorios San Marcos EIRL',
                'ruc' => '20456789123',
                'phone' => '912345678',
                'email' => 'contacto@sanmarcos.test',
                'address' => 'Jr. Los Andes 450, Lima',
            ],
            [
                'name' => 'Medica Importaciones del Norte SAC',
                'ruc' => '20567891234',
                'phone' => '956123478',
                'email' => 'comercial@medicanorte.test',
                'address' => 'Av. Universitaria 880, Trujillo',
            ],
            [
                'name' => 'Pharma Andina Distribuciones SAC',
                'ruc' => '20678912345',
                'phone' => '945612378',
                'email' => 'ventas@pharmaandina.test',
                'address' => 'Psje. Medicinal 75, Arequipa',
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}

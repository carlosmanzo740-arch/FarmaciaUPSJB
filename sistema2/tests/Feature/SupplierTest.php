<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierTest extends TestCase
{
    use RefreshDatabase;

    // Crea un usuario con el rol indicado para probar rutas y vistas
    private function createUserWithRole(string $roleName): User
    {
        $role = Role::create([
            'name' => $roleName,
            'description' => 'Rol de prueba',
        ]);

        return User::create([
            'role_id' => $role->id,
            'name' => 'Usuario de prueba',
            'dni' => '12345678',
            'phone' => '987654321',
            'tuition' => null,
            'email' => 'prueba'.fake()->unique()->numberBetween(1, 9999).'@sanjuan.test',
            'password' => 'password',
        ]);
    }

    public function test_rechaza_un_ruc_de_diez_digitos_en_espanol(): void
    {
        $admin = $this->createUserWithRole('Administrador');

        $response = $this->actingAs($admin)->post(route('suppliers.store'), [
            'name' => 'Proveedor de prueba',
            'ruc' => '1234567890',
            'phone' => '987654321',
            'email' => 'proveedor@sanjuan.test',
            'address' => 'Av. Principal 123',
        ]);

        $response->assertSessionHasErrors([
            'ruc' => 'El campo RUC debe tener 11 dígitos.',
        ]);
    }

    public function test_rechaza_rucs_duplicados_y_permite_editar_sin_cambiarlo(): void
    {
        $admin = $this->createUserWithRole('Administrador');
        $supplier = Supplier::create([
            'name' => 'Proveedor existente',
            'ruc' => '20123456789',
        ]);

        $duplicate = $this->actingAs($admin)->post(route('suppliers.store'), [
            'name' => 'Otro proveedor',
            'ruc' => '20123456789',
        ]);

        $duplicate->assertSessionHasErrors([
            'ruc' => 'El RUC ya está registrado.',
        ]);

        $update = $this->actingAs($admin)->put(route('suppliers.update', $supplier), [
            'name' => 'Proveedor actualizado',
            'ruc' => '20123456789',
        ]);

        $update->assertRedirect(route('suppliers.index'));
    }

    public function test_cajero_no_ve_el_menu_y_recibe_403(): void
    {
        $cashier = $this->createUserWithRole('Cajero');

        $response = $this->actingAs($cashier)->get('/suppliers');

        $response->assertForbidden();
        $responseMenu = $this->actingAs($cashier)->get('/');
        $responseMenu->assertDontSee('Proveedores');
    }
}

<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    // Crea una cuenta válida para las pruebas de autenticación
    private function createLoginUser(): User
    {
        $role = Role::create([
            'name' => 'Administrador',
            'description' => 'Acceso total al sistema',
        ]);

        return User::create([
            'role_id' => $role->id,
            'name' => 'Administrador de prueba',
            'dni' => '12345678',
            'phone' => '987654321',
            'tuition' => null,
            'email' => 'admin@sanjuan.com',
            'password' => 'password',
        ]);
    }

    public function test_muestra_el_formulario_de_inicio_de_sesion(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk()
            ->assertSee('Bienvenido')
            ->assertSee('name="email"', false)
            ->assertSee('name="password"', false)
            ->assertDontSee('id="loginForm"', false)
            ->assertDontSee('js/app.js', false);
    }

    public function test_las_rutas_del_sistema_redirigen_a_login_si_no_hay_sesion(): void
    {
        $this->get('/')
            ->assertRedirect(route('login'));

        $this->get('/medicines')
            ->assertRedirect(route('login'));
    }

    public function test_usuario_autenticado_que_visita_login_es_redirigido_al_dashboard(): void
    {
        $this->actingAs($this->createLoginUser())
            ->get(route('login'))
            ->assertRedirect(route('dashboard.index'));
    }

    public function test_el_menu_muestra_el_nombre_correo_iniciales_y_rol_del_usuario(): void
    {
        $user = $this->createLoginUser();
        $user->update(['name' => 'Álvaro Núñez']);

        $this->actingAs($user)
            ->get('/')
            ->assertOk()
            ->assertSee('Álvaro Núñez')
            ->assertSee('admin@sanjuan.com')
            ->assertSee('ÁL')
            ->assertSee('Administrador');
    }

    public function test_el_modal_de_salida_envia_un_formulario_post_con_csrf(): void
    {
        $response = $this->actingAs($this->createLoginUser())->get('/');

        $response->assertOk()
            ->assertSee('action="'.route('logout').'"', false)
            ->assertSee('method="POST"', false)
            ->assertSee('name="_token"', false)
            ->assertSee('Salir')
            ->assertDontSee('id="confirmLogoutBtn"', false);
    }

    public function test_inicia_sesion_con_credenciales_correctas(): void
    {
        $this->createLoginUser();

        $response = $this->post(route('login'), [
            'email' => 'admin@sanjuan.com',
            'password' => 'password',
            'remember' => '1',
        ]);

        $response->assertRedirect(route('dashboard.index'));
        $this->assertAuthenticated();
    }

    public function test_credenciales_incorrectas_muestran_error_y_conservan_solo_el_correo(): void
    {
        $this->createLoginUser();

        $response = $this->from(route('login'))->post(route('login'), [
            'email' => 'admin@sanjuan.com',
            'password' => 'incorrecta',
        ]);

        $response->assertRedirect(route('login'))
            ->assertSessionHasErrors([
                'email' => 'El correo o la contraseña no son correctos.',
            ])
            ->assertSessionHas('_old_input', [
                'email' => 'admin@sanjuan.com',
            ]);
    }

    public function test_cierra_sesion_e_invalida_la_autenticacion(): void
    {
        $user = $this->createLoginUser();
        $this->actingAs($user);

        $response = $this->post(route('logout'));

        $response->assertRedirect(route('login'))
            ->assertSessionHas('ok', 'Cerraste sesión correctamente.');
        $this->assertGuest();
    }
}

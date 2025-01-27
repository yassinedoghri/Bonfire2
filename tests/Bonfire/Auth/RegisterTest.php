<?php

namespace Tests\Bonfire\Auth;

use CodeIgniter\Test\DatabaseTestTrait;
use Sparks\Shield\Authentication\Actions\EmailActivator;
use Tests\Support\TestCase;

class RegisterTest extends TestCase
{
    use DatabaseTestTrait;

    protected $refresh = true;
    protected $namespace = null;

    public static function setUpBeforeClass(): void
    {
        helper('setting');
    }

    public function testCanViewRegisterPage()
    {
        setting('Auth.allowRegistration', true);

        $response = $this->get(route_to('register'));

        $response->assertOK();
        $response->assertSee('Register');
    }

    public function testCannotViewRegisterPageWhenSet()
    {
        setting('Auth.allowRegistration', false);

        // Should not be able to see Register link on Login page
        $response = $this->get(route_to('login'));
        $response->assertOK();
        $response->assertDontSee('Register');

        // Should not be able to access the register page
        $response = $this->get(route_to('register'));

        $response->assertRedirect();
        $response->assertSessionHas('error', lang('Auth.registerDisabled'));
    }

    public function testRegisterSuccess()
    {
        setting('Auth.allowRegistration', true);
        setting('Auth.actions', [
            'login' => null,
            'register' => null
        ]);

        // Submit registration form
        $response = $this->post(route_to('register'), [
            'email' => 'fred@example.com',
            'username' => 'freddy101',
            'password' => 'secret123abc!@',
            'pass_confirm' => 'secret123abc!@',
        ]);
        $response->assertRedirectTo(site_url());

        // User was created
        $this->seeInDatabase('users', [
            'username' => 'freddy101'
        ]);
        // With an email identity
        $this->seeInDatabase('auth_identities', [
            'type' => 'email_password',
            'secret' => 'fred@example.com'
        ]);
    }

    public function testRedirectsToEmailActivationScreen()
    {
        setting('Auth.allowRegistration', true);
        setting('Auth.actions', [
            'login' => null,
            'register' => EmailActivator::class
        ]);

        // Submit registration form
        $response = $this->post(route_to('register'), [
             'email' => 'fred@example.com',
            'username' => 'freddy101',
            'password' => 'secret123abc!@',
            'pass_confirm' => 'secret123abc!@',
        ]);
        $response->assertRedirectTo('/auth/a/show');
    }
}

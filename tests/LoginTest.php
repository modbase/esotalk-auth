<?php

use Mockery as m;
use Modbase\EsotalkAuth\User;
use Modbase\EsotalkAuth\Guard;

class LoginTest extends PHPUnit_Framework_TestCase {

	public function testLoginSuccess()
	{
		$provider = m::mock('Illuminate\Auth\UserProviderInterface');
		$cookieStorage = m::mock('Modbase\EsotalkAuth\CookieStorage');
		$event = m::mock('Illuminate\Events\Dispatcher');
		$guard = new Guard($provider, $cookieStorage);
		$guard->setDispatcher($event);

		$credentials = array('username' => 'user', 'password' => 'pass');
		$user = new User($credentials);
		$user->id = 1234;
		$provider->shouldReceive('retrieveByCredentials')->once()->with($credentials)->andReturn($user);
		$provider->shouldReceive('validateCredentials')->once()->with($user, $credentials)->andReturn(true);
		$cookieStorage->shouldReceive('login')->once()->with(1234, 'pass', false);

		$event->shouldReceive('fire')->once()->with('auth.attempt', array($credentials, false, true));
		$event->shouldReceive('fire')->once()->with('auth.login', array($user, false));

		$guard->attempt($credentials);

		$this->assertTrue($guard->check());
		$this->assertEquals($guard->user(), $user);
	}

	public function testLoginFailure()
	{
		$provider = m::mock('Illuminate\Auth\UserProviderInterface');
		$cookieStorage = m::mock('Modbase\EsotalkAuth\CookieStorage');
		$guard = new Guard($provider, $cookieStorage);

		$credentials = array('username' => 'user', 'password' => 'pass');
		$user = new User($credentials);
		$user->id = 1234;

		$provider->shouldReceive('retrieveByCredentials')->once()->with($credentials)->andReturn($user);
		$provider->shouldReceive('validateCredentials')->once()->with($user, $credentials)->andReturn(false);
		$cookieStorage->shouldReceive('login')->never();
		$cookieStorage->shouldReceive('getId')->times(3)->andReturn(null);

		$guard->attempt($credentials);

		$this->assertFalse($guard->check());
		$this->assertNull($guard->user());
		$this->assertTrue($guard->guest());
	}

	public function testCookieLogin()
	{
		$provider = m::mock('Illuminate\Auth\UserProviderInterface');
		$cookieStorage = m::mock('Modbase\EsotalkAuth\CookieStorage');
		$guard = new Guard($provider, $cookieStorage);

		$user = new User(array('password' => 'pass'));
		$user->id = 1234;
		$cookieStorage->shouldReceive('getId')->once()->andReturn(array(1234, false));
		$provider->shouldReceive('retrieveById')->once()->with(1234)->andReturn($user);
		$cookieStorage->shouldReceive('login')->once()->with(1234, 'pass', false);

		$this->assertEquals($guard->user(), $user);
		$this->assertTrue($guard->check());
	}

	public function testIdLogin()
	{
		$provider = m::mock('Illuminate\Auth\UserProviderInterface');
		$cookieStorage = m::mock('Modbase\EsotalkAuth\CookieStorage');
		$guard = new Guard($provider, $cookieStorage);

		$credentials = array('username' => 'user', 'password' => 'pass');
		$user = new User($credentials);
		$user->id = 1234;
		$provider->shouldReceive('retrieveById')->once()->with(1234)->andReturn($user);
		$cookieStorage->shouldReceive('login')->once()->with(1234, 'pass', false);

		$guard->loginUsingId(1234);

		$this->assertEquals($guard->user(), $user);
		$this->assertTrue($guard->check());
	}

	public function testLoggedOutByDefault()
	{
		$provider = m::mock('Illuminate\Auth\UserProviderInterface');
		$cookieStorage = m::mock('Modbase\EsotalkAuth\CookieStorage');
		$guard = new Guard($provider, $cookieStorage);

		$cookieStorage->shouldReceive('getId')->once()->andReturn(null);

		$this->assertTrue($guard->guest());
	}

	public function testLogout()
	{
		$provider = m::mock('Illuminate\Auth\UserProviderInterface');
		$cookieStorage = m::mock('Modbase\EsotalkAuth\CookieStorage');
		$guard = new Guard($provider, $cookieStorage);

		$credentials = array('username' => 'user', 'password' => 'pass');
		$user = new User($credentials);
		$user->id = 1234;
		$provider->shouldReceive('retrieveById')->once()->with(1234)->andReturn($user);
		$cookieStorage->shouldReceive('login')->once()->with(1234, 'pass', false);

		$guard->loginUsingId(1234);

		$this->assertEquals($guard->user(), $user);

		$cookieStorage->shouldReceive('logout')->once();
		$provider->shouldReceive('updateRememberToken')->once();

		$guard->logout();

		$this->assertTrue($guard->guest());
		$this->assertNull($guard->user());
	}

	public function tearDown()
	{
		m::close();
	}
}

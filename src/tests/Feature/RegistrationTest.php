<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;    
/**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    // 名前が入力されていない場合、バリデーションメッセージが表示される
    public function name_is_required_for_registration()
    {
        // 登録ルートにPOSTリクエストを送信
        $response = $this->post('/register', [
            'username' => '', //  名前が入力されていない
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // 'username' フィールドでバリデーションエラーが発生したことを確認
        $response->assertSessionHasErrors('username');
    }

    /** @test */
    // メールアドレスが入力されていない場合、バリデーションメッセージが表示される
    public function email_is_required_for_registration()
    {
        $response = $this->post('/register', [
            'username' => 'Test User',
            'email' => '', // ★ メールアドレスが入力されていない
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /** @test */
    // パスワードが入力されていない場合、バリデーションメッセージが表示される
    public function password_is_required_for_registration()
    {
        $response = $this->post('/register', [
            'username' => 'Test User',
            'email' => 'test@example.com',
            'password' => '', // ★ パスワードが入力されていない
            'password_confirmation' => '',
        ]);

        $response->assertSessionHasErrors('password');
    }

    // パスワードが7文字以下の場合、バリデーションメッセージが表示される
    public function password_must_be_at_least_8_characters()
    {
        $response = $this->post('/register', [
            'username' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'pass123', // ★ 7文字のパスワード
            'password_confirmation' => 'pass123',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /** @test */
    // パスワードが確認用パスワードと一致しない場合、バリデーションメッセージが表示される
    public function password_must_be_confirmed()
    {
        $response = $this->post('/register', [
            'username' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different-password', // ★ 異なる確認用パスワード
        ]);

        $response->assertSessionHasErrors('password_confirmation');
    }

     /** @test */
     // 全ての項目が入力されている場合、会員情報が登録され、ログイン画面に遷移される
    public function a_user_can_be_registered()
    {
        $response = $this->post('/register', [
            'username' => 'Test User', // ★ 'name' から 'username' に変更
            'email' => 'new-user@example.com', // 他のテストと重複しないように
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

    // 1. ユーザー登録後はログイン状態ではないことを確認
    $this->assertAuthenticated();

    // 2. 'users' テーブルに新しいユーザーのレコードが存在することを確認
    $this->assertDatabaseHas('users', [
        'username' => 'Test User',
        'email' => 'new-user@example.com'
    ]);

    // 3. ユーザー登録後にメール認証ページにリダイレクトされることを確認
    $response->assertRedirect('/email/verify');
    }
}

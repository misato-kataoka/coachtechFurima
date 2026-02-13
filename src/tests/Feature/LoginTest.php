<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class LoginTest extends TestCase
{
    use RefreshDatabase; // ★ テスト毎にDBをリフレッシュする

    /**
     * @test
     * メールアドレスが入力されていない場合、バリデーションメッセージが表示される
     */
    public function email_is_required_for_login()
    {
        // 2. メールアドレスをnullにしてPOST
        $response = $this->post('/login', [
            'email' => '', // メールアドレスが入力されていない
            'password' => 'password123',
        ]);

        // 3. 'email'に関するバリデーションエラーが返ってくることを確認
        $response->assertSessionHasErrors('email');
    }

    /**
     * @test
     * パスワードが入力されていない場合、バリデーションメッセージが表示される
     */
    public function password_is_required_for_login()
    {
        // 2. パスワードをnullにしてPOST
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '', // パスワードが入力さていない
        ]);

        // 3. 'password'に関するバリデーションエラーが返ってくることを確認
        $response->assertSessionHasErrors('password');
    }

    /**
     * @test
     * 入力情報が間違っている場合、バリデーションメッセージが表示される
     */
    public function a_user_cannot_login_with_incorrect_credentials()
    {
        // 準備: テスト用のユーザーを1人作成
        $user = User::factory()->create();

        // 2. 存在しないパスワードでログイン試行
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password', // 間違ったパスワード
        ]);

        // 3. バリデーションエラーが返ってくることを確認
        $response->assertSessionHasErrors('email');
    }

    /**
     * @test
     * 正しい情報が入力された場合、ログイン処理が実行される
     */
    public function a_user_can_login()
    {
        // 1. 準備: テスト用のユーザーを1人作成
        $user = User::factory()->create([
             'password' => bcrypt('password123'), // パスワードを固定値で作成
        ]);

        // 2. 作成したユーザーの正しい情報でログイン実行
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123', // 上で設定したパスワード
        ]);

        // 3. ログイン状態になっていることを確認
        $this->assertAuthenticatedAs($user);

        // 4. ホーム画面など、ログイン後のページにリダイレクトされることを確認
        $response->assertRedirect('/');
    }

    /**
     * @test
     * ログアウトができる
     */
    public function a_user_can_logout()
    {
        // 1. 準備: ユーザーを作成し、ログイン状態にする
        $user = User::factory()->create();
        $this->actingAs($user); // このユーザーとしてログインした状態にする

        // 2. ログアウト処理を実行
        $response = $this->post('/logout');

        // 3. ログアウト状態（ゲスト状態）になっていることを確認
        $this->assertGuest();

        // 4. トップページなどにリダイレクトされることを確認
        $response->assertRedirect('/login');
    }
}

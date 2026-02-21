<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Comment;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * ログイン済みのユーザーはコメントを送信できる
     */
    public function a_logged_in_user_can_post_a_comment()
    {

        // 1. テスト用のユーザーを作成
        $user = User::factory()->create();

        // 2. テスト用の商品を作成
        $seller = User::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $seller->id,
        ]);
        
        // 3. 投稿するコメントの内容を定義
        $commentData = [
            'item_id' => $item->id,
            'content' => '購入を検討しています。値下げは可能でしょうか？',
        ];

        $fromUrl = route('item.detail', ['id' => $item->id]);

        // 4. 作成したユーザーでログインし、コメント投稿エンドポイントにPOSTリクエストを送信
        $response = $this->actingAs($user)
                 ->withHeaders(['Referer' => $fromUrl])
                ->post(route('comments.store'), $commentData);

        // 5. データベースの 'comments' テーブルに、送信したデータが保存されているか確認
        $this->assertDatabaseHas('comments', [
            'item_id' => $item->id,
            'user_id' => $user->id,
            'content' => '購入を検討しています。値下げは可能でしょうか？',
        ]);
        
        // 6. 投稿後、元の詳細ページにリダイレクトされるか確認
        $response->assertRedirect(route('item.detail', ['id' => $item->id]));

        // 7. 念のため、コメントが1件だけ保存されていることを確認
        $this->assertEquals(1, Comment::count());

    }
    /**
     * @test
     * ログイン前のユーザーはコメントを送信できない
     */    
    public function a_guest_cannot_post_a_comment()
    {

        // 1. テスト用の商品を作成
        $item = Item::factory()->create();

        // 2. 投稿するコメントのデータ
        $commentData = [
            'item_id' => $item->id,
            'content' => 'ゲストによるコメントです。',
        ];


        // 3. ログインしていない状態で、コメント投稿エンドポイントにPOSTリクエストを送信
        $response = $this->post(route('comments.store'), $commentData);

        // 4. データベースの 'comments' テーブルに、データが "保存されていない" ことを確認
        $this->assertDatabaseMissing('comments', [
            'content' => 'ゲストによるコメントです。',
        ]);
        $this->assertEquals(0, Comment::count()); // コメントが1件も無いはず

        // 5. ログインページへのリダイレクトが返されることを確認
        $response->assertRedirect(route('login'));
    }

    /**
     * @test
     * コメントが入力されていない場合、バリデーションメッセージが表示される
     * @return void
     */
    public function comment_content_is_required()
    {

        // 1. ログイン済みのユーザーと商品を作成
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // 2. "content" が空のコメントデータを作成
        $commentData = [
            'item_id' => $item->id,
            'content' => '', // 本文を空にする
        ];

        // 3. リダイレクト元（兼リダイレクト先）となるURLを定義
        $fromUrl = route('item.detail', ['id' => $item->id]);

        // 4. ログイン状態で、本文が空のデータをPOST
        $response = $this->actingAs($user)
                         ->withHeaders(['Referer' => $fromUrl])
                         ->post(route('comments.store'), $commentData);

        // 5. データベースにコメントが保存 "されていない" ことを確認
        $this->assertEquals(0, Comment::count());

        // 6. 'content' フィールドでバリデーションエラーが起きていることを確認
        $response->assertSessionHasErrors('content');

        // 7. 元のページにリダイレクトされていることを確認
        $response->assertRedirect($fromUrl);
    }

    /**
     * @test
     * コメントが255文字以上の場合、バリデーションメッセージが表示される
     * @return void
     */
    public function comment_content_must_not_exceed_max_length()
    {
        // 1. ユーザーと商品を作成
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // 2. 上限文字数を超える文字列を生成
        $longContent = \Illuminate\Support\Str::random(256);

        // 3. 上限オーバーのデータを作成
        $commentData = [
            'item_id' => $item->id,
            'content' => $longContent,
        ];

        // 4. リダイレクト元URLを定義
        $fromUrl = route('item.detail', ['id' => $item->id]);

        // 5. ログイン状態で、文字数オーバーのデータをPOST
        $response = $this->actingAs($user)
                         ->withHeaders(['Referer' => $fromUrl])
                         ->post(route('comments.store'), $commentData);

        // 6. データベースにコメントが保存 "されていない" ことを確認
        $this->assertEquals(0, Comment::count());

        // 7. 'content' フィールドでバリデーションエラーが起きていることを確認
        $response->assertSessionHasErrors('content');

        // 8. 元のページにリダイレクトされていることを確認
        $response->assertRedirect($fromUrl);
    }
}

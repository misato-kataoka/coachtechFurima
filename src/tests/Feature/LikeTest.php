<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;

class LikeTest extends TestCase
{
    use RefreshDatabase; // 各テストの後にデータベースをリセットする

    /**
     * @test
     * いいねアイコンを押下することによって、いいねした商品として登録することができる。
     * 追加済みのアイコンは色が変化する
     */
    public function a_user_can_like_an_item()
    {
        // ログインするユーザーと、いいね対象の商品を作成
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // ログイン状態にして、いいねを追加するエンドポイントにPOSTリクエストを送る
        $response = $this->actingAs($user)->post('/item/' . $item->id . '/like');

        // a. リクエストが成功し、リダイレクトされることを確認
        $response->assertStatus(302);
        $response->assertRedirect('/item/' . $item->id);

        // b. likesテーブルにデータが保存されたことを確認
        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // c. 商品のいいね数が1になったことを確認
        $this->assertEquals(1, $item->fresh()->likes()->count());
    }

    /**
     * @test
     * 再度いいねアイコンを押下することによって、いいねを解除することができる。
     */
    public function a_user_can_unlike_an_item()
    {
        // ログインするユーザーと、商品を作成
        $user = User::factory()->create();
        $item = Item::factory()->create();
        // ★事前に「いいね」された状態を作っておく
        $item->likes()->attach($user->id);

        // いいねが1件存在することを確認
        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
        $this->assertEquals(1, $item->likes()->count());

        // ログイン状態にして、いいねを解除するエンドポイントにDELETEリクエストを送る
        $response = $this->actingAs($user)->delete('/item/' . $item->id . '/unlike');

        // a. リクエストが成功し、リダイレクトされることを確認
        $response->assertStatus(302);
        $response->assertRedirect('/item/' . $item->id);

        // b. likesテーブルからデータが削除されたことを確認
        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // c. 商品のいいね数が0になったことを確認
        $this->assertEquals(0, $item->fresh()->likes()->count());
    }
}

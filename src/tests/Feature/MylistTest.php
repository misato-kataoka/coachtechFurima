<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;

class MylistTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * ログインユーザーは、いいねした商品だけがマイリストに表示される
     */
    public function an_authenticated_user_can_see_only_their_favorited_items_on_mylist_page(): void
    {
        // 1. ログインするユーザーを作成
        $user = User::factory()->create();

        // 2. 他のユーザーを作成（この人の商品は表示されないはず）
        $anotherUser = User::factory()->create();

        // 3. ユーザーがいいねした商品を作成 (2つ)
        //   -> createMany(2) で2つの商品を一気に作成
        $favoritedItems = Item::factory()->count(2)->create([
            'user_id' => $anotherUser->id, // 商品は別のユーザーが出品
        ]);
        // 作成した2つの商品IDをユーザーの「いいね」として中間テーブルに記録
        $user->likes()->attach($favoritedItems->pluck('id'));


        // 4. ユーザーがいいねしていない商品を作成
        $unfavoritedItem = Item::factory()->create([
            'user_id' => $anotherUser->id, // これも別のユーザーが出品
        ]);

        // 5. ログインしてマイリストページ (route('item.mylist')) にアクセス
        $response = $this->actingAs($user)->get(route('item.mylist'));

        // 6. レスポンスが正常（ステータスコード200）であることを確認
        $response->assertStatus(200);

        // 7. いいねした商品名がレスポンスのHTMLに含まれていることを確認
        foreach ($favoritedItems as $item) {
            $response->assertSee($item->item_name);
        }

        // 8. いいねしていない商品名がレスポンスのHTMLに含まれていないことを確認
        $response->assertDontSee($unfavoritedItem->item_name);
    }

    /**
     * @test
     * マイリストで、購入済みの商品には「Sold」と表示される
     */
    public function sold_items_are_marked_as_sold_on_the_mylist_page(): void
    {
        // 1. ユーザーを3人作成
        $user = User::factory()->create();
        $seller = User::factory()->create();

        // 2. 「未購入」の商品を作成し、ログインユーザーがいいねする
        //    (この商品には "Sold" が表示されないはず)
        //    ▼▼▼ 'status' => 'on_sale' を指定 ▼▼▼
        $unsoldItem = Item::factory()->create([
            'user_id' => $seller->id,
            'status' => 'on_sale', // 'on_sale' は出品中
        ]);
        $user->likes()->attach($unsoldItem->id);

        // 3. 「購入済み」の商品を作成し、ログインユーザーがいいねする
        //    (この商品には "Sold" が表示されるはず)
        //    ▼▼▼ 'status' => 'sold_out' などを指定 ▼▼▼
        $soldItem = Item::factory()->create([
            'user_id' => $seller->id,
            'status' => 'complete',
        ]);
        $user->likes()->attach($soldItem->id);

        // 4. ログインしてマイリストページにアクセス
        $response = $this->actingAs($user)->get(route('item.mylist'));

        // 5. レスポンスが正常（ステータスコード200）であることを確認
        $response->assertStatus(200);

        // 6. 両方の商品名が表示されていることを確認
        $response->assertSee($unsoldItem->item_name);
        $response->assertSee($soldItem->item_name);

        // 7. 「購入済み」の商品に対して "Sold" が表示されていることを確認
        $response->assertSee('Sold');

        // 8. "Sold" の文字列がHTML内にちょうど1回だけ出現することを確認
        $substrCount = substr_count($response->getContent(), 'Sold');
        $this->assertEquals(1, $substrCount, '「Sold」の表示が1回だけであることを確認');
    }

    /**
     * @test
     * メール未認証のユーザーがマイリストにアクセスすると、認証案内ページにリダイレクトされる
     */
    public function an_unverified_user_is_redirected_to_the_verification_notice_page(): void
    {
        // 1. メール認証が済んでいないユーザーを作成する
        //    UserFactory の email_verified_at を null にする
        $unverifiedUser = User::factory()->create([
            'email_verified_at' => null,
        ]);

        // 念のため、このユーザーがいいねした商品も作成しておく
        $item = Item::factory()->create();
        $unverifiedUser->likes()->attach($item->id);

        // 2. この「未認証ユーザー」としてログインし、マイリストページにアクセスする
        $response = $this->actingAs($unverifiedUser)->get(route('item.mylist'));

        // 3. レスポンスが「メール認証案内ページ」へのリダイレクト(302)であることを確認する
        //    リダイレクト先のルート名は 'verification.notice'
        $response->assertRedirect(route('verification.notice'));
    }
}

<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * マイページに必要な情報が表示される
     */
    public function it_displays_required_user_information_on_mypage()
    {
        // --- 準備 (Arrange) ---
        $user = User::factory()->create();
        $seller = User::factory()->create();
        $sellingItem = Item::factory()->create(['user_id' => $user->id, 'item_name' => '私が出品した商品']);
        $purchasedItem = Item::factory()->create(['user_id' => $seller->id, 'buyer_id' => $user->id, 'item_name' => '私が購入した商品']);

        // --- 実行＆検証 ---

        // 【1. デフォルト（出品）タブの検証】
        $responseSellTab = $this->actingAs($user)->get(route('mypage'));

        $responseSellTab->assertStatus(200);
        $responseSellTab->assertSee('私が出品した商品');
        // ▼▼▼ ここが重要！購入商品は表示 *されない* ことを確認する ▼▼▼
        $responseSellTab->assertDontSee('私が購入した商品');

        // 【2. 購入済み商品タブの検証】
        $responseBuyTab = $this->actingAs($user)->get(route('mypage', ['tab' => 'buy']));

        $responseBuyTab->assertStatus(200);
        // ▼▼▼ ここが重要！出品商品は表示 *されない* ことを確認する ▼▼▼
        $responseBuyTab->assertDontSee('私が出品した商品');
        $responseBuyTab->assertSee('私が購入した商品');
    }

    /**
     * @test
     * プロフィール編集ページで既存情報が初期値として設定されている
     */
    public function it_prefills_existing_data_on_profile_edit_page()
    {
        // ... こちらのテストは前回提案した内容から変更ありません ...
        // --- 準備 (Arrange) ---
        $user = User::factory()->create([
            'username' => '編集前ユーザー名',
            'profile_pic' => 'images/initial-profile.jpg',
            'post_code' => '123-4567',
            'address' => '東京都テスト区テスト町1-2-3',
            'building' => 'テストビル101',
        ]);

        // --- 実行 (Act) ---
        // ※ルート名は 'routes/web.php' の定義に合わせてください
        //   'mypage.profile.edit' や 'profile.edit' などが考えられます。
        $response = $this->actingAs($user)->get(route('address.edit')); 

        // --- 検証 (Assert) ---
        $response->assertStatus(200);

        // inputのvalue属性を確認
        $response->assertSee('<input type="text" id="username" name="username" value="編集前ユーザー名"', false);
        $response->assertSee('<input type="text" id="post_code" name="post_code" value="123-4567"', false);
        $response->assertSee('<input type="text" id="address" name="address" value="東京都テスト区テスト町1-2-3"', false);
        $response->assertSee('<input type="text" id="building" name="building" value="テストビル101"', false);
        
        // 現在のプロフィール画像の表示を確認
        $response->assertSee('src="'.asset('storage/images/initial-profile.jpg').'"', false);
    }
}
<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Order;

class AddressUpdateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * テスト全体で使うダミーの住所データとユーザー名
     * 
     */
    private function getDummyProfileData(): array
    {
        return [
            'username'    => '新しいテストユーザー名',
            'post_code'   => '123-4567',
            'address'     => '東京都テスト区テスト町1-2-3',
            'building'    => 'テストビル101号室',
        ];
    }

    /**
     * @test
     * プロフィール更新画面で更新した住所が、商品購入画面に反映される
     */
    public function updated_address_is_reflected_on_the_purchase_page(): void
    {

        // 1. ユーザーと商品を作成
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // 2. 新しいプロフィールデータ（住所含む）を準備
        $newProfileData = $this->getDummyProfileData();

        // 3. ログインし、プロフィール更新ルートにPUTリクエストを送信
        $this->actingAs($user)
             ->put(route('address.edit'), $newProfileData);

        // 4. 商品購入画面にアクセス
        $response = $this->actingAs($user)->get(route('purchase.show', ['item_id' => $item->id]));

        // 5. レスポンスが正常であることを確認
        $response->assertStatus(200);

        // 6. レスポンスのHTMLに、更新した住所情報が含まれていることを確認
        $response->assertSee($newProfileData['username']);
        $response->assertSee($newProfileData['post_code']);
        $response->assertSee($newProfileData['address']);
        $response->assertSee($newProfileData['building']);
    }

    /**
     * @test
     * 商品購入時に、更新された送付先住所が注文情報に紐づく
     */
    public function updated_address_is_associated_with_the_order_on_purchase(): void
    {
        // 1. 'orders' テーブルに、意図したデータが保存されているか確認
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'shipping_post_code' => $newProfileData['post_code'], // 更新後の郵便番号
            'shipping_address'     => $newProfileData['address'],     // 更新後の住所
            'shipping_building'    => $newProfileData['building'],    // 更新後の建物名
            'amount'               => $item->price,                 // 商品価格
        ]);

        // 2. 'items' テーブルの buyer_id が更新されたかも確認
        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'buyer_id' => $user->id,
        ]);
    }
}

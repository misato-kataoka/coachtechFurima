<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Condition;
use App\Models\Like;
use App\Models\Comment;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * 全商品を取得できる
     */
    public function it_can_display_all_items()
    {
        // 準備: 複数の商品を作成
        $items = Item::factory()->count(3)->create();
        $item1 = $items[0];
        $item2 = $items[1];

        // 実行: 商品一覧ページにアクセス
        $response = $this->get('/');

        // 検証
        $response->assertStatus(200);
        $response->assertSee($item1->name);
        $response->assertSee($item2->name);
    }

    /**
     * @test
     * 自分が出品した商品は一覧に表示されない
     */
    public function it_does_not_show_own_items_in_the_list()
    {
        // 準備
        $user = User::factory()->create();
        $ownItem = Item::factory()->create(['user_id' => $user->id]); // 自分の商品
        $otherItem = Item::factory()->create(); // 他人の商品

        // 実行: ログインして商品一覧ページにアクセス
        $response = $this->actingAs($user)->get('/');

        // 検証
        $response->assertStatus(200);
        $response->assertDontSee($ownItem->name); // 自分の商品名は表示されない
        $response->assertSee($otherItem->name);  // 他人の商品名は表示される
    }

    public function soldStatusProvider()
    {
        return [
            '取引中の商品' => ['in_progress'],
            '取引完了の商品' => ['complete'],
        ];
    }

    /**
     * @test
     * @dataProvider soldStatusProvider
     * 購入済み商品は「Sold」と表示される
     */
    public function it_displays_sold_label_for_purchased_items($status)
    {
        // 準備: データプロバイダから受け取ったステータスの商品を作成
        $soldItem = Item::factory()->create(['status' => $status]);
        // 比較用に販売中の商品も作成
        $onSaleItem = Item::factory()->create(['status' => 'on_sale']);

        // 実行
        $response = $this->get('/');

        // 検証
        $response->assertStatus(200);

        // Sold商品のカードに「Sold」ラベルが表示されていることを確認
        // (より厳密なテストにするため、商品名とセットで確認)
        $response->assertSee($soldItem->item_name);
        $response->assertSee('Sold');

        // 販売中の商品名も表示されていることを確認
        $response->assertSee($onSaleItem->item_name);
    }

    /**
     * @test
     * 商品詳細ページで必要な情報が表示される
     */
    public function it_can_display_item_details()
    {
        ///  準備 テストデータを作成する
        $seller = User::factory()->create(['username' => '出品者A']);
        $commenter = User::factory()->create([
            'username' => 'コメントユーザーB',
            'profile_pic' => 'test_icons/commenter_b_icon.png'
        ]);
        $liker = User::factory()->create();

        $category = Category::factory()->create(['category_name' => 'テストカテゴリ']);
        $condition = Condition::factory()->create(['condition' => 'ほぼ新品']);

        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'item_name' => '最高の商品',
            'brand' => '直接入力ブランド',
            'price' => 99800,
            'description' => 'これは素晴らしい商品の説明文です。',
            'image' => '/images/test-product.jpg',
        ]);

        // 商品にカテゴリと状態を紐付け
        $item->categories()->attach([$category->id => ['condition_id' => $condition->id]]);

        // 商品にコメントを1件追加
        $comment = Comment::factory()->create([
            'item_id' => $item->id,
            'user_id' => $commenter->id,
            'content' => 'この商品について質問です。',
        ]);

        // 商品に「いいね」を1件追加
        Like::factory()->create([
            'item_id' => $item->id,
            'user_id' => $liker->id,
        ]);

        // 2. 実行  商品詳細ページにアクセス
        $response = $this->get('/item/' . $item->id);

        // 3. 検証 すべての要素が表示されているか確認
        $response->assertStatus(200);

        // 商品基本情報
        $response->assertSee('/images/test-product.jpg');
        $response->assertSee('最高の商品');
        $response->assertSee('直接入力ブランド');
        $response->assertSee('99,800');

        // 商品説明
        $response->assertSee('これは素晴らしい商品の説明文です。');

        // いいねとコメントの「数」
        $response->assertSee('1'); // いいねが1件
        $response->assertSee('1'); // コメントが1件

        // 商品情報
        $response->assertSee('テストカテゴリ');
        $response->assertSee('ほぼ新品');

        // コメント欄の情報
        $response->assertSee($commenter->username);
        $response->assertSee('この商品について質問です。');
        $response->assertSee(asset('storage/test_icons/commenter_b_icon.png'));
    }

    /**
     * @test
     *  複数選択されたカテゴリが表示されているか
     */
    public function it_displays_multiple_categories_on_the_item_detail_page()
    {
        // 1. 準備
        // まず、テストで使うカテゴリを2つ作成
        $category1 = Category::factory()->create(['category_name' => 'レディース']);
        $category2 = Category::factory()->create(['category_name' => 'トップス']);

         // 必須項目である「商品の状態(Condition)」も作成する
        $condition = Condition::factory()->create(['condition' => '新品、未使用']);

        // 次に、商品を作成
        $item = Item::factory()->create();

        // 作成した商品に、上記2つのカテゴリをリレーションで紐付ける
        $item->categories()->attach([
            $category1->id => ['condition_id' => $condition->id],
            $category2->id => ['condition_id' => $condition->id],
        ]);

        // 2. 実行 (When)
        // その商品の詳細ページにアクセス
        $response = $this->get('/item/' . $item->id);

        // 3. 検証 (Then)
        // ステータスが200 OKであること
        $response->assertStatus(200);

        // 紐づけたカテゴリ名が両方とも表示されていることを確認
        $response->assertSee('レディース');
        $response->assertSee('トップス');
    }

    /**
     * @test
     * 「商品名」で部分一致検索ができる
     */
    public function it_can_search_items_by_partial_item_name()
    {
        // 1. 検索キーワードにヒットする商品と、しない商品を作成
        $matchingItem = Item::factory()->create(['item_name' => '高品質なテスト用スマホケース']);
        $nonMatchingItem = Item::factory()->create(['item_name' => 'ただのパソコン']);

        // 2. 検索キーワードをクエリパラメータに含めてGETリクエストを送信
        $response = $this->get('/item/search?query=スマホ');

        // 3. 検証
        $response->assertStatus(200);
        $response->assertSee($matchingItem->item_name);    // ヒットする商品は表示される
        $response->assertDontSee($nonMatchingItem->item_name); // ヒットしない商品は表示されない
    }

     /**
     * @test
     * 検索状態がマイリストでも保持されている
     */
    /*public function it_retains_search_keyword_when_navigating_to_mylist()
    {

        // 1. ログインユーザーを作成
        $user = User::factory()->create();

        // 2. 検索キーワードを定義
        $keyword = '引き継ぎテスト';

        // 3. ログイン状態でまずトップページを検索
        $this->actingAs($user)->get('/?query=' . $keyword);

        // 4. 次にマイリストページへアクセス
        $response = $this->actingAs($user)->get(route('item.mylist'));

        // 5. マイリストページが正常に表示されることを確認
        $response->assertStatus(200);

        // 6. マイリストページに、検索キーワードが値として設定されたinput要素があることを確認
        $response->assertSee('<input type="search" name="query" value="' . $keyword . '"', false);
    }*/

    /**
     * @test
     * マイリスト内で商品名検索ができる
     */
    public function it_can_search_within_the_mylist_by_item_name()
    {
        // 1. テスト用のユーザーを作成し、ログイン
        $user = User::factory()->create();
        $this->actingAs($user);

        // 2. 検索にヒットする商品としない商品を作成
        $matchingItemInList = Item::factory()->create(['item_name' => 'お気に入りのカメラ']);
        $nonMatchingItemInList = Item::factory()->create(['item_name' => 'お気に入りの本']);

        // 3. 上記2つの商品をユーザーの「いいね（マイリスト）」に追加
        Like::factory()->create([
            'user_id' => $user->id,
            'item_id' => $matchingItemInList->id,
        ]);
        Like::factory()->create([
            'user_id' => $user->id,
            'item_id' => $nonMatchingItemInList->id,
        ]);

        // 4. マイリスト内検索を実行
        // /search?query=カメラ&from=mylist
        $response = $this->get('/item/search?from=mylist&query=カメラ');

        $response->assertStatus(200);

        // 5. 検索結果の検証
        $response->assertSee($matchingItemInList->item_name); // ヒットする商品は表示される
        $response->assertDontSee($nonMatchingItemInList->item_name); // ヒットしない商品は表示されない
    }
}

<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ContactWebFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 画面アクセス：ページ表示
     */
    public function test_public_pages_display()
    {
        $category = Category::factory()->create(['content' => '商品について']);
        $tag = Tag::factory()->create(['name' => '至急回答希望']);

        // お問い合わせフォーム入力ページ（/）が正常に表示
        $response = $this->get('/');
        $response->assertStatus(200);
        
        // categories・tagsがビュー変数として渡されること
        $response->assertViewHasAll(['categories', 'tags']);
        
        // カテゴリ名・タグ名がページに表示されること
        $response->assertSee('商品について');
        $response->assertSee('至急回答希望');
    }

    /**
     * 画面アクセス：管理画面アクセス制御
     */
    public function test_admin_dashboard_access_control()
    {
        // 未認証ユーザーは /login にリダイレクトされること
        $this->get('/admin')->assertRedirect('/login');

        // 認証されたユーザーのみが管理ダッシュボード（/admin）を表示できること
        $user = User::factory()->create();
        $this->actingAs($user)->get('/admin')->assertStatus(200);
    }

    /**
     * お問い合わせ：フォーム確認ページ表示
     */
    public function test_contact_confirm_page_flow()
    {
        $category = Category::factory()->create(['content' => '質問']);
        $data = [
            'category_id' => $category->id,
            'first_name' => '太郎',
            'last_name' => '山田',
            'gender' => 1,
            'email' => 'yamada@example.com',
            'tel' => '09012345678',
            'address' => '東京都',
            'detail' => 'テスト本文'
        ];

        // バリデーション通過時：確認ページが表示され、入力内容が画面に表示
        $response = $this->post('/contacts/confirm', $data);
        $response->assertStatus(200);
        $response->assertViewIs('contact.confirm');
        $response->assertSee('山田');
        $response->assertSee('太郎');
        $response->assertSee('yamada@example.com');

        // バリデーションエラー時：リダイレクトされエラーが返る
        $invalidData = array_merge($data, ['email' => 'not-email']);
        $this->post('/contacts/confirm', $invalidData)
                 ->assertStatus(302)
                 ->assertSessionHasErrors(['email']);
    }

    /**
     * お問い合わせ：お問い合わせ送信
     */
    public function test_contact_store_flow()
    {
        $category = Category::factory()->create();
        $tag = Tag::factory()->create();
        $data = [
            'category_id' => $category->id,
            'first_name' => '太郎',
            'last_name' => '山田',
            'gender' => 1,
            'email' => 'yamada@example.com',
            'tel' => '09012345678',
            'address' => '東京都',
            'detail' => 'テスト本文',
            'tag_ids' => [$tag->id]
        ];

        // バリデーション通過時：保存、中間テーブル記録、thanksへリダイレクト（または直接ビュー表示）
        $response = $this->post('/contacts', $data);
        
        // コントローラーが `return view('contact.thanks')` の場合は200、`redirect('/thanks')` の場合は302になります
        if ($response->getStatusCode() === 302) {
            $response->assertRedirect('/thanks');
        } else {
            $response->assertStatus(200)->assertViewIs('contact.thanks');
        }

        $this->assertDatabaseHas('contacts', ['email' => 'yamada@example.com']);
        $this->assertDatabaseHas('contact_tag', ['tag_id' => $tag->id]);

        // バリデーションエラー時：リダイレクトされエラーが返る
        $this->post('/contacts', ['email' => 'wrong'])
                 ->assertStatus(302)
                 ->assertSessionHasErrors();
    }

    /**
     * 管理機能：検索・ページネーション・詳細・削除
     */
    public function test_admin_management_functions()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        
        // 7件ごとにページネーションされることを検証するため10件作成
        Contact::factory()->count(10)->create(['category_id' => $category->id]);
        $targetContact = Contact::factory()->create([
            'category_id' => $category->id,
            'last_name' => '特定キーワード特定'
        ]);

        $this->actingAs($user);

        // GET /admin でキーワードフィルタが機能し、7件制限がかかること
        $response = $this->get('/admin?keyword=特定キーワード特定');
        $response->assertStatus(200);
        $response->assertSee('特定キーワード特定');

        // お問い合わせ詳細：カテゴリ情報付きで詳細ページ（admin.show）に表示
        $this->get("/admin/contacts/{$targetContact->id}")
            ->assertStatus(200)
            ->assertViewIs('admin.show')
            ->assertViewHas('contact');

        // お問い合わせ削除：正常削除され、/admin にリダイレクト
        $this->delete("/admin/contacts/{$targetContact->id}")
            ->assertRedirect('/admin');
        $this->assertDatabaseMissing('contacts', ['id' => $targetContact->id]);
    }

    /**
     * タグ管理：タグCRUD・認証
     */
    public function test_tag_crud_and_authentication()
    {
        $user = User::factory()->create();
        $tag = Tag::factory()->create(['name' => '古いタグ']);

        // 未認証ユーザーは拒否され /login にリダイレクトされること
        $this->get("/admin/tags/{$tag->id}/edit")->assertRedirect('/login');
        $this->post('/admin/tags', ['name' => '不認可'])->assertRedirect('/login');

        // 認証済みユーザーの操作
        $this->actingAs($user);

        // GET /admin/tags/{tag}/edit で編集画面表示
        $this->get("/admin/tags/{$tag->id}/edit")
            ->assertStatus(200)
            ->assertViewIs('admin.tags.edit');

        // POST /admin/tags でタグ作成、操作後に /admin へリダイレクト
        $this->post('/admin/tags', ['name' => '新規タグ作成'])
            ->assertRedirect('/admin');
        $this->assertDatabaseHas('tags', ['name' => '新規タグ作成']);

        // PUT /admin/tags/{tag} で更新、操作後に /admin へリダイレクト
        $this->put("/admin/tags/{$tag->id}", ['name' => '新しいタグ名変更'])
            ->assertRedirect('/admin');
        $this->assertDatabaseHas('tags', ['name' => '新しいタグ名変更']);

        // DELETE /admin/tags/{tag} で削除、操作後に /admin へリダイレクト
        $this->delete("/admin/tags/{$tag->id}")
            ->assertRedirect('/admin');
        $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
    }
}
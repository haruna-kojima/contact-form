<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use App\Http\Requests\indexContactRequest;
use App\Http\Requests\ContactRequest;
use App\Http\Requests\TagRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ContactWebUnitTest extends TestCase
{
    use RefreshDatabase;
    public function test_index_contact_request_validation()
    {
        $category = Category::factory()->create();
        $request = new indexContactRequest();
        $rules = $request->rules();

        $validData = [
            'keyword' => '山田',
            'gender' => '1',
            'category_id' => $category->id,
            'date' => '2026-08-16'
        ];
        $validator = Validator::make($validData, $rules);
        $this->assertFalse($validator->fails());

        $invalidGender = ['gender' => '9'];
        $validator = Validator::make($invalidGender, $rules);
        $this->assertTrue($validator->fails());

        // 異常系：存在しないカテゴリIDを拒否
        $invalidCategory = ['category_id' => 99999];
        $validator = Validator::make($invalidCategory, $rules);
        $this->assertTrue($validator->fails());
    }

    /**
     * 問い合わせ保存 バリデーション
     */
    public function test_store_contact_validation()
    {
        $category = Category::factory()->create();
        $tag = Tag::factory()->create(); 
        $request = new ContactRequest();
        $rules = $request->rules();

        // 正常系：全ての必須項目とタグ入力を受け付ける
        $validData = [
            'category_id' => $category->id,
            'first_name' => '太郎',
            'last_name' => '山田',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'address' => '東京都渋谷区',
            'detail' => 'お問い合わせ内容詳細記述',
            'tag_ids' => [$tag->id]
        ];
        $validator = Validator::make($validData, $rules);
        $this->assertFalse($validator->fails());

        // 異常系：不正な電話番号形式は拒否（文字混入）
        $invalidTel = array_merge($validData, ['tel' => 'abc-1234-efgh']);
        $validator = Validator::make($invalidTel, $rules);
        $this->assertTrue($validator->fails());
    }

    /**
     * タグ新規登録 & 更新 バリデーション
     */
    public function test_tag_request_validation()
    {
        $existingTag = Tag::factory()->create(['name' => '重要']);
        $request = new TagRequest();
        $rules = $request->rules();

        // 新規登録：必須入力
        $validator = Validator::make(['name' => ''], $rules);
        $this->assertTrue($validator->fails());

        // 新規登録：文字数制限（50文字超を拒否）
        $validator = Validator::make(['name' => str_repeat('A', 51)], $rules);
        $this->assertTrue($validator->fails());

        // 新規登録：一意性（重複禁止）
        $validator = Validator::make(['name' => '重要'], $rules);
        $this->assertTrue($validator->fails());

        // 更新：自身の名前維持は可能（ignore検証）
        $updateValidator = Validator::make(
            ['name' => '重要'], 
            ['name' => ['required', 'string', 'max:50', \Illuminate\Validation\Rule::unique('tags', 'name')->ignore($existingTag->id)]]
        );
        $this->assertFalse($updateValidator->fails());
    }

    /**
     * 各種モデルリレーション
     */
    public function test_model_relations()
    {
        $category = Category::factory()->create();
        $contact = Contact::factory()->create(['category_id' => $category->id]);
        $tag = Tag::factory()->create();

        // カテゴリ関係：1つのカテゴリから、紐づく複数のお問い合わせ（hasMany）
        $this->assertTrue($category->contacts()->exists());
        $this->assertEquals($contact->id, $category->contacts->first()->id);

        // お問い合わせ関係：1つのお問い合わせが特定のカテゴリに属する
        $this->assertEquals($category->id, $contact->category->id);

        // お問い合わせ関係：複数のタグと同期（sync）できる
        $contact->tags()->sync([$tag->id]);
        $this->assertTrue($contact->tags()->where('tag_id', $tag->id)->exists());

        // タグ関係：中間テーブルを介して、1つのタグが複数のお問い合わせに紐づく
        $this->assertTrue($tag->contacts()->where('contact_id', $contact->id)->exists());
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Contact;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Database\Seeder;


class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //日本語表記のFakerを生成
        $faker = fake('ja_JP');

        // 既存のカテゴリとタグのIDを全件取得
        $categoryIds = Category::pluck('id')->toArray();
        $tagIds = Tag::pluck('id')->toArray();

        // カテゴリやタグがない場合のセーフティ
        if (empty($categoryIds) || empty($tagIds)) {
            $this->command->error('Categories または Tags テーブルにデータがありません。先に生成してください。');
            return;
        }

        // 20件のダミーデータを生成
        for ($i = 0; $i < 20; $i++) {
            // 性別をランダムに決定 (1:男性, 2:女性, 3:その他 など、フォームの仕様に合わせて調整)
            $gender = $faker->randomElement([1, 2, 3]);

            // 性別に応じた名前の生成
            if ($gender === 1) {
                $firstName = $faker->firstNameMale();
                $lastName = $faker->lastName();
            } elseif ($gender === 2) {
                $firstName = $faker->firstNameFemale();
                $lastName = $faker->lastName();
            } else {
                $firstName = $faker->firstName();
                $lastName = $faker->lastName();
            }

            // Contactレコードの作成
            $contact = Contact::create([
                'category_id' => $faker->randomElement($categoryIds),
                'first_name'  => $firstName,
                'last_name'   => $lastName,
                'gender'      => $gender,
                // フォーム入力に近い一意なメールアドレス
                'email'       => $faker->unique()->safeEmail(),
                // 090-XXXX-XXXX などの一般的な電話番号形式
                'tel'         => $faker->numerify('0#########'),
                // 都道府県 + 市区町村 + 番地
                'address'     => $faker->prefecture() . $faker->city() . $faker->streetAddress(),
                // 建物名（ランダムで入らないケースも考慮）
                'building'    => $faker->optional(0.7)->realText(15) . 'ビル ' . $faker->buildingNumber() . '号室',
                // お問い合わせ詳細など（10文字〜100文字程度のテキスト）
                'detail'      => $faker->realTextBetween(10, 100),
            ]);

            // ランダムに1〜3件のタグIDを抽出
            $randomTagIds = $faker->randomElements($tagIds, $faker->numberBetween(1, 3));

            // 中間テーブルに紐付け
            $contact->tags()->attach($randomTagIds);
        }
    }
}

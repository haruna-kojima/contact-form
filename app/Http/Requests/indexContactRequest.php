<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class indexContactRequest extends FormRequest
{
    /**
     * 認証チェック（今回は誰でも検索可能にするため true にします）
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * 検索パラメーターのバリデーションルール
     */
    public function rules(): array
    {
        return [
            'keyword'     => 'nullable|string|max:255',
            'gender'      => 'nullable|integer|in:0,1,2,3',
            'category_id' => 'nullable|integer|exists:categories,id',
            'date'        => 'nullable|date_format:Y-m-d',
        ];
    }
    public function filter($query)
    {
        $validated = $this->validated();

        // 1. 名前・メールアドレス（部分一致）
        if (!empty($validated['keyword'])) {
            $keyword = $validated['keyword'];
            $query->where(function ($subQuery) use ($keyword) {
                $subQuery->where('first_name', 'like', '%' . $keyword . '%')
                         ->orWhere('last_name', 'like', '%' . $keyword . '%')
                         ->orWhere('email', 'like', '%' . $keyword . '%');
            });
        }

        // 2. 性別（「0」は選択なし扱いなので除外）
        if (isset($validated['gender']) && $validated['gender'] != '0') {
            $query->where('gender', $validated['gender']);
        }

        // 3. カテゴリ
        if (!empty($validated['category_id'])) {
            $query->where('category_id', $validated['category_id']);
        }

        // 4. 日付
        if (!empty($validated['date'])) {
            $query->whereDate('created_at', $validated['date']);
        }

        return $query;
    }
}
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string',
            'description' => 'required|max:255',
            'image' => 'required|image|mimes:jpg,jpeg,png',
            'brand' => 'nullable|string',
            'category' => 'required|array',
            'category.*' => 'integer|exists:categories,id',
            'item_condition' => 'required|string|in:1,2,3,4', // 指定された値内か確認
            'price' => 'required|integer|min:1',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '商品名を入力してください',
            'description.required' => '商品説明を入力してください',
            'description.max' => '255文字以内で入力してください',
            'image.required' => '商品画像を選択してください',
            'image.mimes' => '「.jpeg」または「.png」形式でアップロードしてください',
            'brand.string' => '文字で入力してください',
            'category.required' => 'カテゴリーを選択してください',
            'item_condition.required' => '商品状態を選択してください',
            'price.required' => '販売価格を入力してください',
            'price.integer' => '数値で入力してください',
            'price.min' => '0円以上で入力してください',
        ];
    }
}

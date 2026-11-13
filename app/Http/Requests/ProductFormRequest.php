<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'catagory_id'=> [
                'required',
                'integer'
            ],
            'name'=> [
                'required',
                'string'
            ],
            'slug'=> [
                'required',
                'string',
                'max:255'
            ],
            'brand'=> [
                'required',
                'string',
                'max:255'
            ],
            'small_description'=> [
                'required',
                'string'
            ],
            'description'=> [
                'required',
                'string'
            ],
            // min:1 — a price of 0 would let the product be bought for Tk 0 at checkout.
            'original_price'=> [
                'required',
                'integer',
                'min:1'
            ],
            'selling_price'=> [
                'required',
                'integer',
                'min:1'
            ],
            'quantity'=> [
                'required',
                'integer'
            ],
            'trending'=> [
                'nullable'
            ],
            // No 'status' rule: Visible/Hidden is changed only with the list's toggle button.
            'meta_title'=> [
                'required',
                'string',
                'max:255'
            ],
            'meta_keyword'=> [
                'required',
                'string'
            ],
            'meta_description'=> [
                'required',
                'string'
            ],
            'search_keywords' => ['nullable', 'string', 'max:500'],
            // The form field is name="image[]" and the controller reads $request->file('image').
            'image' => ['nullable', 'array'],
            'image.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],

        ];
    }
}

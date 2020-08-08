<?php
namespace App\Http\Requests;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Http\Requests\Request;




class ProductsCreateOrEditRequest extends Request
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name'          => 'required|min:3|max:255',
            'price'         => 'required|regex:/^\d+(\.\d{1,2})?$/'
        ];
    }

    public function attributes()
    {
        return [
            'name'              => 'Nome',
            'price'             => 'Preço'
        ];
    }

    public function messages()
    {
        return [
            'name.required'         => 'O campo Nome é obrigatório',
            'name.min'              => 'O campo Nome deve conter pelo menos 3 caracteres',
            'name.max'              => 'O campo Nome deve conter no máximo 255 caracteres',
            'price.required'        => 'O campo Preço é obrigatório',
            'price.regex'           => 'Valor inválido no campo Preço'

        ];
    }
}

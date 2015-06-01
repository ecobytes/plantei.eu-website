<?php namespace Modules\Newsletter\Http\Requests;

use Caravel\Http\Requests\Request;

class NewsletterRequest extends Request {

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
			'name' => 'required|string|max:255',
			'email' => 'required|unique:newsletter_subscriptors|email|max:255'
		];
	}

	public function messages(){
		return [
				'name.required' => 'É necessário fornecer um nome. O uso do nome real é opcional',
				'name.string' => 'O nome tem de conter letras',
				'name.max' => 'O nome é demasiado longo',
				'email.required' => 'É necessário preencher o campo de email',
				'email.email' => 'É necessário fornecer um endereço de email válido',
				'email.unique' => 'O endereço já se encontra inscrito na lista',
				'email.max' => 'O endereço de email é demasiado longo'
		];

	}

}

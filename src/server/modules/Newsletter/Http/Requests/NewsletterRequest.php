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
		return \Lang::get('newsletter::validation');
				
	}

}

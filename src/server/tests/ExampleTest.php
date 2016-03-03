<?php

class ExampleTest extends TestCase {


	protected $baseUrl = 'http://plantei.eu';

	/**
	 * A basic functional test example.
	 *
	 * @return void
	 */
	public function testBasicExample()
	{
		$response = $this->call('GET', '/');

		$this->assertEquals(200, $response->getStatusCode());
	}

	public function testRootPage()
	{
		$this->visit('/')
			->see(\Lang::get('projectpresentation::messages')['intro'])
			->see(\Lang::get('projectpresentation::messages')['descriptionTitle1']);
	}

	public function testLanguageArrays()
	{
		foreach([
			'footer', 'passwords', 'pagination', 'validation',
			'projectpresentation::messages',
			'authentication::messages',
			'authentication::validation',
			'authentication::confirmationemail',
			'newsletter::messages',
			'newsletter::validation',
			'newsletter::confirmationemail',
			'seedbank::messages',
			'seedbank::menu'
		] as $messages){
			$pt_size = sizeof(Lang::get($messages, [], 'pt'));
			$availableLanguages = ['en'];
			foreach ($availableLanguages as $lang){
				$sizeoflang = sizeof(Lang::get($messages, [], $lang));
				if ($pt_size != $sizeoflang){
					echo "[".$lang.":".$messages."] has ".$sizeoflang." against [default:pt] which has ".$pt_size."\n";
				}
			}
		
		}
	}
}

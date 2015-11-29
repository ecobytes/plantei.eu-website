<?php

class ExampleTest extends TestCase {


	protected $baseUrl = 'http://localhost';

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
}

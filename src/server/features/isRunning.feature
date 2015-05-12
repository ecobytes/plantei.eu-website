Feature: Is Running
	In order to run tests
	as a developer
	I need to check that the site is up and running

Scenario: Home Page
	Given I am on the homepage
	Then I should see "Caravel 0.2"

Scenario: Admin is not accessible to guests
	When I go to "admin"
	Then the url should match "login"
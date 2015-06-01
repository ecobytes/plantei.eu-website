Feature: Is Running
	In order read the site contents confortably
	as a user
	I need to have the content available in my language

Scenario: Language links show on Homepage
	Given I am on the homepage
	Then I should see "pt en" 

Scenario: Homepage is shown in English by default
	Given I am on the homepage
	Then I should not see "Projecto para a promoção da soberania alimentar"

Scenario: User should be able to get to the portuguese translation from the homepage
	Given I am on the homepage
	When I follow "pt"
	Then I should see "Projecto para a promoção da soberania alimentar"
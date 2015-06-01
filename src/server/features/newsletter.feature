Feature: Newsletter
	In order use the newsletter functionality
	as a user
	I need to be able to manage my subscription

Scenario: Subscribed Email
	Given I am on the homepage
	When I fill in the following:
	|name  | testUser 						|
	|email | testuser@example.com |
	When I press "Subscrever"
	Then I should see "Inscrição bem sucedida"


Scenario: Subscribed Email with bad email
	Given I am on the homepage
	When I fill in the following:
	|name  | testUser 						|
	|email | testuserexample.com |
	When I press "Subscrever"
	Then I should see "É necessário fornecer um endereço de email válido"

Scenario: Subscribed Email with no name
	Given I am on the homepage
	When I fill in the following:
	|name  |  						|
	|email | testuser@example.com |
	When I press "Subscrever"
	Then I should see "É necessário fornecer um nome."

Scenario: Confirmed Email
 Given I have subscribed my email to the newsletter
 When I go to validation url
 Then I should see "Inscrição Confirmada"

Scenario: Failed Confirmation
 Given I have subscribed my email to the newsletter
 When I go to "/newsletter/confirm/wrongvalidationkey"
 Then I should see "Erro na Confirmação da Inscrição"
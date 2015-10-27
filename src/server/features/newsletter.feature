Feature: Newsletter
	In order use the newsletter functionality
	as a user
	I need to be able to manage my subscription

Scenario: Subscribed Email in English
	Given I am on the homepage
	When I fill in the following:
	|name  | testUser 						|
	|email | test@example.com |
	When I press "Subscribe"
	Then I should not see "This email address is already on the list"
	Then I should see "Subscription Successfull"
	Then I should get an email with the title "Please Confirm your account"
	Then I should get an email containing "following the link below"
  When I go to "en" validation url
  Then I should see "Subscription Confirmed"
  When I press "English"
  Then I should see "Subscription Settings Successfully Changed"

Scenario: Subscribed Email in Portuguese
	Given I am on the homepage
	When I go to "/pt"
	When I fill in the following:
	|name  | testUser 						|
	|email | testuser@example.com |
	When I press "Subscrever"
	Then I should not see "O endereço já se encontra inscrito na lista"
	Then I should see "Subscrição bem Sucedida"
	Then I should get an email with the title "Por favor confirme a sua conta"
	Then I should get an email containing "seguindo o link"
  When I go to "pt" validation url
  Then I should see "Inscrição Confirmada"
  When I press "Português"
  Then I should see "Alterações à Subscrição da Newsletter Efectuadas"

Scenario: Subscribed Email with bad email
	Given I am on the homepage
	When I fill in the following:
	|name  | testUser 						|
	|email | testuserexample.com |
	When I press "Subscribe"
	Then I should see "You must supply a valid email address"

Scenario: Subscribed Email with no name
	Given I am on the homepage
	When I fill in the following:
	|name  |  						|
	|email | testuser@example.com |
	When I press "Subscribe"
	Then I should see "You must provide a name."

Scenario: Confirmed Email
 Given I have subscribed my email to the newsletter
 When I go to "en" validation url
 Then I should see "Subscription Confirmed"

Scenario: Failed Confirmation
 Given I have subscribed my email to the newsletter
 When I go to "/newsletter/confirm/wrongvalidationkey"
 Then I should see "Error Confirming your subscription"

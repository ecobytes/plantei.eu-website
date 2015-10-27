Feature: Users
	In order to access the site web application
	as a user
	I need to be able to register login and manage my account

@current
Scenario: I create a new account
	Given I am on the homepage
	When I follow "Register"
	Then I should see "Register a new account"
	When I fill in the following:
	|name    | testUser 						|
	|email   | test@example.com |
	|password| testpassword |
	|password_confirmation| testpassword|
	|subscribeNewsletter| 1 |
	When I press "Register"
	Then I should see "Registration Successful"
	Then I should get an email with the title "Please confirm your account"
	Then I should get an email containing "the link below"
	When I visit the "en" user validation url
	Then I should see "Registration Confirmed"


Scenario: I get an invitation to register in the site
	Given I have recieved and invitation to register on the site

Scenario: I login and logout of my account


Scenario: I change my password

Scenario: I try to change the password but the confirmation does not match

Scenario: I change my email

Scenario: I change my email but the confirmation does not match

Scenario: I destroy my account

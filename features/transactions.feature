Feature: transactions API 

Scenario: 
	Given I call "/transactions/current"
	Then I get a response
	And the response is JSON
	And the response contains at least one transaction
	And the first transaction contains a transaction ID
	And the first transaction contains two teams
	And the first transaction contains a description

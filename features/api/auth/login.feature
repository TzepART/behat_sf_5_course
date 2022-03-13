Feature: Login as existing user
    As an user
    I need the ability to get access to api

    Scenario Outline: Simple order crating
#        Given I am an authenticated user
#        And several my orders exists in database
        When I make request "POST" "/app/v1/device/smart/activate" with body
        """json
        { "activationCode": "012345" }
        """
        Then I should see response code 200
        And I should see that response contains subset
        """json
        {
            "result": "OK",
            "code": "<code>"
        }
        """
        And I should see that Order "<code>" exists in database
        Examples:
            | code    |
            | 12345   |
            | 1234567 |

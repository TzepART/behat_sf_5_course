Feature: Login as existing user
    As an user
    I need the ability to get access to api

    Scenario: Successful login
        And several users exists in database
        When I make request "POST" "/api/login" with body
        """json
        {"username": "user_1234", "password": "qwe123"}
        """
        Then I should see response code 200
        And I should see that response contains subset
        """json
        {
          "user_id": 1
        }
        """
        And I should see that result contains "token" with not empty string


    Scenario: Fail login by incorrect username
        And several users exists in database
        When I make request "POST" "/api/login" with body
        """json
        {"username": "fail_user_1234", "password": "qwe123"}
        """
        Then I should see response code 401
        And I should see that response contains subset
        """json
        {
            "error": "Invalid credentials."
        }
        """

    Scenario: Fail login by null field
        And several users exists in database
        When I make request "POST" "/api/login" with body
        """json
        {"username": "fail_user_1234"}
        """
        Then I should see response code 400
#        And I should see that response contains subset
#        """json
#        {
#            "message": "missing credentials"
#        }
#        """

Feature: Create order
    As an user
    I need the ability to crate order

    Scenario Outline: Simple order crating
        Given User exists in database with apiToken "<apiToken>" and username "<user_id>"
        When I make request "POST" "/api/v1/order/create" with auth_token "<apiToken>" and user_id "<user_id>" with body
        """json
        {"code": "<code>"}
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
            | apiToken         | user_id         | code    |
            | test_api_token   | user_1234       | 12345   |
            | test_api_token   | user_1234       | 1234567 |
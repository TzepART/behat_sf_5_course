Feature: Create order
    As an user
    I need the ability to crate order

    Scenario Outline: Simple order crating
#        Given I am an authenticated user
#        And several my orders exists in database
        When I make request "GET" "/api/v1/order/create?code=<code>"
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


#    Scenario Outline: Activate smart device by registered device
#        Given I have device "<deviceType>" with id "<deviceId>"
#        And I have auth-token "<deviceType>_db_access_token"
#        And registered devices and smart tv with activation code "012345" exists in database
#        When I make request "POST" "/app/v1/device/smart/activate" with body
#        """json
#        { "activationCode": "012345" }
#        """
#        Then I should see response code 200
#        And I should see that response matches spec
#        And I should see that device "smart_tv" with id "smart_tv_for_activation" exists in database for passMediaId "passMediaId_user"
#        And I should see that response contains subset
#        """json
#        {
#            "result": {
#                "count": 12,
#                "offset": 0,
#                "limit": 12,
#                "items": [
#                    {
#                        "id": "smart_tv_for_activation",
#                        "type": "smart_tv",
#                        "os": "smart_tv",
#                        "osVersion": "0.0",
#                    }
#                ]
#            }
#        }
#        """
#        And I should see that result contains "items[0].profile.id" with not empty string
#
#        Examples:
#            | deviceType        | deviceId             |
#            | browser           | browser_id           |
#            | mobile            | mobile_id            |
#            | tablet            | tablet_id            |
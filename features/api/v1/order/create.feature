Feature: Smart activate
    In order to use smart device
    As an user
    I need the ability to activate smart device

    Scenario Outline: Activate smart device by registered device
        Given I have device "<deviceType>" with id "<deviceId>"
        And I have auth-token "<deviceType>_db_access_token"
        And registered devices and smart tv with activation code "012345" exists in database
        When I make request "POST" "/app/v1/device/smart/activate" with body
        """json
        { "activationCode": "012345" }
        """
        Then I should see response code 200
        And I should see that response matches spec
        And I should see that device "smart_tv" with id "smart_tv_for_activation" exists in database for passMediaId "passMediaId_user"
        And I should see that response contains subset
        """json
        {
            "result": {
                "count": 12,
                "offset": 0,
                "limit": 12,
                "items": [
                    {
                        "id": "smart_tv_for_activation",
                        "type": "smart_tv",
                        "os": "smart_tv",
                        "osVersion": "0.0",
                        "applicationVersion": "0.0.1",
                        "userAgent": null,
                        "deviceModel": null,
                        "deviceName": null,
                        "profile": {
                            "child": false,
                            "title": "username",
                            "main": true,
                            "protected": false,
                            "restriction": "all",
                            "restrictionItem": {
                                "id": "all",
                                "title": "Взрослый"
                            },
                            "avatar": null
                        }
                    }
                ]
            }
        }
        """
        And I should see that result contains "items[0].profile.id" with not empty string

        Examples:
            | deviceType        | deviceId             |
            | browser           | browser_id           |
            | mobile            | mobile_id            |
            | tablet            | tablet_id            |
            | android_tv        | android_tv_id        |
            | apple_tv          | apple_tv_id          |
            | smart_tv          | smart_tv_id          |
            | smart_tv_lg       | smart_tv_lg_id       |
            | smart_tv_samsung  | smart_tv_samsung_id  |
            | smart_tv_hisense  | smart_tv_hisense_id  |
            | smart_tv_philips  | smart_tv_philips_id  |
            | huawei_android_tv | huawei_android_tv_id |

    Scenario: Activate smart device by registered device with main profile with avatar. Main profile must be copied to activated device
        Given I have device "browser" with id "smart_activate_browser_with_avatar"
        And I have auth-token "browser_db_access_token"
        And registered browser with avatar and smart tv with activation code "012345" exists in database
        When I make request "POST" "/app/v1/device/smart/activate" with body
        """json
        { "activationCode": "012345" }
        """
        Then I should see response code 200
        And I should see that response matches spec
        And I should see that device "smart_tv" with id "smart_tv_for_activation" exists in database for passMediaId "passMediaId_user"
        And I should see that response contains subset
        """json
        {
            "result": {
                "count": 2,
                "offset": 0,
                "limit": 2,
                "items": [
                    {
                        "id": "smart_tv_for_activation",
                        "type": "smart_tv",
                        "os": "smart_tv",
                        "osVersion": "0.0",
                        "applicationVersion": "0.0.1",
                        "userAgent": null,
                        "deviceModel": null,
                        "deviceName": null,
                        "profile": {
                            "child": true,
                            "restrictionItem": {
                                "id": "12",
                                "title": "Подростки (12+)"
                            },
                            "title": "main profile",
                            "main": true,
                            "protected": true,
                            "restriction": "12",
                            "avatar": {
                                "image": {
                                    "url": "avatar_img.png",
                                    "width": 200,
                                    "height": 100,
                                    "mime": "image/png"
                                }
                            }
                        }
                    },
                    {
                        "id": "smart_activate_browser_with_avatar",
                        "type": "browser",
                        "os": "android",
                        "osVersion": "0.0",
                        "applicationVersion": "0.0.1",
                        "userAgent": null,
                        "deviceModel": null,
                        "deviceName": null,
                        "profile": {
                            "child": true,
                            "restrictionItem": {
                                "id": "12",
                                "title": "Подростки (12+)"
                            },
                            "title": "main profile",
                            "main": true,
                            "protected": true,
                            "restriction": "12",
                            "avatar": {
                                "image": {
                                    "url": "avatar_img.png",
                                    "width": 200,
                                    "height": 100,
                                    "mime": "image/png"
                                }
                            }
                        }
                    }
                ]
            }
        }
        """
        And I should see that result contains "items[0].profile.id" with not empty string
        And I should see that result contains "items[0].profile.avatar.id" with integer > 0
        And I should see that result contains "items[1].profile.id" with not empty string
        And I should see that result contains "items[1].profile.avatar.id" with integer > 0

    Scenario: Smart device not found by activation code
        Given I am an authenticated user
        When I make request "POST" "/app/v1/device/smart/activate" with body
        """json
        { "activationCode": "012345" }
        """
        Then I should see response code 200
        And I should see response json error 2003 "Неверный код"

    Scenario: Smart device with expired activation code must fail activation
        Given I am an authenticated user
        And smart tv with expired activation code exists in database
        When I make request "POST" "/app/v1/device/smart/activate" with body
        """json
        { "activationCode": "012345" }
        """
        Then I should see response code 200
        And I should see response json error 2005 "Время использования кода активации смарт-устройства истекло. Необходимо заново получить код активации"

    Scenario Outline: Activation must fail on code validation constraints violations
        Given I am an authenticated user
        And smart tv with expired activation code exists in database
        When I make request "POST" "/app/v1/device/smart/activate" with body
        """json
        { "activationCode": "<code>" }
        """
        Then I should see response code 200
        And I should see response json error 1405 "Ошибка валидации данных запроса"

        Examples:
            | code    |
            | 12345   |
            | 1234567 |

    Scenario: No required device headers on smart activate
        Given I have device with no headers
        When I make request "POST" "/app/v1/device/smart/activate" with body
        """json
        { "activationCode": "012345" }
        """
        Then I should see response code 200
        And I should see response json error 1400 "Не переданы обязательные заголовки"

    Scenario: No required auth-token header on smart activate
        Given I am an anonymous user
        When I make request "POST" "/app/v1/device/smart/activate" with body
        """json
        { "activationCode": "012345" }
        """
        Then I should see response code 200
        And I should see response json error 1410 "Ни токен ни сессия не найдены"

    Scenario: Broken request json on smart activate
        Given I am an authenticated user
        When I make request "POST" "/app/v1/device/smart/activate" with body
        """json
        { "activationCode": "01
        """
        Then I should see response code 200
        And I should see response json error 1400 "Невалидный json"

    Scenario: Invalid auth token on smart activate
        Given I am a user with invalid auth token
        When I make request "POST" "/app/v1/device/smart/activate" with body
        """json
        { "activationCode": "012345" }
        """
        Then I should see response code 200
        And I should see response json error 1401 "Токен не найден (Unauthorize)"

    Scenario: Expired auth token on smart activate
        Given I am a user with expired auth token
        When I make request "POST" "/app/v1/device/smart/activate" with body
        """json
        { "activationCode": "012345" }
        """
        Then I should see response code 200
        And I should see response json error 1402 "Время жизни токена истекло, необходимо выполнить запрос на обновление токенов"

    Scenario: Unknown device on smart activate
        Given I am a user with unknown device and auth token
        When I make request "POST" "/app/v1/device/smart/activate" with body
        """json
        { "activationCode": "012345" }
        """
        Then I should see response code 200
        And I should see response json error 1403 "Не найдено устройство, от имени которого выполняется запрос"

    Scenario: Device without user on smart activate
        Given I am a user with not activated device
        When I make request "POST" "/app/v1/device/smart/activate" with body
        """json
        { "activationCode": "012345" }
        """
        Then I should see response code 200
        And I should see response json error 1404 "Устройство, от имени которого выполняется запрос, не активировано"

    Scenario: Request with invalid Content-Type on smart activate
        Given I am an anonymous user
        And my device send "application/xml" Content-Type header
        When I make request "POST" "/app/v1/device/smart/activate" with body
        """json
        { "activationCode": "012345" }
        """
        Then I should see response code 200
        And I should see response json error 1400 "Неподдерживаемый Content-Type"

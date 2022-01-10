Feature: Search
  In order to find products dinosaurs love
  As a website user
  I need to be able to search for products

  Background:
    Given I am on "/"

  Scenario Outline: Search for a product
    Given Load all data
    When I fill in the search box with "<term>"
    And I press the search button
    Then I should see "<result>"

    Examples:
      | term    | result              |
      | Samsung | Samsung 3D Slim LED |
      | XBox    | No products found   |

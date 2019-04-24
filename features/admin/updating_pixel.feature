@managing_pixels
Feature: Updating a pixel
  In order to change pixel details
  As an Administrator
  I want to be able to edit a pixel

  Background:
    Given the store has a pixel with pixel id "123456789"
    And I am logged in as an administrator
    And the store operates on a single channel in "United States"

  @ui
  Scenario: Updating pixel id
    Given I want to update the pixel with pixel id "123456789"
    When I update the pixel with pixel id "123456789"
    And I save my changes
    Then I should be notified that it has been successfully edited
    And this pixel's pixel id should be "123456789"

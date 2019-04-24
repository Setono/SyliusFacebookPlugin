@managing_pixels
Feature: Adding a new pixel
  In order to use Facebook pixels for my website
  As an Administrator
  I want to add a new pixel

  Background:
    Given I am logged in as an administrator
    And the store operates on a single channel in "United States"

  @ui
  Scenario: Adding a new pixel
    Given I want to create a new pixel
    When I fill the pixel id with "123456789"
    And I add it
    Then I should be notified that it has been successfully created
    And the pixel "123456789" should appear in the store

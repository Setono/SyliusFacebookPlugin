@facebook_pixel_config
Feature: Adding Facebook Pixel configuration
    In order to use Facebook Pixel tracking capabilities
    As an administrator
    I want to add a new Facebook Pixel config

    Background:
        Given I am logged in as an administrator
        And the store operates on a single channel in "United States"

    @ui
    Scenario: Adding Facebook Pixel config
        When I go to the create config page
        And I fill the code with 123
        And I name it "Testing"
        And I add it
        Then I should be notified that it has been successfully created

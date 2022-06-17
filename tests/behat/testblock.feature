@block @block_moodle_testblock
Feature: Block Moodle Test Block
  In order to view activities information on my course
  As a manager
  I can add the moodle_testblock block in a course

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following activities are set as admin:
      | cmid | modname | name  | added      |
      | 1    | assign  | Test1 | 1655473643 |
      | 2    | quiz    | Test2 | 1655473703 |

  Scenario: View moodle_testblock block on a course
    Given I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    When I add the "Moodle test block" block
    Then I should see "Moodle test block"
    And I should see "1 - Test1 - 17-Jun-2022"
    And I should see "2 - Test2 - 17-Jun-2022"

  Scenario: Hide/show completed
    Given I log in as "student"
    And I am on "Course 1" course homepage
    AND I should see "Moodle test block"
    And I should see "1 - Test1 - 17-Jun-2022"
    And I should see "2 - Test2 - 17-Jun-2022"
    When I click on "Test1"
    Then I should see activity page
    When I click on "Mark as done"
    And I am on "Course 1" homepage
    Then I should see "Moodle test block"
    And I should see "1 - Test1 - 17-Jun-2022 - Completed"
    And I should see "2 - Test2 - 17-Jun-2022"
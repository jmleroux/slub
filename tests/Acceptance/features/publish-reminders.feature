Feature: Publish a reminder of the pull requests to review for every channels
  In order to prevent a squad from forgetting to review a PR
  As an author
  I want to automatically publish a reminder of all the PR in review in the channel

  @nominal
  Scenario: Publish a PR in review missing GTMS
    Given a PR in review not GTMed
    And a PR in review having 2 GTMs
    And a PR merged
    When the system publishes a reminder
    Then the reminder should only contain the PR not GTMed
#    And the reminder should contain the number of days since the PR has been put to review
#    And the reminder should contain the CI status
#    And the reminder should contain the number of GTMs

  @nominal
  Scenario: Publish a reminder of a PR in review for each slack channels
    Given some PRs in review and some PRs merged in multiple channels
    When the system publishes a reminder
    And the reminders should only contain a reference to the PRs in review
    # And they are ordered by descending time in review

  @nominal
  Scenario: Does not publish a reminder of a PR published in unsupported channel
    Given a PR not GTMed published in a supported channel
    And a PR not GTMed published in a unsupported channel
    When the system publishes a reminder
    Then the reminder should only contain the PR not GTMed in the supported channel
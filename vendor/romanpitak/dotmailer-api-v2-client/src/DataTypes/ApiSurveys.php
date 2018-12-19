<?php
/**
 *
 *
 * @author Fanis Strezos <strezosfanis@gmail.com>
 *
 */


namespace DotMailer\Api\DataTypes;

/**
 * Class ApiSurveys
 *
 * @property XsInt id
 * @property XsString name
 * @property XsString url
 * @property XsDateTime dateSurveyCreated
 * @property XsDateTime dateSurveyModified
 * @property XsString state
 * @property XsDateTime firstActiveDate
 * @property XsDateTime lastInactiveDate
 * @property XsDateTime scheduledStartDate
 * @property XsDateTime scheduledEndDate
 * @property XsString confirmationMode
 * @property XsString submissionMode
 * @property XsInt fieldCount
 * @property XsBoolean notifyCreatorOnResponse
 * @property XsString respondentNotificationType
 * @property XsInt respondentNotificationCampaignId
 * @property XsBoolean isAssignedToAddressBook
 * @property XsString assignedAddressBookTarget
 * @property XsInt assignedSpecificAddressBookId
 * @property XsDateTime firstResponseDate
 * @property XsDateTime lastResponseDate
 * @property XsInt totalCompleteResponses
 * @property XsInt totalCompleteResponsesInLastDay
 * @property XsInt totalCompleteResponsesInLastWeek
 * @property XsInt totalIncompleteResponses
 * @property XsInt totalViews
 * @property XsInt totalBounces
 * @property XsInt timeToCompleteMax
 * @property XsInt timeToCompleteMin
 * @property XsInt timeToCompleteTotal
 * @property XsInt sourceDirectTotal
 * @property XsInt sourceEmailTotal
 * @property XsInt sourceEmbeddedTotal
 * @property XsInt sourcePopoverTotal
 * @property XsInt sourceFacebookTotal
 * @property XsInt sourceTwitterTotal
 * @property XsInt sourceGooglePlusTotal
 * @property XsInt sourceOtherTotal
 */
final class ApiSurveys extends JsonObject
{

    protected function getProperties()
    {
        return array(
            'Id' => 'XsInt',
            'Name' => 'XsString',
            'Url' => 'XsString',
            'DateSurveyCreated' => 'XsDateTime',
            'DateSurveyModified' => 'XsDateTime',
            'State' => 'XsString',
            'FirstActiveDate' => 'XsDateTime',
            'LastInactiveDate' => 'XsDateTime',
            'ScheduledStartDate' => 'XsDateTime',
            'ScheduledEndDate' => 'XsDateTime',
            'ConfirmationMode' => 'XsString',
            'SubmissionMode' => 'XsString',
            'FieldCount' => 'XsInt',
            'NotifyCreatorOnResponse' => 'XsBoolean',
            'RespondentNotificationType' => 'XsString',
            'RespondentNotificationCampaignId' => 'XsInt',
            'IsAssignedToAddressBook' => 'XsBoolean',
            'AssignedAddressBookTarget' => 'XsString',
            'AssignedSpecificAddressBookId' => 'XsInt',
            'FirstResponseDate' => 'XsDateTime',
            'LastResponseDate' => 'XsDateTime',
            'TotalCompleteResponses' => 'XsInt',
            'TotalCompleteResponsesInLastDay' => 'XsInt',
            'TotalCompleteResponsesInLastWeek' => 'XsInt',
            'TotalIncompleteResponses' => 'XsInt',
            'TotalViews' => 'XsInt',
            'TotalBounces' => 'XsInt',
            'TimeToCompleteMax' => 'XsInt',
            'TimeToCompleteMin' => 'XsInt',
            'TimeToCompleteTotal' => 'XsInt',
            'SourceDirectTotal' => 'XsInt',
            'SourceEmailTotal' => 'XsInt',
            'SourceEmbeddedTotal' => 'XsInt',
            'SourcePopoverTotal' => 'XsInt',
            'SourceFacebookTotal' => 'XsInt',
            'SourceTwitterTotal' => 'XsInt',
            'SourceGooglePlusTotal' => 'XsInt',
            'SourceOtherTotal' => 'XsInt'

        );
    }

}

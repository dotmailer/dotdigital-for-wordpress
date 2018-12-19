<?php
/**
 *
 *
 * @author Roman Piták <roman@pitak.net>
 *
 */


namespace DotMailer\Api\DataTypes;


final class ApiSurveysList extends JsonArray
{

    protected function getDataClass()
    {
        return 'ApiSurveys';
    }

}

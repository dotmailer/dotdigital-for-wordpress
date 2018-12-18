<?php
/**
 *
 *
 * @author Fanis Strezos <strezosfanis@gmail.com>
 *
 */


namespace DotMailer\Api\DataTypes;


final class ApiSurveys extends JsonArray
{

    protected function getDataClass()
    {
        return 'ApiSurveys';
    }

}

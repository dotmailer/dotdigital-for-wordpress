<?php

function shutdown() {
    $error = error_get_last();
    if (isset($error) && $error['type'] === E_ERROR) {
        echo "There was a problem: " . $error['message'];
    }
}

function returnRequiredFields($post = array()) {
    $required = NULL;
    if (!empty($post)) {
        foreach ($post as $field_id => $field) {
            if ($field[2] == "required")
                $required[$field_id] = $field;
        }
        return $required;
    }else {
        return NULL;
    }
}

register_shutdown_function('shutdown');

/**
 * @param array $a
 * @param array $b
 * @return int
 */
function bookSortDesc(array $a, array $b) {
	if (!isset($a['Name']) || !isset($b['Name'])) {
		return 0;
	}
    return ( strtolower($a['Name']) == strtolower($b['Name']) ? 0 : ( strtolower($a['Name']) > strtolower($b['Name']) ) ) ? 1 : -1;
}

/**
 * @param array $a
 * @param array $b
 * @return int
 */
function bookSortAsc(array $a, array $b) {
	if (!isset($a['Name']) || !isset($b['Name'])) {
		return 0;
	}
    return ( strtolower($a['Name']) == strtolower($b['Name']) ? 0 : ( strtolower($a['Name']) > strtolower($b['Name']) ) ) ? -1 : 1;
}

function clean($data) {
    $trimmed = trim($data);
    $stripped = strip_tags($trimmed);
    $clean = htmlspecialchars($stripped);
    return $clean;
}

function writeFormLine($fieldType, $fieldName, $fieldWording, $required) {
    if ($required == "true") {
        $required = "required";
        $asterisk = "*";
    } else {
        $required = "optional";
        $asterisk = "";
    }

    if (strtolower($fieldType) == "date") {
        echo "<label style='display:block;'  for='{$fieldName}'>$fieldWording$asterisk</label>";
        echo "<input style='display:block;' class='$fieldType'  type='text' id='$fieldName' name='datafields[{$fieldName}][]'/>";
        echo "<input   type='hidden' id='$fieldName' name='datafields[{$fieldName}][]' value='{$fieldType}'/>";
        echo "<input   type='hidden' id='$required' name='datafields[{$fieldName}][]' value='{$required}'/>";
    } elseif (strtolower($fieldType) == "boolean") {
        $fieldType = "radio";
        echo "<label style='display:block;' for='{$fieldName}'>$fieldWording$asterisk</label>";
        echo "Yes <input  class='$fieldType $required'  type='radio' id='$fieldName' name='datafields[{$fieldName}][]' value='TRUE' checked/>";
        echo "No <input class='$fieldType'  type='radio' id='$fieldName' name='datafields[{$fieldName}][]' value='FALSE'/>";
        echo "<input   type='hidden' id='$fieldName' name='datafields[{$fieldName}][]' value='$fieldType'/>";
        echo "<input   type='hidden' id='$required' name='datafields[{$fieldName}][]' value='{$required}'/>";
    } elseif (strtolower($fieldType) == "string") {
        echo "<label style='display:block;' for='{$fieldName}'>$fieldWording$asterisk</label>";
        echo "<input style='display:block;' class='$fieldType'  type='text' id='$fieldName' name='datafields[{$fieldName}][]'/>";
        echo "<input   type='hidden' id='$fieldName' name='datafields[{$fieldName}][]' value='$fieldType'/>";
        echo "<input   type='hidden' id='$required' name='datafields[{$fieldName}][]' value='{$required}'/>";
    } else {
        echo "<label style='display:block;' for='{$fieldName}'>$fieldWording$asterisk</label>";
        echo "<input style='display:block;' class='$fieldType'  type='{$fieldType}' id='$fieldName' name='datafields[{$fieldName}][]'/>";
        echo "<input   type='hidden' id='$fieldName' name='datafields[{$fieldName}][]' value='$fieldType'/>";
        echo "<input   type='hidden' id='$required' name='datafields[{$fieldName}][]' value='{$required}'/>";
    }
}

function writeFormBooks($bookId, $bookWording, $isVisible) {
    if ($isVisible == "true") {
        echo ("<input class='addressBooks' style='margin:0px 5px 5px 0; ' name='books[]' value='$bookId' type='checkbox'/>{$bookWording}<br>");
    } else {
        echo ("<input class='addressBooks' name='books[]' value='$bookId' type='hidden'/>");
    }
}

function validateRequiredFields($requiredFields) {
    $messages_option = get_option('dm_API_messages');
    if (isset($messages_option)) {

        $field_missing_error = $messages_option['dm_API_fill_required'];
    }
    $formErrors = "";

    foreach ($requiredFields as $field_id => $requiredField) {
        if (trim($requiredField[0]) == "") {

            $formErrors[$field_id] = $field_missing_error;
        }
    }
    return $formErrors;
}

function checkBooksVisibility($booksArray) {

    $invisible_books = array();

    foreach ($booksArray as $book => $details) {

        if ($details['isVisible'] == 'false') {

            $invisible_books[] = $book;
        }
    }

    if (count($invisible_books) == count($booksArray)) {
        return true;
    } else {
        return false;
    }
}

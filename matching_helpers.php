<?php

function kcse_grade_points($grade) {
    $scale = [
        'E'  => 1,
        'D-' => 2,
        'D'  => 3,
        'D+' => 4,
        'C-' => 5,
        'C'  => 6,
        'C+' => 7,
        'B-' => 8,
        'B'  => 9,
        'B+' => 10,
        'A-' => 11,
        'A'  => 12,
    ];

    return $scale[$grade] ?? null;
}

/**
 * Returns the full list of KCSE grades, best to worst, for rendering
 * as <select> options.
 */
function kcse_grade_options() {
    return ['A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'C-', 'D+', 'D', 'D-', 'E'];
}

/**
 * Fixed list of activities/talents used for matching.
 * key => human-readable label.
 */
function activity_options() {
    return [
        'academic_excellence' => 'Academic Excellence',
        'sports'              => 'Sports',
        'music'               => 'Music & Performing Arts',
        'visual_arts'         => 'Visual Arts',
        'leadership'          => 'Leadership',
        'community_service'   => 'Community Service',
        'debate'              => 'Debate & Public Speaking',
        'stem'                => 'STEM & Innovation',
        'entrepreneurship'    => 'Entrepreneurship',
    ];
}

/**
 * Converts a comma-separated string of activity keys (as stored in the DB)
 * into an array. Safe against NULL/empty values.
 */
function activities_to_array($csv) {
    if (empty($csv)) {
        return [];
    }
    return array_filter(array_map('trim', explode(',', $csv)));
}

/**
 * Converts an array of activity keys (e.g. from $_POST['activities'])
 * into a safe comma-separated string for storage.
 */
function activities_to_csv($array) {
    if (empty($array) || !is_array($array)) {
        return '';
    }
    $valid_keys = array_keys(activity_options());
    $filtered = array_intersect($array, $valid_keys);
    return implode(',', $filtered);
}

/**
 * Returns true if there is any overlap between two activity arrays.
 * Used to check if a student's activities match a scholarship's focus areas.
 */
function activities_overlap($studentActivities, $scholarshipFocusAreas) {
    if (empty($studentActivities) || empty($scholarshipFocusAreas)) {
        return false;
    }
    return count(array_intersect($studentActivities, $scholarshipFocusAreas)) > 0;
}

/**
 * Renders activity checkboxes for a form.
 * $selected should be an array of currently-checked keys.
 */
function render_activity_checkboxes($selected = []) {
    $html = '';
    foreach (activity_options() as $key => $label) {
        $checked = in_array($key, $selected) ? 'checked' : '';
        $html .= '<label class="checkbox-label">';
        $html .= '<input type="checkbox" name="activities[]" value="' . htmlspecialchars($key) . '" ' . $checked . '>';
        $html .= '<span>' . htmlspecialchars($label) . '</span>';
        $html .= '</label>';
    }
    return $html;
}

/**
 * Renders a <select> of KCSE grade options.
 * $selected is the currently chosen grade (or '' for "no minimum").
 * $includeBlank adds a "No minimum" option at the top.
 */
function render_kcse_select($name, $selected = '', $includeBlank = true) {
    $html = "<select name=\"" . htmlspecialchars($name) . "\">";
    if ($includeBlank) {
        $html .= '<option value="">No minimum / not applicable</option>';
    }
    foreach (kcse_grade_options() as $grade) {
        $is_selected = ($selected === $grade) ? 'selected' : '';
        $html .= '<option value="' . htmlspecialchars($grade) . '" ' . $is_selected . '>' . htmlspecialchars($grade) . '</option>';
    }
    $html .= '</select>';
    return $html;
}
<?php
function calculateAge($birth_date) {
    if (empty($birth_date)) return 0;
    $dob = new DateTime($birth_date);
    $now = new DateTime();
    $diff = $now->diff($dob);
    return $diff->y;
}

function getUserAllowedCategories($user) {
    // If Admin overridden, return that
    if (!empty($user['allowed_categories'])) {
        return array_map('trim', explode(',', $user['allowed_categories']));
    }

    // Otherwise, fallback to age calculation
    $age = calculateAge($user['birth_date']);
    
    $allowed = [];
    if ($age <= 12) {
        $allowed = ['SD'];
    } elseif ($age >= 13 && $age <= 15) {
        $allowed = ['SD', 'SMP'];
    } elseif ($age >= 16 && $age <= 18) {
        $allowed = ['SD', 'SMP', 'SMA'];
    } else {
        $allowed = ['SD', 'SMP', 'SMA', 'Umum'];
    }
    
    return $allowed;
}

function hasCategoryAccess($user, $category) {
    if ($category === 'Semua') return true;
    if ($user['role'] === 'admin') return true; // Admin has full access
    
    $allowed = getUserAllowedCategories($user);
    return in_array($category, $allowed);
}
?>

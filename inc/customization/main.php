<?PHP

function is_user_in_role($role, $user_id = null, $user = null)
{
    $wp_user = ($user) ? $user : (($user_id) ? get_userdata($user_id) : wp_get_current_user());

    if (!$wp_user) { 
        return false;
    }
    if (is_array($role)) {
        foreach ($role as $r) {
            if (in_array($r, $wp_user->roles)) {
                return true;
            }
        }
        return false;
    }

    return in_array($role, $wp_user->roles);
}

require_once "logging.php";
require_once "optimizations.php";

<?php

namespace Source\Models;

use Source\Core\Model;

/**
 * Class Notification
 * @package Source\Models
 */
class Notification extends Model
{
    /**
     * Notification constructor.
     */
    public function __construct()
    {
        parent::__construct("notifications", ["id"], ["image", "title", "link"]);
    }
}
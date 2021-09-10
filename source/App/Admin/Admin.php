<?php

namespace Source\App\Admin;

use Source\Core\Controller;
use Source\Models\Auth;

/**
 * Class Admin
 * @package Source\App\Admin
 */
class Admin extends Controller
{
    /**
     * @var \Source\Models\User|null
     */
    protected $user;

    /**
     * Admin constructor.
     */
    public function __construct()
    {
        parent::__construct(__DIR__ . "/../../../themes/" . CONF_VIEW_ADMIN . "/");

        $this->user = Auth::user();

        if (!$this->user || $this->user->level < 5) {
            $this->message->error("Para acessar é preciso logar-se")->flash();
            redirect("/admin/login");
        }
    }
}
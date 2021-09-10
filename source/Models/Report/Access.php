<?php

namespace Source\Models\Report;

use Source\Core\Model;
use Source\Core\Session;

/**
 * Class Access
 * @package Source\Models\Report
 */
class Access extends Model
{
    /**
     * Access constructor.
     */
    public function __construct()
    {
        parent::__construct("report_access", ["id"], ["users", "views", "pages"]);
    }

    /**
     * @return Access
     */
    public function report(): Access
    {
        $find = $this->find("DATE(created_at) = DATE(now())")->fetch();
        $session = new Session();

        if (!$find) {
            $this->users = 1;
            $this->views = 1;
            $this->pages = 1;

            setcookie("access", true, time() + 86400, "/");
            $session->set("access", true);

            $this->save();
            return $this;
        }

        if (!filter_input(INPUT_COOKIE, "access")) {
            $find->users += 1;
            setcookie("access", true, time() + 86400, "/");
        }

        if (!$session->has("access")) {
            $find->views += 1;
            $session->set("access", true);
        }

        $find->pages += 1;
        $find->save();
        return $this;
    }
}
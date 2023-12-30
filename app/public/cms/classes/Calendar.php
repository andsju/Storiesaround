<?php

/**
 * Class Calendar
 */
class Calendar extends Database
{

    /**
     * Calendar constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param string $name
     * @return int
     */
    public function setNewCalendarViews($name)
    {
        try {
            $sql = "INSERT INTO calendar_views 
			(name) VALUES (:name)";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->execute();
            return $this->db->lastInsertId('calendar_views_id');
        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param string $name
     * @param string $description
     * @param int $active
     * @param int $public
     * @param int $calendar_views_id
     * @return bool
     */
    public function setCalendarViews($name, $description, $active, $public, $calendar_views_id)
    {
        try {
            $sql = "UPDATE calendar_views 
			SET name = :name,
			description = :description,
			active = :active,
			public = :public
			WHERE calendar_views_id = :calendar_views_id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":name", $name, PDO::PARAM_STR);
            $stmt->bindParam(":description", $description, PDO::PARAM_STR);
            $stmt->bindParam(":active", $active, PDO::PARAM_INT);
            $stmt->bindParam(":public", $public, PDO::PARAM_INT);
            $stmt->bindParam(":calendar_views_id", $calendar_views_id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param string category
     * @param string $description
     * @param int $active
     * @param int $public
     * @param int $rss
     * @param int $calendar_categories_id
     * @return bool
     */
    public function setCalendarCategories($category, $description, $active, $public, $rss, $calendar_categories_id)
    {
        try {
            $sql = "UPDATE calendar_categories 
			SET category = :category,
			description = :description,
			active = :active,
			public = :public,
			rss = :rss
			WHERE calendar_categories_id = :calendar_categories_id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":category", $category, PDO::PARAM_STR);
            $stmt->bindParam(":description", $description, PDO::PARAM_STR);
            $stmt->bindParam(":active", $active, PDO::PARAM_INT);
            $stmt->bindParam(":public", $public, PDO::PARAM_INT);
            $stmt->bindParam(":rss", $rss, PDO::PARAM_INT);
            $stmt->bindParam(":calendar_categories_id", $calendar_categories_id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }

    /**
     * @param string $search
     * @return array
     */
    public function getCalendarViewsSearch($search)
    {
        $sql = "SELECT calendar_views_id, name  
		FROM calendar_views 
		WHERE name LIKE :search
		LIMIT 20";

        $search = "%" . $search . "%";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':search', $search, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param string $search
     * @return array
     */
    public function getCalendarCategoriesSearch($search)
    {
        $sql = "SELECT calendar_categories_id, category  
		FROM calendar_categories 
		WHERE category LIKE :search
		LIMIT 100";

        $search = "%" . $search . "%";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':search', $search, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param string $search
     * @return array
     */
    public function getCalendarSearchWords($search)
    {
        $query_parts = array();
        $words = preg_replace('/\s+/', ' ', $search);
        $words = explode(" ", $words);
        foreach ($words as $word) {
            $query_parts[] = "'%" . $word . "%'";
        }
        $categories = implode(' OR category LIKE ', $query_parts);
        $descriptions = implode(' OR description LIKE ', $query_parts);

        $sql = "SELECT calendar_categories_id, category, description, active, public, rss 
		FROM calendar_categories
		WHERE category LIKE {$categories}
		OR description LIKE {$descriptions}
		LIMIT 1000";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':search', $search, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * @param string $search
     * @return array
     */
    public function getCalendarViewsSearchWords($search)
    {
        $query_parts = array();
        $words = preg_replace('/\s+/', ' ', $search);
        $words = explode(" ", $words);
        foreach ($words as $word) {
            $query_parts[] = "'%" . $word . "%'";
        }
        $names = implode(' OR name LIKE ', $query_parts);
        $descriptions = implode(' OR description LIKE ', $query_parts);

        $sql = "SELECT calendar_views_id, name, description, active, public, position 
		FROM calendar_views
		WHERE name LIKE {$names}
		OR description LIKE {$descriptions}
		LIMIT 1000";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':search', $search, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * @param string $category
     * @return string
     */
    public function setNewCalendarCategories($category)
    {
        try {
            $sql = "INSERT INTO calendar_categories 
			(category) VALUES
			(:category)";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':category', $category, PDO::PARAM_STR);
            $stmt->execute();
            return $this->db->lastInsertId('calendar_categories_id');
        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }

    /**
     * @param int $calendar_categories_id
     * @return array
     */
    public function getCalendarViewsUsingCategory($calendar_categories_id)
    {
        $sql = "SELECT calendar_views_members.calendar_views_members_id, calendar_views.name   
		FROM calendar_views_members 
		INNER JOIN calendar_views
		ON calendar_views_members.calendar_views_id = calendar_views.calendar_views_id
		WHERE calendar_views_members.calendar_categories_id = :calendar_categories_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':calendar_categories_id', $calendar_categories_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param int $calendar_views_id
     * @return bool
     */
    public function setCalendarViewsDelete($calendar_views_id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM calendar_views WHERE calendar_views_id = :calendar_views_id");
            $stmt->bindParam(":calendar_views_id", $calendar_views_id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }

    /**
     * @param int $calendar_views_members_id
     * @return bool
     */
    public function setCalendarViewsCategoryDelete($calendar_views_members_id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM calendar_views_members WHERE calendar_views_members_id = :calendar_views_members_id");
            $stmt->bindParam(":calendar_views_members_id", $calendar_views_members_id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }

    /**
     * @return array
     */
    public function getCalendarCategoriesSelect()
    {
        $categories = $this->getCalendarCategories();
        return $categories;
    }

    /**
     * @return array
     */
    public function getCalendarCategories()
    {
        $sql = "SELECT calendar_categories_id, category, description, public
		FROM calendar_categories
		WHERE active = 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @return array
     */
    public function getCalendarViewsSelect()
    {
        $views = $this->getCalendarViewsActive();
        return $views;
    }

    /**
     * @return array
     */
    public function getCalendarViewsActive()
    {
        $sql = "SELECT calendar_views_id, name, description, public
		FROM calendar_views
		WHERE active = 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param int $calendar_views_id
     * @param int $calendar_categories_id
     * @return mixed
     */
    public function checkCalendarViewCategory($calendar_views_id, $calendar_categories_id)
    {
        $sql = "SELECT calendar_categories_id
		FROM calendar_views_members
		WHERE calendar_views_id = :calendar_views_id
		AND calendar_categories_id = :calendar_categories_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':calendar_views_id', $calendar_views_id, PDO::PARAM_INT);
        $stmt->bindParam(':calendar_categories_id', $calendar_categories_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @param int $calendar_views_id
     * @param int $calendar_categories_id
     * @return string
     */
    public function setCalendarViewsCategory($calendar_views_id, $calendar_categories_id)
    {
        $sql = "SELECT calendar_categories_id
		FROM calendar_views_members
		WHERE calendar_views_id = :calendar_views_id
		AND calendar_categories_id = :calendar_categories_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':calendar_views_id', $calendar_views_id, PDO::PARAM_INT);
        $stmt->bindParam(':calendar_categories_id', $calendar_categories_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            try {
                $sql = "INSERT INTO calendar_views_members 
				(calendar_views_id, calendar_categories_id) VALUES
				(:calendar_views_id, :calendar_categories_id)";

                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':calendar_views_id', $calendar_views_id, PDO::PARAM_INT);
                $stmt->bindParam(':calendar_categories_id', $calendar_categories_id, PDO::PARAM_INT);
                $stmt->execute();
                return $this->db->lastInsertId('calendar_views_members_id');

            } catch (PDOException $e) {
                handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
                return false;
            }
        }
    }

    /**
     * @param array $calendar_views_members_id_array
     */
    public function setCalendarCategoriesPosition($calendar_views_members_id_array)
    {
        try {
            // use beginTransaction > commit > rollBack
            $this->db->beginTransaction();
            $stmt = $this->db->prepare("UPDATE calendar_views_members SET position =:position WHERE calendar_views_members_id = :calendar_views_members_id");
            $stmt->bindParam(":calendar_views_members_id", $calendar_views_members_id, PDO::PARAM_INT);
            $stmt->bindParam(":position", $position, PDO::PARAM_INT);
            // counter
            $position = 0;
            foreach ($calendar_views_members_id_array as $calendar_views_members_id) {
                $position = $position + 1;
                $stmt->execute();
            }
            $this->db->commit();
            echo 'calendar categories positions saved: ' . date('H:i:s');

        } catch (PDOException $e) {
            $this->db->rollBack();
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
        }
    }

    /**
     * @return array
     */
    public function getCalendarEventsFeed()
    {
        $date = date('Y-m-d');
        $timestamp = strtotime($date);
        $year = date('Y', $timestamp);
        $month = date('m', $timestamp);
        $day = date('j', $timestamp);
        $d1 = date('Y-m-d', mktime(0, 0, 0, $month, $day, $year));
        $d2 = date('Y-m-d', strtotime('+ 4 days', $timestamp));

        $sql = "SELECT calendar_events.calendar_events_id, calendar_events.event, calendar_events.event_date, calendar_categories.calendar_categories_id, calendar_categories.category
		FROM calendar_events 
		INNER JOIN calendar_categories
		ON calendar_events.calendar_categories_id=calendar_categories.calendar_categories_id
		WHERE calendar_categories.public = 1
		AND calendar_events.event_date BETWEEN '$d1' AND '$d2'
		ORDER BY RAND() LIMIT 5";

        $stmt = $this->db->prepare($sql);
        //$stmt->bindParam(':calendar_categories_id', $calendar_categories_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param int $calendar_categories_id
     * @param int $limit
     * @return array
     */
    public function getCalendarEventsRSS($calendar_categories_id, $limit)
    {
        $d1 = date('Y-m-d');
        $timestamp = strtotime($d1);
        $d2 = date('Y-m-d', strtotime('+ 3 month', $timestamp));

        $sql = "SELECT calendar_events.calendar_events_id, calendar_events.event_title AS title, calendar_events.event AS description, calendar_events.event_date, calendar_events.event_link, calendar_events.utc_modified AS pubdate, calendar_categories.calendar_categories_id, calendar_categories.category
		FROM calendar_events 
		INNER JOIN calendar_categories
		ON calendar_events.calendar_categories_id=calendar_categories.calendar_categories_id
		WHERE calendar_categories.calendar_categories_id IN (:calendar_categories_id)
		AND calendar_events.event_rss = 1
		AND calendar_categories.rss = 1
		AND calendar_categories.public = 1
		AND calendar_categories.active = 1
		AND calendar_events.event_date BETWEEN '$d1' AND '$d2'
		ORDER BY calendar_events.event_date ASC
		limit :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':calendar_categories_id', $calendar_categories_id, PDO::PARAM_STR);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param int $calendar_categories_id
     * @param string $date
     * @return mixed
     */
    public function getCalendarEvent($calendar_categories_id, $date)
    {
        $date = ($this->_isValidDate($date)) ? $date : date('Y-m-d');

        $sql = "SELECT calendar_events.calendar_events_id, calendar_events.event, calendar_events.event_date, calendar_events.event_title, calendar_events.event_rss, calendar_events.event_link, calendar_categories.calendar_categories_id, calendar_categories.rss AS cal_rss
		FROM calendar_events 
		INNER JOIN calendar_categories
		ON calendar_events.calendar_categories_id=calendar_categories.calendar_categories_id
		WHERE calendar_events.event_date = '$date'
		AND calendar_categories.calendar_categories_id = :calendar_categories_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':calendar_categories_id', $calendar_categories_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function _isValidDate($date)
    {
        if (is_string(($date))) {
            if (preg_match("/^(\d{4})-(\d{2})-(\d{2})$/", $date, $matches)) {
                if (checkdate($matches[2], $matches[3], $matches[1])) {
                    return true;
                }
            }
        }
    }

    /**
     * @param int $calendar_categories_id
     * @param string $event_date
     * @param string $event
     * @param string $event_title
     * @param int $event_rss
     * @param string $event_link
     * @param string $utc_created
     * @param string $utc_modified
     * @return bool|string
     */
    public function setEvents($calendar_categories_id, $event_date, $event, $event_title, $event_rss, $event_link, $utc_created, $utc_modified)
    {
        // prevent duplicates
        $stmt = $this->db->prepare("SELECT event FROM calendar_events WHERE event_date = :event_date AND calendar_categories_id = :calendar_categories_id");
        $stmt->bindParam(":calendar_categories_id", $calendar_categories_id, PDO::PARAM_INT);
        $stmt->bindParam(":event_date", $event_date, PDO::PARAM_STR);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!count($rows)) {
            try {
                $sql = "INSERT INTO calendar_events 
				(event_date, event, calendar_categories_id, event_title, event_rss, event_link, utc_created, utc_modified) VALUES
				(:event_date, :event, :calendar_categories_id, :event_title, :event_rss, :event_link, :utc_created, :utc_modified)";

                $stmt = $this->db->prepare($sql);
                $stmt->bindParam("calendar_categories_id", $calendar_categories_id, PDO::PARAM_INT);
                $stmt->bindParam(":event", $event, PDO::PARAM_STR);
                $stmt->bindParam(":event_date", $event_date, PDO::PARAM_STR);
                $stmt->bindParam(":event_title", $event_title, PDO::PARAM_STR);
                $stmt->bindParam(":event_rss", $event_rss, PDO::PARAM_INT);
                $stmt->bindParam(":event_link", $event_link, PDO::PARAM_STR);
                $stmt->bindParam(":utc_created", $utc_created, PDO::PARAM_STR);
                $stmt->bindParam(":utc_modified", $utc_modified, PDO::PARAM_STR);
                $stmt->execute();
                return $this->db->lastInsertId("calendar_events_id");
            } catch (PDOException $e) {
                handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
                return false;
            }
        } else {
            try {
                $sql = "UPDATE calendar_events
				SET event = :event,
				event_title = :event_title,
				event_rss = :event_rss,
				event_link = :event_link,
				utc_modified = :utc_modified				
				WHERE event_date = :event_date
				AND calendar_categories_id = :calendar_categories_id";

                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(":calendar_categories_id", $calendar_categories_id, PDO::PARAM_INT);
                $stmt->bindParam(":event", $event, PDO::PARAM_STR);
                $stmt->bindParam(":event_date", $event_date, PDO::PARAM_STR);
                $stmt->bindParam(":event_title", $event_title, PDO::PARAM_STR);
                $stmt->bindParam(":event_rss", $event_rss, PDO::PARAM_INT);
                $stmt->bindParam(":event_link", $event_link, PDO::PARAM_STR);
                $stmt->bindParam(":utc_modified", $utc_modified, PDO::PARAM_STR);
                return $stmt->execute();
            } catch (PDOException $e) {
                handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
                return false;
            }
        }
    }

    /**
     * @param int $calendar_categories_id
     * @param string $event_date
     * @return bool
     */
    public function deleteEvents($calendar_categories_id, $event_date)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM calendar_events WHERE event_date =:event_date AND calendar_categories_id = :calendar_categories_id");
            $stmt->bindParam(":calendar_categories_id", $calendar_categories_id, PDO::PARAM_INT);
            $stmt->bindParam(":event_date", $event_date, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }

    /**
     * @param int $pages_id
     * @param int $calendar_categories_id
     * @param int $calendar_views_id
     * @param string $date_initiate
     * @param string $period_initiate
     * @param string $calendar_area
     * @param int $calendar_show
     * @return string
     */
    public function setPagesCalendar($pages_id, $calendar_categories_id, $calendar_views_id, $date_initiate, $period_initiate, $calendar_area, $calendar_show)
    {
        // prevent duplicates
        $stmt = $this->db->prepare("DELETE FROM pages_calendars WHERE pages_id = :pages_id");
        $stmt->bindParam(":pages_id", $pages_id, PDO::PARAM_INT);
        $stmt->execute();
        try {
            $sql = "INSERT INTO pages_calendars 
			(pages_id, calendar_categories_id, calendar_views_id, date_initiate, period_initiate, calendar_area, calendar_show) VALUES
			(:pages_id, :calendar_categories_id, :calendar_views_id, :date_initiate, :period_initiate, :calendar_area, :calendar_show)";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam("pages_id", $pages_id, PDO::PARAM_INT);
            $stmt->bindParam("calendar_categories_id", $calendar_categories_id, PDO::PARAM_INT);
            $stmt->bindParam("calendar_views_id", $calendar_views_id, PDO::PARAM_INT);
            $stmt->bindParam(":date_initiate", $date_initiate, PDO::PARAM_STR);
            $stmt->bindParam(":period_initiate", $period_initiate, PDO::PARAM_STR);
            $stmt->bindParam(":calendar_area", $calendar_area, PDO::PARAM_STR);
            $stmt->bindParam("calendar_show", $calendar_show, PDO::PARAM_INT);
            $stmt->execute();
            return $this->db->lastInsertId("pages_calendars_id");
        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }

    /**
     * @param int $pages_id
     */
    public function deletePagesCalendar($pages_id)
    {
        $stmt = $this->db->prepare("DELETE FROM pages_calendars WHERE pages_id = :pages_id");
        $stmt->bindParam(":pages_id", $pages_id, PDO::PARAM_INT);
        $stmt->execute();
    }

    /**
     * @param int $pages_id
     * @return mixed
     */
    public function getPagesCalendar($pages_id)
    {

        $sql = "SELECT date_initiate, period_initiate, calendar_categories_id, calendar_views_id
		FROM pages_calendars WHERE pages_id = :pages_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':pages_id', $pages_id, PDO::PARAM_INT);
        $stmt->execute();
        $arr = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($arr) {
            if ($arr['calendar_categories_id'] > 0) {
                $calendar_categories_id = $arr['calendar_categories_id'];

                $sql = "SELECT calendar_categories.category AS name, pages_calendars.date_initiate, pages_calendars.period_initiate, pages_calendars.calendar_categories_id, pages_calendars.calendar_views_id, pages_calendars.calendar_area, calendar_show
				FROM pages_calendars
				INNER JOIN calendar_categories ON calendar_categories.calendar_categories_id = pages_calendars.calendar_categories_id
				WHERE pages_calendars.pages_id = :pages_id";

                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':pages_id', $pages_id, PDO::PARAM_INT);
                $stmt->execute();
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }
            if ($arr['calendar_views_id'] > 0) {
                $calendar_views_id = $arr['calendar_views_id'];

                $sql = "SELECT calendar_views.name AS name, pages_calendars.date_initiate, pages_calendars.period_initiate, pages_calendars.calendar_categories_id, pages_calendars.calendar_views_id, pages_calendars.calendar_area, calendar_show
				FROM pages_calendars
				INNER JOIN calendar_views ON calendar_views.calendar_views_id = pages_calendars.calendar_views_id
				WHERE pages_calendars.pages_id = :pages_id";

                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':pages_id', $pages_id, PDO::PARAM_INT);
                $stmt->execute();
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }
        }
    }

    /**
     * @param int $id
     * @return null
     */
    public function displayEvent($id)
    {
        if (empty($id)) {
            return null;
        }

        $id = preg_replace('/[^0-9]/', '', $id);
        $event = $this->_loadEventById($id);

        $timestamp = strtotime($event->start);
        $date = date('F d, Y', $timestamp);
        $start = date('g:ia', $timestamp);
        $end = date('g:ia', strtotime($event->end));
        return null;
    }

    public function getCalendarEventsList($calendar_categories_id, $date, $period, $href = null)
    {
        $acc_read = false;
        $category = $this->getCalendarCategory($calendar_categories_id);

        if ($category) {
            if ($category['public'] == 1) {
                $acc_read = true;
            }
        } else {
            if (isset($_SESSION['users_id'])) {
                if (get_role_CMS('editor') == 1) {
                    $acc_read = true;
                } else {

                    //check role_CMS author & contributor
                    if (get_role_CMS('author') == 1 || get_role_CMS('contributor') == 1) {

                        // user rights
                        $calendar_rights = new CalendarRights();
                        $users_id = isset($_SESSION['users_id']) ? $_SESSION['users_id'] : 0;
                        $users_rights = $calendar_rights->getCalendarUsersRights($calendar_categories_id, $users_id);

                        // groups rights
                        $groups_rights = $calendar_rights->getCalendarGroupsRights($calendar_categories_id);

                        // read
                        if ($users_rights) {
                            if ($users_rights['rights_read'] == 1) {
                                $acc_read = true;
                            }
                        } else {
                            if ($groups_rights) {
                                if (get_membership_rights('rights_read', $_SESSION['membership'], $groups_rights)) {
                                    $acc_read = true;
                                }
                            }
                        }
                    }
                }
            }
        }

        // empty array if $acc_read is false
        $events = ($acc_read == true) ? $this->getCalendarEvents($calendar_categories_id, $date, $period) : array();

        if ($events) {
            $s = '<h5 class="calendar-events-category">' . $events[0]['category'] . '</h5>';

            foreach ($events as $event) {
                $ts = strtotime($event['event_date']);
                $weekday = $this->transl(date('D', $ts));
                $day = $this->transl(date('F', $ts));
                $day = date('j', $ts) . ' ' . strtolower($day);

                $s .= '<h6 class="calendar-events-heading">' . $weekday . ' ' . $day . '</h6>';
                $s .= '<div class="calendar-events">' . nl2br(strip_tags($event['event'], $this->validTags())) . '</div>';
            }
            echo $s;
        }
    }

    /**
     * @param int $calendar_categories_id
     * @return mixed
     */
    public function getCalendarCategory($calendar_categories_id)
    {
        $sql = "SELECT * FROM calendar_categories 
		WHERE calendar_categories_id = :calendar_categories_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':calendar_categories_id', $calendar_categories_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @param int $calendar_categories_id
     * @param string $date
     * @param string $period
     * @return array
     */
    public function getCalendarEvents($calendar_categories_id, $date, $period)
    {
        $date = ($this->_isValidDate($date)) ? $date : date('Y-m-d');
        $timestamp = strtotime($date);
        $year = date('Y', $timestamp);
        $month = date('m', $timestamp);
        $day = date('j', $timestamp);

        // get days in month
        $days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        switch ($period) {
            case 'day':
                $d1 = date('Y-m-d', mktime(0, 0, 0, $month, $day, $year));
                $d2 = date('Y-m-d', mktime(0, 0, 0, $month, $day, $year));
                break;
            case '4days':
                $d1 = date('Y-m-d', mktime(0, 0, 0, $month, $day, $year));
                $d2 = date('Y-m-d', strtotime('+ 4 days', $timestamp));
                break;
            case 'week':
                $d1 = date('Y-m-d', mktime(0, 0, 0, $month, $day, $year));
                $d2 = date('Y-m-d', strtotime('+ 1 week', $timestamp));
                break;
            case '2weeks':
                $d1 = date('Y-m-d', mktime(0, 0, 0, $month, $day, $year));
                $d2 = date('Y-m-d', strtotime('+ 2 weeks', $timestamp));
                break;
            case '4weeks':
                $d1 = date('Y-m-d', mktime(0, 0, 0, $month, $day, $year));
                $d2 = date('Y-m-d', strtotime('+ 4 weeks', $timestamp));
                break;
            case '8weeks':
                $d1 = date('Y-m-d', mktime(0, 0, 0, $month, $day, $year));
                $d2 = date('Y-m-d', strtotime('+ 8 weeks', $timestamp));
                break;
            case 'month':
                $d1 = date('Y-m-d', mktime(0, 0, 0, $month, $day, $year));
                $d2 = date('Y-m-d', strtotime('+ 1 month', $timestamp));
                break;
            default:
                $d1 = date('Y-m-d', mktime(0, 0, 0, $month, 1, $year));
                $d2 = date('Y-m-d', mktime(0, 0, 0, $month, $days_in_month, $year));
                break;
        }

        $sql = "SELECT calendar_events.calendar_events_id, calendar_events.event, calendar_events.event_date, calendar_categories.calendar_categories_id, calendar_categories.category
		FROM calendar_events 
		INNER JOIN calendar_categories
		ON calendar_events.calendar_categories_id=calendar_categories.calendar_categories_id
		WHERE calendar_categories.calendar_categories_id = :calendar_categories_id
		AND calendar_events.event_date BETWEEN '$d1' AND '$d2'
		ORDER BY calendar_events.event_date ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':calendar_categories_id', $calendar_categories_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param string $text
     * @return mixed
     */
    private function transl($text)
    {
        $a = array(
            "english" => array("Mon" => "Mon", "Tue" => "Tue", "Wed" => "Wed", "Thu" => "Thu", "Fri" => "Fri", "Sat" => "Sat", "Sun" => "Sun",
                "Monday" => "Monday", "Tuesday" => "Tuesday", "Wednesday" => "Wednesday", "Thursday" => "Thursday", "Friday" => "Friday", "Saturday" => "Saturday", "Sunday" => "Sunday",
                "one day" => "one day", "four days" => "four days", "one week" => "one week", "two weeks" => "two weeks", "four weeks" => "four weeks", "eight weeks" => "eight weeks", "one month" => "one month", "two months" => "two months", "four months" => "four months", "six months" => "six months",
                "January" => "January", "February" => "February", "March" => "March", "April" => "April", "May" => "May", "June" => "June", "July" => "July", "August" => "August", "September" => "September", "October" => "October", "November" => "November", "December" => "December",
                "w" => "w", "today" => "today"),
            "swedish" => array("Mon" => "må", "Tue" => "ti", "Wed" => "on", "Thu" => "to", "Fri" => "fr", "Sat" => "lö", "Sun" => "sö",
                "Monday" => "Måndag", "Tuesday" => "Tisdag", "Wednesday" => "Onsdag", "Thursday" => "Torsdag", "Friday" => "Fredag", "Saturday" => "Lördag", "Sunday" => "Söndag",
                "one day" => "en dag", "four days" => "fyra dagar", "one week" => "en vecka", "two weeks" => "två veckor", "four weeks" => "fyra veckor", "eight weeks" => "åtta veckor", "one month" => "en månad", "two months" => "2 månader", "four months" => "4 månader", "six months" => "6 månader",
                "January" => "Januari", "February" => "Februari", "March" => "Mars", "April" => "April", "May" => "Maj", "June" => "Juni", "July" => "Juli", "August" => "Augusti", "September" => "September", "October" => "Oktober", "November" => "November", "December" => "December",
                "w" => "v", "today" => "idag"));

        $l = isset($_SESSION['language']) ? $_SESSION['language'] : null;
        if (!$l) {
            $l = isset($_SESSION['site_language']) ? $_SESSION['site_language'] : null;
        }
        $s = $l ? $a[$l][$text] : $text;
        return $s;
    }

    /**
     * @return string
     */
    public function validTags()
    {
        $tags = '<a><img><b><i><hr><span>';
        return $tags;
    }

    /**
     * @param null $date
     * @param null $href
     * @param bool $max_width
     * @return string
     */
    public function getCalendarNavigation($date = null, $href = null, $max_width = true)
    {
        $date = ($this->_isValidDate($date)) ? $date : date('Y-m-d');
        $today = date('Y-m-d');

        // get year and month from timestamp
        $timestamp = strtotime($date);
        $year = date('Y', $timestamp);
        $month = date('m', $timestamp);

        // get days in month
        $days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        // this month start weekday
        $timestamp = mktime(0, 0, 0, $month, 1, $year);

        // weeks start on monday
        $start_weekday = (date('w', $timestamp) - 1 < 0) ? 6 : date('w', $timestamp) - 1;

        // calendar month
        $month_m = date('F', strtotime($date));
        $month_y = date('Y', strtotime($date));
        $month_name = $this->transl($month_m) . ' ' . $month_y;

        // weekdays
        $weekdays = array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun');

        // show weeks
        $weekdays [] = "w";

        $holidays = $this->getHolidays($year);
        $flagdays = $this->getFlagdays($year);

        // calendar markup
        $html = "\n<div style=\"margin:0 auto;\">";

        $width = ($max_width == true) ? "96%" : null;

        $html .= "\n<table style=\"width:$width;\" class=\"ui-widget calendar-datepicker\">";
        $html .= "\n\t<thead >";
        $html .= "\n\t\t<tr>";

        $previous = $href . date('Y-m-d', strtotime('- 1 month', $timestamp));
        $next = $href . date('Y-m-d', strtotime('+ 1 month', $timestamp));

        // skip next / previous in this view (ajax update only events or...)
        $html .= "\n\t\t\t<th colspan=\"1\" class=\"ui-widget ui-widget-header\" style=\"text-align:left;\"><a class=\"datepicker-day\" href=\"$previous\" title=\"previous\"><span class=\"ui-icon ui-icon-circle-triangle-w\">&nbsp;</span></a></th>";
        $html .= "\n\t\t\t<th colspan=\"6\" class=\"ui-widget ui-widget-header\"><a><span class=\"\">$month_name</span></a></th>";
        $html .= "\n\t\t\t<th colspan=\"1\" class=\"ui-widget ui-widget-header\"  style=\"text-align:right;\"><a class=\"datepicker-day\" href=\"$next\" title=\"next\"><span class=\"ui-icon ui-icon-circle-triangle-e\">&nbsp;</span></a></th>";
        $html .= "\n\t\t</tr>";
        $html .= "\n\t\t<tr>";

        foreach ($weekdays as $weekday) {
            $html .= "\n\t\t\t<th class=\"ui-widget ui-widget-header datepicker-weekdays\">" . $this->transl($weekday) . "</th>";
        }

        $html .= "\n\t\t</tr>";
        $html .= "\n\t</thead>";
        $html .= "\n\t<tbody>";
        $html .= "\n\t\t<tr>";
        for ($i = 1, $c = 1, $today, $m = date('m', $timestamp), $y = date('Y', $timestamp); $c <= $days_in_month; ++$i) {
            $title_holiday = null;

            // class style
            $class = 'ui-widget ui-widget-content';
            $class .= ($i <= $start_weekday) ? " ui-state-disabled" : null;
            $a_class = 'datepicker-day';

            if ($start_weekday < $i && $days_in_month >= $c) {

                // current date
                $ts = strtotime($y . '-' . $m . '-' . sprintf("%02d", $c));
                $class .= (date('Y-m-d', $ts) == date('Y-m-d', strtotime($today))) ? " ui-state-highlight" : "";

                foreach ($holidays as $key => $value) {
                    if (date('Y-m-d', $ts) == $key) {
                        $class .= " ui-state-contrast";
                        $title_holiday = $value;
                        break;
                    }
                }

                $href_date = $href . date('Y-m-d', $ts);
                $html .= "\n\t\t\t<td class=\"$class\"><a class=\"$a_class\" href=\"$href_date\" title=\"$title_holiday\">" . date('j', $ts) . "</a>";

                $c++;

            } else {

                // current date
                $ts = strtotime($y . '-' . $m . '-01');
                $n = $start_weekday - $i + 1;
                $href_pre = $href . date('Y-m-d', strtotime('- ' . $n . ' day', $ts));
                $html .= "\n\t\t\t<td class=\"$class\">" . date('d', strtotime('- ' . $n . ' day', $ts));
            }

            $html .= "</td>";

            // wrap week
            $wrap = ($i != 0 && $i % 7 == 0) ? "\n\t\t\t<td class=\"$class ui-state-disabled datepicker-week-col\">" . date('W', $ts) . "</td>\n\t\t</tr>\n\t\t<tr>" : null;
            $html .= $wrap;
        }

        $add_days = 1;
        $add_week = false;
        while ($i % 7 != 1) {

            $add_week = true;

            // current date
            $ts = strtotime($y . '-' . $m . '-' . $days_in_month);
            $href_post = $href . date('Y-m-d', strtotime('+ ' . $add_days . ' day', $ts));
            $html .= "\n\t\t\t<td class=\"$class ui-state-disabled\">" . date('j', strtotime('+ ' . $add_days . ' day', $ts)) . "</td>";
            ++$i;
            $add_days++;
        }

        if ($add_week) {
            $html .= "\n\t\t\t<td class=\"$class ui-state-disabled datepicker-week-col\">" . date('W', $ts) . "</td>";
        }

        $html .= "\n\t\t</tr>";
        $html .= "\n\t</tbody>";
        $html .= "\n\t</table>\n";
        $html .= "\n</div>";
        return $html;
    }

    /**
     * @param $year
     * @return array
     */
    private function getHolidays($year)
    {
        $l = isset($_SESSION['language']) ? $_SESSION['language'] : null;
        if (!$l) {
            $l = isset($_SESSION['site_language']) ? $_SESSION['site_language'] : null;
        }
        $days = array();

        switch ($l) {

            case "swedish":
                $days = array(
                    date('Y-m-d', strtotime('+' . (easter_days($year) - 3) . ' days', strtotime($year . '-03-21 12:00:00'))) => "Skärtorsdagen",
                    date('Y-m-d', strtotime('+' . (easter_days($year) - 2) . ' days', strtotime($year . '-03-21 12:00:00'))) => "Långfredagen",
                    date('Y-m-d', strtotime('+' . (easter_days($year) - 1) . ' days', strtotime($year . '-03-21 12:00:00'))) => "Påskafton",
                    date('Y-m-d', strtotime('+' . (easter_days($year)) . ' days', strtotime($year . '-03-21 12:00:00'))) => "Påskdagen",
                    date('Y-m-d', strtotime('+' . (easter_days($year) + 1) . ' days', strtotime($year . '-03-21 12:00:00'))) => "Annandag påsk",
                    date('Y-m-d', strtotime('+' . (easter_days($year) + 39) . ' days', strtotime($year . '-03-21 12:00:00'))) => "Kristi himmelsfärdsdag",
                    date('Y-m-d', strtotime('+' . (easter_days($year) + 49) . ' days', strtotime($year . '-03-21 12:00:00'))) => "Pingstdagen",
                    $this->getMidsommarAfton($year) => "Midsommarafton",
                    $this->getMidsommar($year) => "Midsommardagen",
                    $this->getAllaHelgonsDag($year) => "Alla helgons dag",
                    date('Y-m-d', mktime(0, 0, 0, 1, 1, $year)) => "Nyårsdagen",
                    date('Y-m-d', mktime(0, 0, 0, 1, 5, $year)) => "Trettondagsafton",
                    date('Y-m-d', mktime(0, 0, 0, 1, 6, $year)) => "Trettondag jul",
                    date('Y-m-d', mktime(0, 0, 0, 4, 30, $year)) => "Valborgsmässoafton",
                    date('Y-m-d', mktime(0, 0, 0, 5, 1, $year)) => "Första maj",
                    date('Y-m-d', mktime(0, 0, 0, 6, 6, $year)) => "Sveriges nationaldag",
                    date('Y-m-d', mktime(0, 0, 0, 12, 25, $year)) => "Juldagen",
                    date('Y-m-d', mktime(0, 0, 0, 12, 26, $year)) => "Annandag jul",
                    date('Y-m-d', mktime(0, 0, 0, 12, 31, $year)) => "Nyårsafton",
                );
                break;

            case "english":
                $days = array(
                    date('Y-m-d', strtotime('+' . (easter_days($year) - 3) . ' days', strtotime($year . '-03-21 12:00:00'))) => "Maundy Thursday",
                    date('Y-m-d', strtotime('+' . (easter_days($year) - 2) . ' days', strtotime($year . '-03-21 12:00:00'))) => "Good friday",
                    date('Y-m-d', strtotime('+' . (easter_days($year) - 1) . ' days', strtotime($year . '-03-21 12:00:00'))) => "Holy Saturday",
                    date('Y-m-d', strtotime('+' . (easter_days($year)) . ' days', strtotime($year . '-03-21 12:00:00'))) => "Easter",
                    date('Y-m-d', strtotime('+' . (easter_days($year) + 1) . ' days', strtotime($year . '-03-21 12:00:00'))) => "Easter monday",
                    date('Y-m-d', strtotime('+' . (easter_days($year) + 39) . ' days', strtotime($year . '-03-21 12:00:00'))) => "Ascension day",
                    date('Y-m-d', strtotime('+' . (easter_days($year) + 49) . ' days', strtotime($year . '-03-21 12:00:00'))) => "Pentecost Day",
                    $this->getMidsommarAfton($year) => "St John's Eve",
                    $this->getMidsommar($year) => "Midsummer day",
                    $this->getAllaHelgonsDag($year) => "All Saint's Day",
                    date('Y-m-d', mktime(0, 0, 0, 1, 1, $year)) => "New Year Day",
                    date('Y-m-d', mktime(0, 0, 0, 1, 5, $year)) => "Twelfth Night",
                    date('Y-m-d', mktime(0, 0, 0, 1, 6, $year)) => "Epiphany",
                    date('Y-m-d', mktime(0, 0, 0, 4, 30, $year)) => "Walpurgis Night",
                    date('Y-m-d', mktime(0, 0, 0, 5, 1, $year)) => "May Day",
                    date('Y-m-d', mktime(0, 0, 0, 6, 6, $year)) => "Swedish National Day and the Swedish Flag's Day",
                    date('Y-m-d', mktime(0, 0, 0, 12, 25, $year)) => "Christmas Day",
                    date('Y-m-d', mktime(0, 0, 0, 12, 26, $year)) => "Boxing Day",
                    date('Y-m-d', mktime(0, 0, 0, 12, 31, $year)) => "New Year's Eve",
                );
                break;
        }
        return $days;
    }

    /**
     * @param $year
     * @return false|string
     */
    public function getMidsommarAfton($year)
    {
        $date = $this->getMidsommar($year);
        $ts = strtotime("-1 days", strtotime($date));
        return date('Y-m-d', $ts);
    }

    /**
     * @param $year
     * @return false|string
     */
    public function getMidsommar($year)
    {
        $wd = (date('w', mktime(0, 0, 0, 6, 20, $year)));
        $ts = mktime(0, 0, 0, 6, 20 + (6 - $wd), $year);
        return date('Y-m-d', $ts);
    }

    /**
     * @param $year
     * @return false|string
     */
    public function getAllaHelgonsDag($year)
    {
        $wd = (date('w', mktime(0, 0, 0, 10, 31, $year)));
        $ts = ($wd == 0) ? mktime(0, 0, 0, 10, 31, $year) : mktime(0, 0, 0, 11, $wd, $year);
        return date('Y-m-d', $ts);
    }

    /**
     * @param $year
     * @return array
     */
    private function getFlagdays($year)
    {
        $l = isset($_SESSION['language']) ? $_SESSION['language'] : null;
        if (!$l) {
            $l = isset($_SESSION['site_language']) ? $_SESSION['site_language'] : null;
        }
        $days = array();

        switch ($l) {
            case "swedish":
                $days = array(
                    $year . "-01-01" => "Nyårsdagen",
                    $year . "-01-28" => "Konung Carl XVI Gustafs namnsdag",
                    $year . "-03-12" => "Kronprinsessan Victorias namnsdag",
                    date('Y-m-d', strtotime('+' . (easter_days($year)) . ' days', strtotime($year . '-03-21 12:00:00'))) => "Påskdagen",
                    $year . "-04-30" => "Konung Carl XVI Gustafs födelsedag",
                    $year . "-05-01" => "Första maj",
                    date('Y-m-d', strtotime('+' . (easter_days($year) + 49) . ' days', strtotime($year . '-03-21 12:00:00'))) => "Pingstdagen",
                    $year . "-06-06" => "Sveriges nationaldag och svenska flaggans dag",
                    $this->getMidsommar($year) => "Midsommardagen",
                    $year . "-07-14" => "Kronprinsessan Victorias födelsedag",
                    $year . "-08-08" => "Drottning Silvias namnsdag",
                    $year . "-10-24" => "FN-dagen",
                    $year . "-11-06" => "Gustav Adolfsdagen",
                    $year . "-12-10" => "Nobeldagen",
                    $year . "-12-23" => "Drottning Silvias födelsedag",
                    date('Y-m-d', mktime(0, 0, 0, 12, 25, $year)) => "Juldagen",
                );
                break;

            case "english":
                $days = array(
                    $year . "-01-01" => "New Year's Day",
                    $year . "-01-28" => "Namesday of the King",
                    $year . "-03-12" => "Namesday of the Heiress Apparent",
                    date('Y-m-d', strtotime('+' . (easter_days($year)) . ' days', strtotime($year . '-03-21 12:00:00'))) => "Easter Sunday",
                    $year . "-04-30" => "Birthday of the King",
                    $year . "-05-01" => "May Day",
                    date('Y-m-d', strtotime('+' . (easter_days($year) + 49) . ' days', strtotime($year . '-03-21 12:00:00'))) => "Pentecost Day",
                    $year . "-06-06" => "National Day of Sweden",
                    $this->getMidsommar($year) => "Midsummer day",
                    $year . "-07-14" => "Birthday of the Heiress Apparent",
                    $year . "-08-08" => "Namesday of the Queen",
                    $year . "-10-24" => "United Nations Day",
                    $year . "-11-06" => "Gustavus Adolphus Day",
                    $year . "-12-10" => "Alfred Nobel Day",
                    $year . "-12-23" => "Birthday of the Queen",
                    date('Y-m-d', mktime(0, 0, 0, 12, 25, $year)) => "Christmas Day",
                );
                break;
        }
        return $days;
    }

    /**
     * @param null $date
     * @param $category_or_view
     * @param null $id
     * @param null $period
     * @return string
     */
    public function getCalendarNav($date, $category_or_view, $id = null, $period = null)
    {

        // check $category_or_view
        if ($category_or_view == 'category') {
            if (!is_numeric($id)) {
                die('missing category...');
            }
            $category = $this->getCalendarCategory($id);
            if (!$category) {
                die('missing category...');
            }

            $public = $category['public'];
            $heading = $category['category'];
            $periods = array("one day" => "day", "four days" => "4days", "one week" => "week", "two weeks" => "2weeks", "one month" => "month", "two months" => "2months", "four months" => "4months", "six months" => "6months");
        }

        if ($category_or_view == 'view') {
            if (!is_numeric($id)) {
                die('missing category...');
            }
            $view = $this->getCalendarViews($id);
            if (!$view) {
                die('missing view...');
            }

            $public = $view['public'];
            $heading = $view['name'];
            $periods = array("one day" => "day", "four days" => "4days", "one week" => "week", "two weeks" => "2weeks", "four weeks" => "4weeks", "one month" => "month", "eight weeks" => "8weeks");
        }

        // check date
        $date = ($this->_isValidDate($date)) ? $date : date('Y-m-d');

        // calendar month
        $month_m = date('F', strtotime($date));
        $month_y = date('Y', strtotime($date));
        $month_name = $this->transl($month_m) . ' ' . $month_y;

        $period_select = "\n<select id=\"period\" class=\"calendar_select\">";
        foreach ($periods as $key => $value) {
            $period_select .= '<option value="' . $value . '"';
            if ($value == $period) {
                $period_select .= ' selected=selected';
            }
            $period_select .= '>' . $this->transl($key) . '</option>';
        }
        $period_select .= "\n</select>";

        $html = '';
        $html .= "<input type=\"hidden\" id=\"init_date\" value=\"$date\" />";

        $icon = $public == 1 ? '<span class="ui-icon ui-icon-key" style="display:inline-block;"></span>' : '<span class="ui-icon ui-icon-key" style="display:inline-block;"></span>';

        $html .= "\n<h4>$heading $icon</h4>";

        $html .= "\n<table style=\"width:100%;\">";
        $html .= "\n\t\t<tr>";
        $html .= "\n\t\t<td style=\"width:33%;\">";
        $html .= "<input type=\"text\" class=\"calendar_input\" style=\"width:100px;\" value=\"$month_name\" id=\"datepicker_events\" />";
        $html .= "</td>";
        $html .= "\n\t\t<td style=\"width:33%;\">";
        $html .= $period_select;
        $html .= "</td>";
        $html .= "\n\t\t<td style=\"text-align:right;\">";
        $html .= "<span class=\"ajax_calendar_load\" style=\"display:none;padding-right:20px;\"><img src=\"css/images/spinner_1.gif\" alt=\"spinner\"></span>";
        $today = $this->transl('today');
        $html .= "<span class=\"btn_today\" title=\"$date\"><button type=\"submit\">$today</button></span> ";
        $html .= "<span class=\"btn_previous_period\"><button type=\"submit\">&nbsp;</button></span> ";
        $html .= "<span class=\"btn_next_period\"><button type=\"submit\">&nbsp;</button></span>";
        $html .= "</td>";
        $html .= "\n\t\t</tr>";
        $html .= "\n</table>";

        return $html;
    }

    /**
     * @param int $calendar_views_id
     * @return mixed
     */
    public function getCalendarViews($calendar_views_id)
    {
        $sql = "SELECT * FROM calendar_views 
		WHERE calendar_views_id = :calendar_views_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':calendar_views_id', $calendar_views_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @param null $date
     * @param null $href
     * @param null $calendar_categories_id
     * @param null $period
     * @return string
     */
    public function getCalendarCategoriesRights($date = null, $href = null, $calendar_categories_id = null, $period = null)
    {
        // check category
        if (!is_numeric($calendar_categories_id)) {
            die('missing category...');
        }
        $category = $this->getCalendarCategory($calendar_categories_id);
        if (!$category) {
            die('missing category...');
        }

        $ids = null;
        $ids[] = $calendar_categories_id;

        $acc = $this->getAccessRights($category['calendar_categories_id']);
        $acc_read = $acc[0];
        $acc_edit = $acc[1];
        $acc_read = ($category['public'] == 1) ? true : false;

        // check date
        $date = ($this->_isValidDate($date)) ? $date : date('Y-m-d');

        // mark today
        $today = date('Y-m-d');

        // get year and month from timestamp
        $timestamp = strtotime($date);
        $year = date('Y', $timestamp);
        $month = date('m', $timestamp);

        // get days in month
        $days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        // this month start weekday
        $timestamp = mktime(0, 0, 0, $month, 1, $year);

        // weeks start on monday
        $start_weekday = (date('w', $timestamp) - 1 < 0) ? 6 : date('w', $timestamp) - 1;

        // calendar month
        $month_name = $this->transl(date('F', strtotime($date))) . ' ' . date('Y', strtotime($date));

        $holidays = $this->getHolidays($year);
        $flagdays = $this->getFlagdays($year);
        $events = ($acc_read == true) ? $this->getCalendarEventsMultiple($ids, $date, $period) : array();

        $loop = 0;

        switch ($period) {
            case 'day':
            case '4days':
            case 'week':
            case '2weeks':
            case 'month':
                $loop = 1;
                break;
            case '2months':
                $loop = 2;
                break;
            case '4months':
                $loop = 4;
                break;
            case '6months':
                $loop = 6;
                break;
        }

        // calendar markup
        $html = "\n<div style=\"margin:0 auto;\">";

        switch ($period) {

            case 'day':
            case '4days':

                $html .= "\n<table class=\"ui-widget calendar-event\">";
                $html .= "\n\t<thead>";
                $html .= "\n\t\t<tr class=\"calendar-event-nav\">";
                $html .= "\n\t\t\t<th class=\"ui-widget ui-widget-header\" colspan=\"2\">$month_name</th>";
                $html .= "\n\t\t</tr>";
                $html .= "\n\t</thead>";
                $html .= "\n\t<tbody>";

                $cc = ($period == '4days') ? 4 : 1;

                // current date
                $ts = strtotime($date);

                // one or 4 days
                for ($ii = 1; $ii <= $cc; $ii++) {

                    $ts = ($ii > 1) ? strtotime("+1 day", $ts) : $ts;
                    $date_this = date('Y-m-d', $ts);
                    $class = 'ui-widget ui-widget-content';
                    $class .= (date('w', $ts) == 0 || date('w', $ts) == 6) ? ' ui-state-contrast' : null;
                    $class .= ($date_this == $today) ? ' ui-state-highlight' : null;

                    $html .= "\n\t\t<tr class=\"ui-widget ui-widget-content calendar-event\">";

                    $title_holiday = $this->isSpecialday($date_this, $holidays);
                    $class .= strlen($title_holiday) > 0 ? " ui-state-contrast" : "";
                    $show_holiday = strlen($title_holiday) > 0 ? "<div class=\"ui-state-highlight calendar-somedays \">$title_holiday</div>" : "";

                    $flagday = $this->isSpecialday($date_this, $flagdays);
                    $flagday = strlen($flagday) > 0 ? "<img src=\"css/images/flag_swedish.gif\" title=\"$flagday\" class=\"calendar-flagday\" />" : "";

                    $week_nr = (date('w', $ts) - 1) == 0 ? '<span class="calendar-week">v' . date('W', $ts) . '&nbsp;&nbsp;&nbsp;</span>' : null;
                    $month_name = (date('m', $ts) != date('m', $timestamp) && date('d', $ts) == 1) ? '<div class="calendar-month-name">' . $this->transl(date('F', $ts)) . '</div>' : null;
                    $day = $this->transl(date('D', $ts));
                    $info = "<span class=\"calendar-date\">$month_name $day $week_nr</span><br />";

                    $html .= "\n\t\t\t<td class=\"$class\" width=\"100px\">$info<span class=\"calendar-date\">" . date('j', $ts) . "</span>" . $flagday . $show_holiday . "</td>";

                    $cid = $category['calendar_categories_id'] . '_' . $date_this;
                    $content_cid = 'content_' . $category['calendar_categories_id'] . '_' . $date_this;

                    $event = ($acc_read == true) ? $this->getDayEvent($date_this, $events, $category['calendar_categories_id']) : null;
                    $class_edit = ($acc_edit == true) ? "calendar-event" : null;

                    $html .= "\n\t\t\t<td class=\"$class $class_edit\" id=\"$cid\">";
                    $html .= "<div id=\"$content_cid\" class=\"calendar-event-container\">$event</div></td>";

                    $html .= "\n\t\t</tr>";
                }

                break;

            case 'week':
            case '2weeks':

                $html .= "\n<table class=\"ui-widget calendar-event\">";
                $html .= "\n\t<thead>";
                $html .= "\n\t\t<tr class=\"calendar-event-nav\">";
                $html .= "\n\t\t\t<th class=\"ui-widget ui-widget-header\" colspan=\"2\">$month_name</th>";
                $html .= "\n\t\t</tr>";
                $html .= "\n\t</thead>";
                $html .= "\n\t<tbody>";

                $start = $one_day = strtotime("now", strtotime($date));
                $end = ($period == 'week') ? strtotime("+7 days", strtotime($date)) : strtotime("+14 days", strtotime($date));

                while ($one_day <= $end) {

                    // current date
                    $ts = $one_day;
                    $date_this = date('Y-m-d', $ts);

                    $class = 'ui-widget ui-widget-content';
                    $class .= (date('w', $ts) == 0 || date('w', $ts) == 6) ? ' ui-state-contrast' : null;
                    $class .= ($date_this == $today) ? ' ui-state-highlight' : null;

                    $html .= "\n\t\t<tr class=\"ui-widget ui-widget-content calendar-event\">";

                    $title_holiday = $this->isSpecialday($date_this, $holidays);
                    $class .= strlen($title_holiday) > 0 ? " ui-state-contrast" : "";
                    $show_holiday = strlen($title_holiday) > 0 ? "<div class=\"ui-state-highlight calendar-somedays \">$title_holiday</div>" : "";

                    $flagday = $this->isSpecialday($date_this, $flagdays);
                    $flagday = strlen($flagday) > 0 ? "<img src=\"css/images/flag_swedish.gif\" title=\"$flagday\" class=\"calendar-flagday\" />" : "";

                    $week_nr = (date('w', $ts) - 1) == 0 ? '<span class="calendar-week">v' . date('W', $ts) . '&nbsp;&nbsp;&nbsp;</span>' : null;
                    $month_name = (date('m', $ts) != date('m', $timestamp) && date('d', $ts) == 1) ? '<div class="calendar-month-name">' . $this->transl(date('F', $ts)) . '</div>' : null;
                    $day = $this->transl(date('D', $ts));
                    $info = "<span class=\"calendar-date\">$month_name $day $week_nr</span><br />";

                    $html .= "\n\t\t\t<td class=\"$class\" width=\"100px\">$info<span class=\"calendar-date\">" . date('j', $ts) . "</span>" . $flagday . $show_holiday . "</td>";

                    $cid = $category['calendar_categories_id'] . '_' . $date_this;
                    $content_cid = 'content_' . $category['calendar_categories_id'] . '_' . $date_this;

                    $event = ($acc_read == true) ? $this->getDayEvent($date_this, $events, $category['calendar_categories_id']) : null;
                    $class_edit = ($acc_edit == true) ? "calendar-event" : null;

                    $html .= "\n\t\t\t<td class=\"$class $class_edit\" id=\"$cid\">";
                    $html .= "<div id=\"$content_cid\" class=\"calendar-event-container\">$event</div></td>";

                    $one_day = strtotime("+1 day", $one_day);

                    $html .= "\n\t\t</tr>";

                }
                break;


            case 'month':
            case '2months':
            case '4months':
            case '6months':

                for ($ij = 0; $ij < $loop; $ij++) {

                    $timestamp = strtotime($date);
                    $year = date('Y', $timestamp);
                    $month = date('m', $timestamp);

                    // get days in month
                    $days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);

                    // this month start weekday
                    $timestamp = mktime(0, 0, 0, $month, 1, $year);

                    // weeks start on monday
                    $start_weekday = (date('w', $timestamp) - 1 < 0) ? 6 : date('w', $timestamp) - 1;

                    // calendar month
                    $month_name = $this->transl(date('F', strtotime($date))) . ' ' . date('Y', strtotime($date));

                    // weekdays
                    $weekdays = array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun');

                    // show weeks
                    $weekdays [] = "w";

                    $colspan = 8;

                    $html .= "\n<table class=\"ui-widget calendar-event\">";
                    $html .= "\n\t<thead>";
                    $html .= "\n\t\t<tr class=\"calendar-event-nav\">";
                    $html .= "\n\t\t\t<th class=\"ui-widget ui-widget-header\" colspan=\"$colspan\">$month_name</th>";
                    $html .= "\n\t\t</tr>";
                    $html .= "\n\t\t<tr>";

                    foreach ($weekdays as $weekday) {
                        $html .= "\n\t\t\t<th class=\"ui-widget ui-widget-header weekdays calendar-event\">" . $this->transl($weekday) . "</th>";
                    }

                    $html .= "\n\t\t</tr>";
                    $html .= "\n\t</thead>";
                    $html .= "\n\t<tbody>";
                    $html .= "\n\t\t<tr class=\"ui-widget ui-widget-content calendar-event\">";

                    for ($i = 1, $c = 1, $today, $m = date('m', $timestamp), $y = date('Y', $timestamp); $c <= $days_in_month; ++$i) {

                        $class = ($i <= $start_weekday) ? "ui-state-disabled" : "";

                        if ($start_weekday < $i && $days_in_month >= $c) {

                            $ts = strtotime($y . '-' . $m . '-' . sprintf("%02d", $c));
                            $date_this_month = date('Y-m-d', $ts);
                            $class .= ($date_this_month == $today) ? " ui-state-highlight" : "";
                            $class .= (date('w', $ts) == 0 || date('w', $ts) == 6) ? " ui-state-contrast" : "";

                            $title_holiday = $this->isSpecialday($date_this_month, $holidays);
                            $class .= strlen($title_holiday) > 0 ? " ui-state-contrast" : "";
                            $show_holiday = strlen($title_holiday) > 0 ? "<div class=\"ui-state-highlight calendar-somedays \">$title_holiday</div>" : "";

                            $flagday = $this->isSpecialday($date_this_month, $flagdays);
                            $flagday = strlen($flagday) > 0 ? "<img src=\"css/images/flag_swedish.gif\" title=\"$flagday\" class=\"calendar-flagday\" />" : "";

                            $cid = $category['calendar_categories_id'] . '_' . $date_this_month;
                            $content_cid = 'content_' . $cid;

                            $event = ($acc_read == true) ? $this->getDayEvent($date_this_month, $events, $category['calendar_categories_id']) : null;
                            $class_edit = ($acc_edit == true) ? "calendar-event" : null;

                            $html .= "\n\t\t\t<td class=\"ui-widget ui-widget-content $class_edit $class\" id=\"$cid\" width=\"14%\"><span class=\"calendar-date\">" . date('j', $ts) . "</span>" . $flagday . $show_holiday;

                            $c++;

                        } else {

                            $ts = strtotime($y . '-' . $m . '-01');
                            $n = $start_weekday - $i + 1;
                            $date_previous_month = date('Y-m-d', strtotime('- ' . $n . ' day', $ts));
                            $content_cid = 'content_' . $category['calendar_categories_id'] . '_' . $date_previous_month;
                            $event = ($acc_read == true) ? $this->getDayEvent($date_previous_month, $events, $category['calendar_categories_id']) : null;

                            $html .= "\n\t\t\t<td class=\"ui-widget ui-widget-content $class\" width=\"14%\"><span class=\"calendar-date\">" . date('d', strtotime('- ' . $n . ' day', $ts)) . "</span>";

                        }

                        $html .= "<div id=\"$content_cid\" class=\"calendar-event-container\">$event</div></td>";

                        // wrap week
                        $html .= ($i != 0 && $i % 7 == 0) ? "\n\t\t\t<td class=\"ui-widget ui-widget-content ui-state-highlight\">" . date('W', $ts) . "</td>\n\t\t</tr>\n\t\t<tr class=\"ui-widget ui-widget-content\">" : null;
                        $wrap_close = ($i % 35 == 0) ? true : false;

                    }

                    if (!$wrap_close) {

                        $add_days = 1;
                        while ($i % 7 != 1) {

                            $add_week = true;
                            $ts = strtotime($y . '-' . $m . '-' . $days_in_month);
                            $date_next_month = date('Y-m-d', strtotime('+ ' . $add_days . ' day', $ts));
                            $event = ($acc_read == true) ? $this->getDayEvent($date_next_month, $events, $category['calendar_categories_id']) : null;

                            $html .= "\n\t\t\t<td class=\"ui-widget ui-widget-content ui-state-disabled\"><span class=\"calendar-date\">" . date('j', strtotime('+ ' . $add_days . ' day', $ts)) . "</span>";
                            $html .= "<div class=\"calendar-event-container\">$event</div></td>";

                            ++$i;
                            $add_days++;
                        }

                        $html .= "\n\t\t\t<td class=\"ui-widget ui-widget-content ui-state-highlight\">" . date('W', $ts) . "</td>\n\t\t</tr>";

                    }
                    $html .= "\n\t</tbody>";
                    $html .= "\n\t</table>\n";

                    $date = date('Y-m-d', strtotime("+1 months", strtotime($date)));

                }

                return $html;
                break;

        }

        $html .= "\n\t</tbody>";
        $html .= "\n\t</table>\n";
        $html .= "\n</div>\n";

        return $html;
    }

    /**
     * @param $category
     * @return array
     */
    private function getAccessRights($category)
    {

        //default access rights
        $acc_read = $acc_edit = false;

        if (isset($_SESSION['users_id'])) {
            if (get_role_CMS('editor') == 1) {
                $acc_read = $acc_edit = true;
            } else {

                //check role_CMS author & contributor
                if (get_role_CMS('author') == 1 || get_role_CMS('contributor') == 1) {

                    // user rights to this page
                    $calendar_rights = new CalendarRights();
                    $users_id = isset($_SESSION['users_id']) ? $_SESSION['users_id'] : 0;
                    $users_rights = $calendar_rights->getCalendarUsersRights($category, $users_id);

                    write_debug($users_rights);

                    // groups rights to this page
                    $groups_rights = $calendar_rights->getCalendarGroupsRights($category);

                    // read
                    if ($users_rights) {
                        if ($users_rights['rights_read'] == 1) {
                            $acc_read = true;
                        }
                    } else {
                        if ($groups_rights) {
                            if (get_membership_rights('rights_read', $_SESSION['membership'], $groups_rights)) {
                                $acc_read = true;
                            }
                        }
                    }

                    // edit
                    if ($users_rights) {
                        if ($users_rights['rights_edit'] == 1) {
                            $acc_edit = true;
                        }
                    } else {
                        if ($groups_rights) {
                            if (get_membership_rights('rights_edit', $_SESSION['membership'], $groups_rights)) {
                                $acc_edit = true;
                            }
                        }
                    }
                    

                }
            }
        }
        return array($acc_read, $acc_edit);
    }

    /**
     * @param $ids
     * @param $date
     * @param $period
     * @return array
     */
    public function getCalendarEventsMultiple($ids, $date, $period)
    {

        $qMarks = str_repeat('?,', count($ids) - 1) . '?';

        $date = ($this->_isValidDate($date)) ? $date : date('Y-m-d');
        $timestamp = strtotime($date);
        $year = date('Y', $timestamp);
        $month = date('m', $timestamp);
        $d1 = $date;

        switch ($period) {
            case 'day':
            case '4days':
            case 'week':
                $d2 = date('Y-m-d', strtotime($d1 . ' + 7 days'));
                break;
            case '2weeks':
                $d2 = date('Y-m-d', strtotime($d1 . ' + 14 days'));
                break;
            case '4weeks':
            case '8weeks':
                $d2 = date('Y-m-d', strtotime($d1 . ' + 56 days'));
                break;
            case 'month':
                $d1 = date('Y-m-d', mktime(0, 0, 0, $month, 1, $year));
                $d2 = date('Y-m-d', strtotime($d1 . ' + 31 days'));
            case '2months':
                $d1 = date('Y-m-d', mktime(0, 0, 0, $month, 1, $year));
                $d2 = date('Y-m-d', strtotime($d1 . ' + 62 days'));
            case '4months':
                $d1 = date('Y-m-d', mktime(0, 0, 0, $month, 1, $year));
                $d2 = date('Y-m-d', strtotime($d1 . ' + 124 days'));
                break;
            case '6months':
                $d1 = date('Y-m-d', mktime(0, 0, 0, $month, 1, $year));
                $d2 = date('Y-m-d', strtotime($d1 . ' + 188 days'));
                break;
        }

        $sql = "SELECT calendar_events.calendar_events_id, calendar_events.event, calendar_events.event_date, calendar_categories.calendar_categories_id, calendar_categories.category
		FROM calendar_events
		INNER JOIN calendar_categories ON calendar_events.calendar_categories_id = calendar_categories.calendar_categories_id
		WHERE calendar_events.event_date BETWEEN '$d1' AND '$d2'		
		AND calendar_categories.calendar_categories_id IN
		($qMarks) ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_values($ids));
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param $date
     * @param $days
     * @return mixed
     */
    private function isSpecialday($date, $days)
    {
        foreach ($days as $key => $value) {
            if ($date == $key) {
                return $value;
                break;
            }
        }
    }

    /**
     * @param $date
     * @param $events
     * @param $category_id
     * @return string
     */
    private function getDayEvent($date, $events, $category_id)
    {
        for ($i = 0; $i < count($events); ++$i) {
            if ($category_id == $events[$i]['calendar_categories_id']) {
                if ($date == $events[$i]['event_date']) {
                    return nl2br(strip_tags($events[$i]['event'], $this->validTags()));
                    break;
                }
            }

        }
    }


    /**
     * @param null $date
     * @param null $href
     * @param null $calendar_views_id
     * @param null $period
     * @return string
     */
    public function getCalendarViewsRights($date = null, $href = null, $calendar_views_id = null, $period = null)
    {

        // check category
        if (!is_numeric($calendar_views_id)) {
            die('missing view...');
        }

        // get views
        $views_meta = $this->getCalendarViewCategories($calendar_views_id);

        // validate requested view
        if ($views_meta == null) {
            die('no categories in requested view');
        }

        // extract category id:s from $views_meta
        $ids = null;
        foreach ($views_meta as $views_ids) {
            $ids[] = $views_ids['calendar_categories_id'];
        }

        $acc_read = $acc_edit = true;

        // check date
        $date = ($this->_isValidDate($date)) ? $date : date('Y-m-d');

        // mark today
        $today = date('Y-m-d');

        // get year and month from timestamp
        $timestamp = strtotime($date);
        $year = date('Y', $timestamp);
        $month = date('m', $timestamp);

        // get days in month
        $days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        // this month start weekday
        $timestamp = mktime(0, 0, 0, $month, 1, $year);

        // weeks start on monday

        $start_weekday = (date('w', $timestamp) - 1 < 0) ? 6 : date('w', $timestamp) - 1;
        // calendar month
        $month_name = $this->transl(date('F', strtotime($date))) . ' ' . date('Y', strtotime($date));

        $holidays = $this->getHolidays($year);
        $flagdays = $this->getFlagdays($year);
        $events = ($acc_read == true) ? $this->getCalendarEventsMultiple($ids, $date, $period) : array();

        $loop = 0;
        switch ($period) {
            case 'day':
            case '4days':
            case 'week':
            case '2weeks':
            case '4weeks':
            case '8weeks':
            case 'month':
                $loop = 1;
                break;
            case '2months':
                $loop = 2;
                break;
            case '4months':
                $loop = 4;
                break;
            case '6months':
                $loop = 6;
                break;
        }

        $acc_read = $acc_edit = array();

        // calandar markup
        $html = "\n<div style=\"margin:0 auto;\">";

        switch ($period) {

            case 'day':
            case '4days':

                $size = count($views_meta);
                $colspan = $size + 1;

                $html .= "\n<table class=\"ui-widget calendar-event\">";
                $html .= "\n\t<thead>";
                $html .= "\n\t\t<tr class=\"calendar-event-nav\">";
                $html .= "\n\t\t\t<th class=\"ui-widget ui-widget-header columns\" colspan=\"$colspan\">$month_name</th>";
                $html .= "\n\t\t</tr>";
                $html .= "\n\t\t<tr>";
                $html .= "\n\t\t\t<th class=\"ui-widget ui-widget-header\"></th>";

                foreach ($views_meta as $views) {
                    $acc = $this->getAccessRights($views['calendar_categories_id']);
                    $acc_read[] = ($views['public'] == 1) ? true : $acc[0];
                    $acc_edit[] = $acc[1];
                    $category = $views['category'];
                    $width = floor(90 / $size);
                    $icon = $views['public'] == 1 ? '' : '<span class="ui-icon ui-icon-key" style="display:inline-block;"></span>';
                    $html .= "\n\t\t\t<th style=\"width:$width%;\" class=\"ui-widget ui-widget-header columns\">$category $icon</th>";
                }

                $html .= "\n\t\t</tr>";
                $html .= "\n\t</thead>";
                $html .= "\n\t<tbody>";

                $cc = ($period == '4days') ? 4 : 1;

                // current date
                $ts = strtotime($date);

                // one or 4 days
                for ($ii = 1; $ii <= $cc; $ii++) {

                    // current date
                    $ts = ($ii > 1) ? strtotime("+1 day", $ts) : $ts;
                    $date_this = date('Y-m-d', $ts);

                    $class = 'ui-widget ui-widget-content';
                    $class .= (date('w', $ts) == 0 || date('w', $ts) == 6) ? ' ui-state-contrast' : null;
                    $class .= ($date_this == $today) ? ' ui-state-highlight' : null;

                    $html .= "\n\t\t<tr class=\"ui-widget ui-widget-content calendar-event\">";

                    $title_holiday = $this->isSpecialday($date_this, $holidays);
                    $class .= strlen($title_holiday) > 0 ? " ui-state-contrast" : "";
                    $show_holiday = strlen($title_holiday) > 0 ? "<div class=\"ui-state-highlight calendar-somedays \">$title_holiday</div>" : "";

                    $flagday = $this->isSpecialday($date_this, $flagdays);
                    $flagday = strlen($flagday) > 0 ? "<img src=\"css/images/flag_swedish.gif\" title=\"$flagday\" class=\"calendar-flagday\" />" : "";

                    $week_nr = (date('w', $ts) - 1) == 0 ? '<span class="calendar-week">v' . date('W', $ts) . '&nbsp;&nbsp;&nbsp;</span>' : null;
                    $month_name = (date('m', $ts) != date('m', $timestamp) && date('d', $ts) == 1) ? '<div class="calendar-month-name">' . $this->transl(date('F', $ts)) . '</div>' : null;
                    $day = $this->transl(date('D', $ts));
                    $info = "<span class=\"calendar-date\">$month_name $day $week_nr</span><br />";

                    $html .= "\n\t\t\t<td class=\"$class\" width=\"100px\">$info<span class=\"calendar-date\">" . date('j', $ts) . "</span>" . $flagday . $show_holiday . "</td>";

                    $access_column = 0;
                    foreach ($views_meta as $views) {

                        $cid = $views['calendar_categories_id'] . '_' . $date_this;
                        $content_cid = 'content_' . $views['calendar_categories_id'] . '_' . $date_this;
                        $event = ($acc_read[$access_column] == true) ? $this->getDayEvent($date_this, $events, $views['calendar_categories_id']) : null;
                        $class_edit = ($acc_edit[$access_column] == true) ? "calendar-event" : null;
                        $html .= "\n\t\t\t<td class=\"$class $class_edit\" id=\"$cid\">";
                        $html .= "<div id=\"$content_cid\" class=\"calendar-event-container\">$event</div></td>";
                        $access_column++;
                    }
                    $html .= "\n\t\t</tr>";
                }
                break;

            case 'week':
            case '2weeks':
            case '4weeks':
            case '8weeks':
            case 'month':

                $size = count($views_meta);
                $colspan = $size + 1;

                $html .= "\n<table class=\"ui-widget calendar-event\">";
                $html .= "\n\t<thead>";
                $html .= "\n\t\t<tr class=\"calendar-event-nav\">";
                $html .= "\n\t\t\t<th class=\"ui-widget ui-widget-header columns\" colspan=\"$colspan\">$month_name</th>";
                $html .= "\n\t\t</tr>";
                $html .= "\n\t\t<tr>";
                $html .= "\n\t\t\t<th class=\"ui-widget ui-widget-header\"></th>";

                foreach ($views_meta as $views) {
                    $acc = $this->getAccessRights($views['calendar_categories_id']);
                    $acc_read[] = ($views['public'] == 1) ? true : $acc[0];
                    $acc_edit[] = $acc[1];
                    $category = $views['category'];
                    $width = floor(90 / $size);
                    $icon = $views['public'] == 1 ? '' : '<span class="ui-icon ui-icon-key" style="display:inline-block;"></span>';
                    $html .= "\n\t\t\t<th style=\"width:$width%;\" class=\"ui-widget ui-widget-header columns\">$category $icon</th>";
                }

                $html .= "\n\t\t</tr>";
                $html .= "\n\t</thead>";
                $html .= "\n\t<tbody>";

                $start = $one_day = strtotime("now", strtotime($date));

                if ($period == 'week') {
                    $end = strtotime("+7 days", strtotime($date));
                }
                if ($period == '2weeks') {
                    $end = strtotime("+14 days", strtotime($date));
                }
                if ($period == '4weeks') {
                    $end = strtotime("+28 days", strtotime($date));
                }
                if ($period == '8weeks') {
                    $end = strtotime("+56 days", strtotime($date));
                }
                if ($period == 'month') {
                    $start = $one_day = strtotime($year . '-' . $month . '-01');
                    $end = strtotime($year . '-' . $month . '-' . $days_in_month);
                }

                while ($one_day <= $end) {

                    // current date
                    $ts = $one_day;
                    $date_this = date('Y-m-d', $ts);

                    $class = 'ui-widget ui-widget-content';
                    $class .= (date('w', $ts) == 0 || date('w', $ts) == 6) ? ' ui-state-contrast' : null;
                    $class .= ($date_this == $today) ? ' ui-state-highlight' : null;

                    $html .= "\n\t\t<tr class=\"ui-widget ui-widget-content calendar-event\">";

                    $title_holiday = $this->isSpecialday($date_this, $holidays);
                    $class .= strlen($title_holiday) > 0 ? " ui-state-contrast" : "";
                    $show_holiday = strlen($title_holiday) > 0 ? "<div class=\"ui-state-highlight calendar-somedays \">$title_holiday</div>" : "";

                    $flagday = $this->isSpecialday($date_this, $flagdays);
                    $flagday = strlen($flagday) > 0 ? "<img src=\"css/images/flag_swedish.gif\" title=\"$flagday\" class=\"calendar-flagday\" />" : "";

                    $week_nr = (date('w', $ts) - 1) == 0 ? '<span class="calendar-week">v' . date('W', $ts) . '&nbsp;&nbsp;&nbsp;</span>' : null;
                    $month_name = (date('m', $ts) != date('m', $timestamp) && date('d', $ts) == 1) ? '<div class="calendar-month-name">' . $this->transl(date('F', $ts)) . '</div>' : null;
                    $day = $this->transl(date('D', $ts));
                    $info = "<span class=\"calendar-date\">$month_name $day $week_nr</span><br />";

                    $html .= "\n\t\t\t<td class=\"$class\" width=\"100px\">$info<span class=\"calendar-date\">" . date('j', $ts) . "</span>" . $flagday . $show_holiday . "</td>";

                    $access_column = 0;
                    foreach ($views_meta as $views) {

                        $cid = $views['calendar_categories_id'] . '_' . $date_this;
                        $content_cid = 'content_' . $views['calendar_categories_id'] . '_' . $date_this;

                        $event = ($acc_read[$access_column] == true) ? $this->getDayEvent($date_this, $events, $views['calendar_categories_id']) : null;
                        $class_edit = ($acc_edit[$access_column] == true) ? "calendar-event" : null;

                        $html .= "\n\t\t\t<td class=\"$class $class_edit\" id=\"$cid\">";
                        $html .= "<div id=\"$content_cid\" class=\"calendar-event-container\">$event</div></td>";
                        $access_column++;
                    }

                    $one_day = strtotime("+1 day", $one_day);
                    $html .= "\n\t\t</tr>";
                }
                break;

        }

        $html .= "\n\t</tbody>";
        $html .= "\n\t</table>\n";
        $html .= "\n</div>\n";

        return $html;
    }


    /**
     * @param int $calendar_views_id
     * @return array
     */
    public function getCalendarViewCategories($calendar_views_id)
    {
        $sql = "SELECT calendar_views_members.calendar_views_members_id, calendar_views_members.position, calendar_categories.calendar_categories_id, calendar_categories.category, calendar_categories.description, calendar_categories.public, calendar_categories.rss, calendar_categories.active
		FROM calendar_views_members
		INNER JOIN calendar_categories
		ON calendar_views_members.calendar_categories_id = calendar_categories.calendar_categories_id 
		WHERE calendar_views_id = :calendar_views_id
		ORDER BY calendar_views_members.position";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":calendar_views_id", $calendar_views_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * @param $dateTime
     * @return bool
     */
    private function _isValidDateTime($dateTime)
    {
        if (is_string(($dateTime))) {
            if (preg_match("/^(\d{4})-(\d{2})-(\d{2}) ([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/", $dateTime, $matches)) {
                if (checkdate($matches[2], $matches[3], $matches[1])) {
                    return true;
                }
            }
        }
    }


    /**
     * @param $year
     * @return DateTime
     */
    private function getEaster($year)
    {
        $date = new DateTime($year . '-03-21');
        return $date->add(new DateInterval('P' . easter_days($year) . 'D'));
    }

}

?>
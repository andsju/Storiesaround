<?php

/**
 * Class TablePager
 */
class TablePager
{
    public $url;
    public $sql;
    public $pageIndex;
    public $rowPerPage;
    public $totalRecords;
    public $totalPages;
    public $startIndex;
    public $endIndex;
    public $nextLink;
    public $previousLink;
    public $page = 'page';

    /**
     * TablePager constructor
     */
    public function __construct()
    {
        $this->sql = $sql;
        $index = isset($_REQUEST[$this->page]) ? $_REQUEST[$this->page] : 1;
        $this->pageIndex = ((int)$index == 0) ? 1 : $index;
    }

    /**
     * @return string
     */
    public function getRangeDescription()
    {
        if ($this->totalRecords > 0) {
            $rangeStart = $this->startIndex + 1;
            if (($this->totalRecords < $this->rowPerPage) || ($this->pageIndex == $this->totalPages)) {
                $rangeEnd = $this->totalRecords;
            } else {
                $rangeEnd = $this->endIndex;
            }
            return 'showing ' . $rangeStart . ' - ' . $rangeEnd . ' of ' . $this->totalRecords;
        }
    }

    /**
     *
     */
    public function build()
    {
        $this->setRowPerPage();
        $this->getTotalRecords();
        $this->getTotalPages();
        $this->setPageIndex();
        $this->setStartIndex();
        $this->setEndIndex();
        $this->sql .= ' LIMIT ' . $this->startIndex . ', ' . $this->rowPerPage;
    }

    /**
     * @return mixed
     */
    protected function setRowPerPage()
    {
        if (isset($_GET['hits'])) {
            // aray of allowed values
            $allowed = array(10, 20, 50, 100);
            if (in_array($_GET['hits'], $allowed)) {
                $this->rowPerPage = $_GET['hits'];
            }
            return $this->rowPerPage;
        }
    }

    /**
     * @return string
     */
    protected function getTotalRecords()
    {
        if ($this->sql) {
            $sql = 'SELECT COUNT(*) FROM (' . $this->sql . ') abc';
            $dbh = db_connect();
            $sth = $dbh->query($sql);
            $count = $sth->fetchColumn(0);
            $this->totalRecords = $count;
            $dbh = NULL;
        }
        return $this->totalRecords;
    }

    /**
     * @return int
     */
    protected function getTotalPages()
    {
        if ($this->sql) {
            $this->totalPages = (int)ceil($this->totalRecords / $this->rowPerPage);
        }
        return $this->totalPages;
    }

    /**
     *
     */
    protected function setPageIndex()
    {
        if (isset($totalPages)) {
            if ($this->pageIndex > $this->totalPages) {
                $pageIndex = $totalPages;
            }
        }
    }

    /**
     *
     */
    protected function setStartIndex()
    {
        $this->startIndex = ($this->pageIndex - 1) * $this->rowPerPage;
    }

    /**
     *
     */
    protected function setEndIndex()
    {
        if ($this->totalRecords > $this->rowPerPage) {
            $this->endIndex = $this->startIndex + $this->rowPerPage;
        } else {
            $endIndex = $this->totalRecords;
        }
        if (isset($pageIndex) && isset($totalPages)) {
            if ($pageIndex == $totalPages) {
                if ($totalRecords < $totalPages * $rowPerPage) {
                    $endIndex = $totalRecords;
                }
            }
        }
    }

    /**
     * @return array
     */
    public function getPagedData()
    {
        $dbh = db_connect();
        $sth = $dbh->query($this->sql);
        $rows = $sth->fetchAll(PDO::FETCH_ASSOC);
        $dbh = NULL;
        return $rows;
    }

    /**
     * @param $anchor
     * @param $span
     * @param $anchor_active
     * @param $span_active
     * @param $bookmark
     * @param $i_this
     * @return string
     */
    public function getNumberLinks($anchor, $span, $anchor_active, $span_active, $bookmark, $i_this)
    {
        // previous
        $links = '<a class="' . $anchor . '" href="' . $this->url . $this->getSign() . $this->getQuerypage() . '&' . $this->page . '=' . ($this->pageIndex - 1) . $bookmark . '">&laquo;</a> ';
        // page
        for ($i = 1; $i <= $this->totalPages; $i++) {
            if ($i == $i_this) {
                $links .= '<span class="' . $span_active . '"><a class="' . $anchor_active . '" href="' . $this->url . $this->getSign() . $this->getQuerypage() . '&' . $this->page . '=' . $i . $bookmark . '">' . $i . '</a></span> ';
            } else {
                $links .= '<span class="' . $span . '"><a class="' . $anchor . '" href="' . $this->url . $this->getSign() . $this->getQuerypage() . '&' . $this->page . '=' . $i . $bookmark . '">' . $i . '</a></span> ';
            }
        }
        // next
        if (isset($_GET[$this->page])) {
            $i_next = $_GET[$this->page] + 1;
            if ($i_next <= $this->totalPages) {
                $links .= '<a class="' . $anchor . '" href="' . $this->url . $this->getSign() . $this->getQuerypage() . '&' . $this->page . '=' . ($this->pageIndex + 1) . $bookmark . '">&raquo;</a>';
            }
        }
        return $links;
    }

    /**
     * @return string
     * build links for page result
     * pass css style for span / a tags, a bookmark, active page result
     */
    public function getSign()
    {
        if (strrpos($this->url, '?')) {
            $path = '&';
        } else {
            $path = '?';
        }
        return $path;
    }

    /**
     * @return string
     * build split options
     */
    protected function getQuerypage()
    {
        $path = 'hits=' . (isset($_GET['hits']) ? $_GET['hits'] : '') . '&orderby=' . (isset($_GET['orderby']) ? $_GET['orderby'] : '') . '&sort=' . (isset($_GET['sort']) ? $_GET['sort'] : '');
        return $path;
    }

    /**
     * @param $anchor
     * @param $bookmark
     * @return string
     * build querystring for split options
     */
    public function getSplitOptions($anchor, $bookmark)
    {
        $result = 'result/page: ';
        $result .= '<a class="' . $anchor . '" href=' . $this->url . $this->getSign() . $this->getQuerySplitOptions(10) . $bookmark . '>10</a>|';
        $result .= '<a class="' . $anchor . '" href=' . $this->url . $this->getSign() . $this->getQuerySplitOptions(20) . $bookmark . '>20</a>|';
        $result .= '<a class="' . $anchor . '" href=' . $this->url . $this->getSign() . $this->getQuerySplitOptions(50) . $bookmark . '>50</a>|';
        $result .= '<a class="' . $anchor . '" href=' . $this->url . $this->getSign() . $this->getQuerySplitOptions(100) . $bookmark . '>100</a>|';
        return $result;
    }

    /**
     * @param $splitby
     * @return string
     */
    protected function getQuerySplitOptions($splitby)
    {
        $path = 'hits=' . ($splitby) . '&' . $this->page . '=' . (isset($_GET['page']) ? $_GET['page'] : '') . '&orderby=' . (isset($_GET['orderby']) ? $_GET['orderby'] : '') . '&sort=' . (isset($_GET['sort']) ? $_GET['sort'] : '');
        return $path;
    }

    /**
     * @param $column_name
     * @param $orderby
     * @param $bookmark
     * @return string
     */
    public function getColumnSorter($column_name, $orderby, $bookmark)
    {
        $path = '<a href="' . $this->url . $this->getSign() . $this->getQueryColumn() . '&orderby=' . $orderby . '&sort=' . $this->getSwitchSort() . $bookmark . '" class="paging_th">' . $column_name . '</a>';
        return $path;
    }

    /**
     * @return string
     */
    protected function getQueryColumn()
    {
        $path = 'hits=' . (isset($_GET['hits']) ? $_GET['hits'] : '') . '&' . $this->page . '=';
        return $path;
    }

    /**
     * @return string
     * build column sorter; column (friendly name), column (data field), bookmark
     */
    protected function getSwitchSort()
    {
        $path = '';
        if (isset($_GET['sort'])) {
            $path = $_GET['sort'];
            $path = ($path == 'desc') ? 'asc' : 'desc';
        } else {
            $path = 'asc';
        }
        return $path;
    }

    /**
     * @return string
     * build querystring for edit
     */
    public function getQueryEdit()
    {
        $path = '&hits=' . (isset($_GET['hits']) ? $_GET['hits'] : '') . '&' . $this->page . '=' . (isset($_GET['page']) ? $_GET['page'] : '') . '&orderby=' . (isset($_GET['orderby']) ? $_GET['orderby'] : '') . '&sort=' . (isset($_GET['sort']) ? $_GET['sort'] : '');
        return $path;
    }

}

?>
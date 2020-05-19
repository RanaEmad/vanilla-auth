<?php

namespace VanillaAuth\Services;

use VanillaAuth\Core\Request;

class Pagination
{
    protected $uri;
    protected $totalCount;
    protected $rows;
    protected $pages;
    public function __construct($uri, $totalCount, $rows = 15)
    {
        $this->uri = baseUrl($uri);
        $this->totalCount = $totalCount;
        if ($rows <= 0) {
            $rows = 15;
        }
        $this->rows = $rows;
        $this->pages = ceil($totalCount / $rows);
    }

    public function getOffset()
    {
        $pageNumber = Request::get("page");
        if (!$pageNumber || $pageNumber == 1) {
            return 0;
        }
        $offset = ($pageNumber - 1) * $this->rows;
        return $offset;
    }
    public function getRows()
    {
        return $this->rows;
    }
    public function getCurrentPage()
    {
        $pageNumber = Request::get("page");
        if (!$pageNumber) {
            $pageNumber = 1;
        }
        return $pageNumber;
    }
    public function getPaginationLinks()
    {
        $currentPage = $this->getCurrentPage();
        $links = [];
        $links[1] = $this->uri . "?page=1";
        for ($i = $currentPage - 4; $i <= $currentPage + 4; $i++) {
            if ($i > 1 && $i < $this->pages) {
                $link = $this->uri . "?page=$i";
                $links[$i] = $link;
            }
        }
        $links[$this->pages] = $this->uri . "?page=$this->pages";

        $next = $currentPage + 1;
        if ($next >= 1 && $next <= $this->pages) {
            $links["next"] = $this->uri . "?page=$next";
        } else {
            $links["next"] = "";
        }

        $previous = $currentPage - 1;
        if ($previous >= 1 && $previous <= $this->pages) {
            $links["previous"] = $this->uri . "?page=$previous";
        } else {
            $links["previous"] = "";
        }
        $links["current"] = $currentPage;

        return $links;
    }
}

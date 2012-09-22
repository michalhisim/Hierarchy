<?php

namespace Tree;

/**
 * Nette Hierarchy
 *
 * @copyright Copyright (c) 2012 Michal Šimon
 */

use \Nette\Diagnostics\Debugger;

class ForumNode extends HierarchyNode {

    public $topics = NULL;
    public $changed = NULL;

    function __construct($info) {

        parent::__construct($info);

        $this->topics = $info->topics;
        $this->changed = $info->changed;
    }

}
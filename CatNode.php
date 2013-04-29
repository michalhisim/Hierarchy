<?php

namespace Tree;

/**
 * Nette Hierarchy
 *
 * @copyright Copyright (c) 2012 Michal Šimon
 */
use \Nette\Diagnostics\Debugger;

class CatNode extends HierarchyNode {

    public $type = NULL;
    public $alias = NULL;
    public $description = NULL;
    public $visible = NULL;

    function __construct($info) {
        parent::__construct($info);

        $this->type = $info->type;
        $this->alias = $info->alias;
        $this->description = $info->description;
        $this->id = $info->cat_id;
        $this->visible = $info->visible;
    }

    /**
     * Get visible chidren
     * @param bool $visibilityFilter
     * @return array
     */
    public function getCildren($visibilityFilter = true) {
        if ($this->children != NULL) {
            if ($visibilityFilter) {
                $result = array();

                foreach ($this->children AS $key => $child) {
                    if (!$child->visible) {
                        continue;
                    }

                    $result[$key] = $child;
                }

                return $result;
            }

            return $this->children;
        }

        return array();
    }

    /**
     * Adding a subnode
     * @param HierarchyNode $child
     * @return bool
     */
    public function addChild(IHierarchyNode $child) {

        if ($child->rootId == $this->id) {
            $child->level = $this->level + 1;

            $this->children[$child->alias] = $child;

            return true;
        } else {
            //Not my child -> delegate

            if ($this->children != NULL) {
                foreach ($this->children AS $nodeRow) {

                    if ($nodeRow->addChild($child)) {
                        return true;
                    }
                }
            }

            return false;
        }
    }

}
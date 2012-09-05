<?php

namespace Tree;

/**
 * Nette Hierarchy
 *
 * @copyright Copyright (c) 2012 Michal Šimon
 */

use \Nette\Diagnostics\Debugger;

class HierarchyNode implements IHierarchyNode {

    public $id = NULL;
    public $name = NULL;
    public $rootId = NULL;
    public $children = NULL;

    function __construct($info) {
        $this->id = $info->id;
        $this->name = $info->name;
        $this->rootId = $info->root_id;
    }

    /**
     * Adding a subnode
     * @param HierarchyNode $child
     * @return bool
     */     
    public function addChild(IHierarchyNode $child) {

        if ($child->rootId == $this->id) {
            $this->children[$child->id] = $child;

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

    /**
     * Searching in tree
     * @param int $id
     * @return HierarchyNode or false
     */    
    public function findChild($id) {
        if ($id == $this->id) {
            return $this;
        } elseif ($this->children != NULL) {
            if (isset($this->children[$id])) {
                return $this->children[$id];
            } else {
                foreach ($this->children AS $child) {
                    if ($result = $child->findChild($id)) {
                        return $result;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Generate array of node IDs as path to node
     * @param int $id
     * @return array of IDs
     */     
    public function getPathTo($id) {
        $path = false;

        if ($id == $this->id) {
            $path[] = $this->id;
        } elseif ($this->children != NULL) {
            if (isset($this->children[$id])) {
                $path[] = $this->id;
                $path[] = $this->children[$id]->id;
            } else {
                foreach ($this->children AS $child) {
                    if ($result = $child->getPathTo($id)) {
                        return $result;
                    }
                }
            }
        }

        return $path;
    }
}